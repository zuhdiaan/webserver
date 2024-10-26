<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the current page is 'login.php'
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page === 'login.php') {
    return; // Skip the header if on the login page
}
?>

<header>
  <h1>Jiwani Coffee</h1>
  <nav>
    <ul>
      <?php
      // Dynamically generate navigation links
      $navLinks = array(
        "Home" => "index.php",
        "Orders" => "orders.php",
        "History" => "history.php",
        "Menu" => "menus.php",
        "Add Menu" => "add_menu.php",
        "Top Up Balance" => "top_up.php"
      );

      foreach ($navLinks as $label => $link) {
        echo "<li><a href='$link'>$label</a></li>";
      }
      ?>
    </ul>
  </nav>
</header>

<head>
  <link rel="stylesheet" href="css/styles.css">
</head>
