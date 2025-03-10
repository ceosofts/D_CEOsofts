📌 D_CEOSOFTS: ระบบบัญชี ERP & CRM
🎯 เกี่ยวกับโครงการ
D_CEOSOFTS เป็นเว็บแอพที่ออกแบบมาเพื่อช่วยจัดการระบบบัญชี ERP และ CRM รองรับการทำงานแบบ ออนไลน์และออฟไลน์ โดยมีฟีเจอร์ที่ครอบคลุมทุกแผนกของธุรกิจ ตั้งแต่ฝ่ายขาย, ฝ่ายจัดซื้อ, คลังสินค้า, การเงิน, และฝ่ายบุคคล ไปจนถึงระบบตั้งค่าต่างๆ ที่สามารถปรับแต่งได้ตามต้องการ

🏆 คุณสมบัติหลัก
ระบบบัญชี ERP & CRM ครบวงจร
รองรับการทำงาน Offline Mode
มีระบบ User Management ที่ปลอดภัย
ใช้ ฐานข้อมูลที่ยืดหยุ่น รองรับโครงสร้างที่สามารถขยายได้
ออกแบบมาให้ใช้งานง่าย และ รองรับการใช้งานผ่านมือถือ

📌 ฟีเจอร์หลักของระบบ

1️⃣ ฝ่ายขาย
✅ สินค้าคงคลัง
✅ จัดการลูกค้า
✅ ใบเสนอราคา & ใบแจ้งหนี้
✅ ติดตามการชำระเงิน
✅ รายงานยอดขาย

2️⃣ ฝ่ายจัดซื้อ
✅ จัดการซัพพลายเออร์
✅ ออกใบสั่งซื้อ & ใบรับสินค้า
✅ บริหารการชำระเงิน
✅ รายงานการสั่งซื้อ

3️⃣ คลังสินค้า
✅ ติดตามสินค้าคงเหลือ
✅ รับเข้า & จ่ายออกสินค้า
✅ บริหารวัสดุอุปกรณ์

4️⃣ ฝ่ายการเงิน
✅ ฝาก-ถอนเงิน
✅ จัดการบัญชีธนาคาร
✅ ติดตามเช็ค & สถานะเช็ค

5️⃣ ฝ่ายบุคคล
✅ บริหารพนักงาน
✅ บันทึกเวลาทำงาน
✅ จัดการเงินเดือน & สลิปเงินเดือน

6️⃣ การตั้งค่าระบบ
✅ ข้อมูลบริษัท & สาขา
✅ ตั้งค่าการชำระเงิน
✅ ตั้งค่าผู้ใช้งาน & สิทธิ์การเข้าถึง

🖥 โครงสร้างของเว็บแอพ
🔹 หน้าแรก (Welcome Page)
📌 URL: http://127.0.0.1:8000/
✅ แสดงรายละเอียดเกี่ยวกับ สินค้าและบริการ ของบริษัท
✅ ไม่ต้อง Login ก็สามารถเข้าชมได้

🔹 Dashboard (ต้อง Login ก่อน)
✅ แสดง ข้อมูลสำคัญ เช่น ยอดขาย, ยอดซื้อ, รายการรอชำระ ฯลฯ
✅ Sidebar Menu สำหรับเลือกหมวดหมู่ต่างๆ
✅ Footer แสดงลิขสิทธิ์และข้อมูลติดต่อ

🔹 การเข้าใช้งานระบบ
✅ Login & Logout
✅ ลงทะเบียนผ่าน Email หรือ Facebook
✅ ลืมรหัสผ่าน? ระบบช่วยกู้คืนผ่าน Email

🚀 วิธีติดตั้งและใช้งาน
1️⃣ Clone โปรเจคจาก GitHub
bash
คัดลอก
แก้ไข
git clone https://github.com/USERNAME/D_CEOSOFTS.git
cd D_CEOSOFTS
2️⃣ ติดตั้ง Dependencies
bash
คัดลอก
แก้ไข
composer install
npm install
3️⃣ ตั้งค่าฐานข้อมูล
bash
คัดลอก
แก้ไข
cp .env.example .env
php artisan key:generate
แก้ไขไฟล์ .env เพื่อกำหนดค่าฐานข้อมูลให้ถูกต้อง

4️⃣ รัน Migration และ Seed ข้อมูล
bash
คัดลอก
แก้ไข
php artisan migrate --seed
5️⃣ เริ่มต้นใช้งาน
bash
คัดลอก
แก้ไข
php artisan serve
เข้าใช้งานเว็บที่ http://127.0.0.1:8000/

🛠 เทคโนโลยีที่ใช้
Laravel (Backend)
Vue.js / React (Frontend)
MySQL / SQLite (Database)
Bootstrap / Tailwind CSS (UI Framework)
📩 ติดต่อเรา
📧 Email: support@ceosofts.com
🌐 Website: www.ceosofts.com
📌 Facebook: CEOsofts
📞 โทร: 062-9055-888
