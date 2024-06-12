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

    foreach ($orders as $order) {
      $items = '';
      foreach ($order['items'] as $item) {
        $items .= "{$item['item_name']} x {$item['quantity']}, ";
      }
      $items = rtrim($items, ', ');
      
      echo "<tr>
      <td>{$order['order_id']}</td>
      <td>{$order['order_time']}</td>
      <td>{$order['items']}</td>
      <td>{$order['total_price']}</td>
    </tr>";
    }
    ?>
  </tbody>
</table>

<?php include 'templates/footer.php'; ?>
