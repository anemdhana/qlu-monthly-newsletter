#!/usr/bin/env python3
"""
Rebuild Jnanodayam readings + sample magazine inside local Docker WordPress.

Prereqs:
  - Docker Desktop running
  - WordPress 5-minute install already finished at http://localhost:8080
  - Python 3.9+

Usage (from repo root or anywhere):
  python scripts/bootstrap_content.py
  python scripts/bootstrap_content.py --start   # also docker compose up -d
  python scripts/bootstrap_content.py --skip-magazine
"""

from __future__ import annotations

import argparse
import shutil
import subprocess
import sys
import time
from pathlib import Path

REPO_ROOT = Path(__file__).resolve().parent.parent
CONTENT_DEVI = REPO_ROOT / "content" / "jnanodayam-devi"
ASSETS_IMPORT = REPO_ROOT / "assets" / "import"
SIBLING_DEVI = (
    REPO_ROOT.parent
    / "quantum-life-university"
    / "Swadhyaya"
    / "9-Day-Devi-Navaratri-Dr-Newton"
)

SERVICE = "wordpress"
PHP_SCRIPTS = [
    ("activate-theme.php", None),
    ("fix-htaccess.php", None),
    ("import-jnanodayam-devi.php", "needs_devi"),
    ("enrich-sample-from-assets.php", "needs_assets"),
    ("fix-magazine-jnanodayam-links.php", "needs_magazine"),
]


def run(cmd: list[str], *, check: bool = True, cwd: Path | None = None) -> subprocess.CompletedProcess[str]:
    print(f"\n> {' '.join(cmd)}")
    return subprocess.run(
        cmd,
        cwd=str(cwd or REPO_ROOT),
        check=check,
        text=True,
        encoding="utf-8",
        errors="replace",
    )


def docker_compose(*args: str, check: bool = True) -> subprocess.CompletedProcess[str]:
    return run(["docker", "compose", *args], check=check)


def ensure_env() -> None:
    env = REPO_ROOT / ".env"
    example = REPO_ROOT / ".env.example"
    if not env.exists() and example.exists():
        shutil.copy(example, env)
        print(f"Created {env} from .env.example")


def wait_for_wordpress(timeout_s: int = 120) -> None:
    print("Waiting for WordPress container…")
    deadline = time.time() + timeout_s
    while time.time() < deadline:
        ps = docker_compose("ps", "--status", "running", "--services", check=False)
        if SERVICE in (ps.stdout or ""):
            # Confirm PHP can load wp-load (DB up)
            probe = docker_compose(
                "exec",
                "-T",
                SERVICE,
                "php",
                "-r",
                "echo file_exists('/var/www/html/wp-load.php') ? 'ok' : 'no';",
                check=False,
            )
            if "ok" in (probe.stdout or ""):
                print("WordPress container is up.")
                return
        time.sleep(3)
    raise SystemExit("Timed out waiting for WordPress. Run: docker compose up -d")


def wp_installed() -> bool:
    """True if wp_options exists (installer finished)."""
    result = docker_compose(
        "exec",
        "-T",
        SERVICE,
        "php",
        "-r",
        r"""
require '/var/www/html/wp-load.php';
global $wpdb;
$t = $wpdb->prefix . 'options';
$n = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $t));
echo $n === $t ? 'yes' : 'no';
""",
        check=False,
    )
    out = (result.stdout or "").strip()
    return "yes" in out


def ensure_devi_html() -> Path:
    day1 = CONTENT_DEVI / "day-01-en.html"
    if day1.is_file():
        print(f"Using Devi HTML: {CONTENT_DEVI}")
        return CONTENT_DEVI

    if SIBLING_DEVI.is_dir() and (SIBLING_DEVI / "day-01-en.html").is_file():
        print(f"Copying Devi HTML from sibling:\n  {SIBLING_DEVI}\n  → {CONTENT_DEVI}")
        CONTENT_DEVI.parent.mkdir(parents=True, exist_ok=True)
        if CONTENT_DEVI.exists():
            shutil.rmtree(CONTENT_DEVI)
        shutil.copytree(
            SIBLING_DEVI,
            CONTENT_DEVI,
            ignore=shutil.ignore_patterns("*.docx", "build_docx.py", "__pycache__"),
        )
        return CONTENT_DEVI

    raise SystemExit(
        "Missing Devi HTML.\n"
        f"  Expected: {CONTENT_DEVI}\n"
        f"  Or sibling: {SIBLING_DEVI}\n"
        "See docs/SHARE.md §2."
    )


def ensure_assets() -> None:
    if not ASSETS_IMPORT.is_dir():
        raise SystemExit(f"Missing magazine assets folder: {ASSETS_IMPORT}")
    needed = [
        "qlu-logo.png",
        "global-assets-20261021-2fskhw.png",
        "summary-of-the-themes-covered-in-that-month-20261021-k1yoys.png",
        "highlight-classes-of-the-month-20261022-u1snxe.jpg",
        "dr-newton-s-visits-and-classes-in-other-places-20261022-3zdjv2.jpg",
    ]
    missing = [n for n in needed if not (ASSETS_IMPORT / n).is_file()]
    if missing:
        raise SystemExit(
            "Missing files under assets/import/:\n  - " + "\n  - ".join(missing)
        )
    print(f"Using magazine assets: {ASSETS_IMPORT}")


def wordpress_container_id() -> str:
    result = docker_compose("ps", "-q", SERVICE, check=False)
    cid = (result.stdout or "").strip().splitlines()
    if not cid or not cid[0].strip():
        raise SystemExit("WordPress service not running. Try: python scripts/bootstrap_content.py --start")
    return cid[0].strip()


def docker_cp(src: Path, dest_in_container: str) -> None:
    """Copy host path into the WordPress container (e.g. /tmp/jnanodayam-devi)."""
    # Prefer compose cp; fall back to docker cp + container id.
    cp = subprocess.run(
        [
            "docker",
            "compose",
            "cp",
            str(src),
            f"{SERVICE}:{dest_in_container}",
        ],
        cwd=str(REPO_ROOT),
        text=True,
        encoding="utf-8",
        errors="replace",
    )
    if cp.returncode == 0:
        print(f"> docker compose cp {src} {SERVICE}:{dest_in_container}")
        return
    cid = wordpress_container_id()
    run(["docker", "cp", str(src), f"{cid}:{dest_in_container}"])


def run_php_in_container(script_name: str) -> None:
    local = REPO_ROOT / "scripts" / script_name
    if not local.is_file():
        raise SystemExit(f"Missing script: {local}")
    remote = f"/tmp/{script_name}"
    docker_cp(local, remote)
    docker_compose("exec", "-T", SERVICE, "php", remote)


def main() -> int:
    parser = argparse.ArgumentParser(
        description="Seed Jnanodayam + sample magazine into Docker WordPress."
    )
    parser.add_argument(
        "--start",
        action="store_true",
        help="Run docker compose up -d before seeding",
    )
    parser.add_argument(
        "--skip-magazine",
        action="store_true",
        help="Only import Jnanodayam readings (skip magazine enrich)",
    )
    parser.add_argument(
        "--skip-devi-copy",
        action="store_true",
        help="Do not auto-copy from sibling quantum-life-university folder",
    )
    args = parser.parse_args()

    print(f"Repo: {REPO_ROOT}")
    ensure_env()

    if args.start:
        docker_compose("up", "-d")

    wait_for_wordpress()

    if not wp_installed():
        raise SystemExit(
            "WordPress is not installed yet.\n"
            "Open http://localhost:8080 and finish the installer, then re-run this script."
        )
    print("WordPress install detected.")

    if args.skip_devi_copy and not (CONTENT_DEVI / "day-01-en.html").is_file():
        raise SystemExit(f"--skip-devi-copy set but missing {CONTENT_DEVI}")

    if not args.skip_devi_copy:
        ensure_devi_html()
    else:
        print(f"Using existing Devi HTML: {CONTENT_DEVI}")

    if not args.skip_magazine:
        ensure_assets()

    # Copy content into container paths expected by PHP scripts
    print("\n=== Copy Devi HTML into container ===")
    docker_cp(CONTENT_DEVI, "/tmp/jnanodayam-devi")

    if not args.skip_magazine:
        print("\n=== Copy magazine assets into container ===")
        docker_cp(ASSETS_IMPORT, "/tmp/qlu-import")

    for script, flag in PHP_SCRIPTS:
        if flag == "needs_magazine" and args.skip_magazine:
            print(f"Skip {script} (--skip-magazine)")
            continue
        if flag == "needs_assets" and args.skip_magazine:
            print(f"Skip {script} (--skip-magazine)")
            continue
        print(f"\n=== {script} ===")
        run_php_in_container(script)

    port = "8080"
    env_file = REPO_ROOT / ".env"
    if env_file.exists():
        for line in env_file.read_text(encoding="utf-8").splitlines():
            if line.startswith("WORDPRESS_PORT="):
                port = line.split("=", 1)[1].strip() or port

    print("\nDone.\n")
    print(f"  Programme:   http://localhost:{port}/")
    print(f"  Day 1 EN:    http://localhost:{port}/jnanodayam/devi-navaratri-day-01-en/")
    print(f"  Magazine Te: http://localhost:{port}/issues/october-2026-te/")
    print(f"  Magazine En: http://localhost:{port}/issues/october-2026-en/")
    return 0


if __name__ == "__main__":
    try:
        raise SystemExit(main())
    except subprocess.CalledProcessError as e:
        print(f"\nCommand failed with exit {e.returncode}", file=sys.stderr)
        raise SystemExit(e.returncode) from e
    except KeyboardInterrupt:
        print("\nCancelled.", file=sys.stderr)
        raise SystemExit(130) from None
