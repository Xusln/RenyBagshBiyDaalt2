<?php
session_start();
require 'db.php';

if (!isset($_GET['id'])) {
    die("ID олдсонгүй");
}

$id = $_GET['id'];

// 1. Захиалгыг устгах
$stmt = $pdo->prepare("DELETE FROM orders WHERE book_id = ?");
$stmt->execute([$id]);

// 2. Сэтгэгдлийг устгах
$stmt = $pdo->prepare("DELETE FROM comments WHERE book_id = ?");
$stmt->execute([$id]);

// 3. Үнэлгээг устгах
$stmt = $pdo->prepare("DELETE FROM ratings WHERE book_id = ?");
$stmt->execute([$id]);

// 4. Номыг устгах
$stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
$stmt->execute([$id]);

header("Location: edit_book.php");
exit;
?>
