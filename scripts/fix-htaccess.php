<?php
/**
 * Write standard WordPress rewrite .htaccess and flush rules.
 */
require '/var/www/html/wp-load.php';

$htaccess = <<<'HTA'
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress
HTA;

$path = ABSPATH . '.htaccess';
$ok = file_put_contents($path, $htaccess . "\n");
if ($ok === false) {
	fwrite(STDERR, "Could not write {$path}\n");
	exit(1);
}

update_option('permalink_structure', '/%postname%/');
flush_rewrite_rules(true);

echo "Wrote {$path} ({$ok} bytes)\n";
echo file_get_contents($path);
