<?php
include 'db.php';
session_start();

// Token kontrolü
if (!isset($_GET['token'])) {
    header("Location: index.php");
    exit;
}

$token = $_GET['token'];

// Ürünü token üzerinden bul
$stmt = $db->prepare("SELECT * FROM products WHERE token = ?");
$stmt->execute([$token]);
$product = $stmt->fetch();

// Ürün yoksa veya tek alımlıksa ve satılmışsa (is_sold kontrolü eklenebilir)
if (!$product) {
    die("<div style='text-align:center; margin-top:100px; font-family:sans-serif;'>
            <h1>⚠️ Ürün Bulunamadı</h1>
            <p>Bu bağlantı geçersiz veya ürün tükenmiş olabilir.</p>
            <a href='index.php'>Mağazaya Dön</a>
         </div>");
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $product['name']; ?> | Grizm Store</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/all.min.css">
    <style>
        body { background: #000; color: #fff; font-family: 'Inter', sans-serif; }
        .product-card {
            background: #111;
            border: 1px solid #222;
            border-radius: 30px;
            overflow: hidden;
            max-width: 900px;
            margin: 80px auto;
        }
        .product-img { width: 100%; height: 500px; object-fit: cover; }
        .price-tag { font-size: 2rem; font-weight: 800; color: #fff; }
        .btn-buy {
            background: #fff;
            color: #000;
            border-radius: 15px;
            padding: 15px;
            font-weight: 700;
            transition: 0.3s;
        }
        .btn-buy:hover { background: #ccc; transform: scale(1.02); }
        .badge-single { background: #ff4757; color: white; padding: 5px 15px; border-radius: 50px; font-size: 12px; }
    </style>
</head>
<body>

<div class="container">
    <div class="product-card shadow-lg">
        <div class="row g-0">
            <div class="col-md-6">
                <img src="uploads/<?php echo $product['image']; ?>" class="product-img">
            </div>
            <div class="col-md-6 p-5 d-flex flex-column justify-content-center">
                <?php if($product['is_single']): ?>
                    <div class="mb-3"><span class="badge-single"><i class="fas fa-lock me-2"></i>KİŞİYE ÖZEL / TEK ALIMLIK</span></div>
                <?php endif; ?>
                
                <h1 class="fw-bold mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="text-muted mb-4"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                
                <div class="price-tag mb-5"><?php echo number_format($product['price'], 2); ?> ₺</div>
                
                <form action="cart-add.php?id=<?php echo $product['id']; ?>" method="POST">
                    <button type="submit" class="btn btn-buy w-100 border-0">
                        <i class="fas fa-shopping-cart me-2"></i> SEPETE EKLE VE ÖDE
                    </button>
                </form>
                
                <div class="mt-4 text-center">
                    <small class="text-muted"><i class="fas fa-shield-alt me-1"></i> Grizm Store Güvencesiyle Güvenli Ödeme</small>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>