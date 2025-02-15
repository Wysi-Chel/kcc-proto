Lou Geh Car Dealership Prototype

Technologies Used
Backend: PHP
Database: MySQL
Frontend: HTML, CSS, JavaScript (jQuery)
Server Environment: XAMPP (Apache, MySQL)



Setup Instructions
1. Install XAMPP
Download and install XAMPP from Apache Friends.
Launch the XAMPP Control Panel and start Apache and MySQL.
2. Clone the Repository
Clone or download the repository into your XAMPP htdocs folder. For example, using Git from a command prompt:

bash
cd C:\xampp\htdocs
git clone https://github.com/(your-username)/(your-repo).git my-app
Make sure the project folder is directly under htdocs (e.g., C:\xampp\htdocs\my-app).

3. Database Setup
Open phpMyAdmin:
Navigate to http://localhost/phpmyadmin/ in your browser.

Create the Database:

Create a new database named lougeh_db (or update configs/db.php with your preferred database name).


Import the Schema:

Import the provided SQL schema file or run the necessary SQL commands to create the tables (e.g., Customer, Salesperson, Car, Invoice, ServiceTicket, ServiceTicket_Mechanic, ServicePart) and triggers.


4. Configure Database Connection
Open configs/db.php and ensure the connection settings match your setup. For a default XAMPP installation, the file should look like:

php
<?php
$host = 'localhost';
$db   = 'lougeh_db'; // Must match the database name you created
$user = 'root';               // Default XAMPP user
$pass = '';                   // Default XAMPP password is blank

$dsn = "mysql:host=$host;dbname=$db";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
5. Running the Application
Open your browser and navigate to:
http://localhost/kcc-proto/

You should see the main page with sections for Sales, Service, and Lookup Car Details.

6. Usage Guide
Sales Section:
Record a car sale by selecting a salesperson and a customer from the dropdowns, then entering the car details and sale price. An invoice is generated upon sale.

Service Section:
Record a car service by selecting a customer. You can either enter an existing car serial number or provide car details to register a car for service. You must select at least one mechanic from a multi-select dropdown, and enter the service (repair) cost. An invoice is automatically generated for the service. If the customer chooses to purchase the car during service, a second invoice is created for the purchase.

Lookup Section:
Enter a car serial number to look up car details along with associated customer information.

Add Customer:
In both the Sales and Service sections, click the "Add Customer" button to open a modal. Enter the customer’s name, phone, and email. Once added, the new customer appears in the dropdown.