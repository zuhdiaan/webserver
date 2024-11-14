<?php 
require 'vendor/autoload.php';
include 'templates/header.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Utils;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['member_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<h2>Add New Menu</h2>
<form action="add_menu.php" method="post" enctype="multipart/form-data" class="add-menu-form" onsubmit="return validateForm()">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required class="input-field">
    
    <label for="price">Price:</label>
    <input type="number" id="price" name="price" step="0.01" required class="input-field" style="margin-bottom: 15px;">
    
    <label for="category">Category:</label>
    <select id="category" name="category" required class="input-field">
        <?php
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

<script>
    function showAlert(message) {
        alert(message);
    }

    function validateForm() {
        const avatar = document.getElementById('avatar');
        if (!avatar.value) {
            showAlert('Please upload an image.');
            return false;
        }
        return true;
    }
</script>

<?php   
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image_source = $_FILES['avatar'];

    if (!$image_source['tmp_name']) {
        echo "<script>showAlert('Image upload failed. Please try again.');</script>";
        exit();
    }

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
            echo "<script>showAlert('Menu added successfully!');</script>";
        } else {
            echo "<script>showAlert('Failed to add menu. Status code: " . $response->getStatusCode() . "');</script>";
        }
    } catch (RequestException $e) {
        $responseBody = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : '';
        if (strpos($responseBody, 'Menu item with the same name already exists') !== false) {
            echo "<script>showAlert('Menu item with the same name already exists.');</script>";
        } else {
            echo "<script>showAlert('Failed to add menu. Error: " . htmlspecialchars($e->getMessage()) . "');</script>";
        }
    } catch (Exception $e) {
        echo "<script>showAlert('Failed to add menu. Error: " . htmlspecialchars($e->getMessage()) . "');</script>";
    }
}
?>

<?php include 'templates/footer.php'; ?>
