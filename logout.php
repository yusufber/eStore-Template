<?php
session_start();

// Tüm session (oturum) verilerini temizle
$_SESSION = array();

// Eğer oturum çerezi (cookie) varsa onu da temizle
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Oturumu tamamen yok et
session_destroy();

// Kullanıcıyı ana sayfaya veya giriş sayfasına yönlendir
header("Location: index.php");
exit;