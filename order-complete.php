<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $address = $_POST['address'];
    $total_price = 0;

    if (empty($_SESSION['cart'])) { header("Location: index.php"); exit; }

    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }

    // 1. Siparişi Kaydet
    $stmt = $db->prepare("INSERT INTO orders (user_id, total_price, address, status) VALUES (?, ?, ?, 'Beklemede')");
    $stmt->execute([$user_id, $total_price, $address]);
    $order_id = $db->lastInsertId();

    $siparis_detay = ""; // Telegram bildirimi için ürün listesi

    // 2. Sipariş Ürünlerini Kaydet ve Tek Alımlık Kontrolü Yap
    foreach ($_SESSION['cart'] as $item) {
        // DB'ye ürün kalemini ekle
        $stmt = $db->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $item['name'], $item['quantity'], $item['price']]);

        // --- KRİTİK NOKTA: TEK ALIMLIK ÜRÜNÜ KAPAT ---
        // Eğer ürün is_single = 1 ise, onu is_sold = 1 yaparak satışa kapatıyoruz.
        $stmt_check = $db->prepare("UPDATE products SET is_sold = 1 WHERE id = ? AND is_single = 1");
        $stmt_check->execute([$item['id']]);

        $siparis_detay .= "- " . $item['name'] . " (" . $item['quantity'] . " Adet)\n";
    }

    // --- TELEGRAM BİLDİRİMİ GÖNDER ---
    $bildirim = "🛍️ <b>Yeni Sipariş Geldi! (#$order_id)</b>\n";
    $bildirim .= "--------------------------\n";
    $bildirim .= "👤 <b>Müşteri:</b> " . $_SESSION['username'] . "\n";
    $bildirim .= "📦 <b>Ürünler:</b>\n" . $siparis_detay;
    $bildirim .= "💰 <b>Toplam Tutar:</b> " . number_format($total_price, 2, ',', '.') . " TL\n";
    $bildirim .= "📍 <b>Adres:</b> $address\n";
    $bildirim .= "--------------------------\n";
    $bildirim .= "✅ <i>Grizm Store Yönetim Paneli üzerinden durumu güncelleyebilirsiniz.</i>";
    
    sendTelegram($bildirim);

    // Sepeti temizle ve yönlendir
    unset($_SESSION['cart']);
    header("Location: orders.php?success=1");
}
?>