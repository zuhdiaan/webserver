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

// Pagination setup
$items_per_page = 10; // Number of items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$start_index = ($page - 1) * $items_per_page; // Calculate start index for the page

// Fetch completed orders from the API
$json = file_get_contents('http://localhost:3000/api/order?status=completed');
$orders = json_decode($json, true);

// Apply filter and search
if ($filter_status) {
    $orders = array_filter($orders, function($order) use ($filter_status) {
        return $order['payment_status'] === $filter_status;
    });
}
if ($search_query) {
    $orders = array_filter($orders, function($order) use ($search_query) {
        return stripos($order['user_name'], $search_query) !== false || stripos($order['order_id'], $search_query) !== false;
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

<!-- Filter and Search Form -->
<form method="GET" style="margin-bottom: 20px;">
    <label for="filter_status">Filter by Payment Status:</label>
    <select name="filter_status" id="filter_status">
        <option value="">All</option>
        <option value="paid" <?php echo $filter_status === 'paid' ? 'selected' : ''; ?>>Paid</option>
        <option value="unpaid" <?php echo $filter_status === 'unpaid' ? 'selected' : ''; ?>>Unpaid</option>
    </select>

    <label for="search_query">Search:</label>
    <input type="text" name="search_query" id="search_query" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search by Order ID or Name">

    <button type="submit">Apply</button>
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
