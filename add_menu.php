<?php 
require 'vendor/autoload.php';
include 'templates/header.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;

?>

<h2>Add New Menu</h2>
<form action="add_menu.php" method="post" enctype="multipart/form-data" class="add-menu-form">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required class="input-field">
        
        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" required class="input-field" style="margin-bottom: 15px;">
        
        <label for="category">Category:</label>
        <select id="category" name="category" required class="input-field">
            <option value="Coffee">Coffee</option>
            <option value="Non Coffee">Non Coffee</option>
            <option value="Eat-ables">Eat-ables</option>
        </select>
        
        <!-- <label for="image_source">Image source:</label>
        <input type="url" id="image_source" name="image_source" placeholder="Enter image URL" class="input-field"> -->
        <label for="avatar">Image source:</label>
        <input type="file" id="avatar" name="avatar"class="input-field">
        
        <button type="submit" class="submit-button">Add Menu</button>
    </form>

<?php   
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['name'];
  $price = $_POST['price'];
  $image_source = $_FILES['avatar'];
  $category = $_POST['category'];

  $fileContents = file_get_contents($_FILES['avatar']['tmp_name']);

  $client = new Client();

  $headers = [
    'Content-Type' => 'multipart/form-data',
    'Accept' => '*/*'
  ];
  
  $options = [
    'multipart' => [
      [
        'name' => 'avatar',
        'contents' => Utils ::tryFopen($image_source['tmp_name'], 'r'),
        'filename' => $image_source['full_path'],
        'headers'  => [
          'Content-Type' => '<Content-type header>'
        ]
      ],
      [
        'name' => 'name',
        'contents' => $name
      ],
      [
        'name' => 'price',
        'contents' => $price
      ],
      [
        'name' => 'category',
        'contents' => $category
      ]
  ]];

  try {
    $request = new Request('POST', 'http://localhost:3000/api/menu_items');
    $res = $client->sendAsync($request, $options)->wait();
    // $response = $client->sendAsync('POST', 'http://localhost:3000/api/menu_items', $options);

    if ($res->getStatusCode() == 200) {
      echo "<p>Menu added successfully.</p>";
    } else {
      echo "<p>Failed to add menu. Status code: " . $res->getStatusCode() . "</p>";
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
