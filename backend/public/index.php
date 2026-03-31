<?php
// ===========================================
// 動作確認用ファイル
// http://localhost:8080 でこのページが表示されればOK
// ===========================================

$phpVersion = phpversion();
$extensions = get_loaded_extensions();

// DB接続テスト
$dbStatus = '未接続';
$dbError = '';
try {
    $host = getenv('DB_HOST') ?: 'mysql-server';
    $name = getenv('DB_NAME') ?: 'lms';
    $user = getenv('DB_USER') ?: 'lms_user';
    $pass = getenv('DB_PASS') ?: 'lms_pass';

    $pdo = new PDO(
        "mysql:host={$host};dbname={$name};charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $dbStatus = '接続OK';
} catch (PDOException $e) {
    $dbStatus = '接続エラー';
    $dbError = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS 開発環境</title>
    <style>
        body { font-family: sans-serif; max-width: 700px; margin: 40px auto; padding: 0 20px; color: #333; }
        h1 { color: #02a0c7; }
        .status { padding: 12px 16px; border-radius: 8px; margin: 8px 0; }
        .ok { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        table { border-collapse: collapse; width: 100%; margin-top: 16px; }
        th, td { text-align: left; padding: 8px 12px; border-bottom: 1px solid #ddd; }
        th { background: #f5f5f5; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 4px; font-size: 0.9em; }
    </style>
</head>
<body>
    <h1>LMS 開発環境</h1>
    <p>この画面が表示されていれば、PHP + Apache が正常に動作しています。</p>

    <table>
        <tr><th>項目</th><th>状態</th></tr>
        <tr>
            <td>PHP</td>
            <td class="status ok">v<?= $phpVersion ?></td>
        </tr>
        <tr>
            <td>MySQL接続</td>
            <td class="status <?= $dbStatus === '接続OK' ? 'ok' : 'error' ?>">
                <?= $dbStatus ?>
                <?php if ($dbError): ?><br><small><?= htmlspecialchars($dbError) ?></small><?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>PDO拡張</td>
            <td class="status <?= in_array('pdo_mysql', $extensions) ? 'ok' : 'error' ?>">
                <?= in_array('pdo_mysql', $extensions) ? '有効' : '無効' ?>
            </td>
        </tr>
        <tr>
            <td>Composer</td>
            <td class="status ok"><?= trim(shell_exec('composer --version 2>&1') ?: '不明') ?></td>
        </tr>
    </table>

    <h2 style="margin-top: 32px;">使い方</h2>
    <ul>
        <li><code>backend/public/</code> に PHP ファイルを作成 → <code>http://localhost:8080/ファイル名.php</code> でアクセス</li>
        <li><code>http://localhost:8888</code> で phpMyAdmin（DB管理画面）を開ける</li>
        <li>Laravel を使う場合: コンテナ内で <code>composer create-project laravel/laravel .</code></li>
    </ul>
</body>
</html>
