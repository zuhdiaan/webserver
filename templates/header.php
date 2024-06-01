<header>
  <h1>Jiwani Coffee</h1>
  <nav>
    <ul>
      <?php
      // PHP code to dynamically generate navigation links
      $navLinks = array("Home" => "index.php", "Orders" => "orders.php", "Menu" => "menus.php", "Add Menu" => "add_menu.php");
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
