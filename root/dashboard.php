<?php
session_start();
if (!isset($_SESSION['nim'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
</head>
<body>
  <h1>Selamat datang, <?= $_SESSION['nama'] ?> (<?= $_SESSION['nim'] ?>)</h1>
  <a href="logout.php">Logout</a>
</body>
</html>
