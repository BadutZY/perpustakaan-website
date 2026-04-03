CREATE DATABASE IF NOT EXISTS perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE perpustakaan;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(150),
    is_admin TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    publisher VARCHAR(150) NOT NULL,
    author VARCHAR(150) NOT NULL,
    genre VARCHAR(50) NOT NULL,
    synopsis TEXT,
    cover VARCHAR(255) DEFAULT 'IMG/default.jpg',
    stock INT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS borrows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    borrow_date DATE NOT NULL,
    return_due_date DATE NOT NULL,
    return_date DATE DEFAULT NULL,
    status ENUM('borrowed','returned','overdue') DEFAULT 'borrowed',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (username, password, email, is_admin) VALUES
('Kimmy', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'kimmy@admin.com', 1);

INSERT INTO books (title, publisher, author, genre, synopsis, cover, stock) VALUES
('Dilan Dia Adalah Dilanku Tahun 1990', 'Pastel Books', 'Pidi Baiq', 'romance', 'Milea, dia kembali ke tahun 1990 untuk menceritakan seorang laki-laki yang pernah menjadi seseorang yang sangat dicintainya, Dilan. Laki-laki yang mendekatinya bukan dengan seikat bunga atau kata-kata manis untuk menarik perhatiannya. Namun, melalui ramalan seperti tergambarkan pada penggalan cerita di buku ini.', 'IMG/d90.webp', 3),
('Dilan Dia Adalah Dilanku Tahun 1991', 'Pastel Books', 'Pidi Baiq', 'romance', 'Menceritakan kisah cinta Dilan dan Milea setelah mereka resmi berpacaran. Cerita berfokus pada kehidupan sehari-hari mereka di Bandung, termasuk perkelahian Dilan, kekhawatiran Milea, dan kehadiran Yugo, seorang teman baru yang menyukai Milea.', 'IMG/d91.webp', 3),
('Milea Suara Dari Dilan', 'Pastel Books', 'Pidi Baiq', 'romance', 'Melanjutkan kisah cinta Dilan dan Milea setelah peristiwa kematian teman Dilan, Akew. Novel ini menceritakan bagaimana Dilan dan Milea berpisah karena perbedaan pandangan dan keputusan Dilan untuk tetap berada di geng motor.', 'IMG/m.webp', 3),
('Ancika Dia Yang Bersamaku Tahun 1995', 'Pastel Books', 'Pidi Baiq', 'romance', 'Kelanjutan dari seri novel Dilan, yang menceritakan kisah asmara Dilan setelah putus dengan Milea. Fokus utama buku ini adalah kisah cinta Dilan dengan Ancika Mehrunisa Rabu di tahun 1995.', 'IMG/a.webp', 3),
('Dilan Wo Ai Ni 1983', 'Pastel Books', 'Pidi Baiq', 'romance', 'Mengisahkan Dilan yang kembali ke Bandung setelah tinggal di Timor Timur bersama ayahnya. Di sekolah, ia bertemu dengan Mei Lien, murid pindahan dari Semarang.', 'IMG/d83.webp', 3),
('Laskar Pelangi', 'Bentang Pustaka', 'Andrea Hirata', 'roman', 'Mengisahkan tentang kehidupan dari 10 anak hebat yang mempunyai semangat juang yang tinggi untuk tetap melanjutkan sekolah di kampung Gantung, Kepulauan Bangka Belitung.', 'IMG/lp.jpeg', 3),
('Pulang', 'Jakarta Republika', 'Tere Liye', 'fiksi', 'Mengisahkan perjalanan hidup Bujang, seorang anak laki-laki dari pedalaman Sumatra. Sejak kecil, ia hidup dalam kesederhanaan, namun kemampuannya dalam berburu menarik perhatian Teuku Muda.', 'IMG/p.jpg', 3),
('Mariposa', 'Coconut Books', 'Luluk HF', 'romance', 'Menceritakan kisah cinta antara Acha, gadis ceria yang berusaha mendekati Iqbal, seorang pemuda dingin dan pendiam.', 'IMG/m.jpg', 3),
('Mariposa 2 Part 1', 'Coconut Books', 'Luluk HF', 'romance', 'Menceritakan kisah cinta Acha dan Iqbal setelah lulus SMA. Mereka masih menjalin hubungan, namun Iqbal kuliah kedokteran dan Acha memutuskan untuk gap year.', 'IMG/m2p1.jpg', 3),
('Mariposa 2 Part 2', 'Coconut Books', 'Luluk HF', 'romance', 'Acha dengan kekasihnya sudah di ujung tanduk. Kali ini pertengkarannya dengan Iqbal cukup serius dan masalahnya bukan lagi main-main.', 'IMG/m2p2.png', 3),
('Negeri 5 Menara', 'Gramedia Pustaka Utama', 'Ahmad Fuadi', 'inspiratif', 'Mengisahkan perjalanan Alif, seorang remaja dari Maninjau, yang menuntut ilmu di pesantren dan meraih mimpi besar dengan semangat Man Jadda Wajada.', 'IMG/n5.jpg', 3),
('Saman', 'Kepustakaan Populer Gramedia', 'Ayu Utami', 'politik', 'Mengangkat kisah empat perempuan dan seorang mantan pastor dalam konteks represi politik dan eksplorasi seksualitas di era Orde Baru.', 'IMG/s.jpg', 3),
('Badai Pasti Berlalu', 'Gramedia Pustaka Utama', 'Marga T', 'romance', 'Kisah Siska yang mengalami patah hati dan terjebak dalam hubungan manipulatif, namun akhirnya menemukan harapan baru.', 'IMG/bpb.webp', 3),
('Harimau! Harimau!', 'Pustaka Jaya', 'Mochtar Lubis', 'adventure', 'Sekelompok penebang damar menghadapi serangan harimau di hutan, menguji kepemimpinan dan kepercayaan mereka.', 'IMG/hh.jpg', 3),
('Belenggu', 'Poedjangga Baroe / Dian Rakyat', 'Armijn Pane', 'psikologis', 'Mengisahkan konflik batin seorang dokter antara istri modernnya dan mantan kekasih masa lalunya.', 'IMG/b.jpg', 3),
('Laut Bercerita', 'Kepustakaan Populer Gramedia', 'Leila S. Chudori', 'politik', 'Bercerita tentang aktivis mahasiswa yang hilang pada masa Orde Baru dan perjuangan keluarganya mencari kebenaran.', 'IMG/lb.jpg', 3),
('Daun yang Jatuh Tak Pernah Membenci Angin', 'Gramedia Pustaka Utama', 'Tere Liye', 'romance', 'Kisah Tania, gadis miskin yang jatuh cinta pada pria dewasa yang membantunya keluar dari kemiskinan.', 'IMG/d.jpg', 3),
('Dear Nathan', 'Best Media', 'Erisca Febriani', 'romance', 'Kisah cinta antara Salma, siswi baru yang disiplin, dan Nathan, siswa nakal dengan masa lalu kelam.', 'IMG/dn.jpg', 3),
('Si Putih', 'Gramedia Pustaka Utama', 'Tere Liye', 'adventure', 'Mengisahkan petualangan Si Putih, kucing kuno dari klan Polaris, dalam pencarian masa lalunya.', 'IMG/sp.jpg', 3),
('Tanah Surga Merah', 'Gramedia Pustaka Utama', 'Arafat Nur', 'drama', 'Kisah Murad, mantan anggota GAM, yang kembali ke kampung halamannya dan menghadapi realitas pasca-konflik.', 'IMG/tsm.jpg', 3),
('Penjelajahan Antariksa', 'Gramedia Pustaka Utama', 'Djokolelono', 'sci-fi', 'Perjuangan manusia mencari planet layak huni setelah meninggalkan bumi, namun menghadapi konflik dengan penghuni planet baru.', 'IMG/pa.jpg', 3),
('Destination Jakarta 2040', 'Bhuana Ilmu Populer', 'Mashuri', 'sci-fi', 'Ilmuwan astrofisika Indonesia mengalami perjalanan waktu ke tahun 2040 dan menghadapi tantangan masa depan.', 'IMG/dj.jpg', 3),
('Perang Bali', 'Dolphin', 'Gusti Ngurah Phinda', 'sejarah', 'Mengisahkan perjuangan Gusti Ngurah Pindha dalam perang gerilya melawan Belanda di Bali pada tahun 1946.', 'IMG/pb.jpg', 3),
('Khotbah di Atas Bukit', 'Balai Pustaka', 'Kuntowijoyo', 'religius', 'Novel alegoris tentang pencarian spiritual dan makna hidup dalam konteks masyarakat modern.', 'IMG/kdb.jpg', 3),
('Ronggeng Dukuh Paruk', 'Gramedia Pustaka Utama', 'Ahmad Tohari', 'sosial', 'Kisah Srintil, penari ronggeng dari Dukuh Paruk, yang menghadapi dilema antara tradisi dan modernitas.', 'IMG/rd.jpg', 3);
