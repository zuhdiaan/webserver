<?php
session_start(); // Start a session

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name']; // Retrieve name from the form
    $email = $_POST['email']; // Retrieve email
    $username = $_POST['username']; // Retrieve username
    $password = $_POST['password']; // Retrieve password

    // Send request to the API backend for user registration
    $url = 'http://localhost:3000/api/registerAdmin'; // Adjust this URL if necessary

    // Data to send
    $data = array(
        'name' => $name,
        'email' => $email,
        'username' => $username,
        'password' => $password,
    );

    // Initialize cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Execute cURL
    $response = curl_exec($ch);
    curl_close($ch);

    // Handle response from the backend
    $result = json_decode($response, true);
    if (isset($result['success']) && $result['success']) {
        // Registration successful
        header("Location: index.php?register_success=1"); // Tambahkan query parameter
        exit();
    } else {
        $error_message = $result['message'] ?? "Registration failed."; // Get error message from API
    }
}
// Include header
include 'templates/header.php';
?>

<h2>Register Barista</h2>
<form action="register.php" method="POST">
    <input type="text" name="name" placeholder="Full Name" required> <!-- Name input -->
    <input type="email" name="email" placeholder="Email" required> <!-- Email input -->
    <input type="text" name="username" placeholder="Username" required> <!-- Username input -->
    <input type="password" name="password" placeholder="Password" required> <!-- Password input -->
    <button type="submit">Register</button>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
</form>

<?php
include 'templates/footer.php';
?>
