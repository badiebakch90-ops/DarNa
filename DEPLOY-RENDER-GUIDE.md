# 🚀 Deploy DarNa على Render — دليل بالخطوات

> هاد الدليل بالعربية والفرنسية — كل خطوة فيها صور وتوضيح.
> الوقت المطلوب: ~1 ساعة و 30 دقيقة

---

## 📋 المحتويات

1. [قبل ما تبدا](#prerequisites)
2. [المرحلة 1: GitHub](#step1)
3. [المرحلة 2: Render Account](#step2)
4. [المرحلة 3: Deploy](#step3)
5. [المرحلة 4: Migrations](#step4)
6. [المرحلة 5: تجربة](#step5)
7. [المرحلة 6: ربط مع Netlify](#step6)
8. [استكشاف الأخطاء](#troubleshooting)

---

<a id="prerequisites"></a>
## ✅ قبل ما تبدا (5 دقائق)

غادي خاصك:
- ✉️ إيميل (`badie3079@gmail.com` ديالك يخدم)
- 🌐 حساب GitHub (عندك `badiebakch90-ops`)
- 💻 الكود ديال DarNa (موجود فالـ ZIP)

**ملحوظة:** Render مجاني، ما يطلبش credit card.

---

<a id="step1"></a>
## 1️⃣ المرحلة 1: حط الكود فـ GitHub (15 دقيقة)

### A. خلق Repo جديد فـ GitHub

1. سير لـ https://github.com/new
2. **Repository name:** `DarNa` (ولا أي اسم)
3. **Public** ولا **Private** (Public أحسن للـ portfolio)
4. **ما تختارش** "Initialize with README" — احنا عندنا واحد
5. كليك **Create repository**

### B. Push الكود

افتح Terminal فالـ folder ديال DarNa:

```bash
cd C:\path\to\DarNa
git init
git add .
git commit -m "🚀 Initial commit - DarNa ready for production"
git branch -M main
git remote add origin https://github.com/badiebakch90-ops/DarNa.git
git push -u origin main
```

> **مشكل؟** إلى طلب منك username/password:
> - Username: `badiebakch90-ops`
> - Password: استعمل **Personal Access Token** (ماشي كلمة السر العادية)
> - https://github.com/settings/tokens → Generate new token (classic)

### ✅ النتيجة
الكود غادي يبان فـ https://github.com/badiebakch90-ops/DarNa

---

<a id="step2"></a>
## 2️⃣ المرحلة 2: خلق حساب Render (5 دقائق)

1. سير لـ https://render.com
2. كليك **Get Started**
3. اختار **Sign up with GitHub** (الأسرع)
4. وافق على الـ permissions (Render يقرا الـ repos ديالك)
5. كمل التسجيل

✅ دخلتي للـ Dashboard

---

<a id="step3"></a>
## 3️⃣ المرحلة 3: Deploy على Render (15 دقيقة)

### A. إنشاء الخدمات (Services)

عندنا **3 طرق**:
- 🟢 **الطريقة السهلة (موصى بها):** Blueprint من `render.yaml`
- 🟡 الطريقة اليدوية: Web Service + Database

### الطريقة السهلة — Blueprint

1. فالـ Render Dashboard، كليك **New +** → **Blueprint**
2. اختار الـ repo **DarNa**
3. Render غادي يقرا `render.yaml` ويقترح:
   - ✅ Web Service: `darna-laravel`
   - ✅ PostgreSQL Database: `darna-db`
4. كليك **Apply**
5. صبر 5-10 دقائق (Render يبني الـ Docker image)

### B. ضبط الـ APP_URL

ملي الـ deploy يكمل، Render غادي يعطيك URL بحال:
```
https://darna-laravel.onrender.com
```

1. سير لـ **darna-laravel** → **Environment**
2. لقا `APP_URL` 
3. كليك **Edit** → دخل الـ URL ديالك
4. **Save Changes** (غادي يـ redeploy أوتوماتيك)

---

<a id="step4"></a>
## 4️⃣ المرحلة 4: Migrations + Seeders (10 دقائق)

الـ migrations كيخدمو أوتوماتيك ملي يـ deploy (شفها فالـ `Dockerfile`).

### دبا خاصنا نخلقو الـ admin

1. فالـ Render Dashboard → **darna-laravel** → **Shell**
2. اكتب هاد الـ command:

```bash
php artisan db:seed --force
```

هاد الشي غادي يخلق:
- المستخدم admin (شوف `database/seeders/UserSeeder.php` للـ identifiants)
- العقارات الأولية (شوف `database/seeders/PropertySeeder.php`)

### ⚠️ مهم — بدل كلمة سر admin فوراً

1. سير للـ URL ديالك: `https://darna-laravel.onrender.com/login`
2. دخل بالـ identifiants ديال admin (من الـ seeder)
3. سير لـ **Profile** → بدل كلمة السر **بكلمة سر قوية**

---

<a id="step5"></a>
## 5️⃣ المرحلة 5: تجربة (10 دقائق)

اختبر هاد الحوايج:

- [ ] الموقع كيتفتح: `https://darna-laravel.onrender.com`
- [ ] صفحة `/login` كتخدم
- [ ] صفحة `/register` كتخدم (إلى مفعلة)
- [ ] تقدر تسجل دخول بالـ admin
- [ ] صفحة `/dashboard` كتبان (admin only)
- [ ] صفحة `/hosting` كتبان (host only)
- [ ] العقارات كيبانو فالـ home page

---

<a id="step6"></a>
## 6️⃣ المرحلة 6: ربط مع Netlify (10 دقيقة)

دابا الـ static-preview على Netlify خاصو يكلم الـ Laravel API على Render.

### A. تحديث `api-config.js` فـ static-preview

افتح `static-preview/api-config.js` وبدل:

```javascript
const localLaravelOrigin = 'http://127.0.0.1:8000';
```

بـ:

```javascript
const localLaravelOrigin = 'https://darna-laravel.onrender.com';
```

(استعمل الـ URL ديالك من Render)

### B. Push الكود الجديد

```bash
git add static-preview/api-config.js
git commit -m "🔗 Connect static-preview to Render backend"
git push
```

Netlify غادي يـ redeploy أوتوماتيك.

### ✅ النتيجة
دابا ملي شي زائر يخلي réservation فـ `eclectic-llama-da39dd.netlify.app`، الداتا تتسجل فـ Render PostgreSQL DB، وأنت كتشوفها فـ Laravel admin dashboard.

---

<a id="troubleshooting"></a>
## 🐛 استكشاف الأخطاء

### ❌ "Application Error" ملي تفتح الموقع
- سير لـ Render → **Logs**
- لقا الخطأ
- إلى كان `APP_KEY missing` → تأكد بلي `APP_KEY` فـ Environment

### ❌ Database connection failed
- تأكد بلي `DB_CONNECTION=pgsql` (ماشي mysql)
- تأكد بلي الـ PostgreSQL service خدامة

### ❌ "Class not found"
- فالـ Render Shell:
  ```bash
  composer dump-autoload --optimize
  php artisan config:clear
  php artisan cache:clear
  ```

### ❌ Mise en veille مزعجة
الـ free plan ديال Render كينعس بعد 15 دقيقة. حلول:
1. **استعمل https://uptimerobot.com** (مجاني) — يـ ping الموقع كل 5 دقائق باش ما ينعسش
2. **خلص $7/شهر** للـ Starter plan (ما ينعسش)

### ❌ Migrations ما خدمتش
- فالـ Shell:
  ```bash
  php artisan migrate:status   # شوف الحالة
  php artisan migrate --force  # دير migrate
  ```

---

## 🎉 برافو!

دابا عندك:
- ✅ Frontend على Netlify (سريع، مجاني)
- ✅ Backend Laravel على Render (مجاني)
- ✅ PostgreSQL Database على Render (1 GB مجاني)
- ✅ Vraies réservations كتجي ليك
- ✅ Admin dashboard خدام

**الخطوات الموالية:**
- 🌐 شراء domain خاص (`.com` ~10$/سنة)
- 📧 ربط SMTP باش تصل ليك إيميلات automatique
- 💳 ربط Stripe/CMI للـ paiement online

---

> أي مشكل، عاود اقرا الجزء [استكشاف الأخطاء](#troubleshooting) أولاً.
