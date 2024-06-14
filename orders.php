<?php include 'templates/header.php'; ?>

<h2>Orders</h2>
<table>
  <thead>
    <tr>
      <th>Order ID</th>
      <th>Order Time</th>
      <th>Items</th>
      <th>Total Price</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $json = file_get_contents('http://localhost:3000/api/order');
    $orders = json_decode($json, true);

    $merged_orders = [];
    foreach ($orders as $order) {
      $order_id = $order['order_id'];
      if (!isset($merged_orders[$order_id])) {
        $merged_orders[$order_id] = $order;
      } else {
        // Merge items if order_id already exists
        $merged_orders[$order_id]['items'] .= ", " . $order['items'];
        $merged_orders[$order_id]['total_price'] += $order['total_price'];
      }
    }

    foreach ($merged_orders as $order) {
      $items = '';
      $item_strings = explode(",", $order['items']);
      foreach ($item_strings as $item_string) {
        $item_parts = explode(":", $item_string);
        if (count($item_parts) === 4) {
          $item_name = $item_parts[1];
          $quantity = $item_parts[2];
          $item_price = $item_parts[3];
          $items .= "{$item_name}: {$quantity} x {$item_price}, ";
        }
      }
      $items = rtrim($items, ', ');
      
      echo "<tr>
      <td>{$order['order_id']}</td>
      <td>" . date('Y-m-d H:i:s', strtotime($order['order_time'])) . "</td>
      <td>{$items}</td>
      <td>{$order['total_price']}</td>
    </tr>";
    }
    ?>
  </tbody>
</table>

<?php include 'templates/footer.php'; ?>
