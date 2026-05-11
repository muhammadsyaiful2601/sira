// admin/js/verifikasi.js
document.addEventListener('DOMContentLoaded', function() {
    // Konfirmasi hapus (jika ada tombol hapus nanti)
    const hapusButtons = document.querySelectorAll('.btn-hapus');
    hapusButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat dibatalkan.')) {
                e.preventDefault();
            }
        });
    });

    // Filter dan pencarian tabel
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const tableRows = document.querySelectorAll('#lamaranTable tbody tr');

    function filterTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const statusValue = statusFilter ? statusFilter.value : 'all';

        tableRows.forEach(row => {
            const nama = row.querySelector('.nama-pelamar strong')?.innerText.toLowerCase() || '';
            const email = row.querySelector('.nama-pelamar small')?.innerText.toLowerCase() || '';
            const lowongan = row.cells[1]?.innerText.toLowerCase() || '';
            const rowStatus = row.getAttribute('data-status') || '';

            const matchesSearch = nama.includes(searchTerm) || email.includes(searchTerm) || lowongan.includes(searchTerm);
            const matchesStatus = (statusValue === 'all') || (rowStatus === statusValue);

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInput) searchInput.addEventListener('keyup', filterTable);
    if (statusFilter) statusFilter.addEventListener('change', filterTable);
});