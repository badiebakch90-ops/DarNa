# DarNa 🏡🇲🇦

> Plateforme de location de logements authentiques au Maroc — riads, villas, appartements, maisons et camps Sahara.

[![Demo](https://img.shields.io/badge/demo-live-success)](https://eclectic-llama-da39dd.netlify.app)
[![Stack](https://img.shields.io/badge/stack-Laravel%2012%20%2B%20MySQL-red)](https://laravel.com)
[![License](https://img.shields.io/badge/license-All%20Rights%20Reserved-orange)](./LICENSE)

---

## 📖 Table des matieres

- [A propos](#-a-propos)
- [Architecture](#-architecture)
- [Fonctionnalites](#-fonctionnalites)
- [Demarrage rapide](#-demarrage-rapide)
- [Static preview (Netlify)](#-static-preview-netlify)
- [Backend Laravel](#-backend-laravel)
- [Securite](#-securite)
- [Deploiement](#-deploiement)
- [Roadmap](#-roadmap)

---

## 🌟 A propos

**DarNa** est une plateforme web qui connecte les voyageurs avec des logements authentiques au Maroc. Le projet existe en deux versions :

1. **Static preview** : version vitrine HTML/CSS/JS pure, hebergee sur Netlify pour la demo
2. **Backend Laravel** : application complete avec authentification, gestion des reservations, dashboards admin et hote

### Tech stack

**Frontend (les deux versions) :**
- HTML5, CSS3 (variables, grid, flexbox)
- JavaScript vanilla (pas de framework)
- Bootstrap 5.3 (utilitaires)
- Multi-langue (FR / EN / AR avec RTL)
- Dark mode automatique

**Backend (Laravel) :**
- PHP 8.2+
- Laravel 12.x
- MySQL 8 (production) / SQLite (dev)
- Blade templates
- Architecture MVC + Form Requests

---

## 🏗 Architecture

```
DarNa/
├── static-preview/          # Version statique deployee sur Netlify
│   ├── index.html           # Page d'accueil
│   ├── property.html        # Detail d'un logement
│   ├── reservation.html     # Formulaire de reservation
│   ├── admin.html           # Login admin (LOCAL uniquement)
│   ├── admin-dashboard.html # Dashboard admin
│   ├── 404.html             # Page d'erreur
│   ├── darna-static.js      # Logique JS principale
│   ├── darna-site.css       # Styles principaux
│   ├── _redirects           # Config routing Netlify
│   └── ...
│
├── backend-laravel/         # Application Laravel complete
│   ├── app/                 # Models, Controllers, Middlewares, Requests
│   ├── database/            # Migrations + seeders
│   ├── resources/views/     # Templates Blade
│   ├── routes/              # web.php, api.php
│   └── ...
│
├── netlify.toml             # Config build + headers securite
├── .gitignore               # Fichiers ignores par Git
└── README.md                # Ce fichier
```

---

## ✨ Fonctionnalites

### Cote visiteur

- 🔍 Recherche par destination et type de logement
- 🏷️ Filtres par categorie (riad, villa, appartement, maison, camp)
- 📸 Galerie photos par logement
- 📅 Verification de disponibilite (backend Laravel)
- 📱 Responsive mobile, tablette, desktop
- 🌍 3 langues : Francais, Anglais, Arabe (avec RTL)
- 🌙 Dark mode automatique
- 💬 Contact WhatsApp direct
- ✉️ Formulaire de contact (Netlify Forms)

### Cote hote (Laravel uniquement)

- ➕ Ajouter ses propres logements
- 📊 Voir ses reservations
- 🔔 Recevoir des notifications

### Cote admin (Laravel uniquement)

- 👥 Gestion des utilisateurs
- 🏠 Gestion de tous les logements
- 📋 Gestion de toutes les reservations
- 🛡️ Acces protege par role + middleware

---

## 🚀 Demarrage rapide

### Pre-requis

- Pour le **static preview** : juste un navigateur (ou un serveur HTTP local)
- Pour le **Laravel** : PHP 8.2+, Composer, MySQL 8 (ou SQLite)

### Cloner le projet

```bash
git clone https://github.com/badiebakch90-ops/DarNa.git
cd DarNa
```

---

## 🎨 Static preview (Netlify)

### Lancer en local

```bash
cd static-preview
python3 -m http.server 8000
# Puis ouvre http://localhost:8000
```

### Activer l'admin en local (optionnel)

L'admin est **desactive par defaut** sur la version deployee (securite). Pour l'utiliser en local :

```bash
cd static-preview
cp admin-config.local.example.js admin-config.local.js
# Edite admin-config.local.js et choisis ton username/password
```

Ensuite ouvre `http://localhost:8000/admin.html`.

> ⚠️ **IMPORTANT** : `admin-config.local.js` est dans `.gitignore` — ne le commit JAMAIS.

---

## 💎 Backend Laravel

### Installation

```bash
cd backend-laravel
composer install
cp .env.example .env
php artisan key:generate
```

### Configuration de la base de donnees

Edite `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=darna_laravel
DB_USERNAME=root
DB_PASSWORD=
```

Puis migre + seed :

```bash
php artisan migrate --seed
```

### Lancer le serveur

```bash
php artisan serve
# http://127.0.0.1:8000
```

### Compte admin par defaut (seeder)

Verifie `database/seeders/UserSeeder.php` pour les identifiants par defaut. **Change le mot de passe immediatement** apres le premier login.

---

## 🛡 Securite

### Static preview

- ✅ Pas de mot de passe hardcode (l'admin charge sa config depuis un fichier local non-commit)
- ✅ Admin desactive automatiquement en production
- ✅ Headers de securite (X-Frame-Options, HSTS, etc.) configures dans `netlify.toml`
- ✅ Honeypot anti-spam sur le formulaire de contact
- ✅ Email/telephone non exposes (contact via formulaire ou WhatsApp button)

### Backend Laravel

- ✅ Hashing bcrypt (12 rounds) pour les mots de passe
- ✅ CSRF protection (default Laravel)
- ✅ Rate limiting sur les routes auth
- ✅ Middleware `EnsureAdmin` pour les routes admin
- ✅ Force HTTPS en production (middleware `EnforceSecurity`)
- ✅ Form Requests pour valider toutes les entrees
- ✅ Headers de securite custom
- ✅ SQL injection protected (Eloquent ORM)

---

## 🚢 Deploiement

### Static preview sur Netlify

Le projet est deja configure pour Netlify (voir `netlify.toml` et `_redirects`).

**Methode 1 — Drag & drop**
1. Compresse le dossier `static-preview/` en ZIP
2. Va sur [app.netlify.com](https://app.netlify.com)
3. "Add new site" → "Deploy manually"
4. Drop le ZIP

**Methode 2 — GitHub (auto-deploy a chaque push)**
1. Push le projet sur GitHub
2. Sur Netlify : "Add new site" → "Import from Git"
3. Selectionne le repo
4. **Publish directory** : `static-preview`
5. Deploy

**Activer Netlify Forms** (formulaire de contact)
1. Apres le deploy, va dans **Forms** dans le dashboard Netlify
2. Tu devrais voir le formulaire `darna-contact`
3. **Form notifications** → **Add notification** → **Email notification**
4. Mets `badie3079@gmail.com` pour recevoir les messages

### Backend Laravel

Recommande : **Railway**, **Render** ou **Hostinger**.

Etapes generales :
1. Push sur GitHub
2. Connecte ton repo a l'hebergeur
3. Configure les variables d'environnement (`.env.production.example` comme reference)
4. Lance les migrations apres le premier deploy

---

## 🗺 Roadmap

- [x] Static preview design
- [x] Backend Laravel (auth, models, dashboard)
- [x] Multi-langue FR/EN/AR
- [x] Securisation static preview
- [x] Formulaire de contact Netlify Forms
- [ ] Connexion static preview ↔ backend Laravel via API
- [ ] Paiement en ligne (Stripe / CMI)
- [ ] Notifications par email automatiques
- [ ] Application mobile (React Native ?)
- [ ] Programme de fidelite

---

## 👨‍💻 Auteur

**Abdelbadie Abkhich**
Junior Full-Stack Developer — Rabat, Maroc

- 🌐 Portfolio : [portfolio-ecru-seven-84.vercel.app](https://portfolio-ecru-seven-84.vercel.app)
- 💼 GitHub : [@badiebakch90-ops](https://github.com/badiebakch90-ops)

---

## 📄 Licence

**Copyright © 2026 Abdelbadie Abkhich — All Rights Reserved**

Ce code est publié publiquement à des fins de portfolio uniquement.
Utilisation commerciale ou redistribution **interdite** sans autorisation écrite.

Voir le fichier [LICENSE](./LICENSE) pour les détails complets.

Pour toute demande de licence commerciale ou collaboration : 📧 badie3079@gmail.com
