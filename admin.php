<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="mn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ удирдлага</title>
    <style>
        /* Ерөнхий хэв маяг */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f5f6fa;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Админ контейнер */
.admin-container {
    background-color: #fff;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 600px;
    text-align: center;
}

/* Титр */
h2 {
    font-size: 28px;
    color: #333;
    margin-bottom: 30px;
}

/* Навигаци */
.admin-nav ul {
    list-style: none;
    padding: 0;
}

.admin-nav li {
    margin-bottom: 15px;
}

.admin-nav a {
    display: block;
    padding: 15px;
    background-color: #3498db;
    color: #fff;
    border-radius: 5px;
    font-size: 16px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.admin-nav a:hover {
    background-color: #2980b9;
}

/* Гарах товч */
.logout {
    margin-top: 30px;
}

.btn-logout {
    padding: 12px 25px;
    background-color: #e74c3c;
    color: #fff;
    font-size: 16px;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.btn-logout:hover {
    background-color: #c0392b;
}

/* Алдаа, анхааруулга */
.error {
    color: red;
    font-size: 14px;
    margin-bottom: 10px;
}

    </style>
</head>
<body>
    <div class="admin-container">
        <h2>Админ удирдлага</h2>

        <nav class="admin-nav">
            <ul>
                <li><a href="edit_book.php" class="btn">Номын жагсаалт</a></li>
                <li><a href="add_book.php" class="btn">Ном нэмэх</a></li>
                <li><a href="orders.php" class="btn">Захиалгууд</a></li>
            </ul>
        </nav>

        <div class="logout">
            <a href="logout.php" class="btn-logout">Гарах</a>
        </div>
    </div>

    <script>// Жишээ нь, админ панелийн гаднахи товчлуурын хөдөлгөөн нэмэх
document.querySelector('.btn-logout').addEventListener('click', function(event) {
    if (!confirm('Та гарсаныхаа дараа системээс гарах болно. Та гархыг хүсч байна уу?')) {
        event.preventDefault();
    }
});
</script>
</body>
</html>