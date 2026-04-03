<?php

require_once 'config/database.php';

$db = getDB();

$adminPassword = password_hash('Kimmy123', PASSWORD_BCRYPT);
$stmt = $db->prepare("UPDATE users SET password = ? WHERE username = 'Kimmy'");
$stmt->execute([$adminPassword]);

if ($stmt->rowCount() > 0) {
    echo "<h2 style='color:green;font-family:sans-serif'>✅ Setup Berhasil!</h2>";
    echo "<p style='font-family:sans-serif'>Password admin telah di-hash dengan benar.</p>";
    echo "<p style='font-family:sans-serif'><strong>Username:</strong> Kimmy <br><strong>Password:</strong> Kimmy123</p>";
    echo "<p style='font-family:sans-serif;color:red'><strong>⚠️ Hapus file setup.php ini setelah selesai!</strong></p>";
    echo "<a href='login.php' style='font-family:sans-serif;color:blue'>→ Pergi ke halaman Login</a>";
} else {
    echo "<h2 style='color:orange;font-family:sans-serif'>⚠️ Tidak ada perubahan</h2>";
    echo "<p style='font-family:sans-serif'>Admin mungkin sudah di-setup sebelumnya, atau username 'Kimmy' tidak ditemukan di database.</p>";
    echo "<p style='font-family:sans-serif'>Pastikan Anda sudah import file <strong>perpustakaan.sql</strong> ke phpMyAdmin terlebih dahulu.</p>";
}
