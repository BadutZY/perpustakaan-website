<?php

require_once '../config/database.php';
header('Content-Type: application/json');
startSession();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        getBooks();
        break;
    case 'get':
        getBook((int)($_GET['id'] ?? 0));
        break;
    case 'add':
        addBook();
        break;
    case 'edit':
        editBook();
        break;
    case 'delete':
        deleteBook((int)($_POST['id'] ?? 0));
        break;
    default:
        jsonResponse(false, 'Action tidak valid');
}

function getBooks()
{
    $db = getDB();
    $genre  = $_GET['genre'] ?? 'all';
    $search = trim($_GET['search'] ?? '');
    $userId = null;

    if (isLoggedIn() && !isAdmin()) {
        $user = getCurrentUser();
        $userId = $user['id'];
    }

    $sql = "SELECT b.*, 
                   (b.stock - COALESCE((SELECT COUNT(*) FROM borrows br WHERE br.book_id = b.id AND br.status = 'borrowed'), 0)) AS available_stock";

    if ($userId) {
        $sql .= ", (SELECT COUNT(*) FROM borrows br WHERE br.book_id = b.id AND br.user_id = ? AND br.status = 'borrowed') AS is_borrowed_by_me";
    } else {
        $sql .= ", 0 AS is_borrowed_by_me";
    }

    $sql .= " FROM books b WHERE 1=1";
    $params = $userId ? [$userId] : [];

    if ($genre !== 'all') {
        $sql .= " AND b.genre = ?";
        $params[] = $genre;
    }

    if ($search) {
        $sql .= " AND (b.title LIKE ? OR b.author LIKE ? OR b.publisher LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $sql .= " ORDER BY b.id ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $books = $stmt->fetchAll();

    foreach ($books as &$book) {
        $book['available_stock'] = (int)$book['available_stock'];
        $book['is_borrowed_by_me'] = (bool)$book['is_borrowed_by_me'];
        $book['stock'] = (int)$book['stock'];
    }

    jsonResponse(true, 'OK', ['books' => $books]);
}

function getBook($id)
{
    if (!$id) jsonResponse(false, 'ID tidak valid');
    $db   = getDB();
    $stmt = $db->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $book = $stmt->fetch();
    if (!$book) jsonResponse(false, 'Buku tidak ditemukan');
    jsonResponse(true, 'OK', ['book' => $book]);
}

function addBook()
{
    if (!isAdmin()) jsonResponse(false, 'Akses ditolak');

    $title     = trim($_POST['title'] ?? '');
    $publisher = trim($_POST['publisher'] ?? '');
    $author    = trim($_POST['author'] ?? '');
    $genre     = trim($_POST['genre'] ?? '');
    $synopsis  = trim($_POST['synopsis'] ?? '');
    $stock     = (int)($_POST['stock'] ?? 1);

    if (!$title || !$publisher || !$author || !$genre || !$synopsis) {
        jsonResponse(false, 'Semua kolom harus diisi!');
    }

    $cover = 'IMG/default.jpg';
    if (!empty($_FILES['cover']['name'])) {
        $cover = handleUpload($_FILES['cover']);
    }

    $db   = getDB();
    $stmt = $db->prepare("INSERT INTO books (title, publisher, author, genre, synopsis, cover, stock) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $publisher, $author, $genre, $synopsis, $cover, $stock]);
    $newId = $db->lastInsertId();

    $book = $db->prepare("SELECT * FROM books WHERE id = ?");
    $book->execute([$newId]);

    jsonResponse(true, "Buku \"$title\" berhasil ditambahkan!", ['book' => $book->fetch()]);
}

function editBook()
{
    if (!isAdmin()) jsonResponse(false, 'Akses ditolak');

    $id        = (int)($_POST['id'] ?? 0);
    $title     = trim($_POST['title'] ?? '');
    $publisher = trim($_POST['publisher'] ?? '');
    $author    = trim($_POST['author'] ?? '');
    $genre     = trim($_POST['genre'] ?? '');
    $synopsis  = trim($_POST['synopsis'] ?? '');
    $stock     = (int)($_POST['stock'] ?? 1);

    if (!$id || !$title || !$publisher || !$author || !$genre || !$synopsis) {
        jsonResponse(false, 'Semua kolom harus diisi!');
    }

    $db   = getDB();
    $stmt = $db->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $book = $stmt->fetch();
    if (!$book) jsonResponse(false, 'Buku tidak ditemukan');

    $cover = $book['cover'];
    if (!empty($_FILES['cover']['name'])) {
        $cover = handleUpload($_FILES['cover']);
    }

    $stmt = $db->prepare("UPDATE books SET title=?, publisher=?, author=?, genre=?, synopsis=?, cover=?, stock=? WHERE id=?");
    $stmt->execute([$title, $publisher, $author, $genre, $synopsis, $cover, $stock, $id]);

    jsonResponse(true, "Buku \"$title\" berhasil diperbarui!");
}

function deleteBook($id)
{
    if (!isAdmin()) jsonResponse(false, 'Akses ditolak');
    if (!$id) jsonResponse(false, 'ID tidak valid');

    $db   = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) FROM borrows WHERE book_id = ? AND status = 'borrowed'");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        jsonResponse(false, 'Buku ini sedang dipinjam dan tidak dapat dihapus!');
    }

    $bookStmt = $db->prepare("SELECT title FROM books WHERE id = ?");
    $bookStmt->execute([$id]);
    $book = $bookStmt->fetch();
    if (!$book) jsonResponse(false, 'Buku tidak ditemukan');

    $stmt = $db->prepare("DELETE FROM books WHERE id = ?");
    $stmt->execute([$id]);

    jsonResponse(true, "Buku \"{$book['title']}\" berhasil dihapus!");
}

function handleUpload($file)
{
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        jsonResponse(false, 'Format gambar tidak didukung! Gunakan JPG, PNG, atau WEBP.');
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        jsonResponse(false, 'Ukuran gambar terlalu besar (maksimal 5MB)!');
    }

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'uploads/' . uniqid('book_') . '.' . $ext;
    $dest     = dirname(__DIR__) . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        jsonResponse(false, 'Gagal mengupload gambar!');
    }

    return $filename;
}
