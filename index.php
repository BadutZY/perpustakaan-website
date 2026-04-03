<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.12/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="icon" href="IMG/book-logo.png" type="image/x-icon">
</head>

<body>
    <!-- HEADER -->
    <header class="header" id="header">
        <div class="container header-inner">
            <a href="index.php" class="logo">
                <div class="logo-icon"><i class="fas fa-book-open"></i></div>
                <span>Perpustakaan</span>
            </a>
            <nav class="nav" id="nav">
                <div class="nav-user" id="nav-user">
                    <div class="user-chip" id="user-chip" style="display:none">
                        <i class="fas fa-circle-user"></i>
                        <span id="username-display">User</span>
                        <span class="role-badge" id="role-badge"></span>
                    </div>
                </div>
                <button class="btn-nav" id="btn-auth" onclick="handleAuth()">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </nav>
            <button class="hamburger" id="hamburger" aria-label="Menu" aria-expanded="false">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </div>
    </header>

    <!-- HERO -->
    <section class="hero" id="hero">
        <div class="hero-bg"></div>
        <div class="container hero-content">
            <h1 class="hero-title">Jelajahi Ribuan<br><span>Koleksi Buku</span></h1>
            <p class="hero-sub">Platform perpustakaan digital terlengkap. Pinjam, baca, dan kembalikan buku dengan mudah kapan saja.</p>
            <div class="hero-stats">
                <div class="stat-item"><span id="stat-books">25</span><small>Koleksi Buku</small></div>
                <div class="stat-item"><span id="stat-genres">12</span><small>Genre</small></div>
            </div>
        </div>
    </section>

    <!-- TABS -->
    <main class="main">
        <div class="container">
            <div class="tabs-wrap">
                <div class="tabs" id="tabs">
                    <button class="tab active" data-tab="catalog"><i class="fas fa-th-large"></i> Katalog</button>
                    <button class="tab user-only" data-tab="borrowed" style="display:none"><i class="fas fa-bookmark"></i> Dipinjam <span class="badge" id="borrow-badge"></span></button>
                    <button class="tab admin-only" data-tab="admin" style="display:none"><i class="fas fa-plus-circle"></i> Tambah Buku</button>
                    <button class="tab admin-only" data-tab="admin-borrows" style="display:none"><i class="fas fa-list-alt"></i> Semua Peminjaman</button>
                    <button class="tab admin-only" data-tab="user-mgmt" style="display:none"><i class="fas fa-users-cog"></i> Kelola User</button>
                </div>
            </div>

            <!-- CATALOG TAB -->
            <div class="tab-content active" id="tab-catalog">
                <div class="filter-bar">
                    <div class="search-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search-input" placeholder="Cari judul, penulis, penerbit...">
                        <button onclick="filterBooks()" class="search-btn"><i class="fas fa-arrow-right"></i></button>
                    </div>
                    <select id="genre-filter" onchange="filterBooks()">
                        <option value="all">Semua Genre</option>
                        <option value="fiksi">Fiksi</option>
                        <option value="romance">Romance</option>
                        <option value="roman">Roman</option>
                        <option value="sci-fi">Sci-Fi</option>
                        <option value="inspiratif">Inspiratif</option>
                        <option value="sejarah">Sejarah</option>
                        <option value="politik">Politik</option>
                        <option value="adventure">Adventure</option>
                        <option value="psikologis">Psikologis</option>
                        <option value="drama">Drama</option>
                        <option value="religius">Religius</option>
                        <option value="sosial">Sosial</option>
                    </select>
                </div>
                <div class="book-grid" id="book-grid">
                    <div class="loading-grid">
                        <div class="spinner-lg"></div>
                        <p>Memuat katalog buku...</p>
                    </div>
                </div>
            </div>

            <!-- BORROWED TAB -->
            <div class="tab-content" id="tab-borrowed">
                <div class="section-header">
                    <h2><i class="fas fa-bookmark"></i> Buku yang Sedang Dipinjam</h2>
                </div>
                <div class="table-wrap">
                    <table class="data-table" id="borrow-table">
                        <thead>
                            <tr>
                                <th>Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Tenggat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="borrow-tbody"></tbody>
                    </table>
                </div>
            </div>

            <!-- ADMIN ADD BOOK TAB -->
            <div class="tab-content" id="tab-admin">
                <div class="section-header">
                    <h2><i class="fas fa-plus-circle"></i> Tambah Buku Baru</h2>
                </div>
                <div class="form-card">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Judul Buku <span class="req">*</span></label>
                            <input type="text" id="n-title" placeholder="Masukkan judul buku">
                        </div>
                        <div class="form-group">
                            <label>Penerbit <span class="req">*</span></label>
                            <input type="text" id="n-publisher" placeholder="Masukkan penerbit">
                        </div>
                        <div class="form-group">
                            <label>Penulis <span class="req">*</span></label>
                            <input type="text" id="n-author" placeholder="Masukkan nama penulis">
                        </div>
                        <div class="form-group">
                            <label>Genre <span class="req">*</span></label>
                            <select id="n-genre">
                                <option value="fiksi">Fiksi</option>
                                <option value="romance">Romance</option>
                                <option value="roman">Roman</option>
                                <option value="sci-fi">Sci-Fi</option>
                                <option value="inspiratif">Inspiratif</option>
                                <option value="sejarah">Sejarah</option>
                                <option value="politik">Politik</option>
                                <option value="adventure">Adventure</option>
                                <option value="psikologis">Psikologis</option>
                                <option value="drama">Drama</option>
                                <option value="religius">Religius</option>
                                <option value="sosial">Sosial</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Stok Buku</label>
                            <input type="number" id="n-stock" value="1" min="1" max="99">
                        </div>
                        <div class="form-group">
                            <label>Cover Buku <span class="req">*</span></label>
                            <div class="file-upload" id="file-upload-add">
                                <input type="file" id="n-cover" accept="image/*" onchange="previewCover(this,'preview-add')">
                                <div class="file-upload-ui"><i class="fas fa-cloud-upload-alt"></i><span>Klik atau drag gambar</span><small>JPG, PNG, WEBP max 5MB</small></div>
                                <img id="preview-add" class="cover-preview" alt="preview">
                            </div>
                        </div>
                        <div class="form-group full">
                            <label>Sinopsis <span class="req">*</span></label>
                            <textarea id="n-synopsis" rows="4" placeholder="Masukkan sinopsis buku..."></textarea>
                        </div>
                    </div>
                    <button class="btn-primary" onclick="addBook()"><i class="fas fa-plus"></i> Tambah Buku</button>
                </div>
            </div>

            <!-- ADMIN BORROWS TAB -->
            <div class="tab-content" id="tab-admin-borrows">
                <div class="section-header">
                    <h2><i class="fas fa-list-alt"></i> Semua Peminjaman</h2>
                    <button class="btn-outline" onclick="loadAdminBorrows()"><i class="fas fa-sync-alt"></i> Refresh</button>
                </div>
                <div class="filter-bar">
                    <input type="text" id="borrow-search" placeholder="Cari peminjam atau judul buku..." oninput="filterBorrowsTable()">
                    <select id="borrow-status-filter" onchange="filterBorrowsTable()">
                        <option value="all">Semua Status</option>
                        <option value="borrowed">Dipinjam</option>
                        <option value="overdue">Terlambat</option>
                        <option value="returned">Dikembalikan</option>
                    </select>
                </div>
                <div class="table-wrap">
                    <table class="data-table" id="admin-borrow-table">
                        <thead>
                            <tr>
                                <th>Buku</th>
                                <th>Peminjam</th>
                                <th>Tgl Pinjam</th>
                                <th>Tenggat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="admin-borrow-tbody"></tbody>
                    </table>
                </div>
            </div>

            <!-- USER MANAGEMENT TAB -->
            <div class="tab-content" id="tab-user-mgmt">
                <div class="section-header">
                    <h2><i class="fas fa-users-cog"></i> Kelola Pengguna</h2>
                    <button class="btn-outline" onclick="loadUsers()"><i class="fas fa-sync-alt"></i> Refresh</button>
                </div>
                <div class="table-wrap">
                    <table class="data-table" id="user-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Pinjaman Aktif</th>
                                <th>Total Pinjam</th>
                                <th>Bergabung</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="user-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container footer-inner">
            <div class="footer-logo"><i class="fas fa-book-open"></i> Perpustakaan Digital</div>
            <div class="footer-copy">&copy; 2025 Perpustakaan Digital. Hak Cipta Dilindungi.</div>
        </div>
    </footer>

    <!-- MODAL BOOK DETAIL -->
    <div class="modal-overlay" id="book-modal">
        <div class="modal" id="book-modal-inner">
            <button class="modal-close" onclick="closeModal('book-modal')"><i class="fas fa-times"></i></button>
            <div id="book-modal-body"></div>
        </div>
    </div>

    <!-- MODAL EDIT USER -->
    <div class="modal-overlay" id="user-modal">
        <div class="modal modal-sm" id="user-modal-inner">
            <button class="modal-close" onclick="closeModal('user-modal')"><i class="fas fa-times"></i></button>
            <h3 class="modal-title"><i class="fas fa-user-edit"></i> Edit Pengguna</h3>
            <div class="form-group"><label>Username</label><input type="text" id="edit-username" placeholder="Username baru"></div>
            <div class="form-group"><label>Email</label><input type="email" id="edit-email" placeholder="Email (opsional)"></div>
            <div class="form-group"><label>Password Baru <small>(kosongkan jika tidak ingin diubah)</small></label>
                <div class="input-wrap"><i class="fas fa-lock icon"></i><input type="password" id="edit-password" placeholder="Password baru"><i class="fas fa-eye toggle-pw" onclick="togglePwModal()"></i></div>
            </div>
            <input type="hidden" id="edit-user-id">
            <div style="display:flex;gap:10px;margin-top:20px">
                <button class="btn-primary" style="flex:1" onclick="saveUser()"><i class="fas fa-save"></i> Simpan</button>
                <button class="btn-danger" style="flex:1" onclick="closeModal('user-modal')"><i class="fas fa-times"></i> Batal</button>
            </div>
        </div>
    </div>

    <script src="JS/app.js"></script>
</body>

</html>