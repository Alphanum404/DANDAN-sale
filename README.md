# 🛍️ DANDAN-sale

<p align="center">
    <img src="https://dandanku.com/static/media/logo-new1.4b6ab3d6df11e0d33843.png" alt="Dandanku Logo" width="300">
</p>

> **Powerful Product Scraper for Dandanku Sales**

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![PHP Version](https://img.shields.io/badge/php-7.4%2B-777BB4.svg)
![Bootstrap](https://img.shields.io/badge/bootstrap-5.3.0-7952B3.svg)
![Status](https://img.shields.io/badge/status-active-success.svg)

## 📋 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Technologies Used](#-technologies-used)
- [Prerequisites](#-prerequisites)
- [Installation](#-installation)
- [Usage](#-usage)
- [Project Structure](#-project-structure)
- [How It Works](#-how-it-works)
- [Data Formats](#-data-formats)
- [Contributing](#-contributing)
- [License](#-license)

## 🔍 Overview

DANDAN-sale is a comprehensive web-based tool designed to scrape and display sale products from Dandanku's platform. This application allows users to efficiently browse, search, and filter discounted products, making it easier to find the best deals without manual navigation through the original website.

## ✨ Features

- 📊 **Data Scraping**: Automatically fetches product data from Dandanku API
- 💾 **Local Storage**: Saves product information in JSON and CSV formats
- 🔎 **Search Functionality**: Filter products by name, category, or brand
- 🌓 **Dark/Light Mode**: Toggle between visual themes for better readability
- 📱 **Responsive Design**: Optimized for both desktop and mobile devices
- 🏷️ **Discount Highlighting**: Visual indicators for products on sale
- 🔄 **Progress Indicator**: Visual feedback during data retrieval

## 🛠️ Technologies Used

- ![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white) PHP for backend processing and API communication
- ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black) JavaScript for frontend interactivity
- ![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white) Bootstrap 5 for responsive UI components
- ![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white) HTML5 for structure
- ![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white) CSS3 for styling
- ![JSON](https://img.shields.io/badge/JSON-000000?style=for-the-badge&logo=json&logoColor=white) JSON for data storage

## 🔧 Prerequisites

- PHP 7.4 or higher
- Web server (Apache, Nginx, etc.)
- cURL extension for PHP
- Modern web browser (Chrome, Firefox, Safari, Edge)

## 📥 Installation

1. Clone the repository or download the zip file
   ```bash
   git clone https://github.com/Alphanum404/DANDAN-sale.git
   ```

2. Place the files in your web server's document root or a subdirectory

3. Ensure your server has PHP enabled with the cURL extension

4. Navigate to the project in your web browser
   ```
   http://localhost/DANDAN-sale/
   ```

## 🚀 Usage

1. Open the application in your web browser
2. Click the "Ambil Data" (Fetch Data) button to start scraping products
3. Wait for the data retrieval process to complete
4. Use the search box and filters to find specific products
5. Click on product cards to view more details
6. Toggle between light and dark themes using the button in the bottom right corner

## 📁 Project Structure

```
├── index.php              # Main application UI
├── fetch_products.php     # API handler for fetching products
├── save_data.php          # Handler for saving product data to files
├── products_data.json     # Generated JSON data storage
├── products_data.csv      # Generated CSV data storage (created after first fetch)
└── README.md              # Project documentation
```

## ⚙️ How It Works

1. **Data Retrieval**: The application sends requests to the Dandanku API using cURL in PHP
2. **Data Processing**: Received data is processed and formatted for display and storage
3. **Storage**: Products are stored in both JSON and CSV formats for flexibility
4. **Display**: Bootstrap-based UI presents the products in a responsive grid layout
5. **Filtering**: JavaScript handles search functionality and filtering options

## 📊 Data Formats

### JSON Structure
```json
[
  {
    "id": "product_id",
    "name": "Product Name",
    "slug": "product-slug",
    "price": 100000,
    "discount_value": 20000,
    "brand": {
      "id": "brand_id",
      "name": "Brand Name"
    },
    "category": {
      "id": "category_id", 
      "name": "Category Name"
    },
    "picture": "https://example.com/image.jpg"
  }
]
```

### CSV Columns
- ID
- Name
- Slug
- Price
- Discount Value
- Final Price
- Brand ID
- Brand Name
- Category ID
- Category Name
- Picture URL

## 👥 Contributing

Contributions are welcome! Feel free to submit issues or pull requests if you have suggestions for improvements or found any bugs.

1. Fork the repository
2. Create your feature branch
   ```bash
   git checkout -b feature/amazing-feature
   ```
3. Commit your changes
   ```bash
   git commit -m 'Add some amazing feature'
   ```
4. Push to the branch
   ```bash
   git push origin feature/amazing-feature
   ```
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

---

Made with ❤️ for easier shopping experience