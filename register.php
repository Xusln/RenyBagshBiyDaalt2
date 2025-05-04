<?php
require_once 'db.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    if(strlen($password) < 5) {
        $error = "Нууц үг хамгийн багадаа 5 тэмдэгт байх ёстой.";
    } else {
        $hpass = password_hash($password, PASSWORD_BCRYPT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hpass]);
            header("Location: login.php");
            exit;
        } catch (PDOException $e) {
            $error = "Уучлаарай, энэ имэйлээр бүртгэлтэй байна!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="mn">
<head>
    <meta charset="UTF-8">
    <title>Бүртгүүлэх</title>
</head>
<body>
<h2>Бүртгүүлэх</h2>
<?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
    Нэр: <input type="text" name="name" required><br>
    Имэйл: <input type="email" name="email" required><br>
    Нууц үг: <input type="password" name="password" required><br>
    <button type="submit">Бүртгүүлэх</button>
</form>
<a href="login.php">Нэвтрэх</a>
</body>
</html>