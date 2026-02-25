<?php
include 'db.php';
include 'includes/header.php';

// Ürün silme işlemi
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit;
}
?>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sepetim | Grizmtech Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap"
        rel="stylesheet">

</head>

<div class="container my-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-shopping-cart me-2 text-primary"></i>Sepetim</h5>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light text-muted small uppercase">
                            <tr>
                                <th class="ps-4">ÜRÜN</th>
                                <th>FİYAT</th>
                                <th class="text-center">ADET</th>
                                <th>TOPLAM</th>
                                <th class="pe-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grand_total = 0;
                            if (!empty($_SESSION['cart'])):
                                foreach ($_SESSION['cart'] as $id => $item):
                                    $total = $item['price'] * $item['quantity'];
                                    $grand_total += $total;
                                    ?>
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <img src="uploads/<?php echo $item['image']; ?>"
                                                    class="rounded-3 shadow-sm me-3" width="60" height="60"
                                                    style="object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-muted"><?php echo number_format($item['price'], 2, ',', '.'); ?> TL</td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-light text-dark border px-3 py-2 fs-6"><?php echo $item['quantity']; ?></span>
                                        </td>
                                        <td class="fw-bold"><?php echo number_format($total, 2, ',', '.'); ?> TL</td>
                                        <td class="pe-4 text-end">
                                            <a href="?remove=<?php echo $id; ?>" class="btn btn-outline-danger btn-sm border-0">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="fas fa-shopping-basket fa-3x text-light mb-3"></i>
                                            <p class="text-muted">Sepetiniz şu an boş.</p>
                                            <a href="index.php" class="btn btn-primary btn-sm rounded-pill px-4">Alışverişe
                                                Başla</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
                <h5 class="fw-bold mb-4">Sipariş Özeti</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Ara Toplam</span>
                    <span class="fw-bold"><?php echo number_format($grand_total, 2, ',', '.'); ?> TL</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Kargo</span>
                    <span class="text-success fw-bold">Ücretsiz</span>
                </div>
                <hr class="opacity-50">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="h5 mb-0 fw-bold">Toplam</span>
                    <span class="h4 mb-0 fw-bold text-primary"><?php echo number_format($grand_total, 2, ',', '.'); ?>
                        TL</span>
                </div>

                <?php if ($grand_total > 0): ?>
                    <a href="checkout.php" class="btn btn-dark btn-lg w-100 rounded-pill fw-bold py-3 shadow">
                        Ödemeye Geç <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                <?php else: ?>
                    <button class="btn btn-dark btn-lg w-100 rounded-pill fw-bold py-3 shadow" disabled>
                        Sepet Boş
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>