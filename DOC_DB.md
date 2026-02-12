## Schéma de base de données et commandes (Doctrine / PostgreSQL)

### 1. Entités (les 3 classes qui communiquent)

- **Categorie**
  - `idCategorie` (PK, int, auto-incrément)
  - `nomCategorie` (string)
  - `descriptionType` (text, nullable)
  - Relation : **1 Catégorie** → **N Objets**

- **Objet**
  - `idObjet` (PK, int, auto-incrément)
  - `nom` (string)
  - `descriptionHistorique` (text, nullable)
  - `epoque` (string, nullable)
  - `origine` (string, nullable)
  - `materiaux` (string, nullable)
  - `categorie_id` (FK vers `Categorie.idCategorie`)
  - Relation : **1 Objet** → **N Médias**

- **Media**
  - `idMedia` (PK, int, auto-incrément)
  - `typeMedia` (string, valeurs typiques : `video`, `audio`, `image`)
  - `lienFichier` (string, URL ou chemin)
  - `objet_id` (FK vers `Objet.idObjet`)

Cette structure montre clairement :
- séparation entre **Objets** et **Médias** (plusieurs médias par objet),
- possibilité d’ajouter facilement de nouvelles **Catégories**.

---

### 2. Création / mise à jour du schéma avec Doctrine

Assure-toi d’avoir PostgreSQL lancé et que la variable `DATABASE_URL` dans `.env` pointe vers une base existante (ex. base `app`).

Depuis la racine du projet (`c:\Users\Yassine\mon_projet`) :

```bash
php bin/console doctrine:database:create
php bin/console make:migration   # (optionnel si tu veux générer le fichier de migration)
php bin/console doctrine:migrations:migrate
```

Si tu ne veux pas gérer les migrations pour l’instant et juste synchroniser le schéma rapidement :

```bash
php bin/console doctrine:schema:update --force
```

> Attention : `schema:update --force` est pratique en développement, mais les migrations sont préférables à long terme.

---

### 3. Lancer l’application

Toujours à la racine du projet :

```bash
symfony serve -d   # ou: php -S localhost:8000 -t public
```

Ensuite ouvre ton navigateur sur `http://localhost:8000` :
- page d’accueil : tableau de bord global,
- onglets de navigation : **Catégories**, **Objets**, **Médias** avec les CRUD complets.

