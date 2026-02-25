<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$id = $_GET['id'];
$stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $p['image']; // Varsayılan eski resim

    // Yeni resim seçilmiş mi?
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $image);
    }

    $stmt = $db->prepare("UPDATE products SET name=?, price=?, description=?, image=? WHERE id=?");
    $stmt->execute([$name, $price, $description, $image, $id]);
    header("Location: products.php?msg=updated");
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Ürünü Düzenle | Grizmtech</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="py-5 bg-light">
    <div class="container" style="max-width: 700px;">
        <h2 class="fw-bold mb-4 text-center">Ürünü Düzenle</h2>
        <div class="card p-4 border-0 shadow-sm rounded-4">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label fw-bold">Ürün Adı</label>
                    <input type="text" name="name" class="form-control"
                        value="<?php echo htmlspecialchars($p['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Fiyat (TL)</label>
                    <input type="number" step="0.01" name="price" class="form-control"
                        value="<?php echo $p['price']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Açıklama</label>
                    <textarea name="description" class="form-control" rows="4"
                        required><?php echo htmlspecialchars($p['description']); ?></textarea>
                </div>
                <div class="mb-3 text-center">
                    <label class="d-block text-muted small mb-2">Mevcut Görsel</label>
                    <img src="../uploads/<?php echo $p['image']; ?>" width="100" class="rounded shadow-sm mb-2">
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-success w-100 py-3 rounded-pill fw-bold">Değişiklikleri
                    Kaydet</button>
            </form>
        </div>
    </div>
</body>

</html>