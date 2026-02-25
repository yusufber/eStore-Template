<?php
session_start();

// GÜVENLİK KONTROLÜ: Eğer admin değilse bir üst klasördeki login.php'ye at
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../db.php'; // Veritabanı bağlantısı

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Resim Yükleme İşlemi
    $target_dir = "../uploads/"; // Resimlerin yükleneceği klasör
    $file_name = time() . "_" . basename($_FILES["image"]["name"]); // Çakışma olmasın diye başına zaman ekledik
    $target_file = $target_dir . $file_name;
    $uploadOk = 1;

    // Klasör yoksa oluştur (Opsiyonel)
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Resim başarıyla yüklendi, şimdi DB'ye kaydet
        $stmt = $db->prepare("INSERT INTO products (name, price, image, description) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $price, $file_name, $description])) {
            header("Location: index.php?status=success");
            exit;
        } else {
            $message = "<div class='alert alert-danger'>Veritabanı hatası oluştu.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Resim yüklenirken bir hata oluştu.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Yeni Ürün Ekle | Grizmtech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            display: flex;
            background: #f8f9fa;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #212529;
            color: white;
            padding: 20px;
        }

        .content {
            flex: 1;
            padding: 40px;
        }

        .nav-link {
            color: #adb5bd;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white;
            background: #343a40;
            border-radius: 5px;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h4 class="text-center mb-4 text-primary">GRIZMTECH ADMIN</h4>
        <nav class="nav flex-column">
            <a class="nav-link" href="index.php"><i class="fas fa-box me-2"></i> Ürünler</a>
            <a class="nav-link active" href="add-product.php"><i class="fas fa-plus me-2"></i> Ürün Ekle</a>
            <a class="nav-link text-danger mt-5" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Çıkış</a>
        </nav>
    </div>

    <div class="content">
        <div class="card shadow-sm mx-auto" style="max-width: 700px;">
            <div class="card-header bg-white">
                <h4 class="mb-0">Yeni Ürün Ekle</h4>
            </div>
            <div class="card-body">
                <?php echo $message; ?>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Ürün Adı</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fiyat (TL)</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ürün Görseli</label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control" rows="4"></textarea>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">Vazgeç</a>
                        <button type="submit" class="btn btn-success px-5">Ürünü Yayınla</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>