<?php
session_start();
include '../db.php';

// Güvenlik
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Sipariş Durumu Güncelleme (Hızlı AJAX alternatifi yerine post ile)
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $order_id]);
    header("Location: orders.php?msg=updated");
    exit;
}

// Sipariş Silme
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $db->prepare("DELETE FROM orders WHERE id = ?")->execute([$id]);
    $db->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$id]);
    header("Location: orders.php?msg=deleted");
    exit;
}

// Siparişleri ve Kullanıcı Bilgilerini Çek
$orders = $db->query("SELECT orders.*, users.username FROM orders 
                     JOIN users ON orders.user_id = users.id 
                     ORDER BY orders.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Sipariş Yönetimi | Grizmtech Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 250px;
        }

        body {
            background-color: #f4f7f6;
            font-family: 'Inter', sans-serif;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: #111;
            color: white;
            position: fixed;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 15px;
            text-decoration: none;
            display: block;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }

        .status-badge {
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 600;
        }

        .table-items {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 10px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="p-4 text-center">
            <h4 class="fw-bold text-primary">GRIZMTECH <span class="text-white">PRO</span></h4>
        </div>
        <nav class="nav flex-column mt-3">
            <a class="nav-link" href="index.php"><i class="fas fa-chart-line me-2"></i> Dashboard</a>
            <a class="nav-link active" href="orders.php"><i class="fas fa-shopping-cart me-2"></i> Siparişler</a>
            <a class="nav-link" href="products.php"><i class="fas fa-tshirt me-2"></i> Ürün Yönetimi</a>
            <a class="nav-link" href="users.php"><i class="fas fa-users me-2"></i> Müşteriler</a>
            <hr class="mx-3 opacity-25 text-white">
            <a class="nav-link text-danger" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Çıkış Yap</a>
        </nav>
    </div>

    <div class="main-content">
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-0">Sipariş Yönetimi</h2>
                <p class="text-muted">Gelen talepleri izleyin ve gönderim süreçlerini yönetin.</p>
            </div>
            <div class="bg-white p-2 rounded-pill shadow-sm px-4">
                <span class="text-muted small">Toplam Sipariş: <strong><?php echo count($orders); ?></strong></span>
            </div>
        </header>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-3">
                <i class="fas fa-check-circle me-2"></i> Başarıyla güncellendi.
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Sipariş</th>
                            <th>Müşteri & Adres</th>
                            <th>Ürün Detayları</th>
                            <th>Tutar</th>
                            <th>Durum Güncelle</th>
                            <th class="text-center">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-dark">#<?php echo $o['id']; ?></span><br>
                                    <small
                                        class="text-muted"><?php echo date('d/m/Y H:i', strtotime($o['created_at'])); ?></small>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary"><?php echo htmlspecialchars($o['username']); ?></div>
                                    <div class="text-muted small" style="max-width: 200px;">
                                        <?php echo htmlspecialchars($o['address']); ?></div>
                                </td>
                                <td>
                                    <div class="table-items">
                                        <?php
                                        $items = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
                                        $items->execute([$o['id']]);
                                        while ($item = $items->fetch()): ?>
                                            <div class="d-flex justify-content-between border-bottom mb-1 pb-1">
                                                <span><?php echo $item['product_name']; ?>
                                                    <strong>x<?php echo $item['quantity']; ?></strong></span>
                                                <span class="text-muted small"><?php echo number_format($item['price'], 2); ?>
                                                    ₺</span>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="h6 fw-bold text-dark"><?php echo number_format($o['total_price'], 2, ',', '.'); ?>
                                        ₺</span>
                                </td>
                                <td>
                                    <form method="POST" class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                        <select name="status"
                                            class="form-select form-select-sm border-0 bg-light shadow-none"
                                            style="min-width: 130px;">
                                            <option value="Beklemede" <?php echo ($o['status'] == 'Beklemede' ? 'selected' : ''); ?>>⌛ Beklemede</option>
                                            <option value="Hazırlanıyor" <?php echo ($o['status'] == 'Hazırlanıyor' ? 'selected' : ''); ?>>📦 Hazırlanıyor</option>
                                            <option value="Kargolandı" <?php echo ($o['status'] == 'Kargolandı' ? 'selected' : ''); ?>>🚚 Kargolandı</option>
                                            <option value="Tamamlandı" <?php echo ($o['status'] == 'Tamamlandı' ? 'selected' : ''); ?>>✅ Tamamlandı</option>
                                        </select>
                                        <button type="submit" name="update_status"
                                            class="btn btn-dark btn-sm rounded-circle shadow-sm">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <a href="?delete=<?php echo $o['id']; ?>" class="btn btn-outline-danger btn-sm border-0"
                                        onclick="return confirm('Siparişi silmek istediğine emin misin?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>