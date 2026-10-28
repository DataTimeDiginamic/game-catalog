# DataTime - lancer en local avec Laragon

1. Démarrer Laragon (**Démarrer tout** : Apache + MySQL).
2. Créer la base `datatime`, puis importer `scripts/schema.sql` et `scripts/seed.sql` (phpMyAdmin ou `mysql -uroot datatime < scripts/schema.sql`).
3. Copier `config/db.local.php` (gitignoré) avec `host 127.0.0.1`, `db datatime`, `user root`, mot de passe vide, `port 3306`.
4. Le projet doit être dans `C:\laragon\www\datatime` (`game-catalog/` et `data-time-front/`).
   - API : `http://localhost/datatime/game-catalog/public/api/ping` (le `.htaccess` redirige tout vers `index.php`)
   - Front : `http://localhost/datatime/data-time-front/public/index.html`
5. Se connecter avec le compte du seed : `admin@datatime.local` / `password` (rôle admin).

Rôles : `admin` (tout + gestion des utilisateurs), `recruteur` (tout sauf utilisateurs), `salarie` (lecture + ses absences), `client` (lecture projets et tâches).

Sans Apache, l'API peut aussi tourner avec `php -S localhost:8000 -t public` : dans ce cas mettre `API_BASE_URL = "http://localhost:8000"` dans `data-time-front/src/app.utils.ts`.

---

# PHP Framework - GameCatalog

## Docker commandes de base

- `docker-compose up -d` - permet de lancer ses conteneurs docker présents dans `docker-compose.yml`.
- `docker-compose down` - arrête tous les conteneurs en marche.
- `docker-compose ps` - liste tous les conteneurs et leur status.
- `docker-compose logs -f` - permet de voir les logs des conteneurs en marche.
- `docker-compose exec php bash` - permet d'entrer dans le conteneur php.

Par convention, le fichier doit s'appeler `docker-compose.yml` ou `compose.yml`. Il utilise le YAML.
Pour ajouter des dépendances, il faut utiliser le fichier `Dockerfile`.

## Architecture MVC

Le MVC permet de **séparer clairement les responsabilités** dans l’application.

---

### Controller
Point d’entrée de la requête HTTP.  
Il reçoit l’action de l’utilisateur, appelle le modèle et choisit la vue à afficher.

---

### Model
Cœur de l’application.  
Il gère les données et la logique métier, sans connaître l’HTTP ni l’affichage.

---

### View
Partie visible par l’utilisateur.  
Elle affiche les données sans logique métier ni accès aux données.

---

### Fonctionnement

1. L’utilisateur fait une requête
2. Le Controller la reçoit
3. Le Model traite les données
4. La View affiche le résultat

---

### Règle d’or

- HTTP → Controller
- Métier / données → Model
- Affichage → View

Chaque couche reste à sa place.

## Circulation des données

| Couche     | Fait du SQL | Connait HTTP | Logique métier |
| ---------- | ----------- | ------------ | -------------- |
| BDD        | oui         | non          | non            |
| Repository | oui         | non          | non            |
| Service    | non         | non          | oui            |
| Controller | non         | oui          | non            |

### 1. Base de données (BDD)

**Rôle**
- Stocker les données brutes (tables, lignes)
- Aucune logique applicative
- MySQL / PostgreSQL / SQLite…

**Exemples**
- Table `games`
- Colonnes : `id`, `title`, `platform`, `genre`, `release_year`, `rating`

**Règle**
> La BDD ne sait rien de PHP, des contrôleurs ou de l’UI.

---

### 2. Repository (accès aux données)

**Rôle**
- Parler **uniquement à la base**
- Contenir le SQL
- Retourner des données “simples” (tableaux, objets)

**Responsabilités**
- `SELECT`, `INSERT`, `UPDATE`, `DELETE`
- Aucune logique métier
- Aucune logique HTTP

**Exemple**
```php
GameRepository::findAll()
GameRepository::findAllSortedByRating()
GameRepository::findTop($id)
```
**Règle**

Le repository ne sait pas pourquoi on demande les données,
il sait seulement comment les récupérer.

### 3. Service (logique métier)
**Rôle**
- Orchester les repositories
- Appliquer les règles métier
- Transformer / filtrer / valider les données

**Responsabilités**
- Combiner plusieurs repositories
- Décider quoi faire si une donnée n’existe pas
- Préparer les données pour le contrôleur

**Exemple**
```php
Copier le code
getLimitedGames($id)
getAllGames()
getAllGamesSortedByRating()
```

**Règle**

Le service ne connaît ni `$_GET`, ni `$_POST`, ni le HTML.

### 4. Controller (entrée HTTP)
**Rôle**
- Gérer la requête utilisateur
- Appeler les services
- Choisir la vue à afficher

**Responsabilités**
- Lire `$_GET`, `$_POST`
- Gérer les routes (?page=home, ?page=detail)
- Passer les données à la vue

**Exemple**

```php
Copier le code
home()
gameDetail()
addGame()
```

**Règle**

Le contrôleur ne fait pas de SQL
et ne décide pas des règles métier.

## 5. Routes de l’application

| Méthode | URL           | Action appelée | Description                  |
|---------|---------------|----------------|------------------------------|
| GET     | `/`           | `home()`       | Page d’accueil               |
| GET     | `/random`     | `random()`     | Affiche un jeu aléatoire     |
| GET     | `/add`        | `add()`        | Formulaire d’ajout d’un jeu  |
| POST    | `/add`        | `handleAddGame()`        | Ajout formulaire             |
| GET     | `/games`      | `games()`      | Liste des jeux               |
| GET     | `/games/{id}` | `gameById()`   | Affiche les détails d'un jeu |
| *       | autre         | `notFound()`   | Page 404                     |

## Namespaces et `use`

Le projet utilise des **namespaces** pour organiser le code et éviter les collisions de noms.  
La convention est simple : **namespace = chemin de dossier dans `src/`**.

- `namespace Core;` → `src/Core/...`
- `namespace App\Controller;` → `src/App/Controller/...`

Pour utiliser une classe, on l’importe avec `use` (ou on utilise son nom complet).

Exemples :
- Import : `use Core\Session;`
- Utilisation : `new Session();`

Sans import, il faut écrire le nom complet :
- `new \Core\Session();`

