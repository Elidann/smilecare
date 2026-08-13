FoodSpots PPC 

A web app that showcases local favorite food spots in Puerto Princesa, Palawan — helping locals and tourists discover where to eat beyond the usual tourist trail.

About

FoodSpots PPC is a PHP-based web application that highlights popular restaurants and local dishes around Puerto Princesa. It organizes food spots by category (Grilled & Barbeque, Seafoods, Comfort & Local Favorites) and lets users browse restaurants, view details, and leave reviews after logging in.

Features
Browse restaurants by category: Grilled & Barbeque, Seafoods, Comfort & Local Favorites
View a curated list of restaurants with photos
User registration and login system
Submit and view restaurant reviews
Responsive navigation with dropdown categories
Tech Stack
Backend: PHP
Database: MySQL (via XAMPP)
Frontend: HTML, CSS, JavaScript
Environment: XAMPP (Apache + MySQL)
Project Structure
foodSpotsPPC/
├── index.php              # Homepage
├── restaurants.php        # Restaurant listing
├── grilled.php            # Grilled & Barbeque category
├── seafoods.php           # Seafoods category
├── comfort.php             # Comfort & Local Favorites category
├── login.php / register.php   # User authentication
├── reviews.php             # Reviews page
├── submit_review.php       # Handles review submission
├── get_reviews.php         # Fetches reviews (used for AJAX/dynamic loading)
├── db.php                  # Database connection
├── includes/header.php     # Shared navigation used across pages
└── css/                    # Stylesheets
Getting Started
Prerequisites
XAMPP installed
PHP and MySQL running via XAMPP's control panel
Setup
Clone this repository into your XAMPP htdocs folder:
bash
   git clone https://github.com/Elidann/foodSpotsPPC.git
Start Apache and MySQL from the XAMPP control panel.
Create a database named foodspot_db in phpMyAdmin.
Import the provided SQL file (if included) to set up the tables.
Open http://localhost/foodSpotsPPC/index.php in your browser.

Note: Always access the project through index.php via Apache (http://localhost/...), not by opening the HTML files directly — PHP pages won't run correctly otherwise.



Project Status

This project is a work in progress, built as part of my portfolio while studying BSIT and preparing for OJT.

Author

Elidan  — BSIT student, Palawan, Philippines
