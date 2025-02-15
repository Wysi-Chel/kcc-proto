<?php
// index.php
require_once 'configs/db.php';

// Fetch Salesperson data for sale form
$stmt = $pdo->prepare("SELECT SalespersonID, Name FROM Salesperson");
$stmt->execute();
$salespeople = $stmt->fetchAll();

// Fetch Customer data for dropdowns
$stmt = $pdo->prepare("SELECT CustomerID, Name FROM Customer");
$stmt->execute();
$customers = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT MechanicID, Name FROM Mechanic");
$stmt->execute();
$mechanics = $stmt->fetchAll();
// Define car makes and models (used for both forms)
$carData = [
    "Toyota"    => ["Camry", "Corolla", "Prius", "Highlander"],
    "Honda"     => ["Civic", "Accord", "CR-V"],
    "Suzuki"    => ["Swift", "Vitara"],
    "Ford"      => ["Focus", "Mustang", "F-150"],
    "Chevrolet" => ["Malibu", "Camaro", "Silverado"]
];

$carMakes = array_keys($carData);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Lou Geh Car Dealership</title>
  <link rel="stylesheet" href="css/style.css">
  <!-- Include jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style> 
    @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Text:ital@0;1&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
    body { font-family: "Poppins", serif;font-weight: 400;font-style: normal; margin: 0; padding: 0; }
    header { background: #333; color: #fff; padding: 10px 20px; }
    header h1 { margin: 0; }
    nav ul { list-style: none; padding: 0; margin: 0; display: flex; }
    nav ul li { margin-right: 20px; }
    nav ul li a { color: #fff; text-decoration: none; }
    main { padding: 20px; }
    section { margin-bottom: 40px; border: 1px solid #ccc; padding: 20px; border-radius: 8px; }
    form div { margin-bottom: 15px; }
    label { display: inline-block; width: 150px; }
    input[type="text"], input[type="number"], select, textarea { width: 250px; padding: 5px; }
    button { padding: 8px 15px; background: #007BFF; border: none; color: #fff; border-radius: 4px; cursor: pointer; }
    button:hover { background: #0056b3; }
    /* Modal styles for Add Customer */
    .modal {
      display: none; 
      position: fixed;
      z-index: 1001;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
    }
    .modal-content {
      background: #fff;
      margin: 10% auto;
      padding: 20px;
      width: 400px;
      border-radius: 5px;
      position: relative;
    }
    .close-modal { position: absolute; right: 10px; top: 10px; font-size: 20px; cursor: pointer; }
  </style>
</head>
<body>
  <header class="nav-bar">
    <h1>Lou Geh Car Dealership</h1>
    <nav>
      <ul>
        <li><a href="#sales" class="nav-item">Sales</a></li>
        <li><a href="#service" class="nav-item">Service</a></li>
        <li><a href="#lookup" class="nav-item">Car Lookup</a></li>
      </ul>
    </nav>
  </header>
  <main>
    <!-- Sales Section -->
    <section id="sales">
      <h2>Record a Car Sale</h2>
      <form id="saleForm" action="/kcc-proto/controllers/UserController.php" method="post">
        <input type="hidden" name="action" value="sale">
        <div>
          <label for="salesperson">Salesperson:</label>
          <select id="salesperson" name="salesperson" required>
            <option value="">Select Salesperson</option>
            <?php foreach($salespeople as $person): ?>
              <option value="<?php echo $person['SalespersonID']; ?>"><?php echo $person['Name']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label for="customer">Customer:</label>
          <select id="customer" name="customer" required>
            <option value="">Select Customer</option>
            <?php foreach($customers as $customer): ?>
              <option value="<?php echo $customer['CustomerID']; ?>"><?php echo $customer['Name']; ?></option>
            <?php endforeach; ?>
          </select>
          <!-- <button type="button" id="addCustomerBtn">Add Customer</button> -->
        </div>
        <div>
          <label for="car_make">Car Make:</label>
          <select id="car_make" name="car_make" required>
            <option value="">Select Car Make</option>
            <?php foreach($carMakes as $make): ?>
              <option value="<?php echo $make; ?>"><?php echo $make; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label for="car_model">Car Model:</label>
          <select id="car_model" name="car_model" required>
            <option value="">Select Car Model</option>
          </select>
        </div>
        <div>
          <label for="car_year">Car Year:</label>
          <select id="car_year" name="car_year" required>
            <option value="">Select Car Year</option>
            <?php for($year=2015; $year<=2025; $year++): ?>
              <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
            <?php endfor; ?>
          </select>
        </div>
        <div>
          <label for="car_condition">Car Condition:</label>
          <select id="car_condition" name="car_condition" required>
            <option value="New">New</option>
            <option value="Used">Used</option>
          </select>
        </div>
        <div>
          <label for="sale_price">Sale Price:</label>
          <input type="number" id="sale_price" name="sale_price" required>
        </div>
        <button type="submit">Submit Sale</button>
      </form>
    </section>
    
<!-- Service Section -->
<section id="service">
  <h2>Record a Car Service</h2>
  <form id="serviceForm" action="/kcc-proto/controllers/UserController.php" method="post">
    <input type="hidden" name="action" value="service">
    <div>
      <label for="customer_service">Customer:</label>
      <select id="customer_service" name="customer" required>
        <option value="">Select Customer</option>
        <?php foreach($customers as $customer): ?>
          <option value="<?php echo $customer['CustomerID']; ?>"><?php echo $customer['Name']; ?></option>
        <?php endforeach; ?>
      </select>
      <button type="button" id="addCustomerBtnService">Add Customer</button>
    </div>
    <div>
      <label for="car_serial">Car Serial Number (if registered):</label>
      <input type="text" id="car_serial" name="car_serial">
    </div>
    <div>
      <label for="service_brand">Car Make:</label>
      <select id="service_brand" name="service_brand">
        <option value="">Select Car Make</option>
        <?php foreach($carMakes as $make): ?>
          <option value="<?php echo $make; ?>"><?php echo $make; ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label for="service_model">Car Model:</label>
      <select id="service_model" name="service_model">
        <option value="">Select Car Model</option>
      </select>
    </div>
    <div>
      <label for="service_year">Car Year:</label>
      <select id="service_year" name="service_year">
        <option value="">Select Car Year</option>
        <?php for($year=2015; $year<=2025; $year++): ?>
          <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div>
      <label for="service_condition">Car Condition:</label>
      <select id="service_condition" name="service_condition">
        <option value="">Select Condition</option>
        <option value="New">New</option>
        <option value="Used">Used</option>
      </select>
    </div>
    <div>
      <label for="description">Service Description:</label>
      <textarea id="description" name="description" required></textarea>
    </div>
    <!-- Updated Mechanics Field: Multi-select dropdown -->
    <div>
      <label for="mechanics">Mechanics:</label>
      <select id="mechanics" name="mechanics[]" multiple required>
        <?php foreach($mechanics as $mechanic): ?>
          <option value="<?php echo $mechanic['MechanicID']; ?>"><?php echo $mechanic['Name']; ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <!-- Existing service cost, purchase option, etc. -->
    <div>
      <label for="service_price">Service Cost:</label>
      <input type="number" step="0.01" id="service_price" name="service_price" required>
    </div>
    <button type="submit">Submit Service</button>
  </form>
</section>
    
    <!-- Lookup Car Details Section -->
    <section id="lookup">
      <h2>Car Lookup</h2>
      <form id="lookupForm" action="/kcc-proto/controllers/UserController.php" method="post">
        <input type="hidden" name="action" value="get_car_details">
        <div>
          <label for="serial_input">Car Serial Number:</label>
          <input type="text" id="serial_input" name="serial" required>
        </div>
        <button type="submit">Lookup Details</button>
      </form>
      <div id="lookupResult"></div>
    </section>
  </main>
  
  <!-- Add Customer Modal -->
  <div id="addCustomerModal" class="modal">
    <div class="modal-content">
      <span class="close-modal">&times;</span>
      <h3>Add New Customer</h3>
      <form id="addCustomerForm" action="/kcc-proto/controllers/UserController.php" method="post">
        <input type="hidden" name="action" value="add_customer">
        <div>
          <label for="new_customer_name">Name:</label>
          <input type="text" id="new_customer_name" name="name" required>
        </div>
        <div>
          <label for="new_customer_phone">Phone:</label>
          <input type="text" id="new_customer_phone" name="phone">
        </div>
        <div>
          <label for="new_customer_email">Email:</label>
          <input type="text" id="new_customer_email" name="email">
        </div>
        <button type="submit">Add Customer</button>
      </form>
    </div>
  </div>
  
  <script>
    $(document).ready(function(){
      var carData = <?php echo json_encode($carData); ?>;
      
      // Populate Sale Form Car Model based on selected Car Make
      $("#car_make").change(function(){
        var selectedMake = $(this).val();
        var modelSelect = $("#car_model");
        modelSelect.empty().append('<option value="">Select Car Model</option>');
        if(selectedMake && carData[selectedMake]){
          $.each(carData[selectedMake], function(index, model){
            modelSelect.append('<option value="'+ model +'">'+ model +'</option>');
          });
        }
      });
      
      // Populate Service Form Car Model based on selected service_brand
      $("#service_brand").change(function(){
        var selectedMake = $(this).val();
        var modelSelect = $("#service_model");
        modelSelect.empty().append('<option value="">Select Car Model</option>');
        if(selectedMake && carData[selectedMake]){
          $.each(carData[selectedMake], function(index, model){
            modelSelect.append('<option value="'+ model +'">'+ model +'</option>');
          });
        }
      });
      
      // Show/hide purchase price field in service form
      $("#purchase").change(function(){
        if($(this).is(":checked")){
          $("#purchase_price_div").show();
        } else {
          $("#purchase_price_div").hide();
        }
      });
      
      // AJAX submission for sale form
      $("#saleForm").submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
          url: $(this).attr('action'),
          type: 'POST',
          data: formData,
          success: function(response) {
            alert(response);
          },
          error: function(xhr, status, error) {
            alert(error);
          }
        });
      });
      
      // AJAX submission for service form
      $("#serviceForm").submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
          url: $(this).attr('action'),
          type: 'POST',
          data: formData,
          success: function(response) {
            alert(response);
          },
          error: function(xhr, status, error) {
            alert(error);
          }
        });
      });
      
      // Open Add Customer Modal when Add Customer button is clicked
      $("#addCustomerBtn, #addCustomerBtnService").click(function(){
        $("#addCustomerModal").fadeIn(300);
      });
      
      // Close modal when clicking on close button
      $(".close-modal").click(function(){
        $("#addCustomerModal").fadeOut(300);
      });
      
      // AJAX submission for add customer form
      $("#addCustomerForm").submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
          url: $(this).attr('action'),
          type: 'POST',
          data: formData,
          dataType: 'json',
          success: function(response) {
            if(response.success){
              // Add the new customer to both dropdowns and select it.
              var newOption = '<option value="'+response.customer.CustomerID+'">'+response.customer.Name+'</option>';
              $("#customer, #customer_service").append(newOption);
              $("#customer").val(response.customer.CustomerID);
              $("#customer_service").val(response.customer.CustomerID);
              $("#addCustomerModal").fadeOut(300);
              $("#addCustomerForm")[0].reset();
            } else {
              alert(response.message);
            }
          },
          error: function(xhr, status, error) {
            alert(error);
          }
        });
      });
      
      // Lookup Car Details functionality
      $("#lookupForm").submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
          url: $(this).attr('action'),
          type: 'POST',
          data: formData,
          dataType: 'json',
          success: function(response) {
            if(response.success){
              var details = response.details;
              var html = "<h3>Car Details</h3>";
              html += "<p><strong>Serial Number:</strong> " + details.SerialNumber + "</p>";
              html += "<p><strong>Brand:</strong> " + details.Brand + "</p>";
              html += "<p><strong>Model:</strong> " + details.Model + "</p>";
              html += "<p><strong>Year:</strong> " + details.Year + "</p>";
              html += "<p><strong>Condition:</strong> " + details.Condition + "</p>";
              if(details.CustomerName){
                html += "<h3>Customer Details</h3>";
                html += "<p><strong>Name:</strong> " + details.CustomerName + "</p>";
                html += "<p><strong>Phone:</strong> " + details.CustomerPhone + "</p>";
                html += "<p><strong>Email:</strong> " + details.CustomerEmail + "</p>";
              } else {
                html += "<p>No customer information available.</p>";
              }
              $("#lookupResult").html(html);
            } else {
              $("#lookupResult").html("<p>Error: " + response.message + "</p>");
            }
          },
          error: function(xhr, status, error) {
            $("#lookupResult").html("<p>Error: " + error + "</p>");
          }
        });
      });
    });
  </script>
</body>
</html>
