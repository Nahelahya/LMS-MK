document.addEventListener("DOMContentLoaded", function () {
    // Mencari semua elemen yang punya class .toggle-password
    const toggleButtons = document.querySelectorAll(".toggle-password");

    toggleButtons.forEach((button) => {
        button.addEventListener("click", function () {
            // Cari input yang berada dalam satu kotak (input-group) dengan ikon ini
            const passwordInput = this.parentElement.querySelector("input");

            if (passwordInput) {
                // Tukar tipe input
                const type =
                    passwordInput.getAttribute("type") === "password"
                        ? "text"
                        : "password";
                passwordInput.setAttribute("type", type);

                // Tukar ikon mata
                this.classList.toggle("fa-eye");
                this.classList.toggle("fa-eye-slash");
            }
        });
    });
});
// --- 2. Menangani Pengiriman Form Login (Dinamis) ---
const loginForm = document.getElementById("loginForm");
if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
        e.preventDefault(); // Mencegah reload halaman

        // Mengambil data dari form
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        console.log("Mencoba Login dengan data:", data);

        // Di sini Anda biasanya mengirim data ke server menggunakan fetch/axios
        alert(
            `Mencoba Login untuk user: ${data.name}. (Cek konsol untuk detail data)`,
        );

        // Contoh validasi sederhana
        if (data.name === "admin" && data.password === "1234") {
            alert("Login Sukses! (Simulasi)");
        } else {
            alert("Login Gagal! (Simulasi: gunakan admin/1234)");
        }
    });
}

// --- 3. Menangani Pengiriman Form Signup (Dinamis) ---
const signupForm = document.getElementById("signupForm");
if (signupForm) {
    signupForm.addEventListener("submit", function (e) {
        e.preventDefault(); // Mencegah reload halaman

        // Mengambil data dari form
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        console.log("Mencoba Daftar dengan data:", data);

        // Di sini Anda biasanya mengirim data ke server untuk pendaftaran
        alert(
            `Mencoba Mendaftar untuk user: ${data.name} dengan email ${data.email}. (Cek konsol)`,
        );
    });
}

// --- 4. Interaktivitas Ikon Sosial Media ---
const socialIcons = document.querySelectorAll(".social-icons a");
socialIcons.forEach((icon) => {
    icon.addEventListener("click", function (e) {
        e.preventDefault();
        const platform = this.querySelector("i").classList[1].split("-")[1]; // Mengambil nama platform dari class ikon
        alert(
            `Simulasi login menggunakan ${platform.charAt(0).toUpperCase() + platform.slice(1)}.`,
        );
    });
});
