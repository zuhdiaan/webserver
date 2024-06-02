<?php include 'templates/header.php'; ?>

<h2>Transactions</h2>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Order ID</th>
      <th>Transaction Token</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $json = file_get_contents('http://localhost:3000/api/transaction');
    $transactions = json_decode($json, true);

    foreach ($transactions as $transaction) {
      echo "<tr>
              <td>{$transaction['id']}</td>
              <td>{$transaction['order_id']}</td>
              <td>{$transaction['transaction_token']}</td>
            </tr>";
    }
    ?>
  </tbody>
</table>

<h2>Order Details</h2>
<table>
  <thead>
    <tr>
      <th>Order ID</th>
      <th>Item Name</th>
      <th>Quantity</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $json = file_get_contents('http://localhost:3000/api/order_details');
    $orderDetails = json_decode($json, true);

    foreach ($orderDetails as $detail) {
      echo "<tr>
              <td>{$detail['order_id']}</td>
              <td>{$detail['name']}</td>
              <td>{$detail['quantity']}</td>
            </tr>";
    }
    ?>
  </tbody>
</table>

<?php include 'templates/footer.php'; ?>
