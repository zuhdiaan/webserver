<?php include 'templates/header.php'; ?>

<h2>Orders</h2>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Menu ID</th>
      <th>Table Number</th>
      <th>Payment Status</th>
      <th>Payment Method</th>
      <th>Timestamp</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $json = file_get_contents('http://localhost:3000/api/orders');
    $orders = json_decode($json, true);

    foreach ($orders as $order) {
      echo "<tr>
              <td>{$order['id']}</td>
              <td>{$order['menu_id']}</td>
              <td>{$order['table_number']}</td>
              <td>{$order['payment_status']}</td>
              <td>{$order['payment_method']}</td>
              <td>{$order['timestamp']}</td>
            </tr>";
    }
    ?>
  </tbody>
</table>

<?php include 'templates/footer.php'; ?>
