<?php
session_start();
include '../db.php';

// GÜVENLİK: Admin değilse ana sayfaya at
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php"); 
    exit;
}

$generated_link = "";
$success_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    // Tek alımlık mı kontrolü (Checkbox işaretliyse 1, değilse 0)
    $is_single = isset($_POST['is_single']) ? 1 : 0;
    
    // Ürüne özel benzersiz link anahtarı (Token) oluştur
    $token = bin2hex(random_bytes(8)); 

    // Resim Yükleme İşlemi
    $image = $_FILES['image']['name'];
    $file_extension = pathinfo($image, PATHINFO_EXTENSION);
    $new_image_name = time() . "_" . uniqid() . "." . $file_extension;
    $target = "../uploads/" . $new_image_name;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        // Veritabanına Kayıt
        $stmt = $db->prepare("INSERT INTO products (name, price, description, image, token, is_single) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $price, $description, $new_image_name, $token, $is_single])) {
            
            // Başarı durumunda linki oluştur
            $generated_link = "https://" . $_SERVER['HTTP_HOST'] . "/buy.php?token=" . $token;
            $success_msg = "Ürün başarıyla yayına alındı!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Ürün | DLMTD Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --dlmtd-black: #000000;
            --dlmtd-gray: #f8f9fa;
            --dlmtd-border: #ececec;
        }

        body {
            background-color: var(--dlmtd-gray);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1a1a1a;
        }

        .container-box {
            max-width: 850px;
            margin: 50px auto;
        }

        /* Kart Tasarımı */
        .admin-card {
            background: #fff;
            border: none;
            border-radius: 30px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.04);
            padding: 40px;
        }

        /* Form Elemanları */
        .form-label {
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #888;
            margin-bottom: 10px;
        }

        .form-control {
            border-radius: 15px;
            padding: 15px 20px;
            border: 2px solid var(--dlmtd-border);
            background-color: #fcfcfc;
            font-weight: 500;
            transition: 0.3s all ease;
        }

        .form-control:focus {
            border-color: var(--dlmtd-black);
            box-shadow: 0 0 0 4px rgba(0,0,0,0.05);
            background-color: #fff;
        }

        /* Özel Switch (Tek Alımlık Ürün) */
        .special-option {
            background: #fdfdfd;
            border: 2px solid var(--dlmtd-border);
            border-radius: 20px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .form-check-input:checked {
            background-color: var(--dlmtd-black);
            border-color: var(--dlmtd-black);
        }

        /* Buton */
        .btn-dlmtd {
            background: var(--dlmtd-black);
            color: #fff;
            border: none;
            border-radius: 18px;
            padding: 18px;
            font-weight: 800;
            width: 100%;
            transition: 0.3s;
            letter-spacing: 1px;
        }

        .btn-dlmtd:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
            color: #fff;
        }

        /* Başarı Paneli (Link Kutusu) */
        .success-panel {
            background: #000;
            color: #fff;
            border-radius: 25px;
            padding: 25px;
            margin-bottom: 30px;
            animation: slideDown 0.5s ease;
        }

        .link-display {
            background: rgba(255,255,255,0.1);
            padding: 12px 20px;
            border-radius: 12px;
            font-family: monospace;
            font-size: 0.9rem;
            display: block;
            margin-top: 10px;
            word-break: break-all;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="container container-box">
        
        <div class="d-flex justify-content-between align-items-center mb-4 px-3">
            <div>
                <h1 class="fw-800 m-0" style="letter-spacing: -1.5px;">Yeni Ürün</h1>
                <p class="text-muted small m-0">DLMTD Store Koleksiyonuna Ekleme Yap</p>
            </div>
            <a href="products.php" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">
                <i class="fas fa-arrow-left me-2"></i> Geri Dön
            </a>
        </div>

        <?php if($generated_link): ?>
            <div class="success-panel">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold"><i class="fas fa-magic me-2"></i> <?php echo $success_msg; ?></span>
                    <button onclick="copyToClipboard()" class="btn btn-light btn-sm rounded-pill px-3 fw-bold">Kopyala</button>
                </div>
                <div class="link-display" id="linkTxt"><?php echo $generated_link; ?></div>
            </div>
        <?php endif; ?>

        <div class="admin-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8 mb-4">
                        <label class="form-label">Ürün İsmi</label>
                        <input type="text" name="name" class="form-control" placeholder="Örn: Black Edition Hoodie" required>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Fiyat (₺)</label>
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Ürün Açıklaması</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Kumaş kalitesi, kalıp bilgisi vb..."></textarea>
                </div>

                <div class="special-option">
                    <div>
                        <h6 class="fw-bold m-0 text-dark">Tek Alımlık Ürün (Özel Link)</h6>
                        <p class="text-muted small m-0">Satın alındığı an link devre dışı kalır.</p>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_single" style="width: 45px; height: 22px;">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label">Ürün Görseli</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                    <small class="text-muted mt-2 d-block px-1">En iyi sonuç için kare (1:1) görseller kullanın.</small>
                </div>

                <button type="submit" class="btn btn-dlmtd shadow">
                    <i class="fas fa-plus-circle me-2"></i> ÜRÜNÜ OLUŞTUR VE YAYINLA
                </button>
            </form>
        </div>
    </div>

    <script>
        function copyToClipboard() {
            var copyText = document.getElementById("linkTxt").innerText;
            navigator.clipboard.writeText(copyText);
            alert("Özel satın alım linki kopyalandı!");
        }
    </script>
</body>
</html>