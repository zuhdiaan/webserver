<?php
// Check if the current page is 'login.php'
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page === 'login.php') {
    return; // Skip the footer if on the login page
}
?>

</main>
<footer>
  <p>&copy; 2024 Jiwani Coffee by Zoehds</p>
</footer>
</body>
</html>
