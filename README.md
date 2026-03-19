# OBLOCK — Streetwear E-commerce 🖤

> Site e-commerce complet pour une marque streetwear marocaine, conçu et développé de A à Z.

## 🌐 Demo
**[oblock.wuaze.com](http://oblock.wuaze.com)**

---

## 🛠️ Stack technique

| Front end | Back end | Base de données |
|-----------|----------|-----------------|
| HTML5 | PHP 8 | MySQL |
| CSS3 | PDO | phpMyAdmin |
| JavaScript (Vanilla) | Sessions PHP | |

---

## ✨ Fonctionnalités

### Client
- 🛒 Panier persistant avec localStorage
- 🎨 Sélection couleur, taille et quantité
- 📦 Formulaire de commande complet
- 📍 Détection automatique de la ville par IP
- 🌙 Dark mode avec sauvegarde 24h
- 💬 Modals légales (CGU, remboursement, confidentialité)
- 📱 100% responsive mobile

### Admin
- 🔐 Panel admin protégé par mot de passe
- 📊 Dashboard avec statistiques en temps réel
- 🔍 Recherche et filtres par statut
- ✏️ Mise à jour du statut des commandes
- 🗑️ Suppression des commandes

### Design
- Typographie **Bebas Neue** + **Outfit**
- Animations CSS au scroll
- Splashscreen au chargement
- Ticker défilant
- Slider d'images produit

---

## 📁 Structure du projet
```
oblock/
├── index.html          # Page principale
├── produit1.html       # Page produit
├── app.css             # Styles globaux
├── produit.css         # Styles page produit
├── panier.js           # Logique panier
├── connexion.php       # Connexion base de données
├── commande.php        # API sauvegarde commandes
├── admin.php           # Panel admin
└── favicon.svg         # Icône site
```

---

## 🚀 Installation locale

1. Installe **XAMPP**
2. Clone le repo dans `htdocs/oblock`
3. Démarre **Apache** et **MySQL**
4. Crée une base de données `oblock` dans phpMyAdmin
5. Exécute ce SQL :
```sql
CREATE TABLE commandes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  prenom VARCHAR(100) NOT NULL,
  nom VARCHAR(100) NOT NULL,
  telephone VARCHAR(20) NOT NULL,
  ville VARCHAR(100) NOT NULL,
  adresse TEXT NOT NULL,
  articles TEXT NOT NULL,
  total INT NOT NULL,
  statut ENUM('en attente','confirmée','expédiée','annulée') DEFAULT 'en attente',
  date_commande DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

6. Configure `connexion.php` avec tes identifiants
7. 
8. Ouvre `http://localhost/oblock`

---

## 🔐 Panel Admin

URL : `/admin.php`


## 👨‍💻 Développé par

**YOUNES OG** — [@younes_og_48](https://www.instagram.com/younes_og_48)

---

© 2026 OBLOCK — Tous droits réservés
