<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ✅ Админ баталгаажуулалт ба POST хүсэлтийг хүлээж авах хэсэг
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status']) && $_SESSION['is_admin']) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];

    // Статус шинэчлэх
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $order_id]);

    // Хэрэв амжилттай бол номын үлдэгдэл багасгах
    if ($new_status === 'амжилттай') {
        $stmt = $pdo->prepare("UPDATE books SET total_copies = total_copies - 1 WHERE id = (SELECT book_id FROM orders WHERE id = ?)");
        $stmt->execute([$order_id]);
    }

    // Дахин ачаалах
    header("Location: orders.php");
    exit;
}

// ✅ Захиалгыг татах
if ($_SESSION['is_admin']) {
    $stmt = $pdo->query("SELECT o.*, u.name as uname, b.title as btitle FROM orders o 
                         JOIN users u ON o.user_id=u.id 
                         JOIN books b ON o.book_id=b.id 
                         ORDER BY o.order_date DESC");
} else {
    $uid = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT o.*, b.title as btitle FROM orders o 
                           JOIN books b ON o.book_id=b.id 
                           WHERE o.user_id=? 
                           ORDER BY o.order_date DESC");
    $stmt->execute([$uid]);
}
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="mn">
<head>
    <meta charset="UTF-8">
    <title>Захиалгууд</title>
    <style>/* General Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.container {
    width: 80%;
    margin: 0 auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    color: #333;
}

/* Table Styles */
.order-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.order-table th, .order-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.order-table th {
    background-color: #4CAF50;
    color: white;
}

.order-table tr:hover {
    background-color: #f5f5f5;
}

/* Buttons */
.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

.approve-btn {
    background-color: #28a745;
    color: white;
}

.decline-btn {
    background-color: #dc3545;
    color: white;
}

.approve-btn:hover, .decline-btn:hover {
    opacity: 0.8;
}

/* Links */
.back-link, .admin-link {
    color: #007bff;
    text-decoration: none;
    font-size: 14px;
}

.back-link:hover, .admin-link:hover {
    text-decoration: underline;
}
</style>

</head>
<body>
    <div class="container">
        <h2>Захиалгын жагсаалт</h2>
        <table class="order-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ном</th>
                    <th>Хэрэглэгч</th>
                    <th>Захиалсан огноо</th>
                    <th>Төлөв</th>
                    <?php if ($_SESSION['is_admin']): ?>
                    <th>Үйлдэл</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $o): ?>
                <tr>
                    <td><?= $o['id'] ?></td>
                    <td><?= htmlspecialchars($o['btitle']) ?></td>
                    <td><?= $_SESSION['is_admin'] ? htmlspecialchars($o['uname']) : '' ?></td>
                    <td><?= $o['order_date'] ?></td>
                    <td><?= htmlspecialchars($o['status']) ?></td>

                    <?php if ($_SESSION['is_admin']): ?>
                    <td>
                        <?php if ($o['status'] === 'pending' || $o['status'] === 'хүлээгдэж байна'): ?>
                            <form method="post" action="orders.php" class="order-action-form">
                                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                <input type="hidden" name="new_status" value="амжилттай">
                                <button type="submit" class="btn approve-btn">Зөвшөөрөх</button>
                            </form>
                            <form method="post" action="orders.php" class="order-action-form">
                                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                <input type="hidden" name="new_status" value="татгалзсан">
                                <button type="submit" class="btn decline-btn">Татгалзах</button>
                            </form>
                        <?php else: ?>
                            <?= htmlspecialchars($o['status']) ?>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="index.php" class="back-link">Буцах</a>
        <?php if ($_SESSION['is_admin']): ?>
            | <a href="admin.php" class="admin-link">Админ хэсэг</a>
        <?php endif; ?>
    </div>
    <script>// JavaScript код нэмэх бол эндээс хянах боломжтой
document.addEventListener('DOMContentLoaded', function () {
    const approveButtons = document.querySelectorAll('.approve-btn');
    const declineButtons = document.querySelectorAll('.decline-btn');

    // Зөвшөөрөх товчийг дарсан тохиолдолд
    approveButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            if (!confirm('Та энэ захиалгыг зөвшөөрөх үү?')) {
                event.preventDefault();
            }
        });
    });

    // Татгалзах товчийг дарсан тохиолдолд
    declineButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            if (!confirm('Та энэ захиалгыг татгалзах уу?')) {
                event.preventDefault();
            }
        });
    });
});
</script>
</body>
</html>
