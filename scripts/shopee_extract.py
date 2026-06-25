#!/usr/bin/env python3
"""
Step 1/2 of the Shopee -> CHO.MIN catalogue sync.

Shopee blocks automated access (anti-bot / 403). The reliable path is to read
from a REAL logged-in Chrome tab that already has the shop open, via browser-harness.

USAGE:
    1. Open https://shopee.co.th/chomin640#product_list in your Chrome and let it load.
    2. Run:   browser-harness < scripts/shopee_extract.py
       (browser-harness pre-imports js(), switch_tab(), list_tabs(), ...)
    3. Then run step 2:   python3 scripts/shopee_images.py
       which downloads images + samples swatch colours into
       database/seeders/data/shopee.json.
    4. Seed:   php artisan db:seed --class=Database\\Seeders\\ShopeeProductSeeder

Output of this step: scripts/shopee_raw.json (raw per-family data, no images).
"""
import json
import os
import time

SHOP_ID = 1727921020
OUT = os.path.join(os.path.dirname(__file__), "shopee_raw.json")

# Canonical listing per colour family (each family has 2 duplicate listings on
# Shopee; we keep the one with the richer gallery / cleaner colour codes).
CANON = [
    ("SOFT PASTELS", 29644897450),
    ("EARTH TONES", 49461138202),
    ("OCEAN BLUES", 54561112791),
    ("CLASSIC NEUTRALS", 56161109071),
    ("PINSTRIPE", 41031440569),
    ("BOLD COLORS", 41031176354),
]


def find_shop_tab():
    for t in list_tabs():  # noqa: F821 (provided by browser-harness)
        if "chomin640" in str(t.get("url", "")):
            return t["targetId"]
    return None


def fetch_item(item_id):
    """get_pc is intermittently empty under load — retry with backoff in-page."""
    res = js(f"""
    (async () => {{
      const shopid={SHOP_ID}, itemid={item_id};
      const sleep=ms=>new Promise(r=>setTimeout(r,ms));
      let j=null;
      for(let a=0;a<8;a++){{
        const r=await fetch(`/api/v4/pdp/get_pc?shop_id=${{shopid}}&item_id=${{itemid}}`,
          {{headers:{{'x-api-source':'pc','x-shopee-language':'th'}},credentials:'include'}});
        j=await r.json(); if(j.data&&j.data.item) break; await sleep(700);
      }}
      const it=j.data.item;
      const colorTier=(it.tier_variations||[]).find(t=>t.name&&t.name.includes('สี'))||{{}};
      const sizeTier=(it.tier_variations||[]).find(t=>t.name&&t.name.includes('ไซ'))||{{}};
      const colors=(colorTier.options||[]).map((c,i)=>({{code:c, swatch:(colorTier.images||[])[i]||null}}));
      return JSON.stringify({{
        itemid, title: it.title, title_tr: it.title_tr||"",
        description: it.description||"", description_tr: it.description_tr||"",
        price: it.price_min/100000,
        gallery: (j.data.product_images&&j.data.product_images.images)?j.data.product_images.images:[],
        colors, sizes: sizeTier.options||[]
      }});
    }})()
    """)  # noqa: F821
    return json.loads(res)


def main():
    tab = find_shop_tab()
    if not tab:
        raise SystemExit("Open https://shopee.co.th/chomin640 in Chrome first.")
    switch_tab(tab)  # noqa: F821
    time.sleep(1)

    out = []
    for fam, iid in CANON:
        rec = fetch_item(iid)
        rec["family"] = fam
        out.append(rec)
        print(f"{fam}: colors={len(rec['colors'])} sizes={rec['sizes']} gallery={len(rec['gallery'])}")
        time.sleep(0.6)

    with open(OUT, "w") as f:
        json.dump(out, f, ensure_ascii=False, indent=1)
    print("Wrote", OUT, "families:", len(out))


main()
