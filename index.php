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
    <link rel="stylesheet" href="./assets/css/index.css">
    <script src="./assets//js/main.js" defer></script>

    <!-- External Links -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">
    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js" defer></script>
    <script src="https://kit.fontawesome.com/431759c8d8.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
    <header class="header bg-light">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand fs-4" href="#home">
                    The Cavite Garden
                    <i class="fas fa-leaf ms-1"></i>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link fs-6 text-black" href="#home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-6 text-black" href="#about">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-6 text-black" href="#menu">Menu</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-6 text-black" href="#reviews">Reviews</a>
                        </li>
                    </ul>
                </div>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link text-black" href="#home">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-black" href="#about">Register</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="main">
        <!-- Landing Page Section -->
        <section class="landing-page section" id="home">
            <div id="carouselExample" class="carousel slide h-100">
                <!-- Carousel Setup -->
                <div class="carousel-inner h-100">
                    <div class="carousel-item h-100 d-flex justify-content-center active">
                        <div class="carousel-content mt-5">
                            <h1 class="text-white fs-medium">The Cavite Garden</h1>
                            <p class="fs-1 text-white fs-medium">Blooms of Elegance, <span class="text-emphasis">Roots of Serinity</span> - Your Oasis In Cavite</p>
                            <button type="button" class="btn fs-6 bg-black text-white">BUY NOW</button>
                        </div>
                    </div>
                    <div class="carousel-item h-100">
                        <video class="h-100 w-100" src="./assets/video/the-cavite-garden-video.mp4" loop autoplay muted></video>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </section>


        <!-- About Section -->
        <section class="about section" id="about">
            <h2 class="heading px-5 py-5">
                About Us
                <span class="fs-3">About Us</span>
            </h2>
        </section>

        <!-- Menu Section -->
        <section class="menu section" id="menu">
            <h2 class="heading px-5 py-5">
                Our Products
                <span class="fs-3">Our Products</span>
            </h2>
        </section>

        <!-- Reviews Section -->
        <section class="menu section" id="reviews">
            <h2 class="heading px-5 py-5">
                Reviews
                <span class="fs-3">Reviews</span>
            </h2>
        </section>
    </main>

    <!-- Footer Section -->
    <?php include './view/footer.php' ?>
</body>

</html>