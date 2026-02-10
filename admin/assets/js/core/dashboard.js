// ==========================================
// DASHBOARD - LOAD & UPDATE DATA
// ==========================================

let allCoffeeshops = [];
let autoRefreshInterval = null;

// Load data dari database
async function loadCoffeeshopsFromDatabase() {
  try {
    console.log("📡 Fetching from: ../backend/api/coffeeshops.php");
    const response = await fetch("../backend/api/coffeeshops.php");
    console.log("📊 Response status:", response.status);

    const result = await response.json();
    console.log("📦 API Response:", result);

    if (result.success && result.data && Array.isArray(result.data)) {
      allCoffeeshops = result.data;
      console.log("✅ Data loaded:", allCoffeeshops.length, "items");
      return true;
    } else {
      console.error("❌ API response invalid:", result);
    }
  } catch (error) {
    console.error("❌ Fetch error:", error);
  }
  return false;
}

// Update table
function updateTable() {
  const tbody = document.querySelector(".data-table tbody");
  if (!tbody) {
    console.warn("⚠️ Table element not found");
    return;
  }

  console.log("📝 Updating table with", allCoffeeshops.length, "items");
  let html = "";
  allCoffeeshops.forEach((c, i) => {
    const statusBadge =
      c.status === "Aktif" ? "badge-active" : "badge-inactive";
    html += `
      <tr data-id="${c.id}">
        <td>${i + 1}</td>
        <td><strong>${c.name}</strong></td>
        <td>${c.address}</td>
        <td>${c.latitude.toFixed(4)}, ${c.longitude.toFixed(4)}</td>
        <td><span class="badge-rating">${c.rating} ⭐</span></td>
        <td><span class="${statusBadge}">${c.status}</span></td>
        <td>
          <button class="btn-edit" data-id="${c.id}">✏️</button>
          <button class="btn-delete" data-id="${c.id}">🗑️</button>
        </td>
      </tr>
    `;
  });
  tbody.innerHTML = html;

  // Rebind buttons
  document.querySelectorAll(".btn-edit").forEach((btn) => {
    btn.onclick = (e) => {
      e.preventDefault();
      const id = btn.getAttribute("data-id");
      const coffeeshop = allCoffeeshops.find((c) => c.id == id);
      if (coffeeshop) {
        openEditModal(coffeeshop);
      }
    };
  });

  document.querySelectorAll(".btn-delete").forEach((btn) => {
    btn.onclick = (e) => {
      e.preventDefault();
      const id = btn.getAttribute("data-id");
      const name = btn.closest("tr").cells[1].textContent;

      if (confirm(`🗑️ Apakah yakin hapus "${name}"?`)) {
        deleteCoffeeshop(id, name);
      }
    };
  });

  // Click on row to view detail
  document.querySelectorAll(".data-table tbody tr").forEach((row) => {
    row.style.cursor = "pointer";
    row.onclick = (e) => {
      // Don't open detail if clicking button
      if (e.target.tagName === "BUTTON") return;
      
      const id = row.getAttribute("data-id");
      const coffeeshop = allCoffeeshops.find((c) => c.id == id);
      if (coffeeshop) {
        openDetailModal(coffeeshop);
      }
    };
  });
}

// Update stats
function updateStats() {
  const values = document.querySelectorAll(".stat-value");
  if (values.length >= 4) {
    values[0].textContent = allCoffeeshops.length;
    values[1].textContent = allCoffeeshops.length;

    if (allCoffeeshops.length > 0) {
      const maxRating = Math.max(...allCoffeeshops.map((c) => c.rating));
      values[2].textContent = maxRating.toFixed(1);
    }

    const activeCount = allCoffeeshops.filter(
      (c) => c.status === "Aktif",
    ).length;
    values[3].textContent = activeCount > 0 ? "Aktif" : "Tidak Aktif";
  }
}

// Stop auto-refresh (saat modal terbuka)
function stopAutoRefresh() {
  if (autoRefreshInterval) {
    clearInterval(autoRefreshInterval);
    autoRefreshInterval = null;
    console.log("⏸️ Auto-refresh dihentikan");
  }
}

// Start auto-refresh (saat modal ditutup)
function startAutoRefresh() {
  if (autoRefreshInterval) {
    clearInterval(autoRefreshInterval);
  }

  autoRefreshInterval = setInterval(async () => {
    await loadCoffeeshopsFromDatabase();
    updateTable();
    updateStats();

    // Update coffeeshop section if visible
    const coffeeshopSection = document.getElementById("coffeeshop-section");
    if (coffeeshopSection && coffeeshopSection.style.display !== "none") {
      if (typeof updateCoffeeshopTableDisplay === "function") {
        updateCoffeeshopTableDisplay(allCoffeeshops);
      }
    }
  }, 3000);

  console.log("▶️ Auto-refresh dimulai lagi");
}

// Open detail modal for viewing coffeeshop with photo
function openDetailModal(coffeeshop) {
  // Set values
  document.getElementById("detailName").textContent = coffeeshop.name;
  document.getElementById("detailAddress").textContent = coffeeshop.address;
  document.getElementById("detailPhone").textContent = coffeeshop.phone || "Tidak ada";
  document.getElementById("detailCoords").textContent = 
    `${coffeeshop.latitude.toFixed(4)}, ${coffeeshop.longitude.toFixed(4)}`;
  document.getElementById("detailRating").textContent = `${coffeeshop.rating} ⭐`;
  document.getElementById("detailStatus").textContent = coffeeshop.status;
  
  // Handle photo
  const photoImg = document.getElementById("detailPhoto");
  const noPhoto = document.getElementById("noPhoto");
  
  if (coffeeshop.photo) {
    photoImg.src = "../" + coffeeshop.photo;
    photoImg.style.display = "block";
    noPhoto.style.display = "none";
  } else {
    photoImg.style.display = "none";
    noPhoto.style.display = "block";
  }
  
  // Show modal
  const modal = document.getElementById("detailCoffeeshopModal");
  if (modal) {
    modal.classList.add("active");
  }
  
  stopAutoRefresh();
}

// Close detail modal
function closeDetailModal() {
  const modal = document.getElementById("detailCoffeeshopModal");
  if (modal) {
    modal.classList.remove("active");
  }
  startAutoRefresh();
}

// Close detail modal with close button
document.addEventListener("DOMContentLoaded", () => {
  const detailCloseBtn = document.querySelector(".close-modal-btn-detail");
  const detailModal = document.getElementById("detailCoffeeshopModal");
  
  if (detailCloseBtn) {
    detailCloseBtn.onclick = () => {
      closeDetailModal();
    };
  }
  
  // Close modal when clicking overlay
  if (detailModal) {
    detailModal.onclick = (e) => {
      if (e.target === detailModal) {
        closeDetailModal();
      }
    };
  }
});

// Initialize on page load
document.addEventListener("DOMContentLoaded", async () => {
  console.log("🔄 Dashboard initializing...");

  // Load data
  const loaded = await loadCoffeeshopsFromDatabase();
  if (loaded) {
    updateTable();
    updateStats();
    setupSearch();
    startAutoRefresh();
    console.log(
      "✅ Dashboard ready with " + allCoffeeshops.length + " coffeeshops",
    );
  } else {
    console.warn("⚠️ Failed to load coffeeshops");
    // Retry after 2 seconds
    setTimeout(async () => {
      const retry = await loadCoffeeshopsFromDatabase();
      if (retry) {
        updateTable();
        updateStats();
        setupSearch();
        startAutoRefresh();
      }
    }, 2000);
  }
});

console.log("🚀 Dashboard initialized!");
