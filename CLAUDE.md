# CLAUDE.md — CHO.MIN

Laravel 13 storefront + Filament 4 admin for a Thai fashion shop. Stack: PHP 8.3 · Laravel 13 ·
Filament 4 · Alpine + Tailwind 3.4 (Vite 8) · PHPUnit 12 · Pint. Docs in `loop/` are written in
Thai (narrative); code, paths, commands, and technical terms stay in English.

## The loop (read LOOPS.md)

This repo runs on an agentic loop — rules in `LOOPS.md`, state in `loop/`,
install-time choices in `loop/loop.config.json`.

**Session start:** read `loop/progress.md`, `loop/feature_list.json`, and `loop/contract.md`
before doing feature work. They are the source of truth, not the conversation summary.

**What counts as non-trivial (no self-judged exceptions):**
touching more than 1 file, OR any behavior/logic change, OR anything involving a database
migration / schema change, checkout & payment (COD/PromptPay), auth, i18n & locale routes
(`/{locale}` th|en), PDPA cookie-consent / analytics tags, the Filament admin, or the
seeders / catalogue data. Only exceptions: typo, comment, config value with no behavior impact.
If unsure whether it qualifies as an exception — it does not.

**Non-trivial work follows this order:**
1. **Plan** — launch the `planner` agent to produce a sprint spec.
2. **Contract** — write the checklist into `loop/contract.md`; have the `evaluator` agent push
   back on it BEFORE writing code. The agreed contract is what gets graded.
3. **Generate** — the main session implements. It never grades its own work and never
   declares work done.
4. **Evaluate** — launch the `evaluator` agent. `VERDICT: PASS` is the only definition of done.
5. **Record** — update `loop/progress.md` + `loop/feature_list.json`, append one line to
   `loop/log.md`.
6. **Next** — drain `loop/inbox.md` into `feature_list.json` (status: queued, default P2),
   clear the inbox, then pick the next queued item (P1 → P2 → P3, oldest `queued_at` first)
   and start its loop immediately. Do not wait for the user. Stop only when: the queue is
   truly empty, `loop/PAUSE` exists, or the next item is too vague to spec — in that case
   mark it `needs-clarification` with the exact questions you need answered, then move on
   to the item after it.

**Queue policy (while a sprint is active):**
- New ask that belongs to the current sprint (amending/adding criteria to the same work)
  → renegotiate `loop/contract.md` with the evaluator as a new rev, then continue.
- New ask that is separate work → append to `feature_list.json` as
  `{status: queued, priority: P2 unless stated, queued_at: today, source: user-interrupt}`,
  confirm in ONE line ("รับเข้าคิวแล้ว F0xx, ลำดับที่ N"), and return to the sprint immediately.
- Unsure which case it is → treat as separate work and queue it (prevents scope creep).
- Never decide "this is small, I'll just do it inline." Small still queues.
- Switch mid-sprint ONLY when the message starts with "ด่วน:" or "หยุด sprint" —
  and park cleanly first: update `progress.md` (exactly where things stand, what remains,
  how to resume), mark the current feature `parked`, append `log.md`, then take the interrupt.
  When the interrupt ships, resume the parked sprint before touching the rest of the queue.

**Restart rule:** if an implementation attempt goes sideways, revert and restart from the
contract rather than patching archaeology. Escalate to the human only when the contract itself
is wrong. If the same criterion fails 3 evaluation rounds in a row, stop and flag the
contract line for human review instead of looping again.

Trivial fixes skip the ceremony — but still get a `log.md` line if they ship.

## Adding work to the queue from the terminal

งานเข้า `loop/inbox.md` ได้ 2 ทาง:
- **พิมพ์บอกใน session** ระหว่าง Claude ทำงาน — Claude จดเข้าคิวให้ตาม queue policy ข้างบน
- **เขียนไฟล์ตรงจาก terminal** (เหมาะกับตอนรัน unattended บน tmux/VPS) — ไม่ต้องแตะ session:

  ```bash
  echo "[P1] หน้า login ขึ้น 500 หลัง deploy" >> loop/inbox.md
  ```

  จบ sprint ปัจจุบันแล้วงานจะถูก drain เข้าคิวเอง (Stop hook กันไม่ให้จบ turn ทั้งที่ inbox
  ยังมีของ). พักทั้งระบบด้วย `touch loop/PAUSE` (ลบไฟล์เพื่อลุยต่อ).
