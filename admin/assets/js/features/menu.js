// ==========================================
// MENU SWITCHING & SECTIONS
// ==========================================

// Load coffeeshop management section
function loadCoffeeshopManagement() {
  // Load data dan display table
  loadCoffeeshopsFromDatabase().then(() => {
    updateTable();
    setupSearch();
  });
}

function switchSection(sectionId) {
  // Hide all sections
  document.querySelectorAll(".admin-section").forEach((sec) => {
    sec.style.display = "none";
  });

  // Show selected section
  const section = document.getElementById(sectionId + "-section");
  if (section) {
    section.style.display = "block";
  }

  // Update active menu
  document.querySelectorAll(".menu-item").forEach((item) => {
    item.classList.remove("active");
  });

  const activeMenu = document.querySelector(`[data-menu="${sectionId}"]`);
  if (activeMenu) {
    activeMenu.classList.add("active");
  }

  // Load content untuk section tertentu
  if (sectionId === "laporan") {
    loadLaporanContent();
  } else if (sectionId === "pengaturan") {
    refreshDatabaseInfo();
  }
}

// Initialize menus
function initializeMenus() {
  const menuItems = document.querySelectorAll(".menu-item");
  menuItems.forEach((item) => {
    item.addEventListener("click", (e) => {
      e.preventDefault();
      const menuType = item.getAttribute("data-menu");

      // Load section-specific content
      if (menuType === "coffeeshop") {
        loadCoffeeshopManagement();
      }

      switchSection(menuType);
    });
  });
}

// Setup search functionality
function setupSearch() {
  const searchInput = document.getElementById("searchCoffeeshop");
  if (!searchInput) return;

  searchInput.addEventListener("input", (e) => {
    const query = e.target.value.toLowerCase();
    const rows = document.querySelectorAll(".data-table tbody tr");

    rows.forEach((row) => {
      const name = row.cells[1]?.textContent.toLowerCase() || "";
      const address = row.cells[2]?.textContent.toLowerCase() || "";

      if (name.includes(query) || address.includes(query)) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  });
}

// Run initialization when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  setTimeout(() => {
    initializeMenus();
  }, 500);
});

console.log("✅ Menu system loaded!");
