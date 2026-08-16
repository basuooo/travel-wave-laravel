# 🔌 Travel Wave - Zapier Integration Guide & API Documentation

هذا الملف يحتوي على الدليل الفني الشامل لربط نظام **Travel Wave** بـ **Zapier Platform**.

---

## 🔑 1. المصادقة (Authentication)

تتم المصادقة عبر **Laravel Sanctum Personal Access Tokens** (Bearer Token).

* **Header Required:**
  ```http
  Authorization: Bearer <YOUR_PERSONAL_ACCESS_TOKEN>
  Accept: application/json
  Content-Type: application/json
  ```
* **Test Authentication Endpoint:**
  * **Method:** `GET`
  * **URL:** `https://your-domain.com/api/v1/zapier/me`
  * **Response Sample:**
    ```json
    {
      "id": 1,
      "name": "Admin User",
      "email": "admin@travelwave.com",
      "is_admin": true
    }
    ```

---

## 🪝 2. نظام الـ REST Hooks (Triggers Subscriptions)

عند إنشاء **Trigger** على Zapier، يقوم Zapier بإرسال طلب للتسجيل (Subscribe) وعند إيقاف الـ Zap يلغي التسجيل (Unsubscribe).

### 🟢 التسجيل (Subscribe)
* **Method:** `POST`
* **URL:** `https://your-domain.com/api/v1/zapier/subscribe`
* **Body:**
  ```json
  {
    "event": "customer.created",
    "target_url": "https://hooks.zapier.com/hooks/catch/123456/abcde/"
  }
  ```
* **Response (201 Created):**
  ```json
  {
    "id": "1",
    "event": "customer.created",
    "target_url": "https://hooks.zapier.com/hooks/catch/123456/abcde/",
    "status": "subscribed",
    "created_at": "2026-08-16T15:30:00.000000Z"
  }
  ```

### 🔴 الإلغاء (Unsubscribe)
* **Method:** `DELETE`
* **URL:** `https://your-domain.com/api/v1/zapier/unsubscribe`
* **Body:**
  ```json
  {
    "target_url": "https://hooks.zapier.com/hooks/catch/123456/abcde/"
  }
  ```

---

## ⚡ 3. المحفزات المتاحة (Supported Triggers)

### 1. `customer.created` (عميل جديد في CRM)
* **Polling / Sample Data URL:** `GET /api/v1/zapier/customers?limit=10`
* **Payload Sample:**
  ```json
  {
    "id": "105",
    "customer_code": "CUST-105",
    "full_name": "احمد محمود",
    "phone": "+201000000000",
    "whatsapp_number": "+201000000000",
    "email": "ahmed@example.com",
    "nationality": "Egyptian",
    "country": "Egypt",
    "destination": "Turkey",
    "stage": "new_customer",
    "stage_localized": "عميل جديد",
    "notes": "استفسار عن رحلة تركيا",
    "created_at": "2026-08-16T15:30:00.000000Z"
  }
  ```

### 2. `customer.stage_updated` (تحديث مرحلة العميل)
* **Payload Sample:**
  ```json
  {
    "id": "105",
    "customer_code": "CUST-105",
    "full_name": "احمد محمود",
    "old_stage": "new_customer",
    "new_stage": "under_processing",
    "new_stage_localized": "قيد التنفيذ",
    "updated_at": "2026-08-16T15:35:00.000000Z"
  }
  ```

### 3. `inquiry.created` (طلب/استفسار جديد)
* **Polling / Sample Data URL:** `GET /api/v1/zapier/inquiries?limit=10`
* **Payload Sample:**
  ```json
  {
    "id": "50",
    "full_name": "سارة علي",
    "phone": "+201111111111",
    "whatsapp_number": "+201111111111",
    "email": "sara@example.com",
    "country": "Egypt",
    "destination": "Spain Visa",
    "service_type": "visa",
    "travel_date": "2026-09-01",
    "travelers_count": 2,
    "message": "عايز اعرف متطلبات فيزا اسبانيا",
    "lead_source": "Zapier",
    "status": "new",
    "created_at": "2026-08-16T15:30:00.000000Z"
  }
  ```

### 4. `task.created` (مهمة CRM جديدة)
* **Polling / Sample Data URL:** `GET /api/v1/zapier/tasks?limit=10`
* **Payload Sample:**
  ```json
  {
    "id": "12",
    "title": "متابعة جواز السفر مع العميل",
    "description": "الاتصال بالعميل واستلام الصورة الشخصية",
    "priority": "high",
    "status": "new",
    "category": "documents",
    "task_type": "general",
    "due_at": "2026-08-17T12:00:00.000000Z",
    "created_at": "2026-08-16T15:30:00.000000Z"
  }
  ```

---

## 🛠️ 4. الإجراءات المتاحة (Supported Actions)

### 1. `create_customer` (إضافة عميل جديد)
* **Method:** `POST`
* **URL:** `/api/v1/zapier/customers`
* **Body Request:**
  ```json
  {
    "full_name": "محمد علي",
    "phone": "+201200000000",
    "whatsapp_number": "+201200000000",
    "email": "mohamed@example.com",
    "nationality": "Egyptian",
    "country": "Egypt",
    "destination": "France",
    "notes": "قادم من Zapier Integration"
  }
  ```

### 2. `create_inquiry` (إضافة طلب جديد)
* **Method:** `POST`
* **URL:** `/api/v1/zapier/inquiries`
* **Body Request:**
  ```json
  {
    "full_name": "عمر خالد",
    "phone": "+201011112222",
    "email": "omar@example.com",
    "destination": "Schengen Visa",
    "service_type": "Visa Services",
    "message": "طلب من نموذج الموقع الخارجي",
    "lead_source": "Facebook Ads via Zapier"
  }
  ```

### 3. `create_task` (إضافة مهمة جديدة)
* **Method:** `POST`
* **URL:** `/api/v1/zapier/tasks`
* **Body Request:**
  ```json
  {
    "title": "متابعة العميل الجديد",
    "description": "ارسال قائمة المستندات المطلوبة",
    "priority": "medium",
    "due_at": "2026-08-18 10:00:00"
  }
  ```

---

## 🖥️ 5. خطوات الضبط على منصة Zapier (Zapier Developer Setup)

1. افتح [Zapier Developer Console](https://developer.zapier.com/).
2. أنشئ تطبيق جديد باسم **Travel Wave**.
3. **Authentication Type:** اختر `API Key` أو `Bearer Token`.
   * **URL Test:** `https://your-domain.com/api/v1/zapier/me`
4. **Triggers Configuration:**
   * **Trigger Type:** REST Hook
   * **Subscribe Request:** `POST https://your-domain.com/api/v1/zapier/subscribe`
   * **Unsubscribe Request:** `DELETE https://your-domain.com/api/v1/zapier/unsubscribe`
   * **Perform List (Sample):** `GET https://your-domain.com/api/v1/zapier/customers`
5. **Actions Configuration:**
   * **Create Customer:** `POST https://your-domain.com/api/v1/zapier/customers`
   * **Create Inquiry:** `POST https://your-domain.com/api/v1/zapier/inquiries`
   * **Create Task:** `POST https://your-domain.com/api/v1/zapier/tasks`

---
🎉 **نظامك الآن جاهز تماماً للربط مع أي تطبيق عبر Zapier!**
