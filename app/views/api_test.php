<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Test Interface</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        h1, h2 {
            color: #333;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .endpoint {
            margin-bottom: 20px;
        }
        .endpoint label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .endpoint input, .endpoint textarea, .endpoint select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .endpoint button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .endpoint button:hover {
            background-color: #0056b3;
        }
        .response {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            white-space: pre-wrap;
            font-family: monospace;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h1>API Test Interface</h1>

    <!-- Product Endpoints -->
    <div class="section">
        <h2>Product Endpoints</h2>

        <!-- Get All Products -->
        <div class="endpoint">
            <label>GET /api/all-products</label>
            <button onclick="callApi('GET', '/WebBanHang/api/all-products', null, 'all-products-response')">Fetch All Products</button>
            <div class="response" id="all-products-response"></div>
        </div>

        <!-- Get Products with Filters -->
        <div class="endpoint">
            <label>GET /api/products (with filters)</label>
            <input type="text" id="category_id" placeholder="Category ID (optional)">
            <input type="number" id="price_min" placeholder="Price Min (optional)">
            <input type="number" id="price_max" placeholder="Price Max (optional)">
            <input type="text" id="search_name" placeholder="Search Name (optional)">
            <select id="sort">
                <option value="">Sort (optional)</option>
                <option value="price_asc">Price Ascending</option>
                <option value="price_desc">Price Descending</option>
            </select>
            <button onclick="getProductsWithFilters()">Fetch Products</button>
            <div class="response" id="products-response"></div>
        </div>

        <!-- Get Product by ID -->
        <div class="endpoint">
            <label>GET /api/products/{id}</label>
            <input type="number" id="product_id_get" placeholder="Product ID">
            <button onclick="callApi('GET', `/WebBanHang/api/products/${document.getElementById('product_id_get').value}`, null, 'product-get-response')">Fetch Product</button>
            <div class="response" id="product-get-response"></div>
        </div>

        <!-- Add Product -->
        <div class="endpoint">
            <label>POST /api/products</label>
            <input type="text" id="product_name" placeholder="Name">
            <textarea id="product_description" placeholder="Description"></textarea>
            <input type="number" id="product_price" placeholder="Price">
            <input type="number" id="product_quantity" placeholder="Quantity">
            <input type="number" id="product_category_id" placeholder="Category ID">
            <input type="text" id="product_image" placeholder="Image URL (optional)">
            <button onclick="addProduct()">Add Product</button>
            <div class="response" id="product-add-response"></div>
        </div>

        <!-- Update Product -->
        <div class="endpoint">
            <label>PUT /api/products/{id}</label>
            <input type="number" id="product_id_update" placeholder="Product ID">
            <input type="text" id="product_name_update" placeholder="Name">
            <textarea id="product_description_update" placeholder="Description"></textarea>
            <input type="number" id="product_price_update" placeholder="Price">
            <input type="number" id="product_quantity_update" placeholder="Quantity">
            <input type="number" id="product_category_id_update" placeholder="Category ID">
            <input type="text" id="product_image_update" placeholder="Image URL (optional)">
            <button onclick="updateProduct()">Update Product</button>
            <div class="response" id="product-update-response"></div>
        </div>

        <!-- Delete Product -->
        <div class="endpoint">
            <label>DELETE /api/products/{id}</label>
            <input type="number" id="product_id_delete" placeholder="Product ID">
            <button onclick="callApi('DELETE', `/WebBanHang/api/products/${document.getElementById('product_id_delete').value}`, null, 'product-delete-response')">Delete Product</button>
            <div class="response" id="product-delete-response"></div>
        </div>
    </div>

    <!-- Category Endpoints -->
    <div class="section">
        <h2>Category Endpoints</h2>

        <!-- Get All Categories -->
        <div class="endpoint">
            <label>GET /api/all-categories</label>
            <button onclick="callApi('GET', '/WebBanHang/api/all-categories', null, 'all-categories-response')">Fetch All Categories</button>
            <div class="response" id="all-categories-response"></div>
        </div>

        <!-- Get Categories -->
        <div class="endpoint">
            <label>GET /api/categories</label>
            <button onclick="callApi('GET', '/WebBanHang/api/categories', null, 'categories-response')">Fetch Categories</button>
            <div class="response" id="categories-response"></div>
        </div>

        <!-- Get Category by ID -->
        <div class="endpoint">
            <label>GET /api/categories/{id}</label>
            <input type="number" id="category_id_get" placeholder="Category ID">
            <button onclick="callApi('GET', `/WebBanHang/api/categories/${document.getElementById('category_id_get').value}`, null, 'category-get-response')">Fetch Category</button>
            <div class="response" id="category-get-response"></div>
        </div>

        <!-- Add Category -->
        <div class="endpoint">
            <label>POST /api/categories</label>
            <input type="text" id="category_name" placeholder="Name">
            <textarea id="category_description" placeholder="Description (optional)"></textarea>
            <button onclick="addCategory()">Add Category</button>
            <div class="response" id="category-add-response"></div>
        </div>

        <!-- Update Category -->
        <div class="endpoint">
            <label>PUT /api/categories/{id}</label>
            <input type="number" id="category_id_update" placeholder="Category ID">
            <input type="text" id="category_name_update" placeholder="Name">
            <textarea id="category_description_update" placeholder="Description (optional)"></textarea>
            <button onclick="updateCategory()">Update Category</button>
            <div class="response" id="category-update-response"></div>
        </div>

        <!-- Delete Category -->
        <div class="endpoint">
            <label>DELETE /api/categories/{id}</label>
            <input type="number" id="category_id_delete" placeholder="Category ID">
            <button onclick="callApi('DELETE', `/WebBanHang/api/categories/${document.getElementById('category_id_delete').value}`, null, 'category-delete-response')">Delete Category</button>
            <div class="response" id="category-delete-response"></div>
        </div>
    </div>

    <script>
        // Generic function to call API
        async function callApi(method, url, body, responseId) {
            const responseDiv = document.getElementById(responseId);
            responseDiv.textContent = 'Loading...';
            responseDiv.classList.remove('error');

            try {
                const options = {
                    method,
                    headers: { 'Content-Type': 'application/json' }
                };
                if (body) {
                    options.body = JSON.stringify(body);
                }
                const response = await fetch(url, options);
                const data = await response.json();
                responseDiv.textContent = JSON.stringify(data, null, 2);
                if (!response.ok) {
                    responseDiv.classList.add('error');
                }
            } catch (error) {
                responseDiv.textContent = `Error: ${error.message}`;
                responseDiv.classList.add('error');
            }
        }

        // Get Products with Filters
        async function getProductsWithFilters() {
            const params = new URLSearchParams();
            const categoryId = document.getElementById('category_id').value;
            const priceMin = document.getElementById('price_min').value;
            const priceMax = document.getElementById('price_max').value;
            const searchName = document.getElementById('search_name').value;
            const sort = document.getElementById('sort').value;

            if (categoryId) params.append('category_id', categoryId);
            if (priceMin) params.append('price_min', priceMin);
            if (priceMax) params.append('price_max', priceMax);
            if (searchName) params.append('search_name', searchName);
            if (sort) params.append('sort', sort);

            const url = `/WebBanHang/api/products?${params.toString()}`;
            await callApi('GET', url, null, 'products-response');
        }

        // Add Product
        async function addProduct() {
            const body = {
                name: document.getElementById('product_name').value,
                description: document.getElementById('product_description').value,
                price: parseFloat(document.getElementById('product_price').value),
                quantity: parseInt(document.getElementById('product_quantity').value),
                category_id: parseInt(document.getElementById('product_category_id').value),
                image: document.getElementById('product_image').value
            };
            await callApi('POST', '/WebBanHang/api/products', body, 'product-add-response');
        }

        // Update Product
        async function updateProduct() {
            const id = document.getElementById('product_id_update').value;
            const body = {
                name: document.getElementById('product_name_update').value,
                description: document.getElementById('product_description_update').value,
                price: parseFloat(document.getElementById('product_price_update').value),
                quantity: parseInt(document.getElementById('product_quantity_update').value),
                category_id: parseInt(document.getElementById('product_category_id_update').value),
                image: document.getElementById('product_image_update').value
            };
            await callApi('PUT', `/WebBanHang/api/products/${id}`, body, 'product-update-response');
        }

        // Add Category
        async function addCategory() {
            const body = {
                name: document.getElementById('category_name').value,
                description: document.getElementById('category_description').value
            };
            await callApi('POST', '/WebBanHang/api/categories', body, 'category-add-response');
        }

        // Update Category
        async function updateCategory() {
            const id = document.getElementById('category_id_update').value;
            const body = {
                name: document.getElementById('category_name_update').value,
                description: document.getElementById('category_description_update').value
            };
            await callApi('PUT', `/WebBanHang/api/categories/${id}`, body, 'category-update-response');
        }
    </script>
</body>
</html>