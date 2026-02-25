<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php"); exit;
}

$generated_link = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    // Tek alımlık mı kontrolü
    $is_single = isset($_POST['is_single']) ? 1 : 0;
    // Benzersiz bir token oluşturuyoruz
    $token = bin2hex(random_bytes(8)); 

    $image = $_FILES['image']['name'];
    $file_extension = pathinfo($image, PATHINFO_EXTENSION);
    $new_image_name = time() . "_" . uniqid() . "." . $file_extension;
    $target = "../uploads/" . $new_image_name;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $stmt = $db->prepare("INSERT INTO products (name, price, description, image, token, is_single) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $price, $description, $new_image_name, $token, $is_single]);
        
        // Başarı mesajı ve link oluşturma
        $generated_link = "https://" . $_SERVER['HTTP_HOST'] . "/buy.php?token=" . $token;
        $success_msg = "Ürün başarıyla yayınlandı!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Yönetimi | DLMTD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/all.min.css">
    <style>
        :root { --accent: #000; --bg: #f8f9fa; }
        body { background-color: var(--bg); font-family: 'Inter', sans-serif; color: #333; }
        
        .admin-card {
            background: #fff;
            border: none;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.03);
            overflow: hidden;
        }

        .form-label { font-weight: 600; font-size: 0.9rem; color: #666; margin-bottom: 8px; }
        
        .form-control {
            border-radius: 14px;
            padding: 14px;
            border: 1px solid #eef0f2;
            background: #fcfdfe;
            transition: 0.3s;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(0,0,0,0.05);
            border-color: #000;
        }

        .btn-publish {
            background: #000;
            color: #fff;
            border: none;
            padding: 16px;
            border-radius: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: 0.3s;
        }

        .btn-publish:hover { background: #222; transform: translateY(-2px); color: #fff; }

        /* Switch Tasarımı */
        .form-check-input:checked { background-color: #000; border-color: #000; }
        
        .link-box {
            background: #f0fdf4;
            border: 1px dashed #16a34a;
            border-radius: 14px;
            padding: 15px;
        }
    </style>
</head>
<body class="py-5">

    <div class="container" style="max-width: 800px;">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-800 m-0">Ürün Yayınla</h2>
                <p class="text-muted small">Mağazanıza yeni ve özel ürünler ekleyin.</p>
            </div>
            <a href="products.php" class="btn btn-white border rounded-pill px-4 fw-bold shadow-sm">
                <i class="fas fa-chevron-left me-2"></i> Geri
            </a>
        </div>

        <?php if($generated_link): ?>
            <div class="admin-card p-4 mb-4 border-start border-success border-5">
                <h6 class="fw-bold text-success"><i class="fas fa-check-circle me-2"></i> <?php echo $success_msg; ?></h6>
                <p class="small text-muted mb-2">Bu ürüne özel tek alımlık link:</p>
                <div class="link-box d-flex justify-content-between align-items-center">
                    <code id="buyLink" class="text-success fw-bold"><?php echo $generated_link; ?></code>
                    <button class="btn btn-sm btn-dark rounded-pill px-3" onclick="copyLink()">Kopyala</button>
                </div>
            </div>
        <?php endif; ?>

        <div class="admin-card p-5">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8 mb-4">
                        <label class="form-label">Ürün Başlığı</label>
                        <input type="text" name="name" class="form-control" placeholder="Örn: Limitli Üretim Sweatshirt" required>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Fiyat (TL)</label>
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Ürün Detayları</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Ürün özelliklerini ve müşteriye mesajınızı yazın..."></textarea>
                </div>

                <div class="mb-4 p-3 border rounded-4 d-flex align-items-center justify-content-between bg-light">
                    <div>
                        <h6 class="m-0 fw-bold">Tek Alımlık Ürün</h6>
                        <small class="text-muted">Satın alındıktan sonra link geçersiz olur.</small>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_single" style="width: 40px; height: 20px;">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label">Ürün Fotoğrafı</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                </div>

                <button type="submit" class="btn btn-publish w-100 shadow">
                    <i class="fas fa-cloud-upload-alt me-2"></i> ÜRÜNÜ YAYINLA
                </button>
            </form>
        </div>
    </div>

    <script>
    function copyLink() {
        var link = document.getElementById("buyLink").innerText;
        navigator.clipboard.writeText(link);
        alert("Link panoya kopyalandı!");
    }
    </script>
</body>
</html>