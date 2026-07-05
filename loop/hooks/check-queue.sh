#!/usr/bin/env bash
# Stop hook: กันจบ turn ทั้งที่คิวยังมีงานค้าง (continuous mode)
cd "${CLAUDE_PROJECT_DIR:-.}" || exit 0

# ผู้ใช้สั่งพัก continuous mode → ปล่อยจบได้เสมอ
[ -f loop/PAUSE ] && exit 0

# 1) inbox ยังมีบรรทัดงาน (ไม่นับ comment/บรรทัดว่าง)?
if grep -qE '^[^#[:space:]]' loop/inbox.md 2>/dev/null; then
  echo "loop/inbox.md ยังมีงานค้าง — drain เข้า feature_list.json (status: queued) แล้วหยิบงานถัดไปตาม queue policy ใน CLAUDE.md" >&2
  exit 2
fi

# 2) feature_list ยังมีงาน queued?
if grep -q '"status": *"queued"' loop/feature_list.json 2>/dev/null; then
  echo "คิวยังมีงาน status=queued — หยิบงานถัดไปตาม priority (P1→P2→P3, queued_at เก่าก่อน) แล้วเริ่ม loop ใหม่ หรือสร้างไฟล์ loop/PAUSE เพื่อพัก" >&2
  exit 2
fi

exit 0
