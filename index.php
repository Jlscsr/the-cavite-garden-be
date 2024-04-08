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
    <script type="module" src="./assets/js/main.js" defer></script>

    <!-- External Links -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">
    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js" defer></script>
    <script src="https://kit.fontawesome.com/431759c8d8.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">
    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js" defer></script>

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
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div id="panorama"></div>
                    </div>
                    <div class="col ms-3">
                        <h3 class="fs-semi-bold">The Cavite Garden</h3>
                        <a class="fs-6 fs-medium mb-3 d-block" href="https://www.google.com/maps/place/396+Naga+Rd,+Las+Pi%C3%B1as,+1742+Metro+Manila/@14.4613896,120.9932611,17z/data=!3m1!4b1!4m6!3m5!1s0x3397ce0b80413ddd:0xecc7cddc0b9757d3!8m2!3d14.4613896!4d120.9932611!16s%2Fg%2F11c1yxq05k?entry=ttu" target="_blank">
                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                            396 Naga Road, Pulang Lupa Dos, Las Pinas City.
                        </a>
                        <p class="fs-6 text-black">
                            At The Cavite Garden, We Believe In The Transformative Power Nature And The Profound Connection Between People And Plants. Nestled In The Heart Of Cavite, Our Garden Is Not Just A Space Filled With Flora; It's A Sanctuary Where Beauty, Tranquility, And Inspiration Converge. Established With A Passion For Cultivating Natural Splendor, The Cavite Garden Is A Labor Of Love That Took Root With The Vision Of Creating A Haven Where Individuals And Communities Could Escape The Hustle And Bustle Of Everyday Life. Our Journey Began With The Intention To Not Just Grow Gardens But To Nurture Experiences That Linger In The Hearts Of Our Visitors.
                        </p>
                        <div class="container text-center mt-5">
                            <div class="row">
                                <div class="col">
                                    <img src="./assets/images/plants.png" alt="Plant Illustration" loading="lazy">
                                    <p class="mb-0 mt-1">Quality Plants</p>
                                </div>
                                <div class="col mx-3">
                                    <img src="./assets/images/peso.png" alt="Philippine Pesos Illustration" loading="lazy">
                                    <p class="mb-0 mt-1">Affordable Price</p>
                                </div>
                                <div class="col">
                                    <img src="./assets/images/truck.png" alt="Truck Illustration" loading="lazy">
                                    <p class="mb-0 mt-1">Excellent Service</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Menu Section -->
        <section class="menu section" id="menu">
            <h2 class="heading px-5 py-5">
                Our Products
                <span class="fs-3">Our Products</span>
            </h2>
            <div class="container text-center border border-primary px-5">
                <div class="container text-center border border-warning">
                    <div class="row" id="btn-container">
                        <!-- Inject -->
                    </div>

                    <div class="row">
                        <div class="col">
                            Test
                        </div>
                        <div class="col">
                            Test
                        </div>
                        <div class="col">
                            Test
                        </div>
                        <div class="col">
                            Test
                        </div>
                        <div class="col">
                            Test
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Reviews Section -->
        <section class="reviews section" id="reviews">
            <h2 class="heading px-5 py-5">
                Reviews
                <span class="fs-3">What People Say</span>
            </h2>
        </section>
    </main>

    <!-- Footer Section -->
    <?php include './view/footer.php' ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            pannellum.viewer('panorama', {
                "type": "equirectangular",
                "panorama": "./assets/images/thecavitegarden-360-photo.jpg",
                "autoLoad": true
            });
        })
    </script>
</body>

</html>