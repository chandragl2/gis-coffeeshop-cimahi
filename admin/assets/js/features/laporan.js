// ==========================================
// LAPORAN & STATISTIK
// ==========================================

function generateLaporan() {
  const laporan = {
    totalCoffeeshop: allCoffeeshops.length,
    aktivCoffeeshop: allCoffeeshops.filter((c) => c.status === "Aktif").length,
    ratingTertinggi: Math.max(...allCoffeeshops.map((c) => c.rating)),
    ratingTerendah: Math.min(...allCoffeeshops.map((c) => c.rating)),
    ratingRata: (
      allCoffeeshops.reduce((sum, c) => sum + c.rating, 0) /
      allCoffeeshops.length
    ).toFixed(2),
    coffeeshopTerbaik: allCoffeeshops.reduce((a, b) =>
      a.rating > b.rating ? a : b,
    ),
  };

  return laporan;
}

function loadLaporanContent() {
  const container = document.getElementById("laporan-content");
  if (!container) return;

  const totalCoffeeshop = allCoffeeshops.length;
  const aktivCoffeeshop = allCoffeeshops.filter(
    (c) => c.status === "Aktif",
  ).length;
  const nonAktifCoffeeshop = totalCoffeeshop - aktivCoffeeshop;
  const avgRating =
    allCoffeeshops.length > 0
      ? (
          allCoffeeshops.reduce((sum, c) => sum + c.rating, 0) /
          allCoffeeshops.length
        ).toFixed(2)
      : 0;
  const maxRating =
    allCoffeeshops.length > 0
      ? Math.max(...allCoffeeshops.map((c) => c.rating))
      : 0;
  const minRating =
    allCoffeeshops.length > 0
      ? Math.min(...allCoffeeshops.map((c) => c.rating))
      : 0;

  const html = `
    <div class="laporan-container">
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 30px">
        <div class="stat-card">
          <div class="stat-icon">☕</div>
          <div class="stat-content">
            <h4>Total Coffeeshop</h4>
            <p class="stat-value">${totalCoffeeshop}</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">✅</div>
          <div class="stat-content">
            <h4>Aktif</h4>
            <p class="stat-value">${aktivCoffeeshop}</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">❌</div>
          <div class="stat-content">
            <h4>Tidak Aktif</h4>
            <p class="stat-value">${nonAktifCoffeeshop}</p>
          </div>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 30px">
        <div class="stat-card">
          <div class="stat-content">
            <h4>Rating Rata-rata</h4>
            <p class="stat-value">${avgRating} ⭐</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-content">
            <h4>Rating Tertinggi</h4>
            <p class="stat-value">${maxRating} ⭐</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-content">
            <h4>Rating Terendah</h4>
            <p class="stat-value">${minRating} ⭐</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-content">
            <h4>Persentase Aktif</h4>
            <p class="stat-value">${totalCoffeeshop > 0 ? ((aktivCoffeeshop / totalCoffeeshop) * 100).toFixed(0) : 0}%</p>
          </div>
        </div>
      </div>

      <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px">
        <h3>📊 Daftar Coffeeshop Berdasarkan Rating</h3>
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px">
          <thead>
            <tr style="background: #f5f5f5; border-bottom: 2px solid #ddd">
              <th style="padding: 10px; text-align: left">Nama Coffeeshop</th>
              <th style="padding: 10px; text-align: left">Rating</th>
              <th style="padding: 10px; text-align: left">Status</th>
              <th style="padding: 10px; text-align: left">Telepon</th>
            </tr>
          </thead>
          <tbody>
            ${allCoffeeshops
              .sort((a, b) => b.rating - a.rating)
              .map(
                (c) => `
                <tr style="border-bottom: 1px solid #ddd">
                  <td style="padding: 10px"><strong>${c.name}</strong></td>
                  <td style="padding: 10px">${c.rating} ⭐</td>
                  <td style="padding: 10px">
                    <span style="padding: 5px 10px; border-radius: 4px; background: ${c.status === "Aktif" ? "#d4edda" : "#f8d7da"}; color: ${c.status === "Aktif" ? "#155724" : "#721c24"}">
                      ${c.status}
                    </span>
                  </td>
                  <td style="padding: 10px">${c.phone || "-"}</td>
                </tr>
              `,
              )
              .join("")}
          </tbody>
        </table>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px">
        <button onclick="exportLaporanCSV()" class="btn-submit" style="padding: 12px; background: #28a745">
          📥 Export Laporan CSV
        </button>
        <button onclick="printLaporan()" class="btn-submit" style="padding: 12px; background: #007bff">
          🖨️ Print Laporan
        </button>
      </div>
    </div>
  `;

  container.innerHTML = html;
}

function exportLaporanCSV() {
  let csv =
    "No,Nama Coffeeshop,Alamat,Latitude,Longitude,Rating,Status,Telepon\n";
  allCoffeeshops.forEach((c, i) => {
    csv += `${i + 1},"${c.name}","${c.address}",${c.latitude},${c.longitude},${c.rating},"${c.status}","${c.phone || "-"}"\n`;
  });

  const blob = new Blob([csv], { type: "text/csv" });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = `laporan-coffeeshop-${new Date().toISOString().split("T")[0]}.csv`;
  a.click();
  window.URL.revokeObjectURL(url);
}

function printLaporan() {
  window.print();
}

// ==========================================
// PENGATURAN SISTEM - TAB SWITCHING
// ==========================================

function switchTab(tabName) {
  document.querySelectorAll(".tab-content").forEach((tab) => {
    tab.style.display = "none";
  });

  document.querySelectorAll(".tab-btn").forEach((btn) => {
    btn.classList.remove("active");
  });

  document.getElementById("tab-" + tabName).style.display = "block";
  event.target.classList.add("active");
}

// ==========================================
// PENGATURAN SISTEM - FUNCTIONS
// ==========================================

function updateProfile() {
  const password = document.getElementById("adminPassword").value;
  const email = document.getElementById("adminEmail").value;

  if (!password && !email) {
    alert("⚠️ Tidak ada perubahan");
    return;
  }

  if (password) {
    localStorage.setItem("adminPassword", password);
    alert("✅ Password berhasil diubah!");
  }

  if (email) {
    localStorage.setItem("adminEmail", email);
    alert("✅ Email berhasil diubah!");
  }

  document.getElementById("adminPassword").value = "";
}

function backupDatabase() {
  const laporan = generateLaporan();
  const backup = {
    timestamp: new Date().toISOString(),
    database: "gis_coffeeshop",
    totalRecords: allCoffeeshops.length,
    data: allCoffeeshops,
    statistics: laporan,
  };

  const blob = new Blob([JSON.stringify(backup, null, 2)], {
    type: "application/json",
  });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = `coffeeshop_backup_${
    new Date().toISOString().split("T")[0]
  }.json`;
  a.click();
  window.URL.revokeObjectURL(url);

  alert("✅ Backup berhasil didownload!");
}

function restoreDatabase() {
  const fileInput = document.getElementById("restoreFile");

  if (!fileInput.files.length) {
    alert("⚠️ Pilih file terlebih dahulu");
    return;
  }

  const file = fileInput.files[0];
  const reader = new FileReader();

  reader.onload = (e) => {
    try {
      const backup = JSON.parse(e.target.result);

      if (!backup.data || !Array.isArray(backup.data)) {
        throw new Error("Format file backup tidak valid");
      }

      alert(
        `✅ File backup berhasil dibaca!\nJumlah data: ${backup.data.length}\n\n⚠️ Restore dilakukan di client saja (demo mode)`,
      );
      console.log("Backup data:", backup);
    } catch (error) {
      alert(`❌ Error: ${error.message}`);
    }
  };

  reader.readAsText(file);
}

function refreshDatabaseInfo() {
  document.getElementById("totalRecords").textContent = allCoffeeshops.length;
  const lastUpdated = document.getElementById("lastUpdated");
  if (lastUpdated) {
    lastUpdated.textContent = new Date().toLocaleString("id-ID");
  }
  alert("✅ Informasi database direfresh");
}

console.log("✅ Laporan & Pengaturan system loaded!");
