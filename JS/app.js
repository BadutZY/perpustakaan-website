let currentUser = null;
let allBorrows = [];
let allBooks = [];

document.addEventListener('DOMContentLoaded', async () => {
    initHeader();
    await checkSession();
    await loadBooks();
    initTabs();
    initSearch();
    initMobileMenu();
});

async function checkSession() {
    try {
        const res = await fetch('api/auth.php?action=check');
        const data = await res.json();
        if (data.success) {
            currentUser = data.user;
            updateUI();
        }
    } catch (e) {

    }
}

function updateUI() {
    const userChip = document.getElementById('user-chip');
    const btnAuth = document.getElementById('btn-auth');
    const hero = document.getElementById('hero');
    const userDisplay = document.getElementById('username-display');
    const roleBadge = document.getElementById('role-badge');
    const adminTabs = document.querySelectorAll('.admin-only');
    const userTabs = document.querySelectorAll('.user-only');

    if (currentUser) {
        userChip.style.display = 'flex';
        userDisplay.textContent = currentUser.username;

        if (currentUser.is_admin) {
            roleBadge.textContent = 'Admin';
            roleBadge.classList.add('admin');
        } else {
            roleBadge.textContent = 'Member';
        }

        btnAuth.innerHTML = '<i class="fas fa-sign-out-alt"></i> Logout';

        if (currentUser.is_admin) {
            adminTabs.forEach(t => t.style.display = 'flex');
            userTabs.forEach(t => t.style.display = 'none');
            if (hero) hero.style.display = 'none';
        } else {
            adminTabs.forEach(t => t.style.display = 'none');
            userTabs.forEach(t => t.style.display = 'flex');
        }
    } else {
        userChip.style.display = 'none';
        btnAuth.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login';
        adminTabs.forEach(t => t.style.display = 'none');
        userTabs.forEach(t => t.style.display = 'none');
    }
}

async function handleAuth() {
    if (currentUser) {
        const r = await Swal.fire({
            title: 'Logout?',
            text: 'Apakah Anda yakin?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal'
        });

        if (!r.isConfirmed) return;

        await fetch('api/auth.php', {
            method: 'POST',
            body: new URLSearchParams({ action: 'logout' })
        });

        currentUser = null;
        allBorrows = [];
        updateUI();
        await loadBooks();
        switchTab('catalog');

        const badge = document.getElementById('borrow-badge');
        if (badge) badge.textContent = '';

        Swal.fire({
            title: 'Logout Berhasil',
            icon: 'success',
            timer: 1400,
            showConfirmButton: false
        });
    } else {
        window.location.href = 'login.php';
    }
}

async function loadBooks() {
    const grid = document.getElementById('book-grid');
    const genre = document.getElementById('genre-filter').value;
    const search = document.getElementById('search-input').value.trim();

    grid.innerHTML = `
        <div class="loading-grid">
            <div class="spinner-lg"></div>
            <p>Memuat buku...</p>
        </div>`;

    try {
        const url = 'api/books.php?action=list'
            + '&genre=' + encodeURIComponent(genre)
            + '&search=' + encodeURIComponent(search);
        const res = await fetch(url);
        const data = await res.json();

        if (!data.success) throw new Error(data.message);

        allBooks = data.books;
        renderBooks(allBooks);
        updateStats(allBooks);
    } catch (e) {
        grid.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-exclamation-circle"></i>
                <h3>Gagal memuat buku</h3>
                <p>${e.message}</p>
            </div>`;
    }
}

function renderBooks(books) {
    const grid = document.getElementById('book-grid');
    const isAdmin = currentUser && currentUser.is_admin;

    if (!books.length) {
        grid.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h3>Buku tidak ditemukan</h3>
                <p>Coba kata kunci atau filter lain.</p>
            </div>`;
        return;
    }

    grid.innerHTML = books.map(book => {
        const avail = book.available_stock > 0;
        const mine = book.is_borrowed_by_me;

        let stockPill;
        if (mine) {
            stockPill = `<span class="stock-pill mine"><i class="fas fa-bookmark"></i> Dipinjam</span>`;
        } else if (avail) {
            stockPill = `<span class="stock-pill available"><i class="fas fa-check"></i> Tersedia</span>`;
        } else {
            stockPill = `<span class="stock-pill unavailable"><i class="fas fa-times"></i> Habis</span>`;
        }

        let actionBtn;
        if (isAdmin) {
            actionBtn = `<button class="btn-borrow" onclick="event.stopPropagation();showBookDetail(${book.id})">
                            <i class="fas fa-edit"></i> Edit
                         </button>`;
        } else if (mine) {
            actionBtn = `<button class="btn-borrow borrowed" disabled>
                            <i class="fas fa-bookmark"></i> Dipinjam
                         </button>`;
        } else if (avail) {
            actionBtn = `<button class="btn-borrow" onclick="event.stopPropagation();borrowBook(${book.id})">
                            <i class="fas fa-hand-holding-heart"></i> Pinjam
                         </button>`;
        } else {
            actionBtn = `<button class="btn-borrow borrowed" disabled>
                            <i class="fas fa-times"></i> Habis
                         </button>`;
        }

        return `
        <div class="book-card" onclick="showBookDetail(${book.id})">
            <div class="book-card-cover">
                <img src="${escHtml(book.cover)}"
                     alt="${escHtml(book.title)}"
                     loading="lazy"
                     onerror="this.src='CSS/perpus.jpg'">
                <div class="book-card-pills">
                    <span class="genre-pill">${escHtml(book.genre)}</span>
                    ${stockPill}
                </div>
            </div>
            <div class="book-card-info">
                <div class="book-card-title">${escHtml(book.title)}</div>
                <div class="book-card-author">${escHtml(book.author)}</div>
                <div class="book-card-actions">
                    <button class="btn-detail" onclick="event.stopPropagation();showBookDetail(${book.id})">
                        <i class="fas fa-eye"></i> Detail
                    </button>
                    ${actionBtn}
                </div>
            </div>
        </div>`;
    }).join('');
}

let filterTimer;
function filterBooks() {
    clearTimeout(filterTimer);
    filterTimer = setTimeout(loadBooks, 350);
}

async function showBookDetail(bookId) {
    const book = allBooks.find(b => b.id == bookId);
    if (!book) return;

    const isAdmin = currentUser && currentUser.is_admin;
    const mine = book.is_borrowed_by_me;
    const avail = book.available_stock > 0;

    let actionBtn = '';
    if (!isAdmin) {
        if (mine) {
            actionBtn = `<button class="btn-success" style="opacity:0.7;cursor:not-allowed" disabled>
                            <i class="fas fa-bookmark"></i> Sedang Dipinjam
                         </button>`;
        } else if (avail) {
            actionBtn = `<button class="btn-primary" onclick="borrowBook(${book.id});closeModal('book-modal')">
                            <i class="fas fa-hand-holding-heart"></i> Pinjam Buku Ini
                         </button>`;
        } else {
            actionBtn = `<button class="btn-danger" style="opacity:0.6;cursor:not-allowed" disabled>
                            <i class="fas fa-times"></i> Stok Habis
                         </button>`;
        }
    }

    let adminEdit = '';
    if (isAdmin) {
        const genreList = [
            'fiksi', 'romance', 'roman', 'sci-fi',
            'inspiratif', 'sejarah', 'politik', 'adventure',
            'psikologis', 'drama', 'religius', 'sosial'
        ];
        const genreOptions = genreList
            .map(g => `<option value="${g}" ${book.genre === g ? 'selected' : ''}>
                            ${g.charAt(0).toUpperCase() + g.slice(1)}
                        </option>`)
            .join('');

        adminEdit = `
        <div class="modal-edit-section">
            <h3><i class="fas fa-edit"></i> &nbsp;Edit Buku</h3>
            <input type="hidden" id="e-id" value="${book.id}">
            <div class="form-grid">
                <div class="form-group">
                    <label>Judul <span class="req">*</span></label>
                    <input type="text" id="e-title" value="${escHtml(book.title)}">
                </div>
                <div class="form-group">
                    <label>Penerbit <span class="req">*</span></label>
                    <input type="text" id="e-publisher" value="${escHtml(book.publisher)}">
                </div>
                <div class="form-group">
                    <label>Penulis <span class="req">*</span></label>
                    <input type="text" id="e-author" value="${escHtml(book.author)}">
                </div>
                <div class="form-group">
                    <label>Genre</label>
                    <select id="e-genre">${genreOptions}</select>
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" id="e-stock" value="${book.stock}" min="1">
                </div>
                <div class="form-group">
                    <label>Ganti Cover</label>
                    <div class="file-upload">
                        <input type="file" id="e-cover" accept="image/*"
                               onchange="previewCover(this,'e-preview')">
                        <div class="file-upload-ui">
                            <i class="fas fa-image"></i>
                            <span>Klik untuk ganti</span>
                            <small>Kosongkan jika tidak ingin mengubah</small>
                        </div>
                        <img id="e-preview" class="cover-preview" alt="preview">
                    </div>
                </div>
                <div class="form-group full">
                    <label>Sinopsis <span class="req">*</span></label>
                    <textarea id="e-synopsis" rows="3">${escHtml(book.synopsis)}</textarea>
                </div>
            </div>
            <div style="display:flex;gap:10px">
                <button class="btn-primary" onclick="editBook()">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <button class="btn-danger" onclick="deleteBook(${book.id})">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        </div>`;
    }

    document.getElementById('book-modal-body').innerHTML = `
    <div class="detail-grid">
        <div class="detail-cover">
            <img src="${escHtml(book.cover)}"
                 alt="${escHtml(book.title)}"
                 onerror="this.src='CSS/perpus.jpg'">
        </div>
        <div class="detail-info">
            <span class="detail-genre">${escHtml(book.genre)}</span>
            <div class="detail-title">${escHtml(book.title)}</div>
            <div class="detail-meta">
                <span><strong>Penulis:</strong> ${escHtml(book.author)}</span>
                <span><strong>Penerbit:</strong> ${escHtml(book.publisher)}</span>
                <span><strong>Stok Tersedia:</strong> ${book.available_stock} / ${book.stock}</span>
            </div>
            <div class="detail-synopsis">
                <h4>Sinopsis</h4>
                <p>${escHtml(book.synopsis)}</p>
            </div>
            <div class="detail-actions">${actionBtn}</div>
        </div>
    </div>
    ${adminEdit}`;

    openModal('book-modal');
}

async function borrowBook(bookId) {
    if (!currentUser) {
        const r = await Swal.fire({
            title: 'Login Diperlukan',
            text: 'Silakan login untuk meminjam buku.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Login Sekarang',
            cancelButtonText: 'Batal'
        });
        if (r.isConfirmed) window.location.href = 'login.php';
        return;
    }

    const book = allBooks.find(b => b.id == bookId);
    const r = await Swal.fire({
        title: 'Pinjam Buku?',
        html: `<b>${book ? escHtml(book.title) : ''}</b><br><small>Durasi: 14 hari</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Pinjam!',
        cancelButtonText: 'Batal'
    });

    if (!r.isConfirmed) return;

    const fd = new FormData();
    fd.append('book_id', bookId);

    const res = await fetch('api/borrows.php?action=borrow', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.success) {
        Swal.fire({
            title: 'Berhasil!',
            text: data.message,
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
        await loadBooks();
    } else {
        Swal.fire({ title: 'Gagal', text: data.message, icon: 'error' });
    }
}

async function returnBook(borrowId, title) {
    const r = await Swal.fire({
        title: 'Kembalikan Buku?',
        html: `<b>${escHtml(title)}</b>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kembalikan',
        cancelButtonText: 'Batal'
    });

    if (!r.isConfirmed) return;

    const fd = new FormData();
    fd.append('borrow_id', borrowId);

    const res = await fetch('api/borrows.php?action=return', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.success) {
        Swal.fire({
            title: 'Dikembalikan!',
            text: data.message,
            icon: 'success',
            timer: 1800,
            showConfirmButton: false
        });
        await loadMyBorrows();
        await loadBooks();
    } else {
        Swal.fire({ title: 'Gagal', text: data.message, icon: 'error' });
    }
}

async function loadMyBorrows() {
    const tbody = document.getElementById('borrow-tbody');
    tbody.innerHTML = `
        <tr>
            <td colspan="5" style="text-align:center;padding:32px">
                <div class="spinner-lg" style="margin:auto"></div>
            </td>
        </tr>`;

    const res = await fetch('api/borrows.php?action=my');
    const data = await res.json();

    if (!data.success) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align:center;color:var(--text2)">
                    Gagal memuat data.
                </td>
            </tr>`;
        return;
    }

    const borrows = data.borrows;
    const badge = document.getElementById('borrow-badge');
    badge.textContent = borrows.length || '';

    if (!borrows.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align:center;padding:40px;color:var(--text2)">
                    <i class="fas fa-bookmark" style="font-size:32px;display:block;margin-bottom:10px;opacity:0.3"></i>
                    Anda belum meminjam buku.
                </td>
            </tr>`;
        return;
    }

    tbody.innerHTML = borrows.map(b => {
        const due = new Date(b.return_due_date);
        const today = new Date();
        const daysLeft = Math.ceil((due - today) / 86400000);

        let dlClass = 'ok';
        if (b.status === 'overdue') dlClass = 'overdue';
        else if (daysLeft <= 3) dlClass = 'warning';

        const dlText = b.status === 'overdue'
            ? `Terlambat ${Math.abs(daysLeft)} hari`
            : `${daysLeft} hari lagi`;

        return `
        <tr>
            <td>
                <div class="table-book">
                    <img class="table-book-thumb"
                         src="${escHtml(b.cover)}"
                         alt=""
                         onerror="this.src='CSS/perpus.jpg'">
                    <div class="table-book-info">
                        <strong>${escHtml(b.title)}</strong>
                        <span>${escHtml(b.author)}</span>
                    </div>
                </div>
            </td>
            <td>${fmt(b.borrow_date)}</td>
            <td><span class="days-left ${dlClass}">${fmt(b.return_due_date)}</span></td>
            <td><span class="status-badge status-${b.status}">${statusLabel(b.status)}</span></td>
            <td>
                <button class="btn-icon ret"
                        title="Kembalikan"
                        onclick="returnBook(${b.id},'${escHtml(b.title).replace(/'/g, "\\'")}')">
                    <i class="fas fa-undo"></i>
                </button>
            </td>
        </tr>`;
    }).join('');
}

async function addBook() {
    const title = document.getElementById('n-title').value.trim();
    const publisher = document.getElementById('n-publisher').value.trim();
    const author = document.getElementById('n-author').value.trim();
    const genre = document.getElementById('n-genre').value;
    const synopsis = document.getElementById('n-synopsis').value.trim();
    const stock = document.getElementById('n-stock').value;
    const cover = document.getElementById('n-cover').files[0];

    if (!title || !publisher || !author || !genre || !synopsis || !cover) {
        return Swal.fire({
            title: 'Lengkapi Form',
            text: 'Semua kolom wajib diisi termasuk cover!',
            icon: 'warning'
        });
    }

    const fd = new FormData();
    fd.append('title', title);
    fd.append('publisher', publisher);
    fd.append('author', author);
    fd.append('genre', genre);
    fd.append('synopsis', synopsis);
    fd.append('stock', stock);
    fd.append('cover', cover);

    const res = await fetch('api/books.php?action=add', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.success) {
        Swal.fire({
            title: 'Berhasil!',
            text: data.message,
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
        ['n-title', 'n-publisher', 'n-author', 'n-synopsis'].forEach(id => {
            document.getElementById(id).value = '';
        });
        document.getElementById('n-cover').value = '';
        document.getElementById('preview-add').classList.remove('show');
        await loadBooks();
    } else {
        Swal.fire({ title: 'Gagal', text: data.message, icon: 'error' });
    }
}

async function editBook() {
    const id = document.getElementById('e-id').value;
    const title = document.getElementById('e-title').value.trim();
    const publisher = document.getElementById('e-publisher').value.trim();
    const author = document.getElementById('e-author').value.trim();
    const genre = document.getElementById('e-genre').value;
    const synopsis = document.getElementById('e-synopsis').value.trim();
    const stock = document.getElementById('e-stock').value;
    const cover = document.getElementById('e-cover').files[0];

    if (!title || !publisher || !author || !synopsis) {
        return Swal.fire({
            title: 'Lengkapi Form',
            text: 'Semua kolom wajib diisi!',
            icon: 'warning'
        });
    }

    const fd = new FormData();
    fd.append('id', id);
    fd.append('title', title);
    fd.append('publisher', publisher);
    fd.append('author', author);
    fd.append('genre', genre);
    fd.append('synopsis', synopsis);
    fd.append('stock', stock);
    if (cover) fd.append('cover', cover);

    const res = await fetch('api/books.php?action=edit', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.success) {
        Swal.fire({
            title: 'Berhasil!',
            text: data.message,
            icon: 'success',
            timer: 1800,
            showConfirmButton: false
        });
        closeModal('book-modal');
        await loadBooks();
    } else {
        Swal.fire({ title: 'Gagal', text: data.message, icon: 'error' });
    }
}

async function deleteBook(bookId) {
    const r = await Swal.fire({
        title: 'Hapus Buku?',
        text: 'Tindakan ini tidak dapat dibatalkan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444'
    });

    if (!r.isConfirmed) return;

    const fd = new FormData();
    fd.append('id', bookId);

    const res = await fetch('api/books.php?action=delete', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.success) {
        Swal.fire({
            title: 'Dihapus!',
            text: data.message,
            icon: 'success',
            timer: 1800,
            showConfirmButton: false
        });
        closeModal('book-modal');
        await loadBooks();
    } else {
        Swal.fire({ title: 'Gagal', text: data.message, icon: 'error' });
    }
}

async function loadAdminBorrows() {
    const tbody = document.getElementById('admin-borrow-tbody');
    tbody.innerHTML = `
        <tr>
            <td colspan="6" style="text-align:center;padding:32px">
                <div class="spinner-lg" style="margin:auto"></div>
            </td>
        </tr>`;

    const res = await fetch('api/borrows.php?action=list');
    const data = await res.json();

    if (!data.success) return;

    allBorrows = data.borrows;
    renderAdminBorrows(allBorrows);
}

function renderAdminBorrows(borrows) {
    const tbody = document.getElementById('admin-borrow-tbody');

    if (!borrows.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" style="text-align:center;padding:40px;color:var(--text2)">
                    Belum ada peminjaman.
                </td>
            </tr>`;
        return;
    }

    tbody.innerHTML = borrows.map(b => {
        const due = new Date(b.return_due_date);
        const today = new Date();
        const daysLeft = Math.ceil((due - today) / 86400000);

        let dlClass = 'ok';
        if (b.status === 'overdue') dlClass = 'overdue';
        else if (daysLeft <= 3 && b.status === 'borrowed') dlClass = 'warning';

        const dateDisplay = b.status === 'returned'
            ? fmt(b.return_date || b.return_due_date)
            : fmt(b.return_due_date);

        const returnBtn = b.status !== 'returned'
            ? `<button class="btn-icon ret"
                       title="Tandai Dikembalikan"
                       onclick="adminReturn(${b.id},'${escHtml(b.title).replace(/'/g, "\\'")}')">
                   <i class="fas fa-undo"></i>
               </button>`
            : `<span style="color:var(--text3);font-size:12px">–</span>`;

        return `
        <tr>
            <td>
                <div class="table-book">
                    <img class="table-book-thumb"
                         src="${escHtml(b.cover)}"
                         alt=""
                         onerror="this.src='CSS/perpus.jpg'">
                    <div class="table-book-info">
                        <strong>${escHtml(b.title)}</strong>
                        <span>${escHtml(b.author)}</span>
                    </div>
                </div>
            </td>
            <td><strong>${escHtml(b.username)}</strong></td>
            <td>${fmt(b.borrow_date)}</td>
            <td><span class="days-left ${dlClass}">${dateDisplay}</span></td>
            <td><span class="status-badge status-${b.status}">${statusLabel(b.status)}</span></td>
            <td>${returnBtn}</td>
        </tr>`;
    }).join('');
}

function filterBorrowsTable() {
    const q = document.getElementById('borrow-search').value.toLowerCase();
    const status = document.getElementById('borrow-status-filter').value;

    renderAdminBorrows(allBorrows.filter(b => {
        const matchText = !q || b.title.toLowerCase().includes(q) || b.username.toLowerCase().includes(q);
        const matchStatus = status === 'all' || b.status === status;
        return matchText && matchStatus;
    }));
}

async function adminReturn(borrowId, title) {
    const r = await Swal.fire({
        title: 'Konfirmasi',
        html: `Tandai <b>${escHtml(title)}</b> sebagai dikembalikan?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    });

    if (!r.isConfirmed) return;

    const fd = new FormData();
    fd.append('borrow_id', borrowId);

    const res = await fetch('api/borrows.php?action=admin_return', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.success) {
        Swal.fire({
            title: 'Berhasil!',
            text: data.message,
            icon: 'success',
            timer: 1800,
            showConfirmButton: false
        });
        await loadAdminBorrows();
        await loadBooks();
    } else {
        Swal.fire({ title: 'Gagal', text: data.message, icon: 'error' });
    }
}

async function loadUsers() {
    const tbody = document.getElementById('user-tbody');
    tbody.innerHTML = `
        <tr>
            <td colspan="7" style="text-align:center;padding:32px">
                <div class="spinner-lg" style="margin:auto"></div>
            </td>
        </tr>`;

    const res = await fetch('api/users.php?action=list');
    const data = await res.json();

    if (!data.success) return;

    if (!data.users.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" style="text-align:center;padding:40px;color:var(--text2)">
                    Belum ada pengguna.
                </td>
            </tr>`;
        return;
    }

    tbody.innerHTML = data.users.map(u => `
    <tr>
        <td>
            <div style="display:flex;align-items:center;gap:10px">
                <div style="width:36px;height:36px;border-radius:50%;
                            background:linear-gradient(135deg,var(--secondary),var(--purple));
                            display:flex;align-items:center;justify-content:center;
                            font-weight:800;font-size:14px;color:white;flex-shrink:0">
                    ${escHtml(u.username.charAt(0).toUpperCase())}
                </div>
                <strong>${escHtml(u.username)}</strong>
            </div>
        </td>
        <td>${u.email ? escHtml(u.email) : '<span style="color:var(--text3)">–</span>'}</td>
        <td>
            <span class="status-badge ${u.is_admin ? 'status-overdue' : 'status-borrowed'}">
                ${u.is_admin ? 'Admin' : 'Member'}
            </span>
        </td>
        <td>
            <strong style="color:${u.active_borrows > 0 ? 'var(--accent)' : 'var(--text)'}">
                ${u.active_borrows}
            </strong>
        </td>
        <td>${u.total_borrows}</td>
        <td>${fmtFull(u.created_at)}</td>
        <td style="display:flex;gap:6px">
            ${!u.is_admin
            ? `<button class="btn-icon edit" title="Edit"
                           onclick="openEditUser(${u.id},'${escHtml(u.username)}','${escHtml(u.email || '')}')">
                       <i class="fas fa-edit"></i>
                   </button>
                   <button class="btn-icon del" title="Hapus"
                           onclick="deleteUser(${u.id},'${escHtml(u.username)}')">
                       <i class="fas fa-trash"></i>
                   </button>`
            : '<span style="color:var(--text3);font-size:12px">Admin utama</span>'
        }
        </td>
    </tr>`).join('');
}

function openEditUser(id, username, email) {
    document.getElementById('edit-user-id').value = id;
    document.getElementById('edit-username').value = username;
    document.getElementById('edit-email').value = email || '';
    document.getElementById('edit-password').value = '';
    openModal('user-modal');
}

async function saveUser() {
    const id = document.getElementById('edit-user-id').value;
    const username = document.getElementById('edit-username').value.trim();
    const email = document.getElementById('edit-email').value.trim();
    const password = document.getElementById('edit-password').value;

    if (!username) {
        return Swal.fire({
            title: 'Peringatan',
            text: 'Username tidak boleh kosong!',
            icon: 'warning'
        });
    }

    const fd = new FormData();
    fd.append('id', id);
    fd.append('username', username);
    fd.append('email', email);
    fd.append('password', password);

    const res = await fetch('api/users.php?action=edit', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.success) {
        Swal.fire({
            title: 'Berhasil!',
            text: data.message,
            icon: 'success',
            timer: 1800,
            showConfirmButton: false
        });
        closeModal('user-modal');
        await loadUsers();
    } else {
        Swal.fire({ title: 'Gagal', text: data.message, icon: 'error' });
    }
}

async function deleteUser(id, username) {
    const r = await Swal.fire({
        title: `Hapus "${escHtml(username)}"?`,
        text: 'Pastikan tidak ada pinjaman aktif.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444'
    });

    if (!r.isConfirmed) return;

    const fd = new FormData();
    fd.append('id', id);

    const res = await fetch('api/users.php?action=delete', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.success) {
        Swal.fire({
            title: 'Dihapus!',
            text: data.message,
            icon: 'success',
            timer: 1800,
            showConfirmButton: false
        });
        await loadUsers();
    } else {
        Swal.fire({ title: 'Gagal', text: data.message, icon: 'error' });
    }
}

function initTabs() {
    document.querySelectorAll('.tab').forEach(btn => {
        btn.addEventListener('click', () => switchTab(btn.getAttribute('data-tab')));
    });
}

function switchTab(tabId) {
    document.querySelectorAll('.tab').forEach(b => {
        b.classList.toggle('active', b.getAttribute('data-tab') === tabId);
    });
    document.querySelectorAll('.tab-content').forEach(c => {
        c.classList.toggle('active', c.id === 'tab-' + tabId);
    });

    if (tabId === 'borrowed') loadMyBorrows();
    if (tabId === 'admin-borrows') loadAdminBorrows();
    if (tabId === 'user-mgmt') loadUsers();
}

function updateStats(books) {
    const el = document.getElementById('stat-books');
    if (el) el.textContent = books.length;

    const genres = [...new Set(books.map(b => b.genre))].length;
    const eg = document.getElementById('stat-genres');
    if (eg) eg.textContent = genres;
}

function initHeader() {
    window.addEventListener('scroll', () => {
        document.getElementById('header')
            .classList.toggle('scrolled', window.scrollY > 20);
    });
}

function initSearch() {
    const inp = document.getElementById('search-input');
    if (!inp) return;
    inp.addEventListener('keydown', e => {
        if (e.key === 'Enter') filterBooks();
    });
}

function initMobileMenu() {
    const hb = document.getElementById('hamburger');
    const nav = document.getElementById('nav');
    if (!hb) return;

    hb.addEventListener('click', () => {
        const isOpen = nav.classList.toggle('mobile-open');
        hb.classList.toggle('open', isOpen);
        hb.setAttribute('aria-expanded', isOpen);
    });

    document.addEventListener('click', e => {
        if (!nav.contains(e.target) && !hb.contains(e.target)) {
            nav.classList.remove('mobile-open');
            hb.classList.remove('open');
            hb.setAttribute('aria-expanded', false);
        }
    });

    nav.querySelectorAll('button, a').forEach(el => {
        el.addEventListener('click', () => {
            nav.classList.remove('mobile-open');
            hb.classList.remove('open');
            hb.setAttribute('aria-expanded', false);
        });
    });
}

function openModal(id) {
    document.getElementById(id).classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
    document.body.style.overflow = '';
}

document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => {
        if (e.target === o) closeModal(o.id);
    });
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(o => closeModal(o.id));
    }
});

function previewCover(input, previewId) {
    const prev = document.getElementById(previewId);
    if (!input.files[0]) return;
    const r = new FileReader();
    r.onload = e => {
        prev.src = e.target.result;
        prev.classList.add('show');
    };
    r.readAsDataURL(input.files[0]);
}

function togglePwModal() {
    const inp = document.getElementById('edit-password');
    inp.type = inp.type === 'password' ? 'text' : 'password';
}

function escHtml(str) {
    if (str == null) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function fmt(d) {
    if (!d) return '–';
    const dt = new Date(d);
    const m = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return `${dt.getDate()} ${m[dt.getMonth()]} ${dt.getFullYear()}`;
}

function fmtFull(d) {
    if (!d) return '–';
    const dt = new Date(d);
    return `${String(dt.getDate()).padStart(2, '0')}/${String(dt.getMonth() + 1).padStart(2, '0')}/${dt.getFullYear()}`;
}

function statusLabel(s) {
    return {
        borrowed: 'Dipinjam',
        overdue: 'Terlambat',
        returned: 'Dikembalikan'
    }[s] || s;
}
