<?php 
require 'vendor/autoload.php';
include 'templates/header.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
 ?>

<h2>Add New Menu</h2>
<form action="add_menu.php" method="post">
  <label for="name">Name:</label>
  <input type="text" id="name" name="name" required>
  <label for="price">Price:</label>
  <input type="number" id="price" name="price" step="0.01" required>
  <label for="category">Category:</label>
  <select id="category" name="category" required>
    <option value="Coffee">Coffee</option>
    <option value="Non Coffee">Non Coffee</option>
    <option value="Eat-ables">Eat-ables</option>
  </select>
  <label for="image_source">Image source:</label>
  <textarea id="image_source" name="image_source"></textarea>
  <button type="submit">Add Menu</button>
</form>

<?php   
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['name'];
  $price = $_POST['price'];
  $image_source = $_POST['image_source'];
  $category = $_POST['category'];

  $data = [
    'name' => $name,
    'price' => $price,
    'category' => $category,
    'image_source' => $image_source,
  ];

  $client = new Client();
  try {
    $response = $client->request('POST', 'http://localhost:3000/api/menu_items', [
      'json' => $data,
    ]);

    if ($response->getStatusCode() == 200) {
      echo "<p>Menu added successfully.</p>";
    } else {
      echo "<p>Failed to add menu. Status code: " . $response->getStatusCode() . "</p>";
    }
  } catch (RequestException $e) {
    echo "<p>Failed to add menu. Error: " . $e->getMessage() . "</p>";
    echo "<p>Response: " . $e->getResponse()->getBody()->getContents() . "</p>";
  } catch (Exception $e) {
    echo "<p>Failed to add menu. Error: " . $e->getMessage() . "</p>";
  }
}
?>

<?php include 'templates/footer.php'; ?>
