# LOOPS.md — Agent Loop Rules for This Repo

Adapted from A. Karpathy's "Field Notes on Agents That Run for Days" (loops.md).
Profile: standard. The loop is: **gather → reason → act → verify → repeat.**
Everything below is a footnote on those five verbs.

Repo: CHO.MIN — a Laravel 13 storefront + Filament 4 admin for a Thai fashion shop
(catalog, color-library, cart/checkout, PromptPay slip review, coupons, gift cards, points,
wishlist, content pages, i18n th/en). Install-time choices live in `loop/loop.config.json`.

## 1. Write the loop, not the prompt
Feature work is not a one-shot prompt. It runs as a loop against a contract until the
evaluator passes it. If you are iterating on a single message, stop and write the contract.

## 2. Separate the roles
- **Planner** (`.claude/agents/planner.md`) — turns a vague ask into a sprint spec. Never touches code.
- **Generator** — the main Claude Code session. Writes everything. Forbidden from grading its own work.
- **Evaluator** (`.claude/agents/evaluator.md`) — reads diffs, runs `./vendor/bin/pint --test --dirty && php artisan test && npm run build`,
  exercises the app via **playwright-mcp** (drives the real storefront + Filament admin in a browser).
  Told from the first message that the code is broken; its job is to prove it.

Mixing roles makes the model sycophantic the moment it grades itself.

## 3. Negotiate the contract first
Before the generator writes a single line, `loop/contract.md` must exist — a checklist of
testable assertions — and the evaluator must push back on it once. Target size for this
repo: **10–18 criteria**. Too few and the evaluator rubber-stamps.
The spec is the boundary, but **the contract is what gets graded.**

## 4. Write to disk, not to context
Context windows lie. State lives in `loop/`:
`feature_list.json`, `progress.md`, `log.md` (append-only, `## [YYYY-MM-DD] op | title`).
The work queue also lives on disk: new asks arriving mid-sprint go to `loop/inbox.md` or
`feature_list.json` (status: queued) — never into the running context.
A fresh session must be able to pick up where the last one died by reading these files.
If the state doesn't fit there, the state is too complicated.

## 5. Let the loop restart
If a run goes sideways, throwing the attempt away and restarting from the contract is the loop
working correctly — not a failure. Insert a human only when the **contract** is wrong, not when
the build is.

## 6. Score the subjective
UI work is graded on four axes, 0–1 each, with a paragraph explaining the gap:
**design, originality, craft, functionality.** Reference floor:
`resources/views/pages/products/show.blade.php` (the Product Detail Page — black-and-white
minimal editorial, Reformation-referenced: serif uppercase display type, hairline gray borders,
grid-driven layout, generous whitespace, light mode only). Runner-up unit:
`resources/views/components/product-card.blade.php`.
The model converges toward the taste you wrote down, not taste it invents.

## 7. Read the traces
Debug the loop by reading transcripts, not by re-running. Find the moment judgment diverged,
fix the prompt for that exact moment, run again. Skip this and you are tuning by vibe.

## 8. Delete the harness
Re-read this file, `CLAUDE.md`, and `loop/loop.config.json` against each model release;
delete anything the model now does for free. A harness that only grows is a harness nobody reads.

## 9. The bottleneck always moves
Coding → planning → verification → taste. Done means: find the next bottleneck, fix it,
ship a smaller harness, repeat.
