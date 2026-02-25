<?php 
include 'db.php'; 
include 'includes/header.php'; 

// URL'de 'details' parametresi var mı kontrol et
$detail_id = isset($_GET['details']) ? (int)$_GET['details'] : 0;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grizm Store | Premium Koleksiyon</title>
    <style>
        body { background-color: #fcfcfc; font-family: 'Plus Jakarta Sans', sans-serif; }
        .fw-800 { font-weight: 800; }
        
        /* Banner Stili */
        .hero-banner { 
            background: #000; 
            border-radius: 25px; 
            padding: 60px 20px; 
            color: #fff; 
            text-align: center;
            margin-bottom: 50px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        /* Ürün Kartları */
        .product-card { border: none; border-radius: 20px; transition: 0.3s; background: #fff; height: 100%; overflow: hidden; }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
        .product-img { height: 300px; object-fit: cover; transition: 0.5s; }
        .product-card:hover .product-img { transform: scale(1.05); }

        /* Detay Sayfası */
        .detail-img { width: 100%; border-radius: 25px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .price-tag { font-size: 2rem; font-weight: 800; color: #000; }

        /* Butonlar */
        .btn-buy { background: #000; color: #fff; border-radius: 10px; font-weight: 600; transition: 0.3s; }
        .btn-buy:hover { background: #333; color: #fff; }
        .btn-outline-custom { border: 2px solid #eee; color: #666; border-radius: 10px; font-weight: 600; }
        .btn-outline-custom:hover { background: #f8f9fa; border-color: #ddd; }
    </style>
</head>
<body>

    <div class="container my-5">

        <?php if ($detail_id > 0): 
            // --- ÜRÜN DETAY GÖRÜNÜMÜ ---
            // Detay sayfasında is_single=1 olsa bile (linkle gelindiyse) gösterilmeli, 
            // ama is_sold=1 ise gösterilmemeli.
            $stmt = $db->prepare("SELECT * FROM products WHERE id = ? AND is_sold = 0");
            $stmt->execute([$detail_id]);
            $p = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($p): ?>
                <div class="row g-5 align-items-center">
                    <div class="col-md-6">
                        <img src="uploads/<?php echo $p['image']; ?>" class="detail-img" onerror="this.src='https://via.placeholder.com/800x1000'">
                    </div>
                    <div class="col-md-6">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-4 text-uppercase small" style="letter-spacing: 1px;">
                                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Mağaza</a></li>
                                <li class="breadcrumb-item active fw-bold text-dark"><?php echo htmlspecialchars($p['name']); ?></li>
                            </ol>
                        </nav>
                        <h1 class="display-4 fw-800 mb-2"><?php echo htmlspecialchars($p['name']); ?></h1>
                        <div class="price-tag mb-4 text-dark"><?php echo number_format($p['price'], 2, ',', '.'); ?> TL</div>
                        <p class="lead text-muted mb-5" style="line-height: 1.8;"><?php echo nl2br(htmlspecialchars($p['description'])); ?></p>

                        <div class="d-grid d-md-flex gap-3">
                            <a href="cart-add.php?id=<?php echo $p['id']; ?>" class="btn btn-buy btn-lg px-5 py-3">
                                <i class="fas fa-shopping-bag me-2"></i>Sepete Ekle
                            </a>
                            <a href="index.php" class="btn btn-outline-custom btn-lg px-4 py-3">
                                <i class="fas fa-chevron-left me-2"></i>Geri Dön
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning rounded-4 p-4 text-center">Bu ürün artık mevcut değil veya satılmış olabilir.</div>
            <?php endif; ?>

        <?php else: 
            // --- ANA SAYFA / ÜRÜN LİSTESİ GÖRÜNÜMÜ --- ?>
            
            <div class="hero-banner shadow-lg">
                <h1 class="display-2 fw-800 mb-0" style="letter-spacing: -2px;">GRIZM <span class="text-white opacity-75">STORE</span></h1>
                <p class="opacity-50 text-uppercase mt-2" style="letter-spacing: 5px; font-size: 0.9rem;">Premium Tasarımlar & Özel Üretim</p>
            </div>

            <div class="row g-4">
                <?php 
                // KRİTİK FİLTRE: is_single = 0 (Genel ürünler) ve is_sold = 0 (Satılmamışlar)
                $query = $db->query("SELECT * FROM products WHERE is_single = 0 AND is_sold = 0 ORDER BY id DESC");
                
                if($query->rowCount() > 0):
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card product-card">
                                <a href="index.php?details=<?php echo $row['id']; ?>">
                                    <img src="uploads/<?php echo $row['image']; ?>" class="card-img-top product-img" onerror="this.src='https://via.placeholder.com/500x600'">
                                </a>
                                <div class="card-body p-3">
                                    <h6 class="fw-bold mb-1 text-dark text-truncate"><?php echo htmlspecialchars($row['name']); ?></h6>
                                    <p class="text-muted small mb-3"><?php echo number_format($row['price'], 2, ',', '.'); ?> TL</p>
                                    
                                    <div class="d-flex gap-2">
                                        <a href="cart-add.php?id=<?php echo $row['id']; ?>" class="btn btn-buy btn-sm w-100 py-2">
                                            Satın Al
                                        </a>
                                        <a href="index.php?details=<?php echo $row['id']; ?>" class="btn btn-outline-custom btn-sm px-3 py-2">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; 
                else: ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Şu an sergilenecek ürün bulunmuyor.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>