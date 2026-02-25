<?php
include 'db.php';
include 'includes/header.php';

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Sepet boşsa ana sayfaya gönder
if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$grand_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $grand_total += $item['price'] * $item['quantity'];
}
?>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasa | Grizmtech Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap"
        rel="stylesheet">

</head>

<div class="container my-5">
    <div class="row g-5">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h4 class="fw-bold mb-4">Teslimat Bilgileri</h4>
                <form action="order-complete.php" method="POST" id="checkout-form">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Adresiniz</label>
                        <textarea name="address" class="form-control rounded-3" rows="4"
                            placeholder="Siparişin gönderileceği tam adresi yazınız..." required></textarea>
                    </div>

                    <div class="card bg-light border-0 rounded-4 p-3 mb-4">
                        <label class="form-label fw-bold"><i class="fas fa-tag me-2 text-primary"></i>İndirim
                            Kodu</label>
                        <input type="text" name="coupon_code" id="coupon_code" class="form-control border-0 shadow-sm"
                            placeholder="Varsa kodunuzu girin (Örn: deneme)">
                        <small class="text-muted mt-2 d-block">Not: "deneme" kodu ile doğrudan sipariş
                            oluşturabilirsiniz.</small>
                    </div>

                    <button type="submit" class="btn btn-dark btn-lg w-100 rounded-pill fw-bold py-3 shadow">
                        Siparişi Tamamla <i class="fas fa-check-circle ms-2"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-4 text-secondary">Sipariş Özeti</h5>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><?php echo $item['quantity']; ?>x <?php echo $item['name']; ?></span>
                        <span
                            class="fw-semibold"><?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?>
                            TL</span>
                    </div>
                <?php endforeach; ?>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span class="h5 fw-bold">Toplam</span>
                    <span class="h5 fw-bold text-primary"><?php echo number_format($grand_total, 2, ',', '.'); ?>
                        TL</span>
                </div>
            </div>
        </div>
    </div>
</div>