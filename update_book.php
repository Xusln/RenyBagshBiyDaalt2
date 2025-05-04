<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM books WHERE id=?");
$stmt->execute([$id]);
$book = $stmt->fetch();
if(!$book) exit('Ном олдсонгүй!');

$user_list = $pdo->query("SELECT id, name FROM users")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $author = $_POST['author'] ?? '';
    $publication_year = $_POST['publication_year'] ?? '';
    $isbn = $_POST['isbn'] ?? '';
    $publisher = $_POST['publisher'] ?? '';
    $binding_type = $_POST['binding_type'] ?? '';
    $pages = $_POST['pages'] ?? 0;
    $language = $_POST['language'] ?? '';
    $price = $_POST['price'] ?? 0;
    $category = $_POST['category'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $image = $_POST['image'] ?? '';
    $location = $_POST['location'] ?? '';
    $total_copies = $_POST['total_copies'] ?? 0;
    $available_copies = $_POST['available_copies'] ?? 0;
    $ordered_by = $_POST['ordered_by'] ?: null;
    $description = $_POST['description'] ?? '';
    $status = $_POST['status'] ?? '';
    $edition = $_POST['edition'] ?? '';

    $stmt = $pdo->prepare(
        "UPDATE books SET 
            title=?, author=?, publication_year=?, isbn=?, publisher=?, binding_type=?, pages=?, language=?, price=?, category=?, subject=?, image=?, location=?, total_copies=?, available_copies=?, ordered_by=?, description=?, status=?, edition=?
         WHERE id=?"
    );
    $stmt->execute([
        $title, $author, $publication_year, $isbn, $publisher,
        $binding_type, $pages, $language, $price, $category, $subject, $image,
        $location, $total_copies, $available_copies, $ordered_by, $description, $status, $edition,
        $id
    ]);
    header('Location: edit_book.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="mn">
<head>
    <meta charset="utf-8">
    <title>Ном засах</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background-color: #f9f9f9;
            padding: 40px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        form {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .form-group {
            display: flex;
            margin-bottom: 15px;
            gap: 20px;
        }

        .form-group.single {
            flex-direction: column;
        }

        label {
            width: 200px;
            font-weight: bold;
            padding-top: 8px;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            flex: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            height: 80px;
        }

        button {
            display: block;
            margin: 30px auto 0;
            padding: 10px 40px;
            font-size: 16px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #333;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<h2>Ном засах</h2>
<form method="post">
    <div class="form-group"><label>Гарчиг:</label> <input name="title" value="<?=htmlspecialchars($book['title'])?>" required></div>
    <div class="form-group"><label>Зохиогч:</label> <input name="author" value="<?=htmlspecialchars($book['author'])?>" required></div>
    <div class="form-group"><label>Он:</label>
        <input type="number" name="publication_year" min="1800" max="2100" value="<?= is_numeric($book['publication_year']) ? intval($book['publication_year']) : '' ?>">
    </div>
    <div class="form-group"><label>ISBN:</label> <input name="isbn" value="<?=htmlspecialchars($book['isbn'])?>"></div>
    <div class="form-group"><label>Хэвлэлийн газар:</label> <input name="publisher" value="<?=htmlspecialchars($book['publisher'])?>"></div>
    <div class="form-group"><label>Хийц:</label> <input name="binding_type" value="<?=htmlspecialchars($book['binding_type'])?>"></div>
    <div class="form-group"><label>Хуудасны тоо:</label> <input name="pages" type="number" value="<?=htmlspecialchars($book['pages'])?>"></div>
    <div class="form-group"><label>Хэл:</label>
      <select name="language" required>
        <option value="mn" <?= $book['language']=='mn' ? 'selected':'' ?>>Монгол</option>
        <option value="en" <?= $book['language']=='en' ? 'selected':'' ?>>Англи</option>
      </select>
    </div>
    <div class="form-group"><label>Үнэ:</label> <input name="price" type="number" min="0" step="0.01" value="<?=htmlspecialchars($book['price'])?>"></div>
    <div class="form-group"><label>Категори:</label>
      <select name="category" required>
        <option value="Адал явдалт" <?= $book['category']=='Адал явдалт' ? 'selected':'' ?>>Адал явдалт</option>
        <option value="Уран зөгнөлт" <?= $book['category']=='Уран зөгнөлт' ? 'selected':'' ?>>Уран зөгнөлт</option>
        <option value="Хувь хүний хөгжил" <?= $book['category']=='Хувь хүний хөгжил' ? 'selected':'' ?>>Хувь хүний хөгжил</option>
        <option value="Хүүхдийн" <?= $book['category']=='Хүүхдийн' ? 'selected':'' ?>>Хүүхдийн</option>
      </select>
    </div>
    <div class="form-group"><label>Сэдэв:</label> <input name="subject" value="<?=htmlspecialchars($book['subject'])?>"></div>
    <div class="form-group"><label>Зураг (файлын нэр):</label> <input name="image" value="<?=htmlspecialchars($book['image'])?>"></div>
    <div class="form-group"><label>Байршил:</label> <input name="location" value="<?=htmlspecialchars($book['location'])?>"></div>
    <div class="form-group"><label>Нийт хувь:</label> <input name="total_copies" type="number" value="<?=htmlspecialchars($book['total_copies'])?>"></div>
    <div class="form-group"><label>Чөлөөт хувь:</label> <input name="available_copies" type="number" value="<?=htmlspecialchars($book['available_copies'])?>"></div>
    <div class="form-group"><label>Захиалсан хэрэглэгч ID:</label>
      <select name="ordered_by">
        <option value="">-</option>
        <?php foreach($user_list as $u): ?>
        <option value="<?= $u['id'] ?>" <?php if($book['ordered_by']==$u['id']) echo 'selected'; ?>>
          <?= htmlspecialchars($u['name']) ?> (ID:<?= $u['id'] ?>)
        </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group single"><label>Тайлбар:</label> <textarea name="description"><?=htmlspecialchars($book['description'])?></textarea></div>
    <div class="form-group"><label>Статус:</label>
      <select name="status" required>
        <option value="Бэлэн байгаа" <?= $book['status']=='Бэлэн байгаа' ? 'selected':'' ?>>Бэлэн байгаа</option>
        <option value="Ном дууссан" <?= $book['status']=='Ном дууссан' ? 'selected':'' ?>>Ном дууссан</option>
      </select>
    </div>
    <div class="form-group"><label>Хэвлэл:</label> <input name="edition" value="<?=htmlspecialchars($book['edition'])?>"></div>

    <button type="submit">Засах</button>
</form>
<a href="edit_book.php">← Буцах</a>
</body>
</html>
