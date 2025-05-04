<?php
session_start();
require_once 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && (password_verify($password, $user['password']) || sha1($password) === $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['is_admin'] = $user['is_admin'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Амжилтгүй! Имэйл эсвэл нууц үг буруу.";
    }
}
?>
<!DOCTYPE html>
<html lang="mn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Нэвтрэх</title>
    <style>
        /* Ерөнхий хэв маяг */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f0f4f8;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

/* Логины контейнер */
.login-container {
    background-color: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    text-align: center;
}

/* Титр */
h2 {
    color: #333;
    font-size: 24px;
    margin-bottom: 20px;
}

/* Алдаа мэдэгдэл */
.error {
    color: red;
    font-size: 14px;
    margin-bottom: 10px;
}

/* Input талбар */
.input-group {
    margin-bottom: 15px;
    text-align: left;
}

.input-group label {
    font-size: 14px;
    color: #333;
    display: block;
    margin-bottom: 5px;
}

.input-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    transition: border 0.3s;
}

.input-group input:focus {
    border-color: #3498db;
    outline: none;
}

/* Бүртгүүлэх холбоос */
.register-link {
    display: block;
    margin-top: 10px;
    font-size: 14px;
    color: #3498db;
    text-decoration: none;
}

.register-link:hover {
    text-decoration: underline;
}

/* Нэвтрэх товч */
.btn {
    width: 100%;
    padding: 10px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn:hover {
    background-color: #2980b9;
}
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Нэвтрэх</h2>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <div class="input-group">
                <label for="email">Имэйл</label>
                <input type="email" name="email" id="email" required placeholder="Таны имэйл хаяг">
            </div>
            <div class="input-group">
                <label for="password">Нууц үг</label>
                <input type="password" name="password" id="password" required placeholder="Нууц үгээ оруулна уу">
            </div>
            <button type="submit" class="btn">Нэвтрэх</button>
        </form>
        <a href="register.php" class="register-link">Бүртгүүлэх</a>
    </div>

    <script>// Нэвтрэх формын баталгаажуулалт хийх
document.querySelector("form").addEventListener("submit", function(event) {
    const email = document.querySelector("input[name='email']").value;
    const password = document.querySelector("input[name='password']").value;

    if (!email || !password) {
        event.preventDefault();
        alert("Бүх талбарыг бөглөх шаардлагатай!");
    }
});</script>
</body>
</html>