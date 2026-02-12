/**
 * Main JavaScript Functions
 * File: assets/js/script.js
 */

// Format currency ke format Rupiah
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}

// Kalkulasi total pembayaran
function hitungTotal() {
    const hargaSewa = parseFloat(document.getElementById('harga_sewa')?.value || 0);
    const jumlahBulan = parseInt(document.getElementById('jumlah_bulan')?.value || 0);
    
    const total = hargaSewa * jumlahBulan;
    
    const totalElement = document.getElementById('total_bayar');
    if (totalElement) {
        totalElement.value = total;
        document.getElementById('total_display').textContent = formatRupiah(total);
    }
}

// Kalkulasi tanggal berakhir kontrak
function hitungTanggalBerakhir() {
    const tanggalMulai = document.getElementById('tanggal_mulai')?.value;
    const jumlahBulan = parseInt(document.getElementById('jumlah_bulan')?.value || 0);
    
    if (tanggalMulai && jumlahBulan > 0) {
        const mulai = new Date(tanggalMulai);
        mulai.setMonth(mulai.getMonth() + jumlahBulan);
        
        const tahun = mulai.getFullYear();
        const bulan = String(mulai.getMonth() + 1).padStart(2, '0');
        const tanggal = String(mulai.getDate()).padStart(2, '0');
        
        const tanggalBerakhir = `${tahun}-${bulan}-${tanggal}`;
        
        const berakhirElement = document.getElementById('tanggal_berakhir');
        if (berakhirElement) {
            berakhirElement.value = tanggalBerakhir;
        }
    }
}

// Preview gambar sebelum upload
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Konfirmasi delete
function confirmDelete(message) {
    return confirm(message || 'Apakah Anda yakin ingin menghapus data ini?');
}

// Auto calculate on input change
document.addEventListener('DOMContentLoaded', function() {
    const jumlahBulanInput = document.getElementById('jumlah_bulan');
    const tanggalMulaiInput = document.getElementById('tanggal_mulai');
    
    if (jumlahBulanInput) {
        jumlahBulanInput.addEventListener('change', function() {
            hitungTotal();
            hitungTanggalBerakhir();
        });
    }
    
    if (tanggalMulaiInput) {
        tanggalMulaiInput.addEventListener('change', function() {
            hitungTanggalBerakhir();
        });
    }
});

// Toggle sidebar on mobile
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active');
    }
}
