<?php
session_start(); // Mulai sesi

// Cek apakah pengguna sudah login
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php"); // Redirect ke halaman login
    exit();
}

// Proses logout
if (isset($_GET['logout'])) {
    session_destroy(); // Hapus semua data sesi
    header("Location: login.php"); // Redirect ke halaman login
    exit();
}
?>

<?php include 'templates/header.php'; ?>

<body>
  <div class="container">
    <main>
      <h2>Welcome to Jiwani Coffee Web Server</h2>
      <p>Manage your cafe orders and menus.</p>
      
      <!-- Logout Button -->
      <a href="?logout=true" class="btn btn-danger">Logout</a>
    </main>
  </div>
</body>
</html>

<?php include 'templates/footer.php'; ?>
