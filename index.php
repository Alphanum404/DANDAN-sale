<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dandan Sale Scraper</title>
    <link rel="icon" type="image/x-icon" href="https://dandanku.com/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .product-card {
            height: 100%;
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .product-image {
            height: 200px;
            object-fit: contain;
        }
        .loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            color: white;
        }
        .filters {
            display: none;
        }
        .discount-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
        }
        .discount-period {
            font-size: 0.7rem;
        }
        .theme-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        [data-bs-theme="dark"] .theme-toggle .bi-moon-fill {
            display: none;
        }
        [data-bs-theme="dark"] .theme-toggle .bi-sun-fill {
            display: inline;
        }
        [data-bs-theme="light"] .theme-toggle .bi-moon-fill {
            display: inline;
        }
        [data-bs-theme="light"] .theme-toggle .bi-sun-fill {
            display: none;
        }
        .cursor-pointer {
            cursor: pointer;
        }
        .last-update {
            font-size: 0.8rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <h1 class="text-center mb-4">Dandan Sale Product Scraper</h1>
        
        <div class="row mb-4">
            <div class="col-12 text-center">
                <button id="fetchData" class="btn btn-primary btn-lg">Ambil Data</button>
                <div class="mt-2">
                    <small id="status" class="text-muted"></small>
                    <div class="last-update mt-1" id="lastUpdate"></div>
                </div>
            </div>
        </div>
        
        <div class="filters mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="searchInput" class="form-label">Cari Produk</label>
                    <input type="text" id="searchInput" class="form-control" placeholder="Nama produk...">
                </div>
                <div class="col-md-3">
                    <label for="categoryFilter" class="form-label">Filter Kategori</label>
                    <select id="categoryFilter" class="form-select">
                        <option value="">Semua Kategori</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="brandFilter" class="form-label">Filter Brand</label>
                    <select id="brandFilter" class="form-select">
                        <option value="">Semua Brand</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="discountFilter" class="form-label">Filter Diskon</label>
                    <select id="discountFilter" class="form-select">
                        <option value="">Semua Produk</option>
                        <option value="yes">Produk Diskon</option>
                        <option value="no">Produk Non-Diskon</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Rentang Harga</label>
                    <div class="d-flex">
                        <input type="number" id="priceMin" class="form-control me-2" placeholder="Min">
                        <input type="number" id="priceMax" class="form-control" placeholder="Max">
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button id="applyFilter" class="btn btn-success me-2">Terapkan Filter</button>
                    <button id="resetFilter" class="btn btn-outline-secondary">Reset</button>
                </div>
            </div>
        </div>
        
        <div class="row" id="productContainer">
            <div class="col-12 text-center">
                <p>Belum ada data produk. Klik "Ambil Data" untuk memulai.</p>
            </div>
        </div>
    </div>

    <div class="loading" id="loadingIndicator">
        <div class="text-center">
            <div class="spinner-border mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
            <h4 id="loadingText">Mengambil data... (Halaman 1/?)</h4>
            <div class="progress mt-3" style="width: 300px;">
                <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <button class="btn btn-lg rounded-circle shadow theme-toggle" id="themeToggle">
        <i class="bi bi-moon-fill fs-4"></i>
        <i class="bi bi-sun-fill fs-4"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fetchButton = document.getElementById('fetchData');
            const statusElement = document.getElementById('status');
            const lastUpdateElement = document.getElementById('lastUpdate');
            const productContainer = document.getElementById('productContainer');
            const loadingIndicator = document.getElementById('loadingIndicator');
            const loadingText = document.getElementById('loadingText');
            const progressBar = document.getElementById('progressBar');
            const filtersSection = document.querySelector('.filters');
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');
            const brandFilter = document.getElementById('brandFilter');
            const discountFilter = document.getElementById('discountFilter');
            const priceMinInput = document.getElementById('priceMin');
            const priceMaxInput = document.getElementById('priceMax');
            const applyFilterBtn = document.getElementById('applyFilter');
            const resetFilterBtn = document.getElementById('resetFilter');
            const themeToggle = document.getElementById('themeToggle');
            
            let allProducts = [];
            let filteredProducts = [];
            let categories = new Set();
            let brands = new Set();
            
            // Theme toggling
            themeToggle.addEventListener('click', function() {
                const html = document.documentElement;
                const currentTheme = html.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                html.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);
            });
            
            // Check for saved theme preference or set default
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
            
            // Load products from localStorage if available
            loadFromLocalStorage();
            
            fetchButton.addEventListener('click', async function() {
                loadingIndicator.style.display = 'flex';
                fetchButton.disabled = true;
                statusElement.textContent = 'Mengambil data..';
                
                try {
                    allProducts = [];
                    categories.clear();
                    brands.clear();
                    let pageAmount = 0;
                    
                    // First request to get total pages
                    const initialResponse = await fetch('fetch_products.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'page=1'
                    });
                    
                    if (!initialResponse.ok) {
                        throw new Error(`HTTP error! status: ${initialResponse.status}`);
                    }
                    
                    const initialData = await initialResponse.json();
                    
                    if (initialData.Status === "OK" && initialData.Data) {
                        pageAmount = initialData.Data.page_amount || 0;
                        const productCount = initialData.Data.product_count || 0;
                        
                        // Add products from first page
                        if (initialData.Data.product) {
                            allProducts = [...allProducts, ...initialData.Data.product];
                            
                            // Extract unique categories and brands
                            initialData.Data.product.forEach(product => {
                                extractCategoriesAndBrands(product);
                            });
                        }
                        
                        loadingText.textContent = `Mengambil data... (Halaman 1/${pageAmount})`;
                        
                        // Fetch remaining pages
                        for (let page = 2; page <= pageAmount; page++) {
                            loadingText.textContent = `Mengambil data... (Halaman ${page}/${pageAmount})`;
                            progressBar.style.width = `${(page/pageAmount) * 100}%`;
                            
                            const response = await fetch('fetch_products.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `page=${page}`
                            });
                            
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            
                            const data = await response.json();
                            
                            if (data.Status === "OK" && data.Data && data.Data.product) {
                                allProducts = [...allProducts, ...data.Data.product];
                                
                                // Extract unique categories and brands
                                data.Data.product.forEach(product => {
                                    extractCategoriesAndBrands(product);
                                });
                            }
                        }
                        
                        // Save to local storage
                        saveToLocalStorage(allProducts, Array.from(categories), Array.from(brands), productCount, pageAmount);
                        
                        const lastUpdate = new Date().toLocaleString('id-ID');
                        lastUpdateElement.textContent = `Terakhir diperbarui: ${lastUpdate}`;
                        localStorage.setItem('lastUpdate', lastUpdate);
                        
                        statusElement.textContent = `Berhasil mengambil ${allProducts.length} dari ${productCount} produk (Total halaman: ${pageAmount}).`;
                    } else {
                        throw new Error('Failed to get product data');
                    }
                    
                    // Save data to server
                    await fetch('save_data.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(allProducts)
                    });
                    
                    // Populate filter dropdowns
                    populateFilters();
                    
                    // Show all products initially
                    filteredProducts = [...allProducts];
                    displayProducts(filteredProducts);
                    
                    filtersSection.style.display = 'block';
                    
                } catch (error) {
                    console.error('Error:', error);
                    statusElement.textContent = `Error: ${error.message}`;
                } finally {
                    loadingIndicator.style.display = 'none';
                    fetchButton.disabled = false;
                }
            });
            
            function loadFromLocalStorage() {
                try {
                    // Load products data
                    const savedProducts = localStorage.getItem('dandanProducts');
                    if (savedProducts) {
                        allProducts = JSON.parse(savedProducts);
                        filteredProducts = [...allProducts];
                        
                        // Load categories and brands
                        const savedCategories = localStorage.getItem('dandanCategories');
                        const savedBrands = localStorage.getItem('dandanBrands');
                        
                        if (savedCategories) {
                            JSON.parse(savedCategories).forEach(cat => categories.add(cat));
                        }
                        
                        if (savedBrands) {
                            JSON.parse(savedBrands).forEach(brand => brands.add(brand));
                        }
                        
                        // Populate filter dropdowns
                        populateFilters();
                        
                        // Display products
                        displayProducts(filteredProducts);
                        
                        // Get metadata
                        const productCount = localStorage.getItem('productCount') || allProducts.length;
                        const pageAmount = localStorage.getItem('pageAmount') || '?';
                        const lastUpdate = localStorage.getItem('lastUpdate');
                        
                        if (lastUpdate) {
                            lastUpdateElement.textContent = `Terakhir diperbarui: ${lastUpdate}`;
                        }
                        
                        statusElement.textContent = `Menampilkan ${allProducts.length} dari ${productCount} produk (Tersimpan lokal).`;
                        filtersSection.style.display = 'block';
                    }
                } catch (error) {
                    console.error('Error loading from localStorage:', error);
                }
            }
            
            function saveToLocalStorage(products, categoriesArray, brandsArray, productCount, pageAmount) {
                try {
                    localStorage.setItem('dandanProducts', JSON.stringify(products));
                    localStorage.setItem('dandanCategories', JSON.stringify(categoriesArray));
                    localStorage.setItem('dandanBrands', JSON.stringify(brandsArray));
                    localStorage.setItem('productCount', productCount);
                    localStorage.setItem('pageAmount', pageAmount);
                } catch (error) {
                    console.error('Error saving to localStorage:', error);
                    // If quota exceeded, clear storage and try again
                    if (error instanceof DOMException && error.name === 'QuotaExceededError') {
                        alert('Penyimpanan lokal penuh. Hanya sebagian data yang akan disimpan.');
                        localStorage.clear();
                        try {
                            // Try saving with fewer items if needed
                            const reducedProducts = products.slice(0, 500); // Save only first 500 products
                            localStorage.setItem('dandanProducts', JSON.stringify(reducedProducts));
                            localStorage.setItem('dandanCategories', JSON.stringify(categoriesArray));
                            localStorage.setItem('dandanBrands', JSON.stringify(brandsArray));
                        } catch (e) {
                            console.error('Still failed to save to localStorage after clearing:', e);
                        }
                    }
                }
            }
            
            function extractCategoriesAndBrands(product) {
                if (product.category && product.category.name) {
                    categories.add(JSON.stringify({
                        id: product.category.id,
                        name: product.category.name
                    }));
                }
                
                if (product.brand && product.brand.name) {
                    brands.add(JSON.stringify({
                        id: product.brand.id,
                        name: product.brand.name
                    }));
                }
            }
            
            function populateFilters() {
                // Populate category filter
                categoryFilter.innerHTML = '<option value="">Semua Kategori</option>';
                Array.from(categories)
                    .map(cat => JSON.parse(cat))
                    .sort((a, b) => a.name.localeCompare(b.name))
                    .forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.id;
                        option.textContent = cat.name;
                        categoryFilter.appendChild(option);
                    });
                
                // Populate brand filter
                brandFilter.innerHTML = '<option value="">Semua Brand</option>';
                Array.from(brands)
                    .map(brand => JSON.parse(brand))
                    .sort((a, b) => a.name.localeCompare(b.name))
                    .forEach(brand => {
                        const option = document.createElement('option');
                        option.value = brand.id;
                        option.textContent = brand.name;
                        brandFilter.appendChild(option);
                    });
            }
            
            function formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'});
            }
            
            function displayProducts(products) {
                if (products.length === 0) {
                    productContainer.innerHTML = '<div class="col-12 text-center"><p>Tidak ada produk yang sesuai dengan filter.</p></div>';
                    return;
                }
                
                productContainer.innerHTML = '';
                products.forEach(product => {
                    const discountPrice = product.discount_value ? (product.price - product.discount_value) : product.price;
                    const hasDiscount = product.discount_value > 0;
                    const discountPercent = hasDiscount ? Math.round((product.discount_value / product.price) * 100) : 0;
                    
                    const productCard = document.createElement('div');
                    productCard.className = 'col-md-3 mb-4';
                    
                    let discountBadge = '';
                    if (hasDiscount && product.discount_type) {
                        const startDate = formatDate(product.discount_start_period);
                        const endDate = formatDate(product.discount_end_period);
                        
                        discountBadge = `
                            <div class="discount-badge">
                                <span class="badge bg-danger">-${discountPercent}%</span>
                            </div>
                            <div class="discount-period text-center mt-1 small text-muted">
                                ${startDate} - ${endDate}
                            </div>
                        `;
                    }
                    
                    productCard.innerHTML = `
                        <div class="card product-card cursor-pointer" onclick="window.open('https://dandanku.com/product/${product.slug}', '_blank')">
                            ${discountBadge}
                            <img src="${product.picture}" class="card-img-top product-image" alt="${product.name}" loading="lazy">
                            <div class="card-body">
                                <h6 class="card-title">${product.name}</h6>
                                <p class="card-text mb-1">
                                    ${product.brand && product.brand.name ? `<span class="badge bg-secondary">${product.brand.name}</span>` : ''}
                                    ${product.category && product.category.name ? `<span class="badge bg-info text-dark">${product.category.name}</span>` : ''}
                                </p>
                                <div class="mt-2">
                                    ${hasDiscount ? 
                                        `<p class="card-text mb-0"><del>Rp ${product.price.toLocaleString()}</del></p>
                                        <p class="card-text text-danger fw-bold">Rp ${discountPrice.toLocaleString()}</p>` : 
                                        `<p class="card-text fw-bold">Rp ${product.price.toLocaleString()}</p>`
                                    }
                                </div>
                            </div>
                        </div>
                    `;
                    productContainer.appendChild(productCard);
                });
            }
            
            // Filter functionality
            applyFilterBtn.addEventListener('click', applyFilters);
            
            // Search while typing
            searchInput.addEventListener('input', function() {
                if (searchInput.value.length >= 3 || searchInput.value.length === 0) {
                    applyFilters();
                }
            });
            
            function applyFilters() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const categoryId = categoryFilter.value;
                const brandId = brandFilter.value;
                const discountValue = discountFilter.value;
                const minPrice = priceMinInput.value ? parseInt(priceMinInput.value) : 0;
                const maxPrice = priceMaxInput.value ? parseInt(priceMaxInput.value) : Infinity;
                
                filteredProducts = allProducts.filter(product => {
                    // Filter by search term
                    const matchesSearch = searchTerm === '' || 
                        (product.name && product.name.toLowerCase().includes(searchTerm));
                    
                    // Filter by category
                    const matchesCategory = !categoryId || 
                        (product.category && product.category.id == categoryId);
                    
                    // Filter by brand
                    const matchesBrand = !brandId || 
                        (product.brand && product.brand.id == brandId);
                    
                    // Filter by price
                    const price = product.discount_value ? 
                        (product.price - product.discount_value) : product.price;
                    const matchesPrice = price >= minPrice && price <= maxPrice;
                    
                    // Filter by discount
                    let matchesDiscount = true;
                    if (discountValue === 'yes') {
                        matchesDiscount = product.discount_value > 0;
                    } else if (discountValue === 'no') {
                        matchesDiscount = !product.discount_value || product.discount_value === 0;
                    }
                    
                    return matchesSearch && matchesCategory && matchesBrand && matchesPrice && matchesDiscount;
                });
                
                displayProducts(filteredProducts);
                statusElement.textContent = `Menampilkan ${filteredProducts.length} produk.`;
            }
            
            resetFilterBtn.addEventListener('click', function() {
                searchInput.value = '';
                categoryFilter.value = '';
                brandFilter.value = '';
                discountFilter.value = '';
                priceMinInput.value = '';
                priceMaxInput.value = '';
                
                filteredProducts = [...allProducts];
                displayProducts(filteredProducts);
                statusElement.textContent = `Menampilkan semua ${allProducts.length} produk.`;
            });
        });
    </script>
</body>
</html>
