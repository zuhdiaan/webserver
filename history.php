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
      <th>Item Price</th>
      <th>Total Price</th>
    </tr>
  </thead>
  <tbody>
    <?php
    // Mengambil data pesanan dari API
    $json = file_get_contents('http://localhost:3000/api/order?status=completed');
    $orders = json_decode($json, true);

    $grouped_orders = [];

    foreach ($orders as $order) {
      // Mengelompokkan pesanan berdasarkan ID pesanan
      $order_key = $order['order_id'];
      if (!isset($grouped_orders[$order_key])) {
        $grouped_orders[$order_key] = [
          'order_id' => $order['order_id'],
          'order_date' => date('Y-m-d H:i:s', strtotime($order['order_date'])),
          'user_name' => $order['user_name'],
          'items' => [],
          'total_price' => 0
        ];
      }

      // Menambahkan item ke dalam kelompok pesanan
      $item_name = $order['item_name'];
      $quantity = $order['item_amount'];
      $item_price = $order['item_price'];
      
      $grouped_orders[$order_key]['items'][] = [
        'name' => $item_name,
        'quantity' => $quantity,
        'price' => $item_price
      ];
      $grouped_orders[$order_key]['total_price'] += $quantity * $item_price;
    }

    // Mengurutkan array grouped_orders berdasarkan kunci secara terbalik
    krsort($grouped_orders);

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
        echo "Rp. ".number_format($item['price'], 2)."<br>";
      }
      echo "</td>
            <td>Rp. ".number_format($grouped_order['total_price'], 2)."</td>
            </tr>";
    }
    ?>
  </tbody>
</table>

<?php include 'templates/footer.php'; ?>
