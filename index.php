<?php
session_start(); // Mulai sesi
if (isset($_GET['register_success'])) {
  echo "<script>alert('Pendaftaran berhasil!');</script>";
}
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

// Ambil role dari sesi
$role = $_SESSION['role'];
?>

<?php include 'templates/header.php'; ?>

<body>
  <div class="container">
    <main>
      <h2>Welcome to Jiwani Coffee Web Server</h2>
      <p>Manage your cafe orders and menus.</p>

      <?php if ($role === 'barista'): ?>
          <h3>Barista Dashboard</h3>
          <p>Here you can manage orders, view reports, and adjust settings.</p>
          <!-- Add more admin-specific functionality here -->
      <?php elseif ($role === 'owner'): ?>
          <h3>Owner Dashboard</h3>
          <p>Here you can view sales reports and manage the cafe's menu items.</p>
          <!-- Add more owner-specific functionality here -->
      <?php else: ?>
          <p>Access denied. You do not have permission to view this page.</p>
      <?php endif; ?>

    </main>
  </div>
</body>
</html>

<?php include 'templates/footer.php'; ?>
