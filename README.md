# Proweto Website

Proweto is een **PHP-gebaseerd webplatform** voor het beheren van **cursussen, materialen en reservaties**, met een duidelijk onderscheid tussen **gebruikers** en **admins**.  
Het platform bevat functionaliteiten zoals cursusbeheer, materiaalreservaties, notificaties en e-mailmeldingen.

Dit project is ontwikkeld **zonder framework**, met een duidelijke MVC-achtige structuur.

---

## 📌 Functionaliteiten

### 👤 Gebruikers
- Overzicht van beschikbare cursussen
- Cursusdetails met documenten, FAQ’s en ratings
- Materiaalreservaties (enkelvoudig en meervoudig)
- Overzicht van eigen reservaties
- Notificaties (gelezen / ongelezen)
- E-mailmeldingen bij statusupdates

### 🛠️ Admin
- Beheer van cursussen & categorieën
- Beheer van materialen en beschikbaarheden
- Beheer van reservaties (status, overzicht, export)
- Beheer van subwebsites
- Beheer van admingebruikers
- Admin dashboard

---

## 🧱 Architectuur

- PHP 8 (zonder framework)
- MVC-achtige structuur
- MySQL (MySQLi + prepared statements)
- UUID’s als primary keys
- PHP-sessies voor authenticatie
- Manuele integratie van externe libraries

---

## 📁 Folderstructuur

```
ProwetoWebsite/
├── app/
│   ├── config/
│   ├── controllers/
│   ├── models/
│   ├── core/
│   ├── helpers/
│   ├── libraries/
│   └── views/
│
├── public/
│   ├── ajax/
│   ├── js/
│   ├── css/
│   ├── images/
│   ├── uploads/
│   └── *.php
│
├── vendor/
├── .env
├── composer.json
└── README.md
```

---

## 🛠️ Installatie & Setup

### 1️⃣ Vereisten

- XAMPP (Apache + MySQL)
- PHP 8.0.x  
  Getest met: PHP 8.0.28
- Git
- Composer (optioneel)

---

### 2️⃣ XAMPP starten

Start in XAMPP:
- Apache
- MySQL

---

### 3️⃣ Project clonen

```
git clone <repository-url>
cd ProwetoWebsite
```

---

### 4️⃣ `.env` bestand aanmaken

Maak een `.env` bestand aan in de root van het project:

```
# Database
DB_HOST=
DB_USER=
DB_PASS=
DB_NAME=

# Mailer (SMTP)
MAIL_HOST=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_PORT=
MAIL_FROM=
MAIL_FROM_NAME=
```

---

### 5️⃣ Database configureren

- Maak een database aan in MySQL
- Gebruik dezelfde naam als `DB_NAME`
- Importeer het SQL-bestand (indien voorzien)

⚠️ Het project gebruikt UUID’s in plaats van auto-increment IDs.

---

### 6️⃣ PHPMailer (manuele installatie)

PHPMailer wordt **manueel** geïnstalleerd (niet via Composer).

Volg de officiële handleiding:
https://github.com/PHPMailer/PHPMailer

Plaats de library in:
```
app/libraries/PHPMailer/
```

---

### 7️⃣ Project starten

Ga in de terminal naar de `public` folder:

```
cd public
php -S localhost:8080
```

---

### 8️⃣ Project openen

Open in de browser:
```
http://localhost:8080
```

---

## 🔐 Authenticatie & Rollen

- Authenticatie via PHP-sessies
- Rollen:
  - User
  - Admin

Adminpagina’s zijn beveiligd via `requireAdmin()`.

---

## 📬 Notificaties & E-mail

- Interne notificaties worden opgeslagen in de database
- E-mailverzending via SMTP (PHPMailer)
- SMTP-configuratie via `.env`

---

## 👨‍💻 Auteur

Henrique  
Stageproject – PHP / Web Development
