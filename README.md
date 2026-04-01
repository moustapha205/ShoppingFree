# 🛍️ ShoppingFree

ShoppingFree est une boutique e-commerce full-stack construite avec **Symfony 7**, **MySQL** et **Stripe**. Elle permet de naviguer dans un catalogue de produits tech, de les ajouter au panier et de payer en ligne par carte bancaire.

🌐 **Site en ligne** : [shoppingfree-production.up.railway.app](https://shoppingfree-production.up.railway.app)

---

## ✨ Fonctionnalités

- 🛒 **Boutique** — catalogue de produits avec images, catégories et badges de stock
- 🔍 **Fiche produit** — page détail avec description, prix HT/TTC et bouton d'ajout au panier
- 🧺 **Panier** — gestion de la session avec vérification du stock en temps réel
- 💳 **Paiement Stripe** — intégration Stripe Checkout pour payer par carte bancaire
- 👤 **Authentification** — inscription, connexion, déconnexion
- 📦 **Mes commandes** — historique des achats de l'utilisateur connecté
- 🔐 **Espace Admin** — dashboard avec statistiques, gestion des stocks et remplissage de la boutique
- 🌙 **Dark mode** — thème clair/sombre persistant via localStorage
- 📱 **Responsive** — adapté mobile, tablette et desktop

---

## 🛠️ Stack technique

| Technologie | Rôle |
|-------------|------|
| Symfony 7.4 | Framework PHP backend |
| Doctrine ORM | Gestion de la base de données |
| MySQL | Base de données relationnelle |
| Twig | Moteur de templates |
| Bootstrap 5.3 | Interface utilisateur |
| Stripe | Paiement en ligne |
| Railway | Hébergement (PHP + MySQL) |
| FrankenPHP | Serveur PHP en production |

---

## 🚀 Installation en local

### Prérequis
- PHP 8.2+
- Composer
- MySQL (MAMP, Laragon ou autre)
- Symfony CLI

### Étapes

```bash
# 1. Cloner le projet
git clone https://github.com/moustapha205/ShoppingFree.git
cd ShoppingFree

# 2. Installer les dépendances
composer install

# 3. Configurer l'environnement
cp .env.example .env
# Remplis les valeurs dans .env (DATABASE_URL, STRIPE_SECRET_KEY, etc.)

# 4. Créer la base de données et lancer les migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 5. Lancer le serveur
symfony server:start
```

Ensuite va sur `http://localhost:8000/admin/fill-database` (après t'être connecté en admin) pour remplir la boutique avec les produits.

---

## ⚙️ Variables d'environnement

Copie `.env.example` en `.env` et remplis ces valeurs :

```env
APP_ENV=dev
APP_SECRET=une_chaine_aleatoire_32_caracteres

DATABASE_URL="mysql://user:password@127.0.0.1:3306/ma_base?serverVersion=8.0&charset=utf8mb4"

STRIPE_PUBLIC_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...
```

---

## 🗂️ Structure du projet

```
src/
├── Controller/       # Logique des pages (Home, Shop, Cart, Admin, Payment...)
├── Entity/           # Entités Doctrine (Product, User, Sale, AutoEcole, Eleve)
├── Repository/       # Requêtes base de données
├── Form/             # Formulaires Symfony
├── Security/         # Authentification
└── Service/          # Services métier (StockManager)

templates/
├── home/             # Page d'accueil avec le catalogue
├── shop/             # Boutique et fiches produits
├── cart/             # Panier
├── admin/            # Dashboard admin
├── security/         # Login
├── registration/     # Inscription
└── base.html.twig    # Layout principal (navbar + footer)

migrations/           # Migrations Doctrine
public/
└── images/products/  # Images des produits en local
```

---

## 🔐 Compte admin

Après inscription sur le site, donne le rôle admin via cette requête SQL :

```sql
UPDATE user SET roles = '["ROLE_ADMIN"]' WHERE email = 'ton@email.com';
```

Puis accède au dashboard sur `/admin/dashboard`.

---

## 📦 Déploiement (Railway)

Le projet est déployé sur [Railway](https://railway.app) avec :
- Un service **PHP** (FrankenPHP via Railpack)
- Un service **MySQL**

Les migrations sont exécutées automatiquement avant chaque déploiement via le **Pre-deploy command** :

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

---

## 👨‍💻 Auteur

Développé par **Moustapha Kane**  
GitHub : [@moustapha205](https://github.com/moustapha205)
