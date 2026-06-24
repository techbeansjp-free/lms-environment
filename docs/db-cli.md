# データベース コマンドライン操作ガイド

MySQL コンテナへの接続から、テーブルの作成・データの操作まで、よく使うコマンドをまとめています。

---

## 1. MySQL コンソールへの接続

### 手順

```bash
# ① MySQL コンテナの中に入る
docker compose exec mysql-server bash

# ② MySQL コンソールに接続する
mysql -u lms_user -plms_pass lms
```

`mysql>` プロンプトが表示されれば接続成功です。

> **注意** `-p` の直後にスペースを入れずパスワードを続けて書きます（`-plms_pass`）。  
> `Using a password on the command line interface can be insecure.` という警告は学習環境では無視して構いません。

### 接続情報まとめ

| 項目           | 値            |
|----------------|---------------|
| ユーザー名     | `lms_user`    |
| パスワード     | `lms_pass`    |
| データベース名 | `lms`         |
| ホスト         | `mysql-server`（コンテナ間） / `localhost`（外部ツール） |
| ポート         | `3306`        |

### root で接続したいとき

```bash
mysql -u root -proot
```

---

## 2. コンソールを抜ける

```sql
-- MySQL コンソールを終了
exit
```

```bash
# コンテナから出る
exit
```

---

## 3. データベース・テーブルの確認

```sql
-- データベース一覧を表示
SHOW DATABASES;

-- 使用するデータベースを選択（接続時に指定済みなら不要）
USE lms;

-- テーブル一覧を表示
SHOW TABLES;

-- テーブルのカラム定義を確認
DESCRIBE users;
-- または
SHOW COLUMNS FROM users;
```

---

## 4. テーブルの作成・削除

### テーブルを作る

```sql
CREATE TABLE users (
    id         INT          NOT NULL AUTO_INCREMENT,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(255) NOT NULL UNIQUE,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);
```

### テーブルを削除する

```sql
-- テーブルごと削除（中身も消える）
DROP TABLE users;

-- テーブルの中身だけ消す（構造は残る）
TRUNCATE TABLE users;
```

---

## 5. データの操作（CRUD）

### INSERT — データを追加する

```sql
-- 1件追加
INSERT INTO users (name, email) VALUES ('田中 太郎', 'taro@example.com');

-- 複数件まとめて追加
INSERT INTO users (name, email) VALUES
    ('佐藤 花子', 'hanako@example.com'),
    ('鈴木 一郎', 'ichiro@example.com');
```

### SELECT — データを取得する

```sql
-- 全件取得
SELECT * FROM users;

-- カラムを指定して取得
SELECT id, name FROM users;

-- 条件を絞る
SELECT * FROM users WHERE id = 1;
SELECT * FROM users WHERE name LIKE '田中%';

-- 並び替え・件数制限
SELECT * FROM users ORDER BY created_at DESC LIMIT 10;

-- 件数を数える
SELECT COUNT(*) FROM users;
```

### UPDATE — データを更新する

```sql
-- 特定のレコードを更新
UPDATE users SET name = '田中 次郎' WHERE id = 1;

-- 複数カラムを同時に更新
UPDATE users SET name = '田中 次郎', email = 'jiro@example.com' WHERE id = 1;
```

> **WHERE を忘れると全件更新されるので注意！**

### DELETE — データを削除する

```sql
-- 特定のレコードを削除
DELETE FROM users WHERE id = 1;
```

> **WHERE を忘れると全件削除されるので注意！**

---

## 6. よく使うワンライナー

コンテナに入らずに1つのコマンドで実行する方法です。スクリプトや確認作業に便利です。

```bash
# クエリを直接実行
docker compose exec mysql-server mysql -u lms_user -plms_pass lms -e "SHOW TABLES;"

# テーブルの中身を確認
docker compose exec mysql-server mysql -u lms_user -plms_pass lms -e "SELECT * FROM users;"

# SQLファイルを流し込む
docker compose exec -T mysql-server mysql -u lms_user -plms_pass lms < seed.sql
```

---

## 7. データのリセット

開発中にデータをきれいな状態に戻したいときは、以下の手順で対応できます。

### テーブルの中身だけ消す

```sql
TRUNCATE TABLE users;
```

### コンテナのボリュームごと初期化する

```bash
# コンテナを停止して、ボリュームも削除
docker compose down -v

# 再起動（データベースは空の状態から再作成される）
docker compose up -d
```

> `down -v` を実行すると MySQL のデータが**完全に消えます**。元には戻せないので注意してください。

---

## 8. トラブルシューティング

### `Can't connect to MySQL server` と出る

コンテナが起動していない可能性があります。

```bash
docker compose ps        # コンテナの状態を確認
docker compose up -d     # 起動していなければ起動
```

### `Access denied for user` と出る

ユーザー名・パスワードを確認してください。`-p` の後にスペースが入っていないか見直してください。

```bash
# NG: スペースあり
mysql -u lms_user -p lms_pass lms

# OK: スペースなし
mysql -u lms_user -plms_pass lms
```

### コンテナ内で日本語が入力できない（入力が消える）

`docker compose exec` でコンテナに入ったとき、日本語を入力して Enter を押すと文字が消えてしまう場合は、コンテナのロケール設定が原因です。

`docker-compose.yml` の `mysql-server` に `LANG: C.UTF-8` が設定されているか確認し、コンテナを再起動してください。

```bash
docker compose down
docker compose up -d
```

再起動後は日本語入力が正常に動作します。

---

### MySQL コンソールでコマンドが終わらない

`;`（セミコロン）を忘れると入力待ちのままになります。`;` を入力して Enter を押してください。

```sql
mysql> SELECT * FROM users    -- ← セミコロンがない
    ->                        -- ← 入力待ち状態
    -> ;                      -- ← セミコロンを入力して Enter
```
