// ==========================================
// CRUD OPERATIONS
// ==========================================

// Open edit modal
function openEditModal(coffeeshop) {
  const form = document.getElementById("addCoffeeshopForm");
  if (!form) return;

  // Set edit mode
  form.dataset.editId = coffeeshop.id;
  document.getElementById("coffeeshopName").value = coffeeshop.name;
  document.getElementById("coffeeshopAddress").value = coffeeshop.address;
  document.getElementById("coffeeshopLat").value = coffeeshop.latitude;
  document.getElementById("coffeeshopLng").value = coffeeshop.longitude;
  document.getElementById("coffeeshopRating").value = coffeeshop.rating;
  document.getElementById("coffeeshopStatus").value = coffeeshop.status;
  document.getElementById("coffeeshopPhone").value = coffeeshop.phone || "";

  // Change button text
  const submitBtn = form.querySelector(".btn-submit");
  submitBtn.textContent = "💾 Update Coffeeshop";

  // Show modal
  const modal = document.getElementById("addCoffeeshopModal");
  if (modal) modal.classList.add("active");
}

// Close edit mode
function closeEditMode() {
  const form = document.getElementById("addCoffeeshopForm");
  if (form) {
    delete form.dataset.editId;
    form.reset();
    const submitBtn = form.querySelector(".btn-submit");
    submitBtn.textContent = "💾 Simpan Coffeeshop";
  }
}

// DELETE COFFEESHOP
async function deleteCoffeeshop(id, name) {
  try {
    const response = await fetch("../../backend/api/coffeeshops.php?id=" + id, {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
    });

    const result = await response.json();

    if (result.success) {
      alert(`✅ "${name}" berhasil dihapus!`);
      await loadCoffeeshopsFromDatabase();
      updateTable();
      updateStats();
    } else {
      alert(`❌ Gagal: ${result.message}`);
    }
  } catch (error) {
    alert(`❌ Error: ${error.message}`);
  }
}

// ADD/EDIT COFFEESHOP - Modal handling
const modal = document.getElementById("addCoffeeshopModal");
const closeBtn = document.querySelector(".close-modal-btn");
const cancelBtn = document.querySelector(".btn-cancel");

if (closeBtn) {
  closeBtn.addEventListener("click", () => {
    modal.classList.remove("active");
    startAutoRefresh();
  });
}

if (cancelBtn) {
  cancelBtn.addEventListener("click", () => {
    closeEditMode();
    modal.classList.remove("active");
    startAutoRefresh();
  });
}

window.addEventListener("click", (e) => {
  if (e.target === modal) {
    closeEditMode();
    modal.classList.remove("active");
    startAutoRefresh();
  }
});

// Setup "Tambah Coffeeshop" buttons
document.querySelectorAll(".btn-add-new").forEach((btn) => {
  btn.addEventListener("click", () => {
    closeEditMode();
    stopAutoRefresh();
    const modal = document.getElementById("addCoffeeshopModal");
    if (modal) modal.classList.add("active");
  });
});

// Form submit
const form = document.getElementById("addCoffeeshopForm");
if (form) {
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const editId = form.dataset.editId;
    const data = {
      name: document.getElementById("coffeeshopName").value,
      address: document.getElementById("coffeeshopAddress").value,
      latitude: parseFloat(document.getElementById("coffeeshopLat").value),
      longitude: parseFloat(document.getElementById("coffeeshopLng").value),
      rating: parseFloat(document.getElementById("coffeeshopRating").value),
      status: document.getElementById("coffeeshopStatus").value,
      phone: document.getElementById("coffeeshopPhone").value || null,
    };

    // Jika edit mode, tambahkan ID
    if (editId) {
      data.id = parseInt(editId);
    }

    if (!data.name || !data.address) {
      alert("❌ Nama dan alamat harus diisi!");
      return;
    }

    const btn = form.querySelector(".btn-submit");
    btn.disabled = true;
    const originalText = btn.textContent;
    btn.textContent = "⏳ Menyimpan...";

    try {
      const method = editId ? "PUT" : "POST";
      const endpoint = editId
        ? `../../backend/api/coffeeshops.php?id=${editId}`
        : "../../backend/api/coffeeshops.php";

      const response = await fetch(endpoint, {
        method: method,
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });

      const result = await response.json();

      if (result.success) {
        const message = editId
          ? "✅ Coffeeshop berhasil diupdate!"
          : "✅ Coffeeshop berhasil ditambahkan!";
        alert(message);
        closeEditMode();
        modal.classList.remove("active");

        // Reload data
        await loadCoffeeshopsFromDatabase();
        updateTable();
        updateStats();
        startAutoRefresh();
      } else {
        alert("❌ Gagal: " + result.message);
      }
    } catch (error) {
      alert("❌ Error: " + error.message);
    } finally {
      btn.disabled = false;
      btn.textContent = originalText;
    }
  });
}

// Load Coffeeshop Management
function loadCoffeeshopManagement() {
  const coffeeshopSection = document.getElementById("coffeeshop-section");
  if (!coffeeshopSection) return;

  // Generate table with all data
  const tbody = coffeeshopSection.querySelector("table tbody");
  if (tbody) {
    updateCoffeeshopTableDisplay(allCoffeeshops);
  }
}

function updateCoffeeshopTableDisplay(data) {
  const tbody = document.querySelector("#coffeeshop-section table tbody");
  if (!tbody) return;

  let html = "";
  data.forEach((c, i) => {
    const statusBadge =
      c.status === "Aktif" ? "badge-active" : "badge-inactive";
    html += `
      <tr data-id="${c.id}">
        <td>${i + 1}</td>
        <td><strong>${c.name}</strong></td>
        <td>${c.address}</td>
        <td><span class="badge-rating">${c.rating} ⭐</span></td>
        <td><span class="${statusBadge}">${c.status}</span></td>
        <td>${c.phone || "-"}</td>
        <td>
          <button class="btn-edit" data-id="${c.id}" onclick="handleEditClick(event)">✏️</button>
          <button class="btn-delete" data-id="${c.id}" onclick="handleDeleteClick(event)">🗑️</button>
        </td>
      </tr>
    `;
  });
  tbody.innerHTML = html;
}

// Handle edit click from coffeeshop section
function handleEditClick(event) {
  event.preventDefault();
  const id = parseInt(event.target.dataset.id);
  const coffeeshop = allCoffeeshops.find((c) => c.id === id);
  if (coffeeshop) {
    stopAutoRefresh();
    openEditModal(coffeeshop);
  }
}

// Handle delete click from coffeeshop section
function handleDeleteClick(event) {
  event.preventDefault();
  const id = parseInt(event.target.dataset.id);
  const coffeeshop = allCoffeeshops.find((c) => c.id === id);
  if (coffeeshop) {
    if (confirm(`Yakin ingin hapus "${coffeeshop.name}"?`)) {
      deleteCoffeeshop(id, coffeeshop.name);
    }
  }
}

console.log("✅ CRUD operations loaded!");
