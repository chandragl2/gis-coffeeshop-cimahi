// Filter functionality untuk public map page
// Mendukung filter by kecamatan, kelurahan, category, dan search

let allCoffeeshops = [];
let filteredCoffeeshops = [];
let filterState = {
  kecamatan: "",
  kelurahan: "",
  category: "",
  search: "",
};

// ==========================================
// Initialize Filter
// ==========================================
function initializeFilter() {
  // Get filter elements
  const filterKecamatan = document.getElementById("filterKecamatan");
  const filterKelurahan = document.getElementById("filterKelurahan");
  const filterKategori = document.getElementById("filterKategori");
  const filterSearch = document.getElementById("filterSearch");
  const filterButton = document.getElementById("filterButton");

  if (!filterKecamatan) return; // Filter not available on this page

  // Event listeners
  filterKecamatan.addEventListener("change", (e) => {
    filterState.kecamatan = e.target.value;
    updateKelurahanOptions();
  });

  filterKelurahan.addEventListener("change", (e) => {
    filterState.kelurahan = e.target.value;
  });

  filterKategori.addEventListener("change", (e) => {
    filterState.category = e.target.value;
  });

  filterSearch.addEventListener("input", (e) => {
    filterState.search = e.target.value;
  });

  filterButton.addEventListener("click", applyFilter);

  // Also allow Enter key in search field
  filterSearch.addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
      applyFilter();
    }
  });
}

// ==========================================
// Populate Filter Dropdowns
// ==========================================
function populateFilterOptions(coffeeshops) {
  // Store all coffeeshops
  allCoffeeshops = coffeeshops;
  filteredCoffeeshops = [...coffeeshops];

  // Get unique values
  const kecamatanSet = new Set(
    coffeeshops.map((c) => c.kecamatan).filter(Boolean),
  );
  const kelurahanSet = new Set(
    coffeeshops.map((c) => c.kelurahan).filter(Boolean),
  );
  const categorySet = new Set(
    coffeeshops.map((c) => c.category).filter(Boolean),
  );

  // Populate Kecamatan dropdown
  const filterKecamatan = document.getElementById("filterKecamatan");
  if (filterKecamatan) {
    Array.from(kecamatanSet)
      .sort()
      .forEach((kecamatan) => {
        const option = document.createElement("option");
        option.value = kecamatan;
        option.textContent = kecamatan;
        filterKecamatan.appendChild(option);
      });
  }

  // Populate Kategori dropdown
  const filterKategori = document.getElementById("filterKategori");
  if (filterKategori) {
    Array.from(categorySet)
      .sort()
      .forEach((category) => {
        const option = document.createElement("option");
        option.value = category;
        option.textContent = category;
        filterKategori.appendChild(option);
      });
  }

  // Populate Kelurahan (all initially)
  updateKelurahanOptions();

  // Update stats
  updateFilterStats();
}

// ==========================================
// Update Kelurahan options based on selected Kecamatan
// ==========================================
function updateKelurahanOptions() {
  const filterKecamatan = document.getElementById("filterKecamatan");
  const filterKelurahan = document.getElementById("filterKelurahan");

  if (!filterKecamatan || !filterKelurahan) return;

  const selectedKecamatan = filterKecamatan.value;
  const currentKelurahan = filterKelurahan.value;

  // Remove all options except the first one
  while (filterKelurahan.options.length > 1) {
    filterKelurahan.remove(1);
  }

  // Get kelurahan based on selected kecamatan
  let kelurahanSet = new Set();

  if (selectedKecamatan) {
    kelurahanSet = new Set(
      allCoffeeshops
        .filter((c) => c.kecamatan === selectedKecamatan)
        .map((c) => c.kelurahan)
        .filter(Boolean),
    );
  } else {
    kelurahanSet = new Set(
      allCoffeeshops.map((c) => c.kelurahan).filter(Boolean),
    );
  }

  // Add kelurahan options
  Array.from(kelurahanSet)
    .sort()
    .forEach((kelurahan) => {
      const option = document.createElement("option");
      option.value = kelurahan;
      option.textContent = kelurahan;
      filterKelurahan.appendChild(option);
    });

  // Reset kelurahan if the previously selected value is no longer available
  if (
    currentKelurahan &&
    !Array.from(kelurahanSet).includes(currentKelurahan)
  ) {
    filterKelurahan.value = "";
    filterState.kelurahan = "";
  }
}

// ==========================================
// Apply Filter
// ==========================================
function applyFilter() {
  filteredCoffeeshops = allCoffeeshops.filter((coffeeshop) => {
    let match = true;

    if (
      filterState.kecamatan &&
      coffeeshop.kecamatan !== filterState.kecamatan
    ) {
      match = false;
    }

    if (
      filterState.kelurahan &&
      coffeeshop.kelurahan !== filterState.kelurahan
    ) {
      match = false;
    }

    if (filterState.category && coffeeshop.category !== filterState.category) {
      match = false;
    }

    if (filterState.search) {
      const searchLower = filterState.search.toLowerCase();
      if (
        !coffeeshop.name.toLowerCase().includes(searchLower) &&
        !coffeeshop.address.toLowerCase().includes(searchLower)
      ) {
        match = false;
      }
    }

    return match;
  });

  // Update map with filtered data
  addMarkersToMap(filteredCoffeeshops);

  // Update stats
  updateFilterStats();

  console.log(`✅ Filter applied: ${filteredCoffeeshops.length} results`);
}

// ==========================================
// Update Filter Stats
// ==========================================
function updateFilterStats() {
  // Count unique values in all data
  const allKecamatan = new Set(
    allCoffeeshops.map((c) => c.kecamatan).filter(Boolean),
  );
  const allKelurahan = new Set(
    allCoffeeshops.map((c) => c.kelurahan).filter(Boolean),
  );
  const allKategori = new Set(
    allCoffeeshops.map((c) => c.category).filter(Boolean),
  );

  document.getElementById("statKecamatan").textContent = allKecamatan.size;
  document.getElementById("statKelurahan").textContent = allKelurahan.size;
  document.getElementById("statKategori").textContent = allKategori.size;
  document.getElementById("statCoffeeshop").textContent =
    filteredCoffeeshops.length;
}

// ==========================================
// Reset Filter
// ==========================================
function resetFilter() {
  filterState = {
    kecamatan: "",
    kelurahan: "",
    category: "",
    search: "",
  };

  document.getElementById("filterKecamatan").value = "";
  document.getElementById("filterKelurahan").value = "";
  document.getElementById("filterKategori").value = "";
  document.getElementById("filterSearch").value = "";

  filteredCoffeeshops = [...allCoffeeshops];
  addMarkersToMap(filteredCoffeeshops);
  updateFilterStats();
}
