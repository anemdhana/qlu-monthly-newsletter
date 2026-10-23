#!/usr/bin/env python3
"""
Seed WordPress magazine issues from content/magazine/YYYY-MM/ folders.

See docs/MAGAZINE-SEED.md for naming conventions.

  python scripts/seed_magazine_from_folder.py --month 2026-10
  python scripts/seed_magazine_from_folder.py --month 2026-10 --init-from-template
"""

from __future__ import annotations

import argparse
import json
import re
import shutil
import subprocess
import sys
from pathlib import Path

REPO = Path(__file__).resolve().parent.parent
MAG_ROOT = REPO / "content" / "magazine"
TEMPLATE = MAG_ROOT / "_template"
SERVICE = "wordpress"

SECTION_DIRS = [
    "00-masthead",
    "01-themes",
    "02-highlight-classes",
    "03-experiences",
    "04-visits",
    "05-vanya",
    "06-campus",
    "07-upcoming",
    "08-closing",
]


def run(cmd: list[str], *, check: bool = True) -> subprocess.CompletedProcess[str]:
    print(f"> {' '.join(cmd)}")
    return subprocess.run(
        cmd,
        cwd=str(REPO),
        check=check,
        text=True,
        encoding="utf-8",
        errors="replace",
    )


def compose(*args: str, check: bool = True) -> subprocess.CompletedProcess[str]:
    return run(["docker", "compose", *args], check=check)


def container_id() -> str:
    r = compose("ps", "-q", SERVICE, check=False)
    lines = (r.stdout or "").strip().splitlines()
    if not lines:
        raise SystemExit("WordPress not running. Start with: docker compose up -d")
    return lines[0].strip()


def docker_cp(src: Path, dest: str) -> None:
    r = subprocess.run(
        ["docker", "compose", "cp", str(src), f"{SERVICE}:{dest}"],
        cwd=str(REPO),
        text=True,
        encoding="utf-8",
        errors="replace",
    )
    if r.returncode == 0:
        print(f"> docker compose cp {src} {SERVICE}:{dest}")
        return
    cid = container_id()
    run(["docker", "cp", str(src), f"{cid}:{dest}"])


def init_from_template(month: str) -> Path:
    dest = MAG_ROOT / month
    if dest.exists():
        raise SystemExit(f"Already exists: {dest} (delete it first or omit --init-from-template)")
    if not TEMPLATE.is_dir():
        raise SystemExit(f"Missing template: {TEMPLATE}")
    shutil.copytree(TEMPLATE, dest)
    meta_path = dest / "issue.json"
    meta = {
        "title_te": f"నెలవారీ పత్రిక — {month}",
        "title_en": f"Monthly Magazine — {month}",
        "slug_te": f"{month}-te",
        "slug_en": f"{month}-en",
    }
    meta_path.write_text(json.dumps(meta, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    print(f"Created {dest} from template. Add photos + edit content.*.txt, then re-run without --init-from-template.")
    return dest


def seed(month: str) -> None:
    if not re.fullmatch(r"\d{4}-\d{2}", month):
        raise SystemExit("Month must be YYYY-MM")

    issue_dir = MAG_ROOT / month
    if not issue_dir.is_dir():
        raise SystemExit(
            f"Missing {issue_dir}\n"
            f"  Create with: python scripts/seed_magazine_from_folder.py --month {month} --init-from-template"
        )

    # Soft check: at least one section folder
    found = [d for d in SECTION_DIRS if (issue_dir / d).is_dir()]
    if not found:
        raise SystemExit(f"No section folders under {issue_dir}. See docs/MAGAZINE-SEED.md")

    print(f"Issue folder: {issue_dir}")
    print(f"Sections present: {', '.join(found)}")

    docker_cp(issue_dir, "/tmp/magazine-issue")
    php = REPO / "scripts" / "import-magazine-from-folder.php"
    docker_cp(php, "/tmp/import-magazine-from-folder.php")
    compose("exec", "-T", SERVICE, "php", "/tmp/import-magazine-from-folder.php", month)

    port = "8080"
    env = REPO / ".env"
    if env.exists():
        for line in env.read_text(encoding="utf-8").splitlines():
            if line.startswith("WORDPRESS_PORT="):
                port = line.split("=", 1)[1].strip() or port

    print("\nOpen:")
    print(f"  http://localhost:{port}/issues/{month}-te/")
    print(f"  http://localhost:{port}/issues/{month}-en/")


def main() -> int:
    p = argparse.ArgumentParser(description="Seed magazine from content/magazine/YYYY-MM/")
    p.add_argument("--month", required=True, help="Issue id YYYY-MM")
    p.add_argument(
        "--init-from-template",
        action="store_true",
        help="Copy content/magazine/_template → YYYY-MM and exit",
    )
    args = p.parse_args()

    if args.init_from_template:
        init_from_template(args.month)
        return 0

    seed(args.month)
    return 0


if __name__ == "__main__":
    try:
        raise SystemExit(main())
    except subprocess.CalledProcessError as e:
        print(f"Command failed ({e.returncode})", file=sys.stderr)
        raise SystemExit(e.returncode) from e
