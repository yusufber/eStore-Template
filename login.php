<?php
session_start();
include 'db.php'; // db.php ana dizinde olduğu için direkt çağırıyoruz
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    if (!empty($user) && !empty($pass)) {
        $query = $db->prepare("SELECT * FROM users WHERE username = ?");
        $query->execute([$user]);
        $user_data = $query->fetch(PDO::FETCH_ASSOC);

        if ($user_data && password_verify($pass, $user_data['password'])) {
            // Oturum verilerini güvenle kaydet
            $_SESSION['user_id'] = $user_data['id'];
            $_SESSION['username'] = $user_data['username'];
            $_SESSION['role'] = $user_data['role'];

            // Herkesi ana sayfaya yönlendir
            header("Location: index.php");
            exit;
        } else {
            $error = "Kullanıcı adı veya şifre hatalı!";
        }
    } else {
        $error = "Lütfen tüm alanları doldurun.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap | Grizmtech Shop</title>
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

        .login-card {
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

        .btn-login {
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            background: #000;
            border: none;
            transition: 0.3s;
        }

        .btn-login:hover {
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
                    <h2 class="logo-text">GRIZMTECH <span class="text-primary">SHOP</span></h2>
                    <p class="text-light opacity-50 small">Tekrar hoş geldin, seni özledik.</p>
                </div>

                <div class="card login-card shadow-lg">
                    <h4 class="fw-bold mb-4 text-center">Üye Girişi</h4>

                    <?php if ($error): ?>
                        <div class="alert alert-danger border-0 small py-2 mb-3">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Kullanıcı Adı</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0"
                                    style="border-radius: 12px 0 0 12px;">
                                    <i class="fas fa-user text-muted"></i>
                                </span>
                                <input type="text" name="username" class="form-control border-start-0"
                                    placeholder="Kullanıcı adınız" required style="border-radius: 0 12px 12px 0;">
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
                                    placeholder="••••••••" required style="border-radius: 0 12px 12px 0;">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-login w-100 mb-3">
                            Giriş Yap
                        </button>

                        <div class="text-center mt-3">
                            <p class="small text-muted mb-0">Henüz hesabın yok mu?
                                <a href="register.php" class="text-primary fw-bold text-decoration-none">Kayıt Ol</a>
                            </p>
                        </div>
                    </form>
                </div>

                <div class="text-center mt-4">
                    <a href="index.php" class="text-light opacity-50 small text-decoration-none">
                        <i class="fas fa-arrow-left me-1"></i> Şifremi Unuttum
                    </a>
                </div>

            </div>
        </div>
    </div>

</body>

</html>