<?php
// Create database connection
require_once './config/db_connect.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Cavite Garden</title>
    <!-- Internal Links -->
    <link rel="icon" href="./public/tcg_logo.jpg" type="image/x-icon">
    <link rel="stylesheet" href="./css/reset.css">

    <!-- External Links -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">
    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js" defer></script>
    <script src="https://kit.fontawesome.com/431759c8d8.js" crossorigin="anonymous"></script>
</head>

<body>
    <header class="header">
        <div id="menu-btn" class="fas fa-bars"></div>

        <a href="#home" class="logo">
            The Cavite Garden
            <i class="fas fa-leaf"></i>
        </a>

        <nav class="navbar">
            <a href="#home">home</a>
            <a href="#about">about</a>
            <a href="#menu">menu</a>
            <a href="#review">reviews</a>
        </nav>
    </header>
</body>

</html>