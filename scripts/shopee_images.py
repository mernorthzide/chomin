#!/usr/bin/env python3
"""
Step 2/2 of the Shopee -> CHO.MIN catalogue sync. See scripts/shopee_extract.py.

Reads scripts/shopee_raw.json, downloads gallery + colour-swatch images from the
Shopee CDN into storage/app/public/products/shopee/<family>/, samples an approximate
hex per swatch, and writes database/seeders/data/shopee.json for ShopeeProductSeeder.

Run:  python3 scripts/shopee_images.py   (requires Pillow)
"""
import io
import json
import os
import ssl
import urllib.request

from PIL import Image

HERE = os.path.dirname(__file__)
PROJ = os.path.dirname(HERE)
RAW = os.path.join(HERE, "shopee_raw.json")
IMG_ROOT = os.path.join(PROJ, "storage/app/public/products/shopee")
OUT_JSON = os.path.join(PROJ, "database/seeders/data/shopee.json")
CDN = "https://down-th.img.susercontent.com/file/"

FAMILY_EN = {
    "SOFT PASTELS": "Soft Pastels", "EARTH TONES": "Earth Tones",
    "OCEAN BLUES": "Ocean Blues", "CLASSIC NEUTRALS": "Classic Neutrals",
    "PINSTRIPE": "Pinstripe", "BOLD COLORS": "Bold Colors",
}

_ctx = ssl.create_default_context()
_ctx.check_hostname = False
_ctx.verify_mode = ssl.CERT_NONE


def fetch(hash_):
    req = urllib.request.Request(CDN + hash_, headers={"User-Agent": "Mozilla/5.0"})
    with urllib.request.urlopen(req, timeout=30, context=_ctx) as r:
        return r.read()


def hex_of(img_bytes):
    im = Image.open(io.BytesIO(img_bytes)).convert("RGB")
    w, h = im.size
    im = im.crop((w // 4, h // 4, w * 3 // 4, h * 3 // 4)).resize((1, 1), Image.LANCZOS)
    r, g, b = im.getpixel((0, 0))
    return f"#{r:02X}{g:02X}{b:02X}"


def main():
    os.makedirs(os.path.dirname(OUT_JSON), exist_ok=True)
    raw = json.load(open(RAW))
    out = []
    for rec in raw:
        fam = rec["family"]
        slug = fam.lower().replace(" ", "-")
        d = os.path.join(IMG_ROOT, slug)
        os.makedirs(d, exist_ok=True)

        gallery = []
        for i, h in enumerate(rec["gallery"], 1):
            try:
                open(os.path.join(d, f"gallery-{i}.jpg"), "wb").write(fetch(h))
                gallery.append(f"products/shopee/{slug}/gallery-{i}.jpg")
            except Exception as e:
                print("  gallery fail", fam, str(e)[:60])

        colors = []
        for c in rec["colors"]:
            code, sw = c["code"], c.get("swatch")
            hexv, path = "#CCCCCC", None
            if sw:
                try:
                    b = fetch(sw)
                    open(os.path.join(d, f"swatch-{code}.jpg"), "wb").write(b)
                    path = f"products/shopee/{slug}/swatch-{code}.jpg"
                    hexv = hex_of(b)
                except Exception as e:
                    print("  swatch fail", fam, code, str(e)[:60])
            n = code[-2:].lstrip("0") or "0"
            colors.append({
                "code": code, "name_en": f"{FAMILY_EN[fam]} {n}",
                "name_th": f"{fam} {n}", "hex": hexv, "swatch": path,
            })

        out.append({
            "family": fam, "family_en": FAMILY_EN[fam], "slug": slug,
            "itemid": rec["itemid"], "price": rec["price"],
            "sizes": rec["sizes"], "gallery": gallery, "colors": colors,
            "raw_title": rec["title"], "raw_description": rec["description"],
        })
        print(f"{fam}: gallery={len(gallery)} colors={len(colors)}")

    json.dump(out, open(OUT_JSON, "w"), ensure_ascii=False, indent=1)
    print("Wrote", OUT_JSON, "| products:", len(out),
          "| colors:", sum(len(o["colors"]) for o in out))


main()
