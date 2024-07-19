<?php include 'templates/header.php'; ?>

<h2>Menus</h2>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Price</th>
      <th>Image</th>
      <th>Category</th>
    </tr>
  </thead>
  <tbody>
    <?php
    // Mengambil data menu dari API
    $json = @file_get_contents('http://localhost:3000/api/menu_items');
    if ($json === FALSE) {
      echo "<tr><td colspan='5'>Failed to fetch menus. Please try again later.</td></tr>";
    } else {
      $menus = json_decode($json, true);
      // Memeriksa apakah JSON decoding berhasil
      if ($menus === NULL) {
        echo "<tr><td colspan='5'>Failed to decode JSON. Please try again later.</td></tr>";
      } else {

        foreach ($menus as $menu) {
          // Mendapatkan nama kategori
          $category_id = htmlspecialchars($menu['category_id']);
          $category_json = @file_get_contents('http://localhost:3000/api/categories/' . $category_id);
          $category_name = "Unknown";
          if ($category_json !== FALSE) {
            $category = json_decode($category_json, true);
            if ($category !== NULL && isset($category['category_name'])) {
              $category_name = htmlspecialchars($category['category_name']);
            } else {
              // Menangani kasus jika kategori tidak ditemukan
              echo "<tr><td colspan='5'>Failed to fetch category name for ID $category_id.</td></tr>";
            }
          } else {
            // Menangani kasus jika permintaan API gagal
            echo "<tr><td colspan='5'>Failed to fetch category for ID $category_id.</td></tr>";
          }

          echo "<tr>
                  <td>" . htmlspecialchars($menu['item_id']) . "</td>
                  <td>" . htmlspecialchars($menu['item_name']) . "</td>
                  <td>" . htmlspecialchars($menu['price']) . "</td>
                  <td><img src='http://localhost:3000/uploads/" . htmlspecialchars($menu['image_source']) . "' alt='Menu Image' style='width: 140px; height: 140px; object-fit: cover;'></td>
                  <td>" . $category_name . "</td>
                </tr>";
        }
      }
    }
    ?>
  </tbody>
</table>

<?php include 'templates/footer.php'; ?>
