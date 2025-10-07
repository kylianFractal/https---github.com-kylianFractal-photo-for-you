# PhotoForYou — Scaffold (POO, PSR-12)

This single-file code scaffold contains a full project structure with PSR-4 autoloadable PHP classes, a minimal front controller, templates, SQL schema and composer.json. Paste files into a project and run `composer dump-autoload`.

---

## composer.json

```json
{
    "name": "atelierlumiere/photoforyou",
    "description": "Plateforme PhotoForYou - POO PSR-12 scaffold",
    "type": "project",
    "require": {
        "php": "^8.0",
        "vlucas/phpdotenv": "^5.5"
    },
    "autoload": {
        "psr-4": {
            "PhotoForYou\\": "src/"
        }
    }
}
```

---

## .env.example

```
APP_ENV=dev
DB_DSN=mysql:host=127.0.0.1;dbname=photoforyou;charset=utf8mb4
DB_USER=root
DB_PASS=secret
CREDITS_CONVERSION=5.00
```

---

## Project structure (recommended)

```
photoforyou/
├─ composer.json
├─ .env
├─ migrations/
│  └─ schema.sql
├─ public/
│  └─ index.php
├─ src/
│  ├─ Config/Database.php
│  ├─ Controllers/HomeController.php
│  ├─ Controllers/AuthController.php
│  ├─ Controllers/PhotoController.php
│  ├─ Models/User.php
│  ├─ Models/Photo.php
│  ├─ Repositories/UserRepository.php
│  ├─ Repositories/PhotoRepository.php
│  ├─ Services/PaymentService.php
│  └─ Helpers/View.php
├─ templates/
│  ├─ layout.php
│  └─ home.php
└─ migrations/schema.sql
```

---

## migrations/schema.sql

```sql
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('buyer','photographer','admin') NOT NULL DEFAULT 'buyer',
  `credits` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `photos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `price_credits` INT NOT NULL DEFAULT 2,
  `exclusive` TINYINT(1) NOT NULL DEFAULT 0,
  `width` INT NULL,
  `height` INT NULL,
  `filesize` INT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_photos_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `purchases` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `buyer_id` INT NOT NULL,
  `photo_id` INT NOT NULL,
  `credits` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_purchases_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_purchases_photo` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## public/index.php

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use PhotoForYou\Config\Database;
use PhotoForYou\Controllers\HomeController;
use PhotoForYou\Controllers\AuthController;
use PhotoForYou\Controllers\PhotoController;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$pdo = Database::getInstance();

// Very small router
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

session_start();

if ($path === '/' || $path === '/home') {
    (new HomeController($pdo))->index();
    exit;
}

if ($path === '/auth' && $method === 'POST') {
    (new AuthController($pdo))->login();
    exit;
}

if ($path === '/logout') {
    (new AuthController($pdo))->logout();
    exit;
}

if (str_starts_with($path, '/photo')) {
    (new PhotoController($pdo))->handle($path, $method);
    exit;
}

http_response_code(404);
echo 'Not Found';
```

---

## src/Config/Database.php

```php
<?php

declare(strict_types=1);

namespace PhotoForYou\Config;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = (string) ($_ENV['DB_DSN'] ?? '');
            $user = (string) ($_ENV['DB_USER'] ?? 'root');
            $pass = (string) ($_ENV['DB_PASS'] ?? '');

            try {
                self::$instance = new PDO(
                    $dsn,
                    $user,
                    $pass,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $e) {
                throw $e;
            }
        }

        return self::$instance;
    }
}
```

---

## src/Models/User.php

```php
<?php

declare(strict_types=1);

namespace PhotoForYou\Models;

use DateTimeImmutable;

class User
{
    private ?int $id;
    private string $email;
    private string $passwordHash;
    private string $role;
    private int $credits;
    private DateTimeImmutable $createdAt;

    public function __construct(
        ?int $id,
        string $email,
        string $passwordHash,
        string $role = 'buyer',
        int $credits = 0,
        ?DateTimeImmutable $createdAt = null
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->role = $role;
        $this->credits = $credits;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getCredits(): int
    {
        return $this->credits;
    }

    public function addCredits(int $credits): void
    {
        $this->credits += $credits;
    }
}
```

---

## src/Models/Photo.php

```php
<?php

declare(strict_types=1);

namespace PhotoForYou\Models;

class Photo
{
    private ?int $id;
    private int $userId;
    private string $title;
    private string $filename;
    private int $priceCredits;
    private bool $exclusive;

    public function __construct(
        ?int $id,
        int $userId,
        string $title,
        string $filename,
        int $priceCredits = 2,
        bool $exclusive = false
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->title = $title;
        $this->filename = $filename;
        $this->priceCredits = $priceCredits;
        $this->exclusive = $exclusive;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getPriceCredits(): int
    {
        return $this->priceCredits;
    }

    public function isExclusive(): bool
    {
        return $this->exclusive;
    }
}
```

---

## src/Repositories/UserRepository.php

```php
<?php

declare(strict_types=1);

namespace PhotoForYou\Repositories;

use PDO;
use PhotoForYou\Models\User;
use DateTimeImmutable;

class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (! $row) {
            return null;
        }

        return new User(
            (int) $row['id'],
            $row['email'],
            $row['password'],
            $row['role'],
            (int) $row['credits'],
            new DateTimeImmutable($row['created_at'])
        );
    }

    public function save(User $user): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (email, password, role, credits) VALUES (:email, :password, :role, :credits)'
        );

        $stmt->execute([
            'email' => $user->getEmail(),
            'password' => $user->getPasswordHash(),
            'role' => $user->getRole(),
            'credits' => $user->getCredits(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}
```

---

## src/Repositories/PhotoRepository.php

```php
<?php

declare(strict_types=1);

namespace PhotoForYou\Repositories;

use PDO;
use PhotoForYou\Models\Photo;

class PhotoRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(int $limit = 25, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM photos ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $photos = [];

        foreach ($rows as $row) {
            $photos[] = new Photo(
                (int) $row['id'],
                (int) $row['user_id'],
                $row['title'],
                $row['filename'],
                (int) $row['price_credits'],
                (bool) $row['exclusive']
            );
        }

        return $photos;
    }

    public function findById(int $id): ?Photo
    {
        $stmt = $this->pdo->prepare('SELECT * FROM photos WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (! $row) {
            return null;
        }

        return new Photo(
            (int) $row['id'],
            (int) $row['user_id'],
            $row['title'],
            $row['filename'],
            (int) $row['price_credits'],
            (bool) $row['exclusive']
        );
    }

    public function save(Photo $photo): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO photos (user_id, title, filename, price_credits, exclusive, width, height, filesize) VALUES (:user_id, :title, :filename, :price_credits, :exclusive, :width, :height, :filesize)'
        );

        $stmt->execute([
            'user_id' => $photo->getUserId(),
            'title' => $photo->getTitle(),
            'filename' => $photo->getFilename(),
            'price_credits' => $photo->getPriceCredits(),
            'exclusive' => $photo->isExclusive() ? 1 : 0,
            'width' => null,
            'height' => null,
            'filesize' => null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}
```

---

## src/Services/PaymentService.php

```php
<?php

declare(strict_types=1);

namespace PhotoForYou\Services;

use PDO;

class PaymentService
{
    private PDO $pdo;
    private float $creditValue;

    public function __construct(PDO $pdo, float $creditValue)
    {
        $this->pdo = $pdo;
        $this->creditValue = $creditValue;
    }

    public function purchasePhoto(int $buyerId, int $photoId, int $credits): bool
    {
        $this->pdo->beginTransaction();

        try {
            // Debit buyer
            $stmt1 = $this->pdo->prepare('UPDATE users SET credits = credits - :credits WHERE id = :id AND credits >= :credits');
            $stmt1->execute(['credits' => $credits, 'id' => $buyerId]);

            if ($stmt1->rowCount() === 0) {
                $this->pdo->rollBack();

                return false;
            }

            // Give photographer his share (50%)
            $stmtPhoto = $this->pdo->prepare('SELECT user_id FROM photos WHERE id = :id');
            $stmtPhoto->execute(['id' => $photoId]);
            $owner = $stmtPhoto->fetchColumn();

            $photographerShare = (int) floor($credits * 0.5);

            $stmt2 = $this->pdo->prepare('UPDATE users SET credits = credits + :credits WHERE id = :id');
            $stmt2->execute(['credits' => $photographerShare, 'id' => $owner]);

            // record purchase
            $stmt3 = $this->pdo->prepare('INSERT INTO purchases (buyer_id, photo_id, credits) VALUES (:buyer_id, :photo_id, :credits)');
            $stmt3->execute(['buyer_id' => $buyerId, 'photo_id' => $photoId, 'credits' => $credits]);

            // if exclusive, remove photo from catalog
            $stmtExcl = $this->pdo->prepare('SELECT exclusive FROM photos WHERE id = :id');
            $stmtExcl->execute(['id' => $photoId]);
            $isExclusive = (bool) $stmtExcl->fetchColumn();

            if ($isExclusive) {
                $stmtDel = $this->pdo->prepare('DELETE FROM photos WHERE id = :id');
                $stmtDel->execute(['id' => $photoId]);
            }

            $this->pdo->commit();

            return true;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();

            return false;
        }
    }
}
```

---

## src/Controllers/HomeController.php

```php
<?php

declare(strict_types=1);

namespace PhotoForYou\Controllers;

use PDO;
use PhotoForYou\Repositories\PhotoRepository;
use PhotoForYou\Helpers\View;

class HomeController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function index(): void
    {
        $repo = new PhotoRepository($this->pdo);
        $photos = $repo->findAll(12, 0);

        View::render('home', ['photos' => $photos]);
    }
}
```

---

## src/Controllers/AuthController.php

```php
<?php

declare(strict_types=1);

namespace PhotoForYou\Controllers;

use PDO;
use PhotoForYou\Repositories\UserRepository;
use PhotoForYou\Helpers\View;

class AuthController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function login(): void
    {
        $email = (string) ($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        $repo = new UserRepository($this->pdo);
        $user = $repo->findByEmail($email);

        if ($user === null || ! password_verify($password, $user->getPasswordHash())) {
            View::render('home', ['error' => 'Identifiants invalides']);

            return;
        }

        $_SESSION['user_id'] = $user->getId();

        header('Location: /');
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();

        header('Location: /');
    }
}
```

---

## src/Controllers/PhotoController.php

```php
<?php

declare(strict_types=1);

namespace PhotoForYou\Controllers;

use PDO;
use PhotoForYou\Repositories\PhotoRepository;
use PhotoForYou\Repositories\UserRepository;
use PhotoForYou\Services\PaymentService;
use PhotoForYou\Helpers\View;

class PhotoController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function handle(string $path, string $method): void
    {
        // Example routes: /photo/view?id=1, /photo/buy
        if ($path === '/photo/view') {
            $id = (int) ($_GET['id'] ?? 0);

            $repo = new PhotoRepository($this->pdo);
            $photo = $repo->findById($id);

            if ($photo === null) {
                http_response_code(404);
                echo 'Photo not found';

                return;
            }

            View::render('home', ['photo' => $photo]);

            return;
        }

        if ($path === '/photo/buy' && $method === 'POST') {
            $buyerId = (int) ($_SESSION['user_id'] ?? 0);
            $photoId = (int) ($_POST['photo_id'] ?? 0);

            if ($buyerId === 0) {
                header('Location: /');

                return;
            }

            $repo = new PhotoRepository($this->pdo);
            $photo = $repo->findById($photoId);

            if ($photo === null) {
                echo 'Photo not found';

                return;
            }

            $payment = new PaymentService($this->pdo, (float) ($_ENV['CREDITS_CONVERSION'] ?? 5.0));
            $ok = $payment->purchasePhoto($buyerId, $photoId, $photo->getPriceCredits());

            if ($ok) {
                header('Location: /?purchased=1');

                return;
            }

            echo 'Purchase failed';
        }
    }
}
```

---

## src/Helpers/View.php

```php
<?php

declare(strict_types=1);

namespace PhotoForYou\Helpers;

final class View
{
    public static function render(string $template, array $params = []): void
    {
        extract($params, EXTR_SKIP);

        $templatePath = __DIR__ . '/../../templates/' . $template . '.php';

        if (! file_exists($templatePath)) {
            throw new \RuntimeException('Template not found: ' . $templatePath);
        }

        include __DIR__ . '/../../templates/layout.php';
    }
}
```

---

## templates/layout.php

```php
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>PhotoForYou</title>
    <link rel="stylesheet" href="/assets/app.css" />
</head>
<body>
<header>
    <nav>
        <a href="/">PhotoForYou</a>
        <form action="/auth" method="post" style="display:inline-block;">
            <input name="email" placeholder="email" />
            <input name="password" placeholder="password" type="password" />
            <button type="submit">Se connecter</button>
        </form>
    </nav>
</header>
<main>
    <?php include __DIR__ . '/' . basename("$template.php"); ?>
</main>
</body>
</html>
```

---

## templates/home.php

```php
<?php
/** @var \PhotoForYou\Models\Photo[] $photos */
?>
<section>
    <h1>Dernières photos</h1>

    <?php if (! empty($error)) : ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="gallery">
        <?php foreach ($photos ?? [] as $photo) : ?>
            <article>
                <a href="/photo/view?id=<?= $photo->getId() ?>">
                    <img src="/uploads/<?= htmlspecialchars($photo->getFilename()) ?>" alt="<?= htmlspecialchars($photo->getTitle()) ?>" />
                </a>
                <h3><?= htmlspecialchars($photo->getTitle()) ?></h3>
                <p>Prix: <?= $photo->getPriceCredits() ?> crédits</p>
                <form action="/photo/buy" method="post">
                    <input type="hidden" name="photo_id" value="<?= $photo->getId() ?>" />
                    <button type="submit">Acheter</button>
                </form>
            </article>
        <?php endforeach; ?>
    </div>
</section>
```

---

## README.md (brief)

```
Installation:
1. composer install
2. copy .env.example to .env and edit
3. create database and import migrations/schema.sql
4. point webserver docroot to public/
```

---

### Notes & next steps

- This scaffold uses plain PHP templates for simplicity. For production consider using Twig and a proper router.
- Add input validation, CSRF protection, file upload validation, and tests.
- Add unit tests (PHPUnit) and make sure code is PSR-12 formatted via PHPCBF/PHPCS.

---

*Tu veux que j’écrive ces fichiers dans un dépôt ou que je génère un zip téléchargeable ?*
