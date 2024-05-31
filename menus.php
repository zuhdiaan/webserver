<?php include 'templates/header.php'; ?>

<h2>Menus</h2>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Price</th>
      <th>Image Source</th>
      <th>Category</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $json = @file_get_contents('http://localhost:3000/api/menu_items');
    if ($json === FALSE) {
      echo "<tr><td colspan='5'>Failed to fetch menus. Please try again later.</td></tr>";
    } else {
      $menus = json_decode($json, true);
      // Checking if the JSON decoding was successful
      if ($menus === NULL) {
        echo "<tr><td colspan='5'>Failed to decode JSON. Please try again later.</td></tr>";
      } else {

        foreach ($menus as $menu) {
          echo "<tr>
                  <td>" . htmlspecialchars($menu['id']) . "</td>
                  <td>" . htmlspecialchars($menu['name']) . "</td>
                  <td>" . htmlspecialchars($menu['price']) . "</td>
                  <td><img src='" . htmlspecialchars($menu['image_source']) . "' alt='Menu Image' style='width: 140px; height: 140px;'></td>
                  <td>" . htmlspecialchars($menu['category']) . "</td>
                </tr>";
              }
            }
          }
          ?>
        </tbody>
      </table>

<?php include 'templates/footer.php'; ?>
