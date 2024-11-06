<?php 
include 'templates/header.php'; 

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
?>

<body>
  <div class="container">
    <main>
      <h2>Top Up Balance</h2>
      <form id="topUpForm">
        <label for="member_id">Member ID:</label>
        <input type="number" id="member_id" name="member_id" required><br><br>

        <label for="amount">Top Up Amount:</label>
        <input type="number" id="amount" name="amount" step="0.01" required><br><br>

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
    var member_id = $('#member_id').val();
    var amount = $('#amount').val();

    $.ajax({
      url: 'http://localhost:3000/api/topupAdmin',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({ member_id: member_id, amount: amount }),
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
