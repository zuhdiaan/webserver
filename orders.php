<?php include 'templates/header.php'; ?>

<h2>Orders</h2>
<table>
  <thead>
    <tr>
      <th>Order ID</th>
      <th>Order Time</th>
      <th>User Name</th>
      <th>Item Name</th>
      <th>Quantity</th>
      <th>Item Price</th>
      <th>Total Price</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $json = file_get_contents('http://localhost:3000/api/order');
    $orders = json_decode($json, true);

    $grouped_orders = [];

    foreach ($orders as $order) {
      $order_key = $order['order_id'] . '_' . $order['order_time'];
      if (!isset($grouped_orders[$order_key])) {
        $grouped_orders[$order_key] = [
          'order_id' => $order['order_id'],
          'order_time' => date('Y-m-d H:i:s', strtotime($order['order_time'])),
          'user_name' => $order['user_name'], // Assuming the API response includes 'user_name'
          'items' => [],
          'total_price' => 0
        ];
      }

      $items = explode(",", $order['items']);
      foreach ($items as $item) {
        $item_parts = explode(":", $item);
        if (count($item_parts) === 4) {
          $item_name = $item_parts[1];
          $quantity = $item_parts[2];
          $item_price = $item_parts[3];
          $grouped_orders[$order_key]['items'][] = [
            'name' => $item_name,
            'quantity' => $quantity,
            'price' => $item_price
          ];
          $grouped_orders[$order_key]['total_price'] += $quantity * $item_price;
        }
      }
    }

    foreach ($grouped_orders as $grouped_order) {
      echo "<tr>
              <td>{$grouped_order['order_id']}</td>
              <td>{$grouped_order['order_time']}</td>
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
