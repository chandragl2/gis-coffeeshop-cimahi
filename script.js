// ==========================================
// DATA DUMMY COFFEESHOP
// ==========================================
const coffeeshops = [
  {
    id: 1,
    name: "Kopi Bersaudara",
    address: "Jl. Pendidikan No. 45, Cimahi",
    latitude: -6.8886,
    longitude: 107.557,
    description:
      "Kedai kopi tradisional dengan suasana hangat dan cozy. Menyediakan berbagai pilihan kopi premium dari berbagai daerah.",
  },
  {
    id: 2,
    name: "The Coffee House",
    address: "Jl. Raya Cimahi No. 120, Cimahi",
    latitude: -6.895,
    longitude: 107.548,
    description:
      "Tempat nongkrong modern dengan Wi-Fi cepat. Cocok untuk bekerja atau belajar dengan kopi yang nikmat.",
  },
  {
    id: 3,
    name: "Café Indah",
    address: "Jl. Sipakubumen No. 78, Cimahi",
    latitude: -6.882,
    longitude: 107.562,
    description:
      "Kafe dengan interior minimalis yang elegan. Menawarkan menu kopi specialty dan pastry yang lezat.",
  },
  {
    id: 4,
    name: "Kopi Nusantara",
    address: "Jl. Kompas No. 32, Cimahi",
    latitude: -6.9,
    longitude: 107.55,
    description:
      "Kedai kopi lokal yang mengutamakan biji kopi pilihan dari Indonesia. Pelayanan ramah dan harga terjangkau.",
  },
  {
    id: 5,
    name: "Brew Station",
    address: "Jl. Moch. Toha No. 15, Cimahi",
    latitude: -6.89,
    longitude: 107.555,
    description:
      "Specialty coffee shop dengan barista berpengalaman. Menyediakan kelas brewing dan tasting kopi.",
  },
  {
    id: 6,
    name: "Warkop Seuseupan",
    address: "Jl. Cipaganti No. 99, Cimahi",
    latitude: -6.875,
    longitude: 107.565,
    description:
      "Warung kopi tradisional yang legendaris. Terkenal dengan kopi hitam nikmat dan suasana yang ramai.",
  },
  {
    id: 7,
    name: "Coffee & Co.",
    address: "Jl. Pasteur No. 67, Cimahi",
    latitude: -6.905,
    longitude: 107.542,
    description:
      "Kedai kopi contemporary dengan menu internasional. Dilengkapi dengan area outdoor yang nyaman.",
  },
  {
    id: 8,
    name: "Kopitiam",
    address: "Jl. Cikampak No. 45, Cimahi",
    latitude: -6.885,
    longitude: 107.57,
    description:
      "Rumah kopi klasik dengan sentuhan vintage. Menghadirkan pengalaman minum kopi ala nenek moyang.",
  },
];

// ==========================================
// MAPBOX INITIALIZATION
// ==========================================

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
// FUNCTION: Add Markers to Map
// ==========================================
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
    const popupContent = `
            <div class="popup-content">
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
  infoCont.innerHTML = `
        <div class="info-detail">
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

// Wait for map to load before adding markers
map.on("load", () => {
  addMarkersToMap(coffeeshops);
  console.log("✅ Map loaded successfully!");
  console.log(`📍 Total coffeeshop: ${coffeeshops.length}`);
});

console.log("🚀 Sistem Informasi Geografis Coffeeshop Cimahi - Ready!");
