<?php
// Hostinger Veritabanı Bilgileri
$host = 'localhost';
$dbname = '';
$user = '';
$pass = '';
$charset = 'utf8mb4';

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $db = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// --- TELEGRAM BİLDİRİM FONKSİYONU ---
function sendTelegram($message)
{
    $token = ""; // BotFather'dan aldığın token
    $chatID = "";    // Senin Chat ID'n

    $data = [
        'chat_id' => $chatID,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];

    $url = "https://api.telegram.org/bot$token/sendMessage?" . http_build_query($data);
    @file_get_contents($url);
}
?>