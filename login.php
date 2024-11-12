<?php
session_start(); // Start session

// Check if the user is already logged in
if (isset($_SESSION['member_id'])) {
    header("Location: dashboard.php"); // Redirect to dashboard if already logged in
    exit();
}

// Process login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Send request to the API backend for login
    $url = 'http://localhost:3000/api/loginAdmin'; // Ensure this URL matches your backend

    // Data to be sent
    $data = array(
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

    // Handle the response from backend
    $result = json_decode($response, true);

    if (isset($result['success']) && $result['success']) {
        // Store session data on successful login
        $_SESSION['member_id'] = $result['member_id'];
        $_SESSION['role'] = $result['role'];
        header("Location: index.php"); // Redirect to dashboard
        exit();
    } else {
        $error_message = $result['message'] ?? "Username atau password salah.";
    }
}

// Include header
include 'templates/header.php';
?>

<link rel="stylesheet" type="text/css" href="css/styles.css">

<body2>
    <div class="container2">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <?php if (isset($error_message)): ?>
                <p class="error_message2"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </form>

        <!-- Registration and Forgot Password Links -->
        <p>Belum memiliki akun? <a href="register.php">Daftar Sekarang</a></p>
        <p>Lupa password? <a href="forgot_password.php">Klik di sini</a></p>
    </div>
</body2>

<?php
include 'templates/footer.php';
?>
