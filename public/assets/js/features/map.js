// DATA COFFEESHOP - DARI DATABASE SAJA
let coffeeshops = [];

// MAPBOX INITIALIZATION

// Set Mapbox Access Token
mapboxgl.accessToken =
  "pk.eyJ1IjoiaXN3YW50bzIwMjBhIiwiYSI6ImNtajhhMDhnOTAwYXMzZW9yaTFmOWt4bm0ifQ.WX7Uc1oLbvmsdqb2rly6Tw";

// Initialize Map
const map = new mapboxgl.Map({
  container: "map",
  style: "mapbox://styles/mapbox/streets-v12",
  center: [107.557, -6.89],
  zoom: 13,
  pitch: 0,
  bearing: 0,
});

// Add Navigation Control
map.addControl(new mapboxgl.NavigationControl(), "top-right");

// ==========================================
// FUNCTION: Load data dari database
// ==========================================
async function loadCoffeeshopsFromDatabase() {
  try {
    const response = await fetch("../backend/api/coffeeshops.php");
    const result = await response.json();

    if (result.success && result.data && result.data.length > 0) {
      coffeeshops = result.data;
      console.log("✅ Data dari database:", coffeeshops.length, "item");
      return true;
    } else {
      console.warn("⚠️ Tidak ada data di database");
      coffeeshops = [];
      return false;
    }
  } catch (error) {
    console.error("❌ Error load database:", error);
    coffeeshops = [];
    return false;
  }
}

// FUNCTION: Add Markers to Map

function addMarkersToMap(coffeeshopsToShow = coffeeshops) {
  // Remove existing markers
  document.querySelectorAll(".mapboxgl-marker").forEach((marker) => {
    marker.remove();
  });

  coffeeshopsToShow.forEach((coffeeshop) => {
    // Create custom marker element
    const markerElement = document.createElement("div");
    markerElement.className = "custom-marker";
    markerElement.innerHTML = "☕";
    markerElement.style.fontSize = "32px";
    markerElement.style.cursor = "pointer";

    // Create popup
    const photoHTML = coffeeshop.photo ? 
      `<div style="margin-bottom: 10px; text-align: center;">
         <img src="../${coffeeshop.photo}" alt="${coffeeshop.name}" 
              style="width: 100%; max-width: 300px; height: auto; border-radius: 6px; max-height: 250px; object-fit: cover;">
       </div>` : 
      '';
    
    const popupContent = `
            <div class="popup-content">
                ${photoHTML}
                <h3>${coffeeshop.name}</h3>
                <p><strong>📍 Alamat:</strong></p>
                <p>${coffeeshop.address}</p>
                <p><strong>📝 Deskripsi:</strong></p>
                <p>${coffeeshop.description}</p>
            </div>
        `;

    const popup = new mapboxgl.Popup({
      offset: 25,
    }).setHTML(popupContent);

    // Add marker to map
    const marker = new mapboxgl.Marker(markerElement)
      .setLngLat([coffeeshop.longitude, coffeeshop.latitude])
      .setPopup(popup)
      .addTo(map);

    // Create tooltip element
    const tooltip = document.createElement("div");
    tooltip.className = "marker-tooltip";
    tooltip.innerHTML = coffeeshop.name;
    tooltip.style.display = "none";
    tooltip.style.position = "absolute";
    tooltip.style.backgroundColor = "rgba(111, 78, 55, 0.95)";
    tooltip.style.color = "white";
    tooltip.style.padding = "8px 12px";
    tooltip.style.borderRadius = "6px";
    tooltip.style.fontSize = "12px";
    tooltip.style.fontWeight = "500";
    tooltip.style.whiteSpace = "nowrap";
    tooltip.style.zIndex = "1000";
    tooltip.style.pointerEvents = "none";
    tooltip.style.boxShadow = "0 2px 8px rgba(0, 0, 0, 0.2)";
    tooltip.style.border = "1px solid rgba(255, 255, 255, 0.2)";
    document.body.appendChild(tooltip);

    // Event: Marker Click
    markerElement.addEventListener("click", () => {
      displayCoffeeshopInfo(coffeeshop);
    });

    // Event: Marker Hover - ubah ukuran font dan tampilkan tooltip
    markerElement.addEventListener("mouseenter", (e) => {
      markerElement.style.fontSize = "40px";
      markerElement.style.transition = "font-size 0.2s ease";

      // Tampilkan tooltip
      tooltip.style.display = "block";
      const rect = markerElement.getBoundingClientRect();
      tooltip.style.left =
        rect.left + rect.width / 2 - tooltip.offsetWidth / 2 + "px";
      tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + "px";
    });

    markerElement.addEventListener("mouseleave", () => {
      markerElement.style.fontSize = "32px";
      tooltip.style.display = "none";
    });
  });
}

// ==========================================
// FUNCTION: Display Coffeeshop Info in Sidebar
// ==========================================
function displayCoffeeshopInfo(coffeeshop) {
  const infoCont = document.getElementById("coffeeshopInfo");
  const photoHTML = coffeeshop.photo ? 
    `<div style="margin-bottom: 15px; text-align: center;">
      <img src="../${coffeeshop.photo}" alt="${coffeeshop.name}" 
           style="width: 100%; border-radius: 8px; max-height: 300px; object-fit: cover;">
    </div>` : 
    '<p style="color: #999;">Foto tidak tersedia</p>';
    
  infoCont.innerHTML = `
        <div class="info-detail">
            ${photoHTML}
            <h4>${coffeeshop.name}</h4>
            <p><strong>📍 Alamat:</strong></p>
            <p>${coffeeshop.address}</p>
            <p><strong>📝 Deskripsi:</strong></p>
            <p>${coffeeshop.description}</p>
            <p style="margin-top: 10px; font-size: 11px; color: #999;">
                Koordinat: ${coffeeshop.latitude.toFixed(4)}, ${coffeeshop.longitude.toFixed(4)}
            </p>
        </div>
    `;

  // Fly to coffeeshop location
  map.flyTo({
    center: [coffeeshop.longitude, coffeeshop.latitude],
    zoom: 15,
    duration: 1000,
  });
}

// ==========================================
// FUNCTION: Search Coffeeshop
// ==========================================
function searchCoffeeshop(query) {
  const searchResults = document.getElementById("searchResults");
  searchResults.innerHTML = "";

  if (query.trim() === "") {
    // If empty, show all coffeeshops on map
    addMarkersToMap(coffeeshops);
    return;
  }

  // Filter coffeeshops based on search query
  const filtered = coffeeshops.filter(
    (coffeeshop) =>
      coffeeshop.name.toLowerCase().includes(query.toLowerCase()) ||
      coffeeshop.address.toLowerCase().includes(query.toLowerCase()),
  );

  // Display search results
  if (filtered.length === 0) {
    searchResults.innerHTML =
      '<p class="info-placeholder" style="padding: 10px;">Coffeeshop tidak ditemukan</p>';
    addMarkersToMap([]);
  } else {
    filtered.forEach((coffeeshop) => {
      const resultItem = document.createElement("div");
      resultItem.className = "search-result-item";
      resultItem.innerHTML = `
                <strong>${coffeeshop.name}</strong><br>
                <small>${coffeeshop.address}</small>
            `;
      resultItem.addEventListener("click", () => {
        document.getElementById("searchInput").value = coffeeshop.name;
        displayCoffeeshopInfo(coffeeshop);
        searchResults.innerHTML = "";
      });
      searchResults.appendChild(resultItem);
    });

    // Show only filtered markers on map
    addMarkersToMap(filtered);
  }
}

// ==========================================
// EVENT LISTENERS
// ==========================================

// Search Input Event
document.getElementById("searchInput").addEventListener("keyup", (e) => {
  searchCoffeeshop(e.target.value);
});

// About Button
document.getElementById("btnAbout").addEventListener("click", () => {
  document.getElementById("aboutModal").style.display = "block";
});

// Close About Modal
document.getElementById("closeAbout").addEventListener("click", () => {
  document.getElementById("aboutModal").style.display = "none";
});

// Close Modal when clicking outside
window.addEventListener("click", (e) => {
  const modal = document.getElementById("aboutModal");
  if (e.target === modal) {
    modal.style.display = "none";
  }
});

// ==========================================
// INITIALIZATION
// ==========================================

// Wait for map to load
map.on("load", async () => {
  console.log("📍 Map loaded, loading coffeeshop data...");

  // Load dari database
  await loadCoffeeshopsFromDatabase();

  // Initialize filter and populate options
  initializeFilter();
  populateFilterOptions(coffeeshops);

  addMarkersToMap(coffeeshops);

  // Auto-refresh setiap 5 detik
  setInterval(async () => {
    await loadCoffeeshopsFromDatabase();
    // Only add markers if no active filter
    const hasFilter =
      filterState.kecamatan ||
      filterState.kelurahan ||
      filterState.category ||
      filterState.search;
    if (!hasFilter) {
      addMarkersToMap(coffeeshops);
      updateFilterStats();
    }
  }, 5000);
});

console.log("🚀 GIS Coffeeshop Cimahi - Database Only!");
