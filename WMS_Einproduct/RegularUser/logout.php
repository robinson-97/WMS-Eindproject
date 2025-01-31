<?php
session_start();
session_destroy(); // Vernietig de sessie
header('Location: admin_login.php'); // Verwijs terug naar de admin login
exit;
?>
