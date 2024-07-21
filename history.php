<?php include 'templates/header.php'; ?>

<h2>Order History</h2>
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
    // Mengambil data pesanan dari API dengan status 'completed'
    $json = file_get_contents('http://localhost:3000/api/order?status=completed');
    $orders = json_decode($json, true);

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

<?php include 'templates/footer.php'; ?>
