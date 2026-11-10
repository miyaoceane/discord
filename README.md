# Discord — application de messagerie avec Symfony

## 1. Présentation du projet

Ce projet consiste à développer une application web de messagerie inspirée de Discord, dans le cadre du travail demandé sur le framework Symfony.

L'application permet à plusieurs utilisateurs de créer un compte, de se connecter, de consulter des salons de discussion et d'y échanger des messages. Chaque message est associé à son auteur et au salon dans lequel il a été envoyé.

L'interface utilise un thème sombre avec des nuances de violet, de gris et de bleu-violet. Le projet met en pratique les principales notions travaillées avec Symfony : contrôleurs, routes, formulaires, validation, entités Doctrine, relations entre entités, migrations, authentification et templates Twig.

## 2. Fonctionnalités réalisées

### Gestion des utilisateurs

- Inscription avec pseudo, adresse e-mail et mot de passe.
- Vérification de l'acceptation des conditions lors de l'inscription.
- Vérification de l'unicité de l'adresse e-mail par Doctrine/Symfony.
- Hachage du mot de passe avant son enregistrement en base de données.
- Connexion avec l'adresse e-mail et le mot de passe.
- Déconnexion.

### Gestion des salons

- Affichage de la liste des salons.
- Création d'un salon avec un nom et une description facultative.
- Modification du nom et de la description d'un salon.
- Suppression d'un salon vide, après confirmation.
- Refus de la suppression d'un salon qui contient déjà des messages, afin de conserver l'historique des conversations.

### Messagerie

- Accès à la conversation d'un salon.
- Envoi de messages par l'utilisateur connecté.
- Enregistrement de la date et de l'heure d'envoi.
- Affichage du pseudo de l'auteur et de la date de chaque message.
- Affichage des messages du salon sélectionné uniquement.
- Refus des messages vides.
- Limitation du contenu d'un message à 2 000 caractères.

### Interface et sécurité

- Navigation commune entre les pages.
- Interface personnalisée avec un thème sombre violet/gris.
- Accès aux salons et aux conversations réservé aux utilisateurs authentifiés.
- Protection CSRF de la suppression d'un salon.

> **Remarque :** les messages ne sont pas actualisés en temps réel. Après l'envoi d'un message, la page de conversation est rechargée pour afficher le nouvel échange.

## 3. Technologies utilisées

- **PHP 8.4 ou supérieur**
- **Symfony 8.1**
- **Doctrine ORM** et **Doctrine Migrations**
- **MariaDB** (développement réalisé avec MariaDB 10.4.32)
- **Twig** pour les templates
- **Symfony Forms** et **Symfony Validator** pour les formulaires et leurs contrôles
- **Symfony Security** pour l'authentification et le hachage des mots de passe
- **HTML et CSS** pour l'interface
- **Symfony AssetMapper** pour la gestion des assets
- **Git et GitHub** pour le versionnement du projet

Le projet utilise AssetMapper et ne nécessite pas de compilation JavaScript avec npm pour le fonctionnement actuel de l'interface. Il n'y a donc pas de `package.json` à installer dans cette version.

## 4. Prérequis

Avant l'installation, vérifier que la machine dispose de :

- PHP 8.4 ou supérieur, avec PDO MySQL (`pdo_mysql`) activé, ainsi que les extensions PHP requises par Composer (`ctype` et `iconv`).
- Composer 2.
- MariaDB ou MySQL en fonctionnement.
- Symfony CLI pour lancer le serveur local avec la commande `symfony server:start`.
- Git pour récupérer le dépôt.

Le projet utilise Symfony 8.1. Il faut donc une version de PHP compatible avec cette version de Symfony et avec les dépendances déclarées dans `composer.json`.

## 5. Installation et lancement de l'application

Les commandes ci-dessous sont à exécuter depuis un terminal.

### 5.1. Récupérer le projet

```bash
git clone https://github.com/miyaoceane/discord.git
cd discord
```

### 5.2. Installer les dépendances PHP

```bash
composer install
```

Cette commande installe les dépendances listées dans `composer.json` et verrouillées dans `composer.lock`. Le dossier `vendor/` est recréé automatiquement et n'est pas versionné dans Git.

### 5.3. Configurer la connexion à MariaDB

Démarrer le service MariaDB (par exemple depuis XAMPP), puis créer un fichier **`.env.local` à la racine du projet**. Ce fichier local n'est pas envoyé sur GitHub : il contient les paramètres propres à la machine qui exécute l'application.

Exemple de contenu pour une installation MariaDB locale :

```dotenv
APP_ENV=dev
APP_SECRET=remplacer-par-une-cle-secrete-propre-a-la-machine
DATABASE_URL="mysql://root:@127.0.0.1:3306/discord?serverVersion=mariadb-10.4.32&charset=utf8mb4"
```

Adapter cette ligne à l'environnement de la machine :

- remplacer `root` par le nom de l'utilisateur MariaDB si nécessaire ;
- renseigner le mot de passe après `root:` si un mot de passe est défini ;
- adapter l'hôte et le port si MariaDB n'écoute pas sur `127.0.0.1:3306` ;
- adapter `serverVersion` à la version réelle de MariaDB/MySQL installée.

La valeur `mariadb-10.4.32` correspond à l'environnement de développement utilisé pour réaliser ce projet. Les caractères spéciaux présents dans un mot de passe doivent être encodés dans l'URL de connexion.

**Ne pas remplacer le fichier `.env` suivi par Git par des identifiants personnels et ne jamais publier `.env.local`.**

### 5.4. Créer la base de données

Vérifier que l'utilisateur MariaDB renseigné dans `.env.local` a le droit de créer une base, puis exécuter :

```bash
php bin/console doctrine:database:create
```

La base attendue porte le nom `discord`.

Si elle existe déjà, ne pas la supprimer : passer directement à l'étape des migrations après avoir vérifié que la connexion pointe vers la bonne base.

### 5.5. Créer les tables avec les migrations Doctrine

Exécuter :

```bash
php bin/console doctrine:migrations:migrate
```

Confirmer avec `yes` si Doctrine demande l'autorisation d'appliquer les migrations.

Les migrations présentes dans le dossier `migrations/` créent progressivement le schéma de la base. Il ne faut pas créer manuellement les tables avant leur exécution.

Pour consulter l'état des migrations :

```bash
php bin/console doctrine:migrations:status
```

Si Doctrine affiche explicitement une erreur indiquant que le stockage des métadonnées des migrations n'est pas à jour, exécuter :

```bash
php bin/console doctrine:migrations:sync-metadata-storage
php bin/console doctrine:migrations:migrate
```

Cette commande de synchronisation est une commande de dépannage ; elle n'est normalement pas nécessaire sur une nouvelle installation correctement configurée.

### 5.6. Lancer le serveur Symfony

Depuis la racine du projet :

```bash
symfony server:start --port=8000
```

Ouvrir ensuite l'application dans le navigateur :

```text
http://127.0.0.1:8000
```

Pour arrêter le serveur, utiliser `Ctrl + C` dans le terminal.

### 5.7. Première utilisation

1. Ouvrir `/register` et créer un compte.
2. Ouvrir `/login` et se connecter avec l'adresse e-mail et le mot de passe du compte.
3. Ouvrir `/salon` pour consulter les salons.
4. Utiliser **Créer un salon** pour ajouter un espace de discussion.
5. Cliquer sur **Ouvrir** pour entrer dans un salon et envoyer des messages.

Après l'inscription, se connecter depuis la page `/login` si la session n'est pas déjà authentifiée.

## 6. Routes principales

| Fonction | URL | Accès |
|---|---|---|
| Accueil | `/` | Public |
| Inscription | `/register` | Public |
| Connexion | `/login` | Public |
| Déconnexion | `/logout` | Utilisateur connecté |
| Liste des salons | `/salon` | Utilisateur connecté |
| Créer un salon | `/salon/nouveau` | Utilisateur connecté |
| Modifier un salon | `/salon/{id}/modifier` | Utilisateur connecté |
| Supprimer un salon | `/salon/{id}/supprimer` | Utilisateur connecté, requête POST avec jeton CSRF |
| Consulter/envoyer des messages | `/salon/{id}/messages` | Utilisateur connecté |

Les pages de consultation et de gestion des salons ainsi que les conversations sont protégées par un contrôle d'authentification dans les contrôleurs.

## 7. Structure de la base de données

Le modèle métier comporte trois entités principales : `User`, `Salon` et `Message`.

```text
USER (1)  -------------------- (N) MESSAGE (N) -------------------- (1) SALON
  |                                  |                                  |
  | un utilisateur peut              | chaque message possède           | un salon peut
  | envoyer plusieurs messages       | un seul auteur et un seul salon  | contenir plusieurs messages
```

### 7.1. Table `user`

Cette table contient les comptes des utilisateurs.

| Champ | Type | Règle / rôle |
|---|---|---|
| `id` | INT | Clé primaire, auto-incrémentée |
| `email` | VARCHAR(180) | Obligatoire et unique ; identifiant de connexion |
| `roles` | JSON | Rôles de sécurité Symfony |
| `password` | VARCHAR(255) | Mot de passe haché, jamais stocké en clair |
| `pseudo` | VARCHAR(50) | Pseudo affiché dans les conversations |

Un utilisateur peut être associé à plusieurs messages. La relation est définie par `User.messages` (OneToMany).

### 7.2. Table `salon`

Cette table contient les espaces de discussion.

| Champ | Type | Règle / rôle |
|---|---|---|
| `id` | INT | Clé primaire, auto-incrémentée |
| `nom` | VARCHAR(100) | Obligatoire |
| `description` | LONGTEXT | Facultative, peut être NULL |

Un salon peut contenir plusieurs messages. La relation est définie par `Salon.messages` (OneToMany).

### 7.3. Table `message`

Cette table contient les messages envoyés dans les salons.

| Champ | Type | Règle / rôle |
|---|---|---|
| `id` | INT | Clé primaire, auto-incrémentée |
| `contenu` | LONGTEXT | Texte du message, obligatoire |
| `date_envoi` | DATETIME | Date et heure d'envoi |
| `auteur_id` | INT | Clé étrangère vers `user.id`, obligatoire |
| `salon_id` | INT | Clé étrangère vers `salon.id`, obligatoire |

Chaque message appartient à un seul utilisateur et à un seul salon : les deux relations sont de type ManyToOne depuis `Message`. Les clés étrangères empêchent de rattacher un message à un utilisateur ou un salon inexistant.

### 7.4. Schéma SQL équivalent des tables métier

Le code suivant représente la structure SQL des trois tables métier telle qu'elle est créée par les migrations Doctrine. **Il est fourni pour expliquer le modèle ; pour installer le projet, il faut exécuter les migrations plutôt que lancer ce SQL manuellement.**

```sql
CREATE TABLE `user` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `email` VARCHAR(180) NOT NULL,
    `roles` JSON NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `pseudo` VARCHAR(50) NOT NULL,
    UNIQUE INDEX `UNIQ_IDENTIFIER_EMAIL` (`email`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4;

CREATE TABLE `salon` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `nom` VARCHAR(100) NOT NULL,
    `description` LONGTEXT DEFAULT NULL,
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4;

CREATE TABLE `message` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `contenu` LONGTEXT NOT NULL,
    `date_envoi` DATETIME NOT NULL,
    `auteur_id` INT NOT NULL,
    `salon_id` INT NOT NULL,
    INDEX `IDX_MESSAGE_AUTEUR` (`auteur_id`),
    INDEX `IDX_MESSAGE_SALON` (`salon_id`),
    PRIMARY KEY (`id`),
    CONSTRAINT `FK_MESSAGE_AUTEUR`
        FOREIGN KEY (`auteur_id`) REFERENCES `user` (`id`),
    CONSTRAINT `FK_MESSAGE_SALON`
        FOREIGN KEY (`salon_id`) REFERENCES `salon` (`id`)
) DEFAULT CHARACTER SET utf8mb4;
```

### 7.5. Tables techniques

- `doctrine_migration_versions` : table créée et utilisée par Doctrine Migrations pour mémoriser les migrations déjà exécutées. Elle est gérée par Doctrine et ne doit pas être créée manuellement.
- `messenger_messages` : table technique créée par la migration initiale pour le transport Doctrine Messenger configuré dans le squelette Symfony. Les messages de discussion de l'application sont, eux, enregistrés dans la table `message`.

La migration initiale crée également cette table technique :

```sql
CREATE TABLE `messenger_messages` (
    `id` BIGINT AUTO_INCREMENT NOT NULL,
    `body` LONGTEXT NOT NULL,
    `headers` LONGTEXT NOT NULL,
    `queue_name` VARCHAR(190) NOT NULL,
    `created_at` DATETIME NOT NULL,
    `available_at` DATETIME NOT NULL,
    `delivered_at` DATETIME DEFAULT NULL,
    INDEX `IDX_MESSENGER_QUEUE_AVAILABLE_DELIVERED`
        (`queue_name`, `available_at`, `delivered_at`, `id`),
    PRIMARY KEY (`id`)
);
```

Cette table est liée au transport Messenger du squelette Symfony. Elle ne remplace pas la table `message` qui stocke les conversations de l'application.

## 8. Migrations fournies

Les fichiers suivants se trouvent dans `migrations/` et doivent être conservés dans le dépôt Git :

| Migration | Opérations principales |
|---|---|
| `Version20261110144839` | Création de `user` et de la table technique `messenger_messages` |
| `Version20261110160732` | Création de `salon` |
| `Version20261110164940` | Création de `message`, de ses index et de ses clés étrangères vers `user` et `salon` |

Les migrations permettent de reconstruire la structure de la base sur une autre machine sans avoir à transmettre une copie de la base de données.

## 9. Tests fonctionnels réalisés

Les vérifications suivantes ont été effectuées manuellement pendant le développement :

| Test | Résultat attendu / constaté |
|---|---|
| Créer un compte utilisateur | Le compte est enregistré |
| Vérifier le mot de passe en base | La valeur stockée est un hash, pas le mot de passe en clair |
| Se connecter et se déconnecter | Les deux opérations fonctionnent |
| Créer un salon | Le salon apparaît dans la liste |
| Modifier un salon | Le nom et/ou la description sont mis à jour |
| Supprimer un salon vide | La suppression est autorisée après confirmation |
| Supprimer un salon contenant des messages | La suppression est refusée et l'historique est conservé |
| Envoyer un message | Le message apparaît dans la conversation avec son auteur et sa date |
| Envoyer un message vide | Le formulaire refuse l'envoi |
| Échanger entre deux comptes | Les messages des deux utilisateurs apparaissent dans le même salon |
| Vérifier l'isolation des salons | Un message envoyé dans un salon n'apparaît pas dans un autre |
| Accéder aux salons sans connexion | L'utilisateur est redirigé vers la connexion |

Ces tests ont été réalisés manuellement dans le navigateur. Le projet contient également un fichier de test de contrôleur généré avec Symfony, mais une suite PHPUnit complète couvrant toutes les fonctionnalités reste à développer. **Point à corriger avant d'exécuter ce test automatisé :** `tests/Controller/HomeControllerTest.php` demande actuellement la route `/home`, alors que `HomeController` déclare la route `/`. Le test doit être aligné sur la route réellement définie, puis exécuté.

## 10. Difficultés rencontrées et solutions apportées

### Configuration des migrations Doctrine avec MariaDB

Une erreur indiquait que le stockage des métadonnées des migrations n'était pas à jour. La version du serveur MariaDB devait être correctement déclarée dans `DATABASE_URL`. La configuration locale a été adaptée à MariaDB 10.4.32, puis le stockage a été synchronisé avec :

```bash
php bin/console doctrine:migrations:sync-metadata-storage
```

La génération et l'exécution des migrations ont ensuite fonctionné.

### Relations entre les entités

Il a fallu définir les relations entre les trois entités : un utilisateur peut envoyer plusieurs messages, un salon peut contenir plusieurs messages, et chaque message est rattaché à un seul auteur et un seul salon. Les relations ont été créées avec Doctrine (`OneToMany` et `ManyToOne`), puis matérialisées en base par des clés étrangères.

### Validation des messages

Le formulaire de message a été complété avec `NotBlank` pour empêcher les messages vides et `Length` pour limiter le contenu à 2 000 caractères.

### Suppression d'un salon

La suppression d'un salon contenant des messages aurait pu provoquer une erreur liée à la clé étrangère. Une règle métier a donc été ajoutée : un salon n'est supprimable que s'il ne contient aucun message. La requête de suppression est envoyée en POST et protégée par un jeton CSRF.

### Organisation des templates et du style

La navigation commune a été placée dans `base.html.twig` afin d'être disponible sur les différentes pages. Le CSS a ensuite été regroupé dans `public/css/style.css` pour appliquer une identité visuelle cohérente.

## 11. Améliorations possibles

Plusieurs évolutions pourraient être ajoutées dans une prochaine version :

- Mettre en place une messagerie en temps réel avec Mercure ou WebSocket, sans rechargement de page.
- Ajouter des droits de gestion : par exemple, autoriser uniquement le créateur ou un administrateur à modifier et supprimer un salon. Dans la version actuelle, tout utilisateur connecté peut accéder aux actions de gestion des salons.
- Permettre la modification et la suppression des messages, avec des règles d'autorisation adaptées.
- Ajouter des profils utilisateurs, des avatars et une procédure de réinitialisation du mot de passe.
- Ajouter une pagination ou un chargement progressif pour les conversations longues.
- Compléter les tests automatisés PHPUnit pour les inscriptions, authentifications, salons, messages, validations et contrôles d'accès.
- Améliorer encore l'ergonomie sur mobile et ajouter des notifications de nouveaux messages.

## 12. Organisation du projet

```text
discord/
├── config/                  # Configuration Symfony, Doctrine et sécurité
├── migrations/              # Migrations Doctrine pour créer/faire évoluer la base
├── public/
│   ├── css/
│   │   └── style.css        # Feuille de style de l'application
│   └── index.php            # Point d'entrée HTTP de Symfony
├── src/
│   ├── Controller/          # Contrôleurs : accueil, inscription, connexion, salons, messages
│   ├── Entity/              # Entités User, Salon et Message
│   ├── Form/                # Formulaires Symfony
│   └── Repository/          # Requêtes Doctrine
├── templates/               # Templates Twig
├── tests/                   # Tests automatisés
├── .env                     # Valeurs d'environnement par défaut, sans secrets personnels
├── .env.local               # Configuration locale à créer, non versionnée
├── .gitignore               # Fichiers et dossiers exclus de Git
├── composer.json            # Dépendances PHP
├── composer.lock            # Versions exactes des dépendances
└── README.md                # Documentation du projet
```

## 13. Dépôt Git

Les dossiers `vendor/`, `node_modules/` et le fichier `.env.local` ne sont pas versionnés. Les fichiers `composer.json`, `composer.lock`, les sources PHP, les templates Twig et les migrations Doctrine sont conservés pour permettre l'installation et la reconstruction de la base de données.
