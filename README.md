# Velvet Vogue - E-commerce Website

Velvet Vogue is an e-commerce website designed for trendy casual and formal wear, targeting young adults who want to express their identity through style.

## Features

* **User Registration and Login:** Secure user account system.
* **Customer Dashboard:** Manage account information and order history.
* **Product Browse:** Search and view products by various categories.
* **Product Details Page:** Detailed information for each product.
* **Shopping Cart:** Add, modify quantity, and remove selected products.
* **Checkout Process:** (Currently in the initial phase)
* **Admin Panel:** Manage products, orders (partially), and customer inquiries.
* **Contact Form and FAQ:** For customer support.
* **Static Pages:** Informational pages like Shipping & Returns, Privacy Policy.
* **Responsive Design:** User interface adaptable to various devices.

## Technologies Used

* **Frontend:** HTML, CSS, JavaScript
* **Backend:** PHP
* **Database:** MySQL (as implied by `config.php` and `setup-database.php` files)
* **Web Server:** Apache, Nginx (to be set up by the user)

## Setup and Installation

1.  **Clone the Repository:**
    ```bash
    git clone <https://github.com/SathsaraJayantha01/Velvet-Vogue-ecommerce.git>
    ```
2.  **Set up a Web Server:** Install a web server environment like XAMPP, WAMP, or MAMP.
3.  **Create a MySQL Database:** Create a database named `velvet_vogue`.
4.  **Import Database Structure:** Run the `setup-database.php` file through your web browser to set up the database schema and initial data.
    (e.g., `http://localhost/your_project_folder/setup-database.php`)
5.  **(Optional) Populate Categories:** Run `add-categories.php` if categories were not populated during setup.
6.  **(Optional) Populate Sample Inquiries:** Run `add-inquiries.php` to add sample contact inquiries.
7.  **Update Database Credentials:** If necessary, update the database connection details (`$servername`, `$username`, `$password`, `$dbname`) in the `config.php` file.
8.  **Access the Project:** Open the project in your web browser via your local web server (e.g., `http://localhost/velvet-vogue-ecommerce/`).

## Admin Panel

* **Access:** Via the `admin.php` file.
* **Default Credentials** (as per `setup-database.php`):
    * **Username:** `admin`
    * **Password:** `admin123`
    * This your are can be change
