<?php

require_once '../config/database.php';
header('Content-Type: application/json');
startSession();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        listBorrows();
        break;
    case 'my':
        myBorrows();
        break;
    case 'borrow':
        borrowBook();
        break;
    case 'return':
        returnBook();
        break;
    case 'admin_return':
        adminReturn();
        break;
    default:
        jsonResponse(false, 'Action tidak valid');
}

function listBorrows()
{
    if (!isAdmin()) jsonResponse(false, 'Akses ditolak');

    $db = getDB();
    $sql = "SELECT br.id, br.borrow_date, br.return_due_date, br.return_date, br.status,
                   u.username, u.id AS user_id,
                   b.title, b.cover, b.id AS book_id, b.author
            FROM borrows br
            JOIN users u ON br.user_id = u.id
            JOIN books b ON br.book_id = b.id
            ORDER BY br.status ASC, br.return_due_date ASC";
    $stmt = $db->query($sql);
    $borrows = $stmt->fetchAll();

    $today = date('Y-m-d');
    $updateStmt = $db->prepare("UPDATE borrows SET status='overdue' WHERE status='borrowed' AND return_due_date < ?");
    $updateStmt->execute([$today]);

    jsonResponse(true, 'OK', ['borrows' => $borrows]);
}

function myBorrows()
{
    if (!isLoggedIn()) jsonResponse(false, 'Silakan login terlebih dahulu');

    $user = getCurrentUser();
    $db   = getDB();

    $today = date('Y-m-d');
    $db->prepare("UPDATE borrows SET status='overdue' WHERE status='borrowed' AND return_due_date < ?")->execute([$today]);

    $sql = "SELECT br.id, br.borrow_date, br.return_due_date, br.return_date, br.status,
                   b.id AS book_id, b.title, b.cover, b.author, b.publisher
            FROM borrows br
            JOIN books b ON br.book_id = b.id
            WHERE br.user_id = ? AND br.status IN ('borrowed','overdue')
            ORDER BY br.borrow_date DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute([$user['id']]);

    jsonResponse(true, 'OK', ['borrows' => $stmt->fetchAll()]);
}

function borrowBook()
{
    if (!isLoggedIn()) jsonResponse(false, 'Silakan login untuk meminjam buku');
    if (isAdmin()) jsonResponse(false, 'Admin tidak dapat meminjam buku');

    $bookId = (int)($_POST['book_id'] ?? 0);
    if (!$bookId) jsonResponse(false, 'ID buku tidak valid');

    $user = getCurrentUser();
    $db   = getDB();

    $stmt = $db->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch();
    if (!$book) jsonResponse(false, 'Buku tidak ditemukan');

    $stmt = $db->prepare("SELECT id FROM borrows WHERE user_id = ? AND book_id = ? AND status IN ('borrowed','overdue')");
    $stmt->execute([$user['id'], $bookId]);
    if ($stmt->fetch()) jsonResponse(false, 'Anda sudah meminjam buku ini!');

    $stmt = $db->prepare("SELECT COUNT(*) FROM borrows WHERE book_id = ? AND status IN ('borrowed','overdue')");
    $stmt->execute([$bookId]);
    $borrowedCount = (int)$stmt->fetchColumn();

    if ($borrowedCount >= $book['stock']) {
        jsonResponse(false, 'Stok buku tidak tersedia saat ini!');
    }

    $borrowDate = date('Y-m-d');
    $dueDate    = date('Y-m-d', strtotime('+14 days'));

    $stmt = $db->prepare("INSERT INTO borrows (user_id, book_id, borrow_date, return_due_date, status) VALUES (?, ?, ?, ?, 'borrowed')");
    $stmt->execute([$user['id'], $bookId, $borrowDate, $dueDate]);

    jsonResponse(true, "Buku \"{$book['title']}\" berhasil dipinjam! Batas pengembalian: " . formatTanggal($dueDate));
}

function returnBook()
{
    if (!isLoggedIn()) jsonResponse(false, 'Silakan login terlebih dahulu');

    $borrowId = (int)($_POST['borrow_id'] ?? 0);
    $user     = getCurrentUser();
    $db       = getDB();

    $stmt = $db->prepare("SELECT br.*, b.title FROM borrows br JOIN books b ON br.book_id = b.id WHERE br.id = ? AND br.user_id = ?");
    $stmt->execute([$borrowId, $user['id']]);
    $borrow = $stmt->fetch();

    if (!$borrow) jsonResponse(false, 'Data peminjaman tidak ditemukan!');

    $stmt = $db->prepare("UPDATE borrows SET status='returned', return_date=? WHERE id=?");
    $stmt->execute([date('Y-m-d'), $borrowId]);

    jsonResponse(true, "Buku \"{$borrow['title']}\" berhasil dikembalikan!");
}

function adminReturn()
{
    if (!isAdmin()) jsonResponse(false, 'Akses ditolak');

    $borrowId = (int)($_POST['borrow_id'] ?? 0);
    $db       = getDB();

    $stmt = $db->prepare("SELECT br.*, b.title FROM borrows br JOIN books b ON br.book_id = b.id WHERE br.id = ?");
    $stmt->execute([$borrowId]);
    $borrow = $stmt->fetch();

    if (!$borrow) jsonResponse(false, 'Data peminjaman tidak ditemukan!');

    $stmt = $db->prepare("UPDATE borrows SET status='returned', return_date=? WHERE id=?");
    $stmt->execute([date('Y-m-d'), $borrowId]);

    jsonResponse(true, "Buku \"{$borrow['title']}\" berhasil ditandai sebagai dikembalikan!");
}

function formatTanggal($date)
{
    $d = new DateTime($date);
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return $d->format('d') . ' ' . $months[(int)$d->format('m') - 1] . ' ' . $d->format('Y');
}
