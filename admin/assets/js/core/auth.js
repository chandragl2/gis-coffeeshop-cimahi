// ==========================================
// AUTH - LOGIN & LOGOUT
// ==========================================
const loginForm = document.getElementById("loginForm");

if (loginForm) {
  loginForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    if (username === "admin" && password === "admin123") {
      localStorage.setItem("adminUser", JSON.stringify({ username: username }));
      alert("✅ Login berhasil!");
      window.location.href = "index.html";
    } else {
      alert(
        "❌ Username atau password salah!\n\nUsername: admin\nPassword: admin123",
      );
      document.getElementById("password").value = "";
    }
  });
  console.log("🔐 Login page loaded");
}

// Check login on dashboard page
function checkAdminLogin() {
  const adminUser = localStorage.getItem("adminUser");
  if (!adminUser && window.location.pathname.includes("index.html")) {
    alert("⚠️ Silakan login terlebih dahulu!");
    window.location.href = "login.html";
  }
}

// Run check if on admin page
if (
  document.body.classList.contains("admin-page") ||
  document.querySelector(".admin-container")
) {
  checkAdminLogin();
}

// Logout handler
document.querySelectorAll(".btn-logout").forEach((btn) => {
  btn.addEventListener("click", (e) => {
    e.preventDefault();
    if (confirm("Logout?")) {
      localStorage.removeItem("adminUser");
      window.location.href = "login.html";
    }
  });
});

console.log("🔐 Authentication system loaded!");
