<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}
$user_list = $pdo->query("SELECT id, name FROM users")->fetchAll();
$error = '';

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
    $location = $_POST['location'] ?? '';
    $total_copies = $_POST['total_copies'] ?? 0;
    $available_copies = $_POST['available_copies'] ?? 0;
    $ordered_by = $_POST['ordered_by'] ?: null;
    $description = $_POST['description'] ?? '';
    $status = $_POST['status'] ?? '';
    $edition = $_POST['edition'] ?? '';
    // --- FILE UPLOAD
    $image = '';
    if(isset($_FILES['image_file']) && $_FILES['image_file']['error']==0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)) {
            $imgname = time().'_'.rand(100,999).'.'.$ext;
            move_uploaded_file($_FILES['image_file']['tmp_name'], 'assets/'.$imgname);
            $image = $imgname;
        } else {
            $error = "Зөвхөн зураг файл оруулна уу! (jpg, jpeg, png, gif)";
        }
    }

    if(!$error) {
        $stmt = $pdo->prepare(
            "INSERT INTO books 
            (title, author, publication_year, isbn, publisher, binding_type, pages, language, price, category, subject, image, location, total_copies, available_copies, ordered_by, description, status, edition) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $title, $author, $publication_year, $isbn, $publisher,
            $binding_type, $pages, $language, $price, $category, $subject, $image,
            $location, $total_copies, $available_copies, $ordered_by, $description, $status, $edition
        ]);
        header('Location: edit_book.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="mn">
<head>
  <meta charset="utf-8">
  <title>Ном нэмэх</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h3 class="mb-0">Шинэ ном нэмэх</h3>
        </div>
        <div class="card-body">
          <form method="post" enctype="multipart/form-data">
            <!-- Гарчиг ба Зохиогч -->
<div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label">Гарчиг *</label>
    <input type="text" name="title" class="form-control" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">Зохиогч *</label>
    <input type="text" name="author" class="form-control" required>
  </div>
</div>


<!-- Он ба ISBN -->
<div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label">Он</label>
    <input type="number" name="publication_year" class="form-control"
       value="<?= isset($book['publication_year']) ? $book['publication_year'] : '' ?>">
  </div>
  <div class="col-md-6">
    <label class="form-label">ISBN</label>
    <input type="text" name="isbn" class="form-control">
  </div>
</div>

<!-- Хэвлэлийн газар ба Хийц -->
<div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label">Хэвлэлийн газар</label>
    <input type="text" name="publisher" class="form-control">
  </div>
  <div class="col-md-6">
    <label class="form-label">Хийц</label>
    <input type="text" name="binding_type" class="form-control">
  </div>
</div>

<!-- Хуудасны тоо ба Хэл -->
<div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label">Хуудасны тоо</label>
    <input type="number" name="pages" class="form-control">
  </div>
  <div class="col-md-6">
    <label class="form-label">Хэл *</label>
    <select name="language" class="form-select" required>
      <option value="">Сонгох...</option>
      <option value="mn">Монгол</option>
      <option value="en">Англи</option>
    </select>
  </div>
</div>

<!-- Үнэ ба Категори -->
<div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label">Үнэ</label>
    <input type="number" name="price" class="form-control" min="0" step="0.01">
  </div>
  <div class="col-md-6">
    <label class="form-label">Категори *</label>
    <select name="category" class="form-select" required>
      <option value="">Сонгох...</option>
      <option value="Адал явдалт">Адал явдалт</option>
      <option value="Уран зөгнөлт">Уран зөгнөлт</option>
      <option value="Хувь хүний хөгжил">Хувь хүний хөгжил</option>
      <option value="Хүүхдийн">Хүүхдийн</option>
    </select>
  </div>
</div>
<!-- Сэдэв ба Статус -->
<div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label">Сэдэв</label>
    <input type="text" name="subject" class="form-control">
  </div>
  <div class="col-md-6">
    <label class="form-label">Статус</label>
    <select name="status" class="form-select">
      <option value="">Сонгох...</option>
      <option value="available">Бэлэн байгаа</option>
      <option value="unavailable">Дууссан</option>
    </select>
  </div>
</div>

<!-- Хэвлэл ба Байршил -->
<div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label">Хэвлэл</label>
    <input type="text" name="edition" class="form-control">
  </div>
  <div class="col-md-6">
    <label class="form-label">Байршил *</label>
    <input type="text" name="location" class="form-control" required>
  </div>
</div>

<!-- Тайлбар ба Зураг -->
<div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label">Тайлбар</label>
    <textarea name="description" class="form-control"></textarea>
  </div>
  <div class="col-md-6">
    <label class="form-label">Зураг сонгох</label>
    <input type="file" name="image_file" class="form-control" accept="image/*">
  </div>
</div>

<!-- Нийт хувь ба Чөлөөт хувь -->
<div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label">Нийт хувь</label>
    <input type="number" name="total_copies" class="form-control">
  </div>
  <div class="col-md-6">
    <label class="form-label">Чөлөөт хувь</label>
    <input type="number" name="available_copies" class="form-control">
  </div>
  <div class="d-flex justify-content-between align-items-center mt-4">
  <a href="admin.php" class="btn btn-outline-secondary">Буцах</a>
  <button type="submit" class="btn btn-primary">Ном нэмэх</button>
</div>
</div>
      </div>
      <!-- Амжилтын alert -->
      <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success mt-4">Ном амжилттай нэмэгдлээ!</div>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>