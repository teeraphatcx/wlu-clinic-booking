# ระบบจองคิวคลินิกแพทย์แผนไทย
## โรงพยาบาลศูนย์การแพทย์ มหาวิทยาลัยวลัยลักษณ์ (WLU Medical Center)

ระบบบริหารจัดการและจองคิวบริการแพทย์แผนไทยออนไลน์ พัฒนาด้วย Laravel 11/12 (PHP 8.3-FPM), MySQL 8.0, Nginx, Redis และ phpMyAdmin ภายใต้สถาปัตยกรรม Docker Containers 100%

---

## 🛠️ โครงสร้างบริการ Docker (Infrastructure)

| Service | Container Name | Version / Base Image | Internal Port | Host Port | คำอธิบาย |
|---|---|---|---|---|---|
| **app** | `wlu_clinic_app` | PHP 8.3-FPM (Custom) | 9000 | - | Laravel Backend Application & Artisan |
| **nginx** | `wlu_clinic_nginx` | nginx:alpine | 80 | **8008** | Web Server (HTTP Gateway) |
| **mysql** | `wlu_clinic_mysql` | mysql:8.0 | 3306 | **3307** | Relational Database (UTF8MB4) |
| **phpmyadmin** | `wlu_clinic_phpmyadmin` | phpmyadmin:latest | 80 | **8082** | Database Web GUI Management |
| **redis** | `wlu_clinic_redis` | redis:alpine | 6379 | **6380** | Cache & Session Store |

> 💡 **หมายเหตุเรื่องพอร์ต:** พอร์ตบนเครื่อง Host ได้รับการจัดสรรเพื่อหลีกเลี่ยงการชนกับระบบอื่นในเครื่อง:
> - เว็บไซต์ระบบจองคิว: [http://localhost:8008](http://localhost:8008)
> - ระบบจัดการฐานข้อมูล phpMyAdmin: [http://localhost:8082](http://localhost:8082)
> - พอร์ต MySQL ภายนอก: `localhost:3307`
> - พอร์ต Redis ภายนอก: `localhost:6380`

---

## 🚀 วิธีการติดตั้งและเริ่มต้นใช้งาน (Quick Start)

เนื่องจากเครื่องพัฒนาไม่มี PHP หรือ Composer ติดตั้งในระดับ OS ให้รันคำสั่งทั้งหมดผ่าน Docker CLI:

### 1. โคลนโปรเจกต์และตรวจสอบไฟล์คอนฟิก
โปรเจกต์นี้กำหนดค่าคอนฟิกไว้ใน `.env` เรียบร้อยแล้ว (สามารถดูตัวอย่างได้ที่ `.env.example`)

### 2. เริ่มต้น Docker Containers
```powershell
docker compose up -d
```
ตรวจสอบสถานะคอนเทนเนอร์:
```powershell
docker compose ps
```

### 3. รัน Database Migration และ Seeder
```powershell
docker compose exec app php artisan migrate --seed
```

### 4. รันการทดสอบระบบ (Automated Tests)
```powershell
docker compose exec app php artisan test
```

## 🚀 Deploy บน Render

โปรเจกต์นี้มี [render.yaml](./render.yaml) และ Docker image แบบ single-service สำหรับ Render แล้ว

1. Push repository นี้ขึ้น GitHub
2. ใน Render เลือก **New → Blueprint** แล้วเลือก repository นี้
3. Render จะสร้าง Web Service, Postgres และ Key Value ให้ตาม Blueprint

การตั้งค่านี้ใช้ Free plan สำหรับทดสอบ/เดโม: Postgres ฟรีหมดอายุหลัง 30 วัน และไฟล์อัปโหลดบน Web Service ไม่ถาวร หากใช้งานจริงควรอัปเกรดฐานข้อมูลและเพิ่ม Persistent Disk หรือ Object Storage

---

## 🔑 ข้อมูลบัญชีเริ่มต้น (Default Credentials)

### ผู้ดูแลระบบสูงสุด (Super Admin)
- **Email:** `admin@wlu-clinic.local`
- **Password:** `Admin@1234`
- **บทบาท:** Super Admin (สิทธิ์ครอบคลุมทุกเมนูและระบบ)

### ผู้จัดการคลินิก (Clinic Admin)
- **Email:** `manager@wlu-clinic.local`
- **Password:** `Admin@1234`

### เจ้าหน้าที่เคาน์เตอร์ (Staff)
- **Email:** `staff@wlu-clinic.local`
- **Password:** `Admin@1234`

### phpMyAdmin
- **URL:** [http://localhost:8082](http://localhost:8082)
- **Server:** `mysql`
- **Username:** `root`
- **Password:** `root_password`
- **หรือ Database User:** `clinic_user` / `clinic_password` (Database: `clinic_booking`)

---

## 🗄️ โครงสร้างฐานข้อมูล (Phase 1 Database Architecture)

### 1. ระบบผู้ใช้งานและสิทธิ์ (RBAC & Audit)
- `roles` - กำหนดบทบาทเจ้าหน้าที่ (`super_admin`, `admin`, `staff`)
- `permissions` - สิทธิ์การเข้าถึงแยกตามโมดูล (bookings, customers, services, therapists, schedules, settings ฯลฯ)
- `role_permission` - ตาราง Pivot ความสัมพันธ์ Many-to-Many
- `admins` - ข้อมูลเจ้าหน้าที่และผู้ดูแลระบบ รองรับ Soft Deletes
- `audit_logs` - บันทึกประวัติการกระทำของเจ้าหน้าที่ เก็บค่าก่อนและหลังแก้ไข (JSON) พร้อม IP/User Agent

### 2. ข้อมูลผู้รับบริการ (Customers)
- `customers` - ข้อมูลผู้รับบริการ (HN, ชื่อ-นามสกุล, เบอร์โทร, เพศ, วันเกิด, ที่อยู่, วันที่กดยินยอม PDPA, Soft Deletes)

### 3. หัตถการและแพทย์แผนไทย/หมอนวด (Services & Therapists)
- `services` - รายการหัตถการแพทย์แผนไทย (รหัส, ชื่อไทย-อังกฤษ, ราคา, ระยะเวลา, กำหนดว่าต้องเลือกหมอหรือไม่, ลำดับการแสดง)
  - *หัตถการเริ่มต้น 9 รายการ เช่น นวดแผนไทย (60/90 นาที), ประคบสมุนไพร, อบสมุนไพร, นวดเท้า, นวดหน้า, ทับหม้อเกลือ, เจ้าหน้าที่จัดหมอให้อัตโนมัติ*
- `therapists` - ข้อมูลหมอนวด/แพทย์แผนไทย 5 ท่านแรกพร้อมประวัติความเชี่ยวชาญ
- `therapist_services` - Pivot จับคู่ว่าหมอท่านใดสามารถทำหัตถการประเภทใดได้บ้าง

### 4. ตารางเวลาและวันหยุด (Schedules & Slots)
- `clinic_schedules` - ตารางเปิด-ปิดคลินิก (เปิดจันทร์-ศุกร์ 08:00-16:30 น., เสาร์-อาทิตย์ ปิด)
- `clinic_holidays` - ปฏิทินวันหยุดราชการ/วันหยุดคลินิก
- `time_slots` - รอบเวลาให้บริการ 9 รอบ (08:00 - 16:30 น.)
- `therapist_schedules` - ตารางเวลาปฏิบัติงานรายสัปดาห์และเวลาพักของหมอแต่ละท่าน
- `therapist_days_off` - วันลาหยุดเฉพาะบุคคลของหมอนวด
- `therapist_blocked_periods` - ช่วงเวลาปิดรับจองเฉพาะกิจของหมอนวด

### 5. การจองคิวและประวัติ (Bookings)
- `bookings` - บันทึกการจองคิว
  - รหัสการจองมาตรฐาน: `WLU-YYMMDD-XXXX` (เช่น `WLU-261111-0001`)
  - สถานะ: `pending`, `confirmed`, `completed`, `cancelled`, `no_show`
  - มี Indexes ครบถ้วน: `booking_no (unique)`, `(booking_date, status)`, `customer_id`, `therapist_id`, `time_slot_id`
- `booking_histories` - บันทึกประวัติการเปลี่ยนแปลงสถานะ วันที่ และผู้แก้ไขทุกขั้นตอน

### 6. การตั้งค่าและเนื้อหาประชาสัมพันธ์ (Settings & Contents)
- `settings` - ระบบการตั้งค่า Key-Value พร้อม `Setting::get('key', default)`:
  - `booking_advance_days`: จองล่วงหน้าได้ไม่เกิน 30 วัน
  - `booking_close_before_hours`: ปิดรับจองก่อน 2 ชั่วโมง
  - `cancel_before_hours`: ยกเลิกได้ก่อน 24 ชั่วโมง
  - `reschedule_before_hours`: เลื่อนนัดได้ก่อน 24 ชั่วโมง
  - `max_active_bookings_per_customer`: จองค้างไว้ได้สูงสุด 3 คิวต่อท่าน
  - `clinic_name_th`, `clinic_name_en`, `hospital_name`
  - `notification_channels`: `["email"]`
  - `reminder_before_hours`: 24 ชั่วโมง
- `contents` - ข่าวสาร ประกาศ แบนเนอร์ และคำถามที่พบบ่อย (FAQ)

---

## 💻 คำสั่ง Artisan และ Docker ที่ใช้บ่อย (Useful Commands)

```powershell
# รัน Artisan ภายใน Container
docker compose exec app php artisan <command>

# รัน Composer ภายใน Container
docker compose exec app composer <command>

# ดู Log ของ Application หรือ Nginx
docker compose logs -f app
docker compose logs -f nginx

# รีเซ็ตฐานข้อมูลและรัน Seeder ใหม่ทั้งหมด
docker compose exec app php artisan migrate:fresh --seed

# ตรวจสอบสถานะการทดสอบระบบ
docker compose exec app php artisan test
```
