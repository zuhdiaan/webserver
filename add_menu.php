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
        <?php
        // Menampilkan daftar kategori dari database
        $client = new Client();
        try {
            $response = $client->get('http://localhost:3000/api/categories');
            $categories = json_decode($response->getBody(), true);
            foreach ($categories as $category) {
                echo "<option value=\"" . htmlspecialchars($category['category_id']) . "\">" . htmlspecialchars($category['category_name']) . "</option>";
            }
        } catch (RequestException $e) {
            echo "<option value=\"\">Failed to load categories</option>";
        } catch (Exception $e) {
            echo "<option value=\"\">Error: " . htmlspecialchars($e->getMessage()) . "</option>";
        }
        ?>
    </select>
    
    <label for="avatar">Image source:</label>
    <input type="file" id="avatar" name="avatar" class="input-field">
    
    <button type="submit" class="submit-button">Add Menu</button>
</form>

<?php   
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image_source = $_FILES['avatar'];

    $client = new Client();

    $options = [
        'multipart' => [
            [
                'name'     => 'avatar',
                'contents' => Utils::tryFopen($image_source['tmp_name'], 'r'),
                'filename' => $image_source['name']
            ],
            [
                'name'     => 'item_name',
                'contents' => $name
            ],
            [
                'name'     => 'price',
                'contents' => $price
            ],
            [
                'name'     => 'category_id',
                'contents' => $category
            ]
        ]
    ];

    try {
        $response = $client->post('http://localhost:3000/api/menu_items', $options);

        if ($response->getStatusCode() == 200) {
            echo "<p>Menu added successfully.</p>";
        } else {
            echo "<p>Failed to add menu. Status code: " . $response->getStatusCode() . "</p>";
        }
    } catch (RequestException $e) {
        echo "<p>Failed to add menu. Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    } catch (Exception $e) {
        echo "<p>Failed to add menu. Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<?php include 'templates/footer.php'; ?>
