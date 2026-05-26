# LMS 開発環境

エンジニア育成カリキュラム用の開発環境です。

## Docker のしくみ

このカリキュラムでは **Docker** を使います。Docker は、**あなたの PC の中に「コンテナ」という仮想的な作業環境を作るしくみ**です。

- PHP や MySQL を PC へ直接インストールする必要はありません。必要なソフトはすべてコンテナの中に入っています。
- そのため OS（Windows / Mac）が違っても、**全員がまったく同じ環境**で学習できます。
- ファイルは PC 側のエディタで編集し、コマンドを動かすときは「コンテナに入って」実行します（手順は後述の「使い方」）。

```mermaid
flowchart TB
    user(["👤 あなた（受講者）"])

    subgraph PC["💻 あなたの PC（Windows / Mac）"]
        files["📁 プロジェクトのファイル<br/>backend/ ・ frontend/"]
        subgraph DOCKER["🐳 Docker: PC の中に仮想環境を作るしくみ"]
            subgraph PHPC["📦 php コンテナ（PHP 実行環境）"]
                PHP["PHP 8.2 + Apache<br/>Composer"]
            end
            subgraph DBC["📦 mysql-server コンテナ（DB 環境）"]
                DB[("MySQL 8.0")]
            end
        end
    end

    user -->|"ブラウザで http://localhost:8080"| PHPC
    user -->|"docker compose exec ... bash で中に入って作業"| PHPC
    files -.->|"ファイルを共有（マウント）"| PHPC
    PHPC <-->|"DB 接続（host: mysql-server）"| DBC
```

> 📦 のコンテナは「使い捨ての環境」です。壊れても `docker compose down` → `up` でいつでもクリーンな状態に戻せます。

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

### コンテナに入って PHP を実行する — 第3章〜

`php` や `composer` などのコマンドは、受講者の PC に直接インストールせず、**コンテナの中に入って実行します**。こうすることで OS（Windows / macOS）に関わらず、全員が同じ PHP バージョン・同じ環境で学習できます（受講者ごとの環境差をなくすため）。

コンテナに入る:

```bash
docker compose exec php bash
```

プロンプトが `root@xxxxxxxx:/var/www/html#` に変わればコンテナの中です。`backend/` が `/var/www/html` に対応しているので、`backend/` に置いたファイルをそのまま実行できます。

例として `backend/helloworld.php` を用意して実行します:

```php
<?php
// backend/helloworld.php
echo 'Hello, World!';
```

```bash
php helloworld.php   # => Hello, World!
php -v               # PHP のバージョン確認
```

作業が終わったらコンテナから出る:

```bash
exit
```

### データベース（MySQL）— 第4章〜

MySQL コンソールへは、MySQL コンテナに入ってから接続します:

```bash
docker compose exec mysql-server bash   # MySQL コンテナに入る
mysql -u lms_user -plms_pass lms        # MySQL コンソールに接続（パスワードは -p の直後に続けて書く）
```

`mysql>` プロンプトが出れば成功です。`exit` で MySQL コンソールを抜け、もう一度 `exit` でコンテナから出ます。
（`Using a password on the command line interface can be insecure.` という警告は出ますが、学習環境では無視して構いません。）

PHP からの接続情報:

| 項目 | 値 |
|------|-----|
| ホスト | mysql-server |
| データベース名 | lms |
| ユーザー名 | lms_user |
| パスワード | lms_pass |

```php
<?php
$pdo = new PDO('mysql:host=mysql-server;dbname=lms;charset=utf8mb4', 'lms_user', 'lms_pass');
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
