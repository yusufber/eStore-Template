<?php
session_start();
include '../db.php';

// Güvenlik: Sadece adminler girebilir
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Rol Güncelleme İşlemi
if (isset($_POST['update_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    // Kendini kazara "customer" yapmasını engellemek için küçük bir kontrol (opsiyonel)
    if ($user_id == $_SESSION['user_id'] && $new_role == 'customer') {
        header("Location: users.php?msg=self_error");
    } else {
        $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$new_role, $user_id]);
        header("Location: users.php?msg=updated");
    }
    exit;
}

// Kullanıcı Silme İşlemi
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id != $_SESSION['user_id']) { // Kendi hesabını silemesin
        $db->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        header("Location: users.php?msg=deleted");
    } else {
        header("Location: users.php?msg=self_delete_error");
    }
    exit;
}

// Tüm kullanıcıları çek
$users = $db->query("SELECT * FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Müşteri Yönetimi | Grizmtech Admin</title>
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
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: #eee;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
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
            <a class="nav-link" href="orders.php"><i class="fas fa-shopping-cart me-2"></i> Siparişler</a>
            <a class="nav-link" href="products.php"><i class="fas fa-tshirt me-2"></i> Ürün Yönetimi</a>
            <a class="nav-link active" href="users.php"><i class="fas fa-users me-2"></i> Müşteriler</a>
            <hr class="mx-3 opacity-25 text-white">
            <a class="nav-link text-danger" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Çıkış Yap</a>
        </nav>
    </div>

    <div class="main-content">
        <header class="mb-5">
            <h2 class="fw-bold mb-0">Müşteri Yönetimi</h2>
            <p class="text-muted">Kayıtlı kullanıcıları yönetin ve yetkilendirin.</p>
        </header>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-info border-0 shadow-sm mb-4">
                <?php
                if ($_GET['msg'] == 'updated')
                    echo "Kullanıcı rolü başarıyla güncellendi.";
                if ($_GET['msg'] == 'deleted')
                    echo "Kullanıcı başarıyla silindi.";
                if ($_GET['msg'] == 'self_error')
                    echo "Kendi yetkinizi düşüremezsiniz!";
                if ($_GET['msg'] == 'self_delete_error')
                    echo "Kendi hesabınızı silemezsiniz!";
                ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Kullanıcı</th>
                            <th>Rol</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="ps-4 text-muted">#<?php echo $user['id']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3 text-primary">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <span
                                                class="fw-bold d-block"><?php echo htmlspecialchars($user['username']); ?></span>
                                            <small class="text-muted">ID: <?php echo $user['id']; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <form method="POST" class="d-flex gap-2">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <select name="role" class="form-select form-select-sm border-0 bg-light w-auto">
                                            <option value="customer" <?php if ($user['role'] == 'customer')
                                                echo 'selected'; ?>>Müşteri</option>
                                            <option value="admin" <?php if ($user['role'] == 'admin')
                                                echo 'selected'; ?>>Admin
                                            </option>
                                        </select>
                                        <button type="submit" name="update_role" class="btn btn-success btn-sm"><i
                                                class="fas fa-save"></i></button>
                                    </form>
                                </td>
                                <td>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="?delete=<?php echo $user['id']; ?>"
                                            class="btn btn-outline-danger btn-sm border-0"
                                            onclick="return confirm('Bu kullanıcıyı tamamen silmek istediğine emin misin?')">
                                            <i class="fas fa-user-minus"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary px-3">Siz</span>
                                    <?php endif; ?>
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