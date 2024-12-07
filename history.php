<?php 
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

include 'templates/header.php'; 
?>

<h2>Order History</h2>

<?php
// Check if the logged-in user is an owner
if ($_SESSION['role'] === 'owner') {
    echo '<button onclick="exportToExcel()" style="margin-bottom: 20px;">Export to Excel</button>';
}

// Get filter and search parameters
$filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : '';
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Pagination setup
$items_per_page = 10; // Number of items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_index = ($page - 1) * $items_per_page;

$json = file_get_contents('http://localhost:3000/api/order?status=completed');
$orders = json_decode($json, true);

if ($filter_status) {
    $orders = array_filter($orders, function($order) use ($filter_status) {
        return isset($order['payment_status']) && 
               strtolower(trim($order['payment_status'])) === strtolower(trim($filter_status));
    });
}

if ($search_query) {
    $orders = array_filter($orders, function($order) use ($search_query) {
        return stripos($order['user_name'], $search_query) !== false || stripos($order['order_id'], $search_query) !== false;
    });
}

if ($start_date || $end_date) {
    $orders = array_filter($orders, function ($order) use ($start_date, $end_date) {
        if (empty($order['order_time'])) { // Ensure order_time is used if order_date is missing
            error_log("Missing order_time for Order ID: {$order['order_id']}");
            return false; // Skip this order
        }
        
        try {
            $order_date = new DateTime($order['order_time']); // Use correct date field
            $start_date_time = $start_date ? new DateTime($start_date . ' 00:00:00') : null;
            $end_date_time = $end_date ? new DateTime($end_date . ' 23:59:59') : null;

            return (!$start_date_time || $order_date >= $start_date_time) &&
                   (!$end_date_time || $order_date <= $end_date_time);
        } catch (Exception $e) {
            error_log("Date Parsing Error: " . $e->getMessage());
            return false;
        }
    });
}

// Group orders
$grouped_orders = [];

foreach ($orders as $order) {
    $order_key = $order['order_id'];
    if (!isset($grouped_orders[$order_key])) {
        $grouped_orders[$order_key] = [
            'order_id' => $order['order_id'],
            'order_date' => date('Y-m-d H:i:s', strtotime($order['order_time'])),
            'user_name' => $order['user_name'],
            'items' => [],
            'total_price' => 0,
            'payment_status' => $order['payment_status'],
            'table_number' => $order['table_number'],
            'payment_method' => $order['payment_method']
        ];
    }

    $items = explode(',', $order['items']);
    foreach ($items as $item) {
        list($item_id, $item_name, $item_amount, $item_total_price) = explode(':', $item);

        $grouped_orders[$order_key]['items'][] = [
            'name' => $item_name,
            'quantity' => $item_amount,
            'total_price' => $item_total_price
        ];
        $grouped_orders[$order_key]['total_price'] += $item_total_price;
    }
}

// Get total orders and calculate pagination details
$total_orders = count($grouped_orders);
$total_pages = ceil($total_orders / $items_per_page);

// Slice the grouped orders for the current page
$grouped_orders = array_slice($grouped_orders, $start_index, $items_per_page);

?>

<form method="GET" style="margin-bottom: 20px;">
    <!-- <label for="filter_status">Filter by Payment Status:</label>
    <select name="filter_status" id="filter_status">
        <option value="">All</option>
        <option value="paid" <?php echo $filter_status === 'paid' ? 'selected' : ''; ?>>Paid</option>
        <option value="not paid" <?php echo $filter_status === 'not paid' ? 'selected' : ''; ?>>Unpaid</option>
    </select> -->

    <label for="search_query">Search:</label>
    <input type="text" name="search_query" id="search_query" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search by Order ID or Name">

    <label for="start_date">Start Date:</label>
    <input type="date" name="start_date" id="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">

    <label for="end_date">End Date:</label>
    <input type="date" name="end_date" id="end_date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">

    <button type="submit">Apply</button>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        // When the start date changes
        startDateInput.addEventListener('change', () => {
            const startDateValue = startDateInput.value;
            if (startDateValue) {
                // Set the minimum value for the end date to the selected start date
                endDateInput.min = startDateValue;

                // If end date is earlier than start date, clear the end date
                if (endDateInput.value && endDateInput.value < startDateValue) {
                    endDateInput.value = startDateValue;
                }
            }
        });

        // When the end date changes
        endDateInput.addEventListener('change', () => {
            const endDateValue = endDateInput.value;
            if (endDateValue) {
                // Ensure the end date is not earlier than the start date
                if (startDateInput.value && endDateValue < startDateInput.value) {
                    alert('End Date cannot be earlier than Start Date.');
                    endDateInput.value = startDateInput.value;
                }
            }
        });
    });
</script>
</form>

<table>
  <thead>
    <tr>
      <th>Order ID</th>
      <th>Order Date</th>
      <th>User Name</th>
      <th>Items</th>
      <th>Quantity</th>
      <th>Item Total Price</th>
      <th>Total Price</th>
      <th>Payment Status</th>
      <th>Table Number</th>
      <th>Payment Method</th>
    </tr>
  </thead>
  <tbody>
    <?php
    if (empty($grouped_orders)) {
        echo "<tr><td colspan='10'>No orders found matching the selected criteria.</td></tr>";
    } else {
        foreach ($grouped_orders as $grouped_order) {
            echo "<tr>
                    <td>{$grouped_order['order_id']}</td>
                    <td>{$grouped_order['order_date']}</td>
                    <td>{$grouped_order['user_name']}</td>
                    <td>";
            foreach ($grouped_order['items'] as $item) {
                echo "{$item['name']}<br>";
            }
            echo "</td>
                  <td>";
            foreach ($grouped_order['items'] as $item) {
                echo "{$item['quantity']}<br>";
            }
            echo "</td>
                  <td>";
            foreach ($grouped_order['items'] as $item) {
                echo "Rp. " . number_format($item['total_price'], 2) . "<br>";
            }
            echo "</td>
                  <td>Rp. " . number_format($grouped_order['total_price'], 2) . "</td>
                  <td>{$grouped_order['payment_status']}</td>
                  <td>{$grouped_order['table_number']}</td>
                  <td>{$grouped_order['payment_method']}</td>
                  </tr>";
        }
    }
    ?>
  </tbody>
</table>

<div style="margin-top: 20px;">
  <!-- Pagination Links -->
  <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <a href="?page=<?php echo $i; ?>&filter_status=<?php echo $filter_status; ?>&search_query=<?php echo urlencode($search_query); ?>" 
         style="margin-right: 5px; <?php echo $i === $page ? 'font-weight: bold;' : ''; ?>">
         <?php echo $i; ?>
      </a>
  <?php endfor; ?>
</div>

<script>
function exportToExcel() {
    window.location.href = 'http://localhost:3000/api/exportOrders'; // Call the API to export to Excel
}
</script>

<?php include 'templates/footer.php'; ?>
