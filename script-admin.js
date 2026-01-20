// ==========================================
// ADMIN SCRIPT - LOGIN & DASHBOARD
// ==========================================

// ==========================================
// LOGIN PAGE FUNCTIONALITY
// ==========================================
const loginForm = document.getElementById("loginForm");

if (loginForm) {
  loginForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;
    const rememberMe = document.getElementById("rememberMe").checked;

    // Simple validation (belum terhubung database)
    if (username === "admin" && password === "admin123") {
      // Simpan session ke localStorage
      localStorage.setItem(
        "adminUser",
        JSON.stringify({
          username: username,
          loginTime: new Date(),
          rememberMe: rememberMe,
        }),
      );

      // Redirect ke dashboard
      alert("✅ Login berhasil! Selamat datang Admin.");
      window.location.href = "dashboard-admin.html";
    } else {
      alert(
        "❌ Username atau password salah!\n\nGunakan:\nUsername: admin\nPassword: admin123",
      );
      document.getElementById("password").value = "";
    }
  });

  console.log("🔐 Login page loaded");
}

// ==========================================
// DASHBOARD PAGE FUNCTIONALITY
// ==========================================

// Check if user is logged in
function checkAdminLogin() {
  const adminUser = localStorage.getItem("adminUser");

  if (!adminUser && window.location.pathname.includes("dashboard")) {
    alert("⚠️ Anda harus login terlebih dahulu!");
    window.location.href = "login-admin.html";
  }

  if (adminUser && window.location.pathname.includes("dashboard")) {
    const userData = JSON.parse(adminUser);
    console.log("✅ Admin logged in:", userData.username);
  }
}

// Run login check
checkAdminLogin();

// Logout functionality
document.querySelectorAll(".btn-logout").forEach((btn) => {
  btn.addEventListener("click", (e) => {
    e.preventDefault();
    if (confirm("Apakah anda yakin ingin logout?")) {
      localStorage.removeItem("adminUser");
      alert("✅ Anda telah logout");
      window.location.href = "login-admin.html";
    }
  });
});

// ==========================================
// DASHBOARD INTERACTIONS
// ==========================================

// Menu item active state
document.querySelectorAll(".menu-item").forEach((item) => {
  item.addEventListener("click", (e) => {
    e.preventDefault();
    document
      .querySelectorAll(".menu-item")
      .forEach((m) => m.classList.remove("active"));
    item.classList.add("active");

    const menuText = item.textContent.trim();
    console.log("📌 Menu clicked:", menuText);

    // Placeholder untuk fitur yang akan datang
    if (menuText.includes("Coffeeshop")) {
      console.log("⚠️ Fitur Coffeeshop Management - Belum diimplementasikan");
    } else if (menuText.includes("Laporan")) {
      console.log("⚠️ Fitur Laporan - Belum diimplementasikan");
    } else if (menuText.includes("Pengaturan")) {
      console.log("⚠️ Fitur Pengaturan - Belum diimplementasikan");
    }
  });
});

// Add new coffeeshop button
document.querySelectorAll(".btn-add-new").forEach((btn) => {
  btn.addEventListener("click", () => {
    alert(
      "➕ Fitur tambah coffeeshop belum diimplementasikan\n\nPada tahap selanjutnya akan ada form untuk menambah data coffeeshop baru.",
    );
  });
});

// Edit button functionality
document.querySelectorAll(".btn-edit").forEach((btn) => {
  btn.addEventListener("click", (e) => {
    e.preventDefault();
    const row = btn.closest("tr");
    const coffeeshopName = row.cells[1].textContent;
    alert(
      `✏️ Edit fitur untuk "${coffeeshopName}" belum diimplementasikan\n\nPada tahap selanjutnya akan ada form untuk edit data.`,
    );
  });
});

// Delete button functionality
document.querySelectorAll(".btn-delete").forEach((btn) => {
  btn.addEventListener("click", (e) => {
    e.preventDefault();
    const row = btn.closest("tr");
    const coffeeshopName = row.cells[1].textContent;

    if (
      confirm(
        `🗑️ Apakah anda yakin ingin menghapus "${coffeeshopName}"?\n\n(Fitur delete belum fungsional - tahap awal)`,
      )
    ) {
      console.log("Attempting to delete:", coffeeshopName);
      alert("⚠️ Backend untuk delete belum diimplementasikan");
    }
  });
});

console.log("🚀 Admin Panel Ready - Tahap Awal (Mockup)");
console.log("📋 Fitur yang sudah tersedia:");
console.log("  ✅ UI Login");
console.log("  ✅ Dashboard Mockup");
console.log("  ✅ Tabel Data Dummy");
console.log("");
console.log("⏳ Fitur yang belum diimplementasikan:");
console.log("  ⏳ Backend API");
console.log("  ⏳ Database Connection");
console.log("  ⏳ CRUD Operations (Edit & Delete)");
console.log("  ⏳ Form Validation");
