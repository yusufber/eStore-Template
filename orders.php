<?php
include 'db.php';
include 'includes/header.php';

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Siparişleri çek
$query = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$query->execute([$user_id]);
$orders = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Siparişlerim | Grizmtech Shop</title>
    <style>
        body { background-color: #f8f9fa; }
        .order-card { border: none; border-radius: 20px; transition: all 0.3s ease; }
        .order-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; }
        .status-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 8px; }
        
        /* Durum Renkleri */
        .status-beklemede { background-color: #ffc107; color: #856404; }
        .status-hazirlaniyor { background-color: #0dcaf0; color: #055160; }
        .status-kargolandi { background-color: #0d6efd; color: #fff; }
        .status-tamamlandi { background-color: #198754; color: #fff; }
        
        .product-badge { background: #f1f3f5; border-radius: 10px; padding: 10px 15px; margin-bottom: 8px; }
    </style>
</head>
<body>

<div class="container my-5" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-800 mb-1">Siparişlerim</h2>
            <p class="text-muted small">Tüm alışveriş geçmişinizi buradan takip edebilirsiniz.</p>
        </div>
        <i class="fas fa-box-open fa-3x text-light"></i>
    </div>

    <?php if (count($orders) > 0): ?>
        <?php foreach ($orders as $order): ?>
            <div class="card order-card shadow-sm mb-4 p-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <span class="text-muted small d-block">Sipariş Numarası</span>
                            <span class="fw-bold fs-5">#DLM-<?php echo $order['id']; ?></span>
                        </div>
                        <div class="text-end">
                            <?php 
                                $s = $order['status'];
                                $class = "";
                                if($s == 'Beklemede') $class = "bg-warning-subtle text-warning-emphasis";
                                elseif($s == 'Hazırlanıyor') $class = "bg-info-subtle text-info-emphasis";
                                elseif($s == 'Kargolandı') $class = "bg-primary-subtle text-primary";
                                elseif($s == 'Tamamlandı') $class = "bg-success-subtle text-success";
                            ?>
                            <span class="badge <?php echo $class; ?> rounded-pill px-3 py-2">
                                <i class="fas fa-circle me-1 small"></i> <?php echo $s; ?>
                            </span>
                            <span class="text-muted small d-block mt-1"><?php echo date('d M Y', strtotime($order['created_at'])); ?></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="fw-bold mb-3 text-muted small uppercase">Sipariş İçeriği</h6>
                            <?php
                            $items = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
                            $items->execute([$order['id']]);
                            while($item = $items->fetch()): ?>
                                <div class="product-badge d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold"><?php echo $item['product_name']; ?> <small class="text-muted ms-2">x<?php echo $item['quantity']; ?></small></span>
                                    <span class="text-dark small fw-bold"><?php echo number_format($item['price'], 2, ',', '.'); ?> TL</span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        
                        <div class="col-md-4 border-start ps-4">
                            <div class="mb-4">
                                <h6 class="text-muted small fw-bold mb-1">Teslimat Adresi</h6>
                                <p class="small text-secondary mb-0"><?php echo htmlspecialchars($order['address']); ?></p>
                            </div>
                            <div class="pt-3 border-top">
                                <h6 class="text-muted small fw-bold mb-1">Toplam Ödeme</h6>
                                <span class="h4 fw-800 text-dark"><?php echo number_format($order['total_price'], 2, ',', '.'); ?> <small class="h6">TL</small></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center py-5 bg-white rounded-5 shadow-sm">
            <img src="https://cdn-icons-png.flaticon.com/512/4555/4555971.png" width="120" class="mb-4 opacity-50">
            <h4 class="fw-bold">Henüz siparişiniz yok</h4>
            <p class="text-muted">Grizmtech dünyasındaki eşsiz ürünleri keşfetmeye ne dersiniz?</p>
            <a href="index.php" class="btn btn-dark btn-lg rounded-pill px-5 mt-3 shadow">Alışverişe Başla</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>