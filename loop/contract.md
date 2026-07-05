# Contract — (ยังไม่มี sprint ที่ active)

เมื่อเริ่ม sprint ให้แทนที่ไฟล์นี้ด้วย:
- **Title** + วันที่ร่าง + เลข rev
- **Spec summary** — สรุปหนึ่งย่อหน้า (ภาษาไทย)
- **Decisions baked into this contract** — bullet ของทุกการตัดสินใจ/judgment call
- **E2E mode for this sprint** — playwright-mcp (ระบุ flow/หน้าที่ต้องขับจริงด้วย browser)
- **Checklist** — C01..Cnn (**10–18 ข้อ** ตาม profile: standard) แต่ละข้อเป็น assertion ที่ทดสอบได้
  พร้อมระบุวิธีพิสูจน์ (unit/feature test · playwright-mcp · curl · manual step) จัดกลุ่มตาม area

> contract คือสิ่งที่ถูก grade — ไม่ใช่ prompt เดิม ไม่ใช่สรุปของ generator. evaluator ต้อง push back
> รอบหนึ่งก่อนเขียนโค้ด (reject ถ้าต่ำกว่า 10 ข้อ หรือมี assertion ที่ทดสอบไม่ได้)
