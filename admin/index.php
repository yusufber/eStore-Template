<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Telegram Test Butonu İşlemi
if (isset($_POST['test_tg'])) {
    sendTelegram("🚀 <b>Grizmtech Store Bildirim Sistemi Aktif!</b>\nŞu an admin panelinden bir test mesajı aldınız.");
    $tg_msg = "Test mesajı başarıyla gönderildi!";
}

// İstatistikler
$revenue = $db->query("SELECT SUM(total_price) FROM orders WHERE status = 'Tamamlandı'")->fetchColumn() ?: 0;
$orders_count = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$products_count = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();

// Son Siparişler
$latest = $db->query("SELECT orders.*, users.username FROM orders JOIN users ON orders.user_id = users.id ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Grizmtech</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .sidebar {
            width: 260px;
            height: 100vh;
            background: #000;
            color: #fff;
            position: fixed;
        }

        .main {
            margin-left: 260px;
            padding: 40px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.6);
            padding: 15px 25px;
            transition: 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }

        .stat-card {
            border: none;
            border-radius: 20px;
            padding: 25px;
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="p-4 mb-4">
            <h3 class="fw-bold">GRIZMTECH <small class="text-primary fs-6">PRO</small></h3>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link active" href="index.php"><i class="fas fa-home me-2"></i> Dashboard</a>
            <a class="nav-link" href="orders.php"><i class="fas fa-shopping-cart me-2"></i> Siparişler</a>
            <a class="nav-link" href="products.php"><i class="fas fa-box me-2"></i> Ürünler</a>
            <a class="nav-link" href="users.php"><i class="fas fa-users me-2"></i> Kullanıcılar</a>
            <a class="nav-link text-danger mt-5" href="../logout.php"><i class="fas fa-power-off me-2"></i> Çıkış</a>
        </nav>
    </div>

    <div class="main">
        <header class="d-flex justify-content-between mb-5">
            <h2 class="fw-bold">Yönetim Paneli</h2>
            <form method="POST">
                <button type="submit" name="test_tg" class="btn btn-dark rounded-pill px-4">
                    <i class="fab fa-telegram-plane me-2"></i> Telegram'ı Test Et
                </button>
            </form>
        </header>

        <?php if (isset($tg_msg)): ?>
            <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4"><?php echo $tg_msg; ?></div>
        <?php endif; ?>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card bg-white shadow-sm text-center">
                    <p class="text-muted small text-uppercase fw-bold">Toplam Kazanç</p>
                    <h2 class="fw-bold"><?php echo number_format($revenue, 2, ',', '.'); ?> ₺</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-white shadow-sm text-center">
                    <p class="text-muted small text-uppercase fw-bold">Sipariş Sayısı</p>
                    <h2 class="fw-bold"><?php echo $orders_count; ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-white shadow-sm text-center">
                    <p class="text-muted small text-uppercase fw-bold">Aktif Ürünler</p>
                    <h2 class="fw-bold"><?php echo $products_count; ?></h2>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-4">Son Siparişler</h5>
            <table class="table align-middle">
                <thead>
                    <tr class="text-muted small">
                        <th>MÜŞTERİ</th>
                        <th>TUTAR</th>
                        <th>DURUM</th>
                        <th>TARİH</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latest as $o): ?>
                        <tr>
                            <td><strong><?php echo $o['username']; ?></strong></td>
                            <td><?php echo number_format($o['total_price'], 2); ?> ₺</td>
                            <td><span class="badge bg-light text-dark rounded-pill"><?php echo $o['status']; ?></span></td>
                            <td class="small text-muted"><?php echo date('d.m.Y H:i', strtotime($o['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>