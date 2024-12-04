<?php
session_start(); 

if (isset($_SESSION['member_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $url = 'http://localhost:3000/api/loginAdmin'; 
    $data = array(
        'username' => $username,
        'password' => $password,
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['success']) && $result['success']) {
        $_SESSION['member_id'] = $result['member_id'];
        $_SESSION['role'] = $result['role'];
        header("Location: index.php"); 
        exit();
    } else {
        $error_message = $result['message'] ?? "Username atau password salah.";
    }
}

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
        <!-- <p>Lupa password? <a href="forgot_password.php">Klik di sini</a></p> -->
    </div>
</body2>

<?php
include 'templates/footer.php';
?>
