# LMS 開発環境

エンジニア育成カリキュラム用の開発環境です。

## 動作環境

- Windows（Docker Desktop）
- macOS Intel（Docker Desktop）
- macOS Apple Silicon（Docker Desktop）

## 構成

| サービス | URL | 用途 |
|----------|-----|------|
| PHP + Apache | http://localhost:8080 | バックエンド（PHP実行） |
| MySQL 8.0 | localhost:3306 | データベース |

## セットアップ

```bash
cd lms-environment
docker compose up -d
```

http://localhost:8080 にアクセスして動作確認画面が表示されればOK。

## ディレクトリ構成

```
lms-environment/
├── backend/          ← PHP ファイルを置く場所
│   └── public/       ← Web公開ディレクトリ（ここがドキュメントルート）
│       └── index.php
├── frontend/         ← HTML/CSS/JavaScript ファイルを置く場所
│   ├── index.html
│   ├── style.css
│   └── app.js
├── docker/
│   └── php/
│       └── Dockerfile
└── docker-compose.yml
```

## 使い方

### バックエンド（PHP）— 第3章〜

`backend/public/` に PHP ファイルを作成すると `http://localhost:8080/ファイル名.php` でアクセスできます。

```php
<?php
// backend/public/hello.php
echo 'Hello, World!';
```

### データベース（MySQL）— 第4章〜

MySQL にはコンテナ内からコンソールでアクセスします:

```bash
docker compose exec db mysql -u lms_user -plms_pass lms
```

PHP からの接続情報:

| 項目 | 値 |
|------|-----|
| ホスト | db |
| データベース名 | lms |
| ユーザー名 | lms_user |
| パスワード | lms_pass |

```php
<?php
$pdo = new PDO('mysql:host=db;dbname=lms;charset=utf8mb4', 'lms_user', 'lms_pass');
```

### フロントエンド（HTML/CSS/JavaScript）— 第2章・第8章

`frontend/` のHTMLファイルをブラウザで直接開いて使います。

### Laravel — 第6章〜

コンテナ内で Laravel プロジェクトを作成します:

```bash
docker compose exec php bash
composer create-project laravel/laravel .
```

## 停止・削除

```bash
# 停止
docker compose down

# データベースも含めて完全に削除
docker compose down -v
```
