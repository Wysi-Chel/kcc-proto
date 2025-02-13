<?php
// controllers/UserController.php
require_once __DIR__ . '/../configs/db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method.");
    }
    if (!isset($_POST['action'])) {
        throw new Exception("No action specified.");
    }
    $action = $_POST['action'];
    
    if ($action == 'sale') {
        // Process a car sale
        $salesperson   = $_POST['salesperson'] ?? null;
        $customer      = $_POST['customer'] ?? null; // CustomerID from dropdown
        $brand         = $_POST['car_make'] ?? null;
        $car_model     = $_POST['car_model'] ?? null;
        $car_year      = $_POST['car_year'] ?? null;
        $car_condition = $_POST['car_condition'] ?? null;
        $sale_price    = $_POST['sale_price'] ?? null;
        
        if (empty($salesperson)) {
            throw new Exception("A car sale must be associated with exactly one salesperson.");
        }
        if (empty($customer)) {
            throw new Exception("Customer selection is required for sale.");
        }
        
        // Check if the car is already sold (assumes uniqueness by Brand, Model, Year)
        $stmt = $pdo->prepare("SELECT * FROM Car WHERE Brand = ? AND Model = ? AND Year = ? AND CustomerID IS NOT NULL");
        $stmt->execute([$brand, $car_model, $car_year]);
        if ($stmt->rowCount() > 0) {
            throw new Exception("This car is already sold and cannot be sold again.");
        }
        
        // Insert into Car table; trigger auto-generates SerialNumber.
        $stmt = $pdo->prepare("INSERT INTO Car (Brand, Model, Year, `Condition`, SalespersonID, CustomerID) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$brand, $car_model, $car_year, $car_condition, $salesperson, $customer]);
        
        // Retrieve generated SerialNumber.
        $stmt = $pdo->prepare("SELECT SerialNumber FROM Car WHERE Brand = ? AND Model = ? AND Year = ? AND SalespersonID = ? AND CustomerID = ? ORDER BY SerialNumber DESC LIMIT 1");
        $stmt->execute([$brand, $car_model, $car_year, $salesperson, $customer]);
        $car = $stmt->fetch();
        if (!$car) {
            throw new Exception("Failed to retrieve the car record.");
        }
        $serialNumber = $car['SerialNumber'];
        
        // Insert into Invoice table; trigger auto-generates InvoiceNumber.
        $stmt = $pdo->prepare("INSERT INTO Invoice (CarSerialNumber, SalespersonID, CustomerID, SaleDate, SalePrice) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->execute([$serialNumber, $salesperson, $customer, $sale_price]);
        
        echo "Sale recorded successfully.";
        
    } elseif ($action == 'service') {
        // Process a car service
        $customer = $_POST['customer'] ?? null; // CustomerID from dropdown
        $car_serial = trim($_POST['car_serial'] ?? "");
        $description = $_POST['description'] ?? null;
        $service_price = $_POST['service_price'] ?? null;
        if (empty($service_price)) {
            throw new Exception("Service cost is required.");
        }
        $purchase = isset($_POST['purchase']) ? true : false;
        $purchase_price = $_POST['purchase_price'] ?? null;
        
        if (empty($customer)) {
            throw new Exception("Customer selection is required for service.");
        }
        
        if (empty($car_serial)) {
            // No car serial provided – expect car details.
            $service_brand = $_POST['service_brand'] ?? null;
            $service_model = $_POST['service_model'] ?? null;
            $service_year = $_POST['service_year'] ?? null;
            $service_condition = $_POST['service_condition'] ?? null;
            
            if (empty($service_brand) || empty($service_model) || empty($service_year) || empty($service_condition)) {
                throw new Exception("For service, please provide car details if the car is not yet registered.");
            }
            
            // Insert new Car record (no Salesperson; only for service)
            $stmt = $pdo->prepare("INSERT INTO Car (Brand, Model, Year, `Condition`, SalespersonID, CustomerID) VALUES (?, ?, ?, ?, NULL, ?)");
            $stmt->execute([$service_brand, $service_model, $service_year, $service_condition, $customer]);
            
            // Retrieve generated SerialNumber.
            $stmt = $pdo->prepare("SELECT SerialNumber FROM Car WHERE Brand = ? AND Model = ? AND Year = ? AND CustomerID = ? ORDER BY SerialNumber DESC LIMIT 1");
            $stmt->execute([$service_brand, $service_model, $service_year, $customer]);
            $car = $stmt->fetch();
            if (!$car) {
                throw new Exception("Failed to register the car for service.");
            }
            $car_serial = $car['SerialNumber'];
        } else {
            // If car serial provided, ensure it exists.
            $stmt = $pdo->prepare("SELECT * FROM Car WHERE SerialNumber = ?");
            $stmt->execute([$car_serial]);
            if ($stmt->rowCount() == 0) {
                throw new Exception("Serial number does not exist.");
            }
        }
        
        // Insert service ticket.
        $stmt = $pdo->prepare("INSERT INTO ServiceTicket (CarSerialNumber, CustomerID, ServiceDate, Description) VALUES (?, ?, NOW(), ?)");
        $stmt->execute([$car_serial, $customer, $description]);
        $ticketID = $pdo->lastInsertId();
        
        // Mechanic is now required for repair.
        if (isset($_POST['mechanics']) && !empty($_POST['mechanics'])) {
            $mechanics = $_POST['mechanics']; // This is already an array.
            $stmt = $pdo->prepare("INSERT INTO ServiceTicket_Mechanic (TicketID, MechanicID) VALUES (?, ?)");
            foreach ($mechanics as $mechanicID) {
                $stmt->execute([$ticketID, $mechanicID]);
            }
        }
        // Always generate an invoice for the service (repair) cost.
        // Using 0 for SalespersonID as a placeholder.
        $stmt = $pdo->prepare("INSERT INTO Invoice (CarSerialNumber, SalespersonID, CustomerID, SaleDate, SalePrice) VALUES (?, NULL, ?, NOW(), ?)");
        $stmt->execute([$car_serial, $customer, $service_price]);
    
        
    } elseif ($action == 'add_customer') {
        // Add new customer (return JSON)
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        if (empty($name)) {
            throw new Exception("Customer name is required.");
        }
        $stmt = $pdo->prepare("INSERT INTO Customer (Name, Phone, Email) VALUES (?, ?, ?)");
        $stmt->execute([$name, $phone, $email]);
        $customerId = $pdo->lastInsertId();
        $customer = [
            "CustomerID" => $customerId,
            "Name" => $name
        ];
        echo json_encode(["success" => true, "customer" => $customer]);
        
    } elseif ($action == 'get_car_details') {
        // Lookup car details by serial number.
        $serial = trim($_POST['serial'] ?? '');
        if (empty($serial)) {
            throw new Exception("Serial number is required.");
        }
        $stmt = $pdo->prepare("SELECT Car.*, Customer.Name AS CustomerName, Customer.Phone AS CustomerPhone, Customer.Email AS CustomerEmail
                               FROM Car
                               LEFT JOIN Customer ON Car.CustomerID = Customer.CustomerID
                               WHERE Car.SerialNumber = ?");
        $stmt->execute([$serial]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            throw new Exception("Car not found.");
        }
        echo json_encode(["success" => true, "details" => $result]);
        
    } else {
        throw new Exception("Invalid action.");
    }
    
} catch (Exception $e) {
    if (in_array($_POST['action'], ['add_customer', 'get_car_details'])) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    } else {
        echo $e->getMessage();
    }
}
