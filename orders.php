<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['member_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to the login page or show an error message
    header("Location: login.php"); // Redirect to the login page
    exit();
}

include 'templates/header.php'; 
?>

<h2>Orders</h2>
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
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php
    // Fetching orders with pending status from backend API
    $json = file_get_contents('http://localhost:3000/api/order?status=pending');
    $orders = json_decode($json, true);

    if (is_array($orders) && !empty($orders)) {
      $grouped_orders = [];

      foreach ($orders as $order) {
        $order_key = $order['order_id'];
        
        // Initialize the order details if not already present
        if (!isset($grouped_orders[$order_key])) {
          $grouped_orders[$order_key] = [
            'order_id' => $order['order_id'],
            'order_date' => !empty($order['order_time']) ? date('Y-m-d H:i:s', strtotime($order['order_time'])) : 'N/A',
            'user_name' => $order['user_name'] ?? 'Unknown User',
            'items' => [],
            'total_price' => 0,
            'payment_status' => $order['payment_status'] ?? 'Unknown',
            'table_number' => $order['table_number'] ?? 'N/A',
            'payment_method' => $order['payment_method'] ?? 'Unknown',
          ];
        }

        $items = explode(',', $order['items']);
        foreach ($items as $item) {
          list($item_id, $item_name, $item_amount, $item_total_price) = explode(':', $item);

          $grouped_orders[$order_key]['items'][] = [
            'name' => $item_name ?? 'Unknown Item',
            'quantity' => $item_amount ?? 0,
            'total_price' => $item_total_price ?? 0
          ];
          $grouped_orders[$order_key]['total_price'] += $item_total_price ?? 0;
        }
      }

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
              <td>
                <button onclick=\"updateOrderStatus('{$grouped_order['order_id']}', 'completed')\">Complete Order</button>
                <button onclick=\"cancelOrder('{$grouped_order['order_id']}')\">Cancel Order</button>
              </td>
              </tr>";
      }
    } else {
      echo "<tr><td colspan='11'>No pending orders found.</td></tr>";
    }
    ?>
  </tbody>
</table>

<script>
function updateOrderStatus(orderId, status) {
  fetch('http://localhost:3000/api/updateOrderStatus', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ orderId: orderId, status: status })
  })
  .then(response => response.json())
  .then(data => {
    console.log('Response:', data);
    location.reload();  // Optionally refresh the page after update
  });
}

function cancelOrder(orderId) {
  fetch('http://localhost:3000/api/cancelOrder', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ orderId: orderId })
  })
  .then(response => response.json())
  .then(data => {
    console.log('Response:', data);
    location.reload();  // Optionally refresh the page after cancellation
  });
}
</script>

<?php include 'templates/footer.php'; ?>
