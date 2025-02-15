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

5. Running the Application
Open your browser and navigate to:
http://localhost/kcc-proto/

You should see the main page with sections for Sales, Service, and Lookup Car Details.