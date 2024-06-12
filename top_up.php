<?php include 'templates/header.php'; ?>

<body>
  <div class="container">
    <main>
      <h2>Top Up Balance</h2>
      <form id="topUpForm">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="amount">Top Up Amount:</label>
        <input type="number" id="amount" name="amount" required><br><br>

        <button type="submit">Top Up</button>
      </form>
      <div id="message"></div>
    </main>
  </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script>
$(document).ready(function() {
  $('#topUpForm').submit(function(event) {
    event.preventDefault();
    var username = $('#username').val();
    var amount = $('#amount').val();

    $.ajax({
      url: 'http://localhost:3000/api/topup',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({ username: username, amount: amount }),
      success: function(response) {
        $('#message').text('Balance updated successfully. New balance: ' + response.balance);
      },
      error: function(xhr, status, error) {
        $('#message').text('Failed to update balance: ' + xhr.responseJSON.error);
      }
    });
  });
});
</script>
</body>

<?php include 'templates/footer.php'; ?>
