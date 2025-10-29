// Function untuk mengelola Dark Mode
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');

    const isDarkMode = document.body.classList.contains('dark-mode');
    const newText = isDarkMode ? "Light Mode" : "Dark Mode";

    // Gunakan querySelectorAll by CLASS
    const toggleButtons = document.querySelectorAll(".dark-mode-toggle");
    toggleButtons.forEach(button => {
        button.textContent = newText;
    });

    localStorage.setItem('darkMode', isDarkMode);
}

// Menangani Event Load Halaman
window.addEventListener("load", function() {
    const statusElement = document.getElementById("contact-status");
    if (statusElement) {
        statusElement.textContent = "Hubungi kami! Kami siap melayani.";
        statusElement.style.fontWeight = "bold";
        statusElement.style.color = "#3498db"; // Warna biru
    }

    // Mengecek Local Storage saat halaman dimuat
    const savedDarkMode = localStorage.getItem('darkMode');
    const isDarkMode = (savedDarkMode === 'true');
    const newText = isDarkMode ? "Light Mode" : "Dark Mode";

    if (isDarkMode) {
        // Hanya tambahkan class jika belum ada (mencegah flicker)
        if (!document.body.classList.contains('dark-mode')) {
            document.body.classList.add('dark-mode');
        }
    }

    // Terapkan status teks ke SEMUA tombol dark mode
    const toggleButtons = document.querySelectorAll(".dark-mode-toggle");
    toggleButtons.forEach(button => {
        button.textContent = newText;
    });
});

// Menangani Event setelah DOM Content Loaded
document.addEventListener("DOMContentLoaded", function() {

    const taglineElement = document.querySelector(".tagline");
    if (taglineElement) {
        taglineElement.textContent = "Solusi mudah untuk menyewa perlengkapan outdoor dan camping. Mulai petualanganmu sekarang!";
    }

    // Menambahkan paragraf baru di bawah 'services'
    const newParagraph = document.createElement("p");
    newParagraph.textContent = "Klik pada alat di atas untuk melihat detail dan menyewa.";
    newParagraph.style.textAlign = "center";
    newParagraph.style.marginTop = "20px";
    newParagraph.style.fontStyle = "italic";

    const servicesSection = document.getElementById("services");
    if (servicesSection) {
        // Cek dulu apakah paragraf sudah ada sebelum menambah
        if (!servicesSection.querySelector(".container > p:last-child[style*='italic']")) {
            servicesSection.querySelector(".container").appendChild(newParagraph);
        }
    }

    // Tambahkan Event Listener ke SEMUA tombol dark mode
    const darkModeToggles = document.querySelectorAll(".dark-mode-toggle");
    darkModeToggles.forEach(toggle => {
        toggle.addEventListener("click", toggleDarkMode);
    });

    /* ============================================== */
    /* == FUNGSI USER DROPDOWN MENU == */
    /* ============================================== */
    const userMenu = document.getElementById("userMenu"); // Container utama menu
    const userMenuButton = document.getElementById("userMenuButton"); // Tombol klik
    const userMenuDropdown = document.getElementById("userMenuDropdown"); // Konten dropdown

    if (userMenu && userMenuButton && userMenuDropdown) {
        userMenuButton.addEventListener("click", function(event) {
            // Toggle (tampilkan/sembunyikan) dropdown
            userMenuDropdown.classList.toggle("show");
            userMenuButton.classList.toggle("active");
            event.stopPropagation(); // Hentikan event agar tidak ditangkap window
        });

        // Menutup dropdown jika klik di luar menu
        window.addEventListener("click", function(event) {
            // Cek jika dropdown sedang tampil DAN klik BUKAN di dalam menu itu sendiri
            if (userMenuDropdown.classList.contains("show") && !userMenu.contains(event.target)) {
                    userMenuDropdown.classList.remove("show");
                    userMenuButton.classList.remove("active");
            }
        });
    }

}); // <-- Akhir dari DOMContentLoaded


/* ============================================== */
/* == FUNGSI SHOW/HIDE PASSWORD (TOGGLE MATA) == */
/* ============================================== */

function togglePasswordVisibility(inputId) {
    // Dapatkan elemen input berdasarkan ID yang diberikan
    const passwordInput = document.getElementById(inputId);

    // Pastikan input ditemukan
    if (!passwordInput) {
        console.error("Error: Input password dengan ID '" + inputId + "' tidak ditemukan.");
        return;
    }

    // Cari elemen SPAN yang *langsung* mengikuti input password
    const toggleIconContainer = passwordInput.nextElementSibling;

    // Pastikan elemen SPAN ditemukan dan punya class yang benar
    if (!toggleIconContainer || !toggleIconContainer.classList.contains('toggle-password')) {
        console.error("Error: Tombol toggle (span.toggle-password) tidak ditemukan tepat setelah input #" + inputId);
        return;
    }

    // Cari KEDUA ikon SVG *di dalam* span tersebut
    const eyeIcon = toggleIconContainer.querySelector('.bi-eye-fill');
    const eyeSlashIcon = toggleIconContainer.querySelector('.bi-eye-slash-fill');

    // Pastikan KEDUA ikon SVG benar-benar ditemukan
    if (!eyeIcon || !eyeSlashIcon) {
        console.error("Error: Ikon SVG mata (terbuka atau tertutup) tidak ditemukan di dalam tombol toggle untuk #" + inputId);
        return;
    }

    // Logika untuk mengganti tipe input dan ikon
    try {
        if (passwordInput.type === "password") {
            // Ubah ke text agar password terlihat
            passwordInput.type = "text";
            eyeIcon.style.display = "none";        // Sembunyikan mata terbuka
            eyeSlashIcon.style.display = "inline"; // Tampilkan mata tertutup
        } else {
            // Ubah kembali ke password (disembunyikan)
            passwordInput.type = "password";
            eyeIcon.style.display = "inline";      // Tampilkan mata terbuka
            eyeSlashIcon.style.display = "none";   // Sembunyikan mata tertutup
        }
    } catch (e) {
        console.error("Error saat mengubah tipe input atau style ikon:", e);
    }
}