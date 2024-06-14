<?php include 'templates/header.php'; ?>

<h2>Orders</h2>
<table>
  <thead>
    <tr>
      <th>Order ID</th>
      <th>Order Time</th>
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

    $prev_order_id = null;
    $prev_order_time = null;
    $total_price = 0;

    foreach ($orders as $order) {
      $items = explode(", ", $order['items']);
      foreach ($items as $item) {
        $item_parts = explode(":", $item);
        if (count($item_parts) === 4) {
          $item_name = $item_parts[1];
          $quantity = $item_parts[2];
          $item_price = $item_parts[3];

          if ($order['order_id'] !== $prev_order_id || $order['order_time'] !== $prev_order_time) {
            // Display the total price for the previous order group
            if ($prev_order_id !== null) {
              echo "<tr>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td>{$total_price}</td>
                    </tr>";
              $total_price = 0; // Reset total price for the new order group
            }

            // Display the order details for the new order group
            echo "<tr>
                    <td>{$order['order_id']}</td>
                    <td>" . date('Y-m-d H:i:s', strtotime($order['order_time'])) . "</td>
                    <td>{$item_name}</td>
                    <td>{$quantity}</td>
                    <td>{$item_price}</td>
                    <td></td>
                  </tr>";
            $prev_order_id = $order['order_id'];
            $prev_order_time = $order['order_time'];
          } else {
            // Display the item details for the same order group
            echo "<tr>
                    <td></td>
                    <td></td>
                    <td>{$item_name}</td>
                    <td>{$quantity}</td>
                    <td>{$item_price}</td>
                    <td></td>
                  </tr>";
          }

          // Accumulate the item prices for the total price calculation
          $total_price += $quantity * $item_price;
        }
      }
    }

    // Display the total price for the last order group
    if ($prev_order_id !== null) {
      echo "<tr>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td>{$total_price}</td>
            </tr>";
    }
    ?>
  </tbody>
</table>

<?php include 'templates/footer.php'; ?>
