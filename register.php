<?php
// Veritabanı bağlantısı includes klasöründe olduğu için yolu güncelledik
include 'db.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'customer'; // Yeni kayıt olan herkes müşteri

        try {
            $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashed_password, $role]);

            // Kayıt başarılıysa direkt login sayfasına yönlendir veya mesaj göster
            $message = "<div class='alert alert-success border-0 shadow-sm'>
                            <i class='fas fa-check-circle me-2'></i>Kayıt başarılı! 
                            <a href='login.php' class='fw-bold text-decoration-none'>Giriş yapın.</a>
                        </div>";
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger border-0 shadow-sm'>
                            <i class='fas fa-exclamation-circle me-2'></i>Bu kullanıcı adı zaten alınmış.
                        </div>";
        }
    } else {
        $message = "<div class='alert alert-warning border-0 shadow-sm'>Lütfen tüm alanları doldurun.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol | Grizmtech Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0f0f0f;
            /* Koyu arka plan */
            height: 100vh;
        }

        .register-card {
            background: #fff;
            border: none;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            border: 1px solid #eee;
            background: #f8f9fa;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
            border-color: #0d6efd;
        }

        .btn-register {
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            background: #000;
            border: none;
            transition: 0.3s;
        }

        .btn-register:hover {
            background: #333;
            transform: translateY(-2px);
        }

        .logo-text {
            font-weight: 800;
            letter-spacing: -1px;
            color: #000;
        }
    </style>
</head>

<body class="d-flex align-items-center">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">

                <div class="text-center mb-4">
                    <h2 class="logo-text">GRIZMTECH <span class="text-primary text-sm">SHOP</span></h2>
                    <p class="text-light opacity-50 small">Aramıza katıl ve alışverişe başla.</p>
                </div>

                <div class="card register-card">
                    <h4 class="fw-bold mb-4 text-center">Hesap Oluştur</h4>

                    <?php echo $message; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Kullanıcı Adı</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0"
                                    style="border-radius: 12px 0 0 12px;">
                                    <i class="fas fa-user text-muted"></i>
                                </span>
                                <input type="text" name="username" class="form-control border-start-0"
                                    placeholder="Kullanıcı adınızı seçin" required
                                    style="border-radius: 0 12px 12px 0;">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Şifre</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0"
                                    style="border-radius: 12px 0 0 12px;">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                                <input type="password" name="password" class="form-control border-start-0"
                                    placeholder="Güçlü bir şifre girin" required style="border-radius: 0 12px 12px 0;">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-register w-100 mb-3">
                            Kayıt Olmayı Tamamla
                        </button>

                        <div class="text-center">
                            <p class="small text-muted mb-0">Zaten bir hesabın var mı?
                                <a href="login.php" class="text-primary fw-bold text-decoration-none">Giriş Yap</a>
                            </p>
                        </div>
                    </form>
                </div>

                <div class="text-center mt-4">
                    <a href="index.php" class="text-light opacity-50 small text-decoration-none">
                        <i class="fas fa-arrow-left me-1"></i> Mağazaya geri dön
                    </a>
                </div>

            </div>
        </div>
    </div>

</body>

</html>