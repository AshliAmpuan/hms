<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Shepherd Animal Clinic</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="Veterinary services, pet care, animal clinic, pet health, vaccinations, surgery, grooming, consultation" name="keywords" />
    <meta content="Shepherd Animal Clinic offers expert veterinary care with professional veterinarians providing vaccination, surgery, grooming, consultation, and laboratory services to ensure your pet's health and happiness." name="description" />

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon" />

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&amp;family=Roboto:wght@500;700;900&amp;display=swap" rel="stylesheet" />

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet" />
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet" />
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet" />

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet" />
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Topbar Start -->
<div class="container-fluid bg-light p-0 wow fadeIn" data-wow-delay="0.1s">
    <div class="row gx-0 d-none d-lg-flex">
        <div class="col-lg-7 px-5 text-start">
            <div class="h-100 d-inline-flex align-items-center py-2 me-4"> <!-- Reduced padding from py-3 to py-2 -->
                <small class="fa fa-map-marker-alt text-success me-2" style="font-size: 1.2rem;"></small> <!-- Increased icon size -->
                <small>1427 Pedro Gil St. Corner Peñafrancia, Paco, Philippines</small>
            </div>
            <div class="h-100 d-inline-flex align-items-center py-2 me-4"> <!-- Reduced padding from py-3 to py-2 -->
                <small class="far fa-clock text-success me-2" style="font-size: 1.2rem;"></small> <!-- Increased icon size -->
                <small>Open Daily: 12:00 PM - 08:00 PM</small>
            </div>
        </div>
        <div class="col-lg-5 px-5 text-end">
            <div class="h-100 d-inline-flex align-items-center py-2 me-4"> <!-- Reduced padding from py-3 to py-2 -->
                <small class="fa fa-phone-alt text-success me-2" style="font-size: 1.2rem;"></small> <!-- Increased icon size -->
                <small>Contact: (02) 9066 662</small>
            </div>
            <div class="h-100 d-inline-flex align-items-center">
                <a class="btn btn-sm-square rounded-circle bg-white text-success me-1 d-flex align-items-center justify-content-center" href="#"><i class="fab fa-facebook-f"></i></a>
                <a class="btn btn-sm-square rounded-circle bg-white text-success me-1 d-flex align-items-center justify-content-center" href="#"><i class="fab fa-twitter"></i></a>
                <a class="btn btn-sm-square rounded-circle bg-white text-success me-1 d-flex align-items-center justify-content-center" href="#"><i class="fab fa-linkedin-in"></i></a>
                <a class="btn btn-sm-square rounded-circle bg-white text-success me-0 d-flex align-items-center justify-content-center" href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
</div>
<!-- Topbar End -->




    <!-- Header Start -->
    <?php include('include/front_navbar.php'); ?>
    <!-- Header End -->

    <!-- About Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <div class="d-flex flex-column">
                        <img class="img-fluid rounded w-75 align-self-end" src="img/about-dog.jpg" alt="Happy dog at Shepherd Animal Clinic" />
                        <img class="img-fluid rounded w-50 bg-white pt-3 pe-3" src="img/about-dog1.jpg" alt="Veterinarian caring for a dog" style="margin-top: -25%;" />
                    </div>
                </div>
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                    <p class="d-inline-block border rounded-pill py-1 px-4">About Us</p>
                    <h1 class="mb-4">Trusted Veterinary Care for Your Beloved Pets</h1>
                    <p>At Shepherd Animal Clinic, we provide comprehensive veterinary services to ensure your pets live a healthy and happy life. Our dedicated team employs modern techniques and compassionate care to meet your pet’s unique needs.</p>
                    <p class="mb-4">Our state-of-the-art facility offers premium pet health services including preventive care, diagnostics, surgery, and wellness programs designed to keep your pet in the best condition.</p>
                    <p><i class="far fa-check-circle text-primary me-3"></i>Expert Veterinary Care & Consultation</p>
                    <p><i class="far fa-check-circle text-primary me-3"></i>Affordable and Comprehensive Treatment</p>
                    <p><i class="far fa-check-circle text-primary me-3"></i>Personalized Care Tailored to Each Pet</p>
                    <a class="btn btn-primary rounded-pill py-3 px-5 mt-3" href="#">Learn More About Us</a>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->

    <!-- Service Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <p class="d-inline-block border rounded-pill py-1 px-4">Services</p>
                <h1>Comprehensive Pet Health Care Solutions</h1>
                <p>We offer a wide range of services to keep your pet healthy and happy, delivered by experienced veterinarians using the latest technology.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item bg-light rounded h-100 p-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-bone text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-3">Consultation and Treatment</h4>
                        <p>Professional veterinary consultation to diagnose and treat various pet conditions, providing expert advice on the best care practices for your beloved companion.</p>
                        <a class="btn" href="#"><i class="fa fa-plus text-primary me-3"></i>Read More</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item bg-light rounded h-100 p-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-syringe text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-3">Vaccination/Deworming</h4>
                        <p>Comprehensive vaccination programs and deworming treatments to protect your pets from common and dangerous diseases, ensuring their long-term health and well-being.</p>
                        <a class="btn" href="#"><i class="fa fa-plus text-primary me-3"></i>Read More</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="service-item bg-light rounded h-100 p-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-brain text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-3">Surgery</h4>
                        <p>Advanced and compassionate surgical care for various conditions, performed by skilled veterinarians with the latest technology.</p>
                        <a class="btn" href="#"><i class="fa fa-plus text-primary me-3"></i>Read More</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="service-item bg-light rounded h-100 p-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-scissors text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-3">Grooming</h4>
                        <p>Complete grooming services to keep your pet clean, healthy, and comfortable, including baths, haircuts, and nail care.</p>
                        <a class="btn" href="#"><i class="fa fa-plus text-primary me-3"></i>Read More</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.9s">
                    <div class="service-item bg-light rounded h-100 p-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-stethoscope text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-3">Pet Medical Supplies</h4>
                        <p>High-quality medical supplies and medications for your pets, including prescription drugs, supplements, and essential health products for ongoing care.</p>
                        <a class="btn" href="#"><i class="fa fa-plus text-primary me-3"></i>Read More</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="1.1s">
                    <div class="service-item bg-light rounded h-100 p-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-vials text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-3">Laboratory Test</h4>
                        <p>On-site laboratory testing for fast and accurate diagnosis, supporting effective treatment plans tailored for your pet.</p>
                        <a class="btn" href="#"><i class="fa fa-plus text-primary me-3"></i>Read More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Service End -->

    <!-- Features Start -->
    <div class="container-fluid bg-primary overflow-hidden my-5 px-lg-0">
        <div class="container feature px-lg-0">
            <div class="row g-0 mx-lg-0">
                <div class="col-lg-6 feature-text py-5 wow fadeIn" data-wow-delay="0.1s">
                    <div class="p-lg-5 ps-lg-0">
                        <p class="d-inline-block border rounded-pill text-light py-1 px-4">Features</p>
                        <h1 class="text-white mb-4">Why Choose Shepherd Animal Clinic</h1>
                        <p class="text-white mb-4 pb-2">
                            Shepherd Animal Clinic is dedicated to providing compassionate and expert care for your pets. With experienced veterinarians, advanced facilities, and 24/7 support, we ensure the best health outcomes for your furry family members.
                        </p>
                        <div class="row g-4">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-light" style="width: 55px; height: 55px;">
                                        <i class="fa fa-user-md text-primary"></i>
                                    </div>
                                    <div class="ms-4">
                                        <p class="text-white mb-2">Experienced</p>
                                        <h5 class="text-white mb-0">Veterinarians</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-light" style="width: 55px; height: 55px;">
                                        <i class="fa fa-check text-primary"></i>
                                    </div>
                                    <div class="ms-4">
                                        <p class="text-white mb-2">Quality</p>
                                        <h5 class="text-white mb-0">Services</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-light" style="width: 55px; height: 55px;">
                                        <i class="fa fa-comment-medical text-primary"></i>
                                    </div>
                                    <div class="ms-4">
                                        <p class="text-white mb-2">Compassionate</p>
                                        <h5 class="text-white mb-0">Consultation</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-light" style="width: 55px; height: 55px;">
                                        <i class="fa fa-headphones text-primary"></i>
                                    </div>
                                    <div class="ms-4">
                                        <p class="text-white mb-2">24/7</p>
                                        <h5 class="text-white mb-0">Support</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 pe-lg-0 wow fadeIn" data-wow-delay="0.5s" style="min-height: 400px;">
                    <div class="position-relative h-100">
                        <img class="position-absolute img-fluid w-100 h-100" src="img/feature-cat.jpg" alt="Cat receiving care at Shepherd Animal Clinic" style="object-fit: cover;" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Features End -->

    <!-- Team Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <p class="d-inline-block border rounded-pill py-1 px-4">Veterinarians</p>
                <h1>Meet Our Experienced Veterinarians</h1>
                <p>Our caring and highly trained veterinarians are dedicated to providing the best care to your pets.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="team-item position-relative rounded overflow-hidden">
                        <div class="overflow-hidden">
                            <img class="img-fluid" src="img/team-1.jpg" alt="Dr. Maria Santiago, Small Animal Specialist" />
                        </div>
                        <div class="team-text bg-light text-center p-4">
                            <h5>Dr. Maria Santiago</h5>
                            <p class="text-primary">Small Animal Specialist</p>
                            <div class="team-social text-center">
                                <a class="btn btn-square" href="#"><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square" href="#"><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-square" href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="team-item position-relative rounded overflow-hidden">
                        <div class="overflow-hidden">
                            <img class="img-fluid" src="img/team-2.jpg" alt="Dr. John Reyes, Surgery Specialist" />
                        </div>
                        <div class="team-text bg-light text-center p-4">
                            <h5>Dr. John Reyes</h5>
                            <p class="text-primary">Surgery Specialist</p>
                            <div class="team-social text-center">
                                <a class="btn btn-square" href="#"><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square" href="#"><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-square" href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="team-item position-relative rounded overflow-hidden">
                        <div class="overflow-hidden">
                            <img class="img-fluid" src="img/team-3.jpg" alt="Dr. Ana Lopez, Preventive Care" />
                        </div>
                        <div class="team-text bg-light text-center p-4">
                            <h5>Dr. Ana Lopez</h5>
                            <p class="text-primary">Preventive Care</p>
                            <div class="team-social text-center">
                                <a class="btn btn-square" href="#"><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square" href="#"><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-square" href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="team-item position-relative rounded overflow-hidden">
                        <div class="overflow-hidden">
                            <img class="img-fluid" src="img/team-4.jpg" alt="Dr. Miguel Cruz, Emergency Care" />
                        </div>
                        <div class="team-text bg-light text-center p-4">
                            <h5>Dr. Miguel Cruz</h5>
                            <p class="text-primary">Emergency Care</p>
                            <div class="team-social text-center">
                                <a class="btn btn-square" href="#"><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square" href="#"><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-square" href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Team End -->

    <!-- Appointment Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <p class="d-inline-block border rounded-pill py-1 px-4">Appointment</p>
                    <h1 class="mb-4">Schedule Your Pet's Appointment Easily</h1>
                    <p class="mb-4">
                        Book an appointment with our veterinarians for consultation, treatments, or vaccinations. Our caring staff will assist you in providing the best care for your pet.
                    </p>
                    <div class="bg-light rounded d-flex align-items-center p-5 mb-4">
                        <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                            <i class="fa fa-phone-alt text-primary"></i>
                        </div>
                        <div class="ms-4">
                            <p class="mb-2">Call Us Now</p>
                            <h5 class="mb-0">+012 345 6789</h5>
                        </div>
                    </div>
                    <div class="bg-light rounded d-flex align-items-center p-5">
                        <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                            <i class="fa fa-envelope-open text-primary"></i>
                        </div>
                        <div class="ms-4">
                            <p class="mb-2">Email Us</p>
                            <h5 class="mb-0">info@shepherdclinic.com</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="bg-light rounded h-100 d-flex align-items-center p-5">
                        <form>
                            <div class="row g-3">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="form-control border-0" placeholder="Your Name" style="height: 55px" aria-label="Your Name" required />
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="email" class="form-control border-0" placeholder="Your Email" style="height: 55px" aria-label="Your Email" required />
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="tel" class="form-control border-0" placeholder="Your Mobile" style="height: 55px" aria-label="Your Mobile" required pattern="[0-9]{10,15}" />
                                </div>
                                <div class="col-12 col-sm-6">
                                    <select class="form-select border-0" style="height: 55px" aria-label="Choose Veterinarian" required>
                                        <option selected disabled>Choose Veterinarian</option>
                                        <option value="1">Dr. Maria Santiago</option>
                                        <option value="2">Dr. John Reyes</option>
                                        <option value="3">Dr. Ana Lopez</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="date" id="date" data-target-input="nearest">
                                        <input type="text" class="form-control border-0 datetimepicker-input" placeholder="Choose Date" data-target="#date" data-toggle="datetimepicker" style="height: 55px" aria-label="Choose Date" required />
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="time" id="time" data-target-input="nearest">
                                        <input type="text" class="form-control border-0 datetimepicker-input" placeholder="Choose Time" data-target="#time" data-toggle="datetimepicker" style="height: 55px" aria-label="Choose Time" required />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <textarea class="form-control border-0" rows="5" placeholder="Describe your pet's condition or concerns" aria-label="Describe your problem" required></textarea>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3" type="submit" aria-label="Book Appointment">Book Appointment</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Appointment End -->

    <!-- Testimonial Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <p class="d-inline-block border rounded-pill py-1 px-4">Testimonial</p>
                <h1>What Our Pet Owners Say</h1>
            </div>
            <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
                <div class="testimonial-item text-center">
                    <img class="img-fluid bg-light rounded-circle p-2 mx-auto mb-4" src="img/testimonial-1.jpg" style="width: 100px; height: 100px;" alt="Photo of happy pet owner" />
                    <div class="testimonial-text rounded text-center p-4">
                        <p>“Shepherd Animal Clinic provided exceptional care for my dog during his surgery. The veterinarians are compassionate and highly skilled.”</p>
                        <h5 class="mb-1">Maria Dela Cruz</h5>
                        <span class="fst-italic">Pet Owner</span>
                    </div>
                </div>
                <div class="testimonial-item text-center">
                    <img class="img-fluid bg-light rounded-circle p-2 mx-auto mb-4" src="img/testimonial-2.jpg" style="width: 100px; height: 100px;" alt="Photo of satisfied pet owner" />
                    <div class="testimonial-text rounded text-center p-4">
                        <p>“The vaccination program and consultations have kept my cats healthy and happy. Friendly staff and excellent service!”</p>
                        <h5 class="mb-1">Juan Santos</h5>
                        <span class="fst-italic">Pet Owner</span>
                    </div>
                </div>
                <div class="testimonial-item text-center">
                    <img class="img-fluid bg-light rounded-circle p-2 mx-auto mb-4" src="img/testimonial-3.jpg" style="width: 100px; height: 100px;" alt="Photo of grateful pet owner" />
                    <div class="testimonial-text rounded text-center p-4">
                        <p>“Highly recommend Shepherd Animal Clinic. The team is professional, caring, and always puts the pets’ welfare first.”</p>
                        <h5 class="mb-1">Ana Garcia</h5>
                        <span class="fst-italic">Pet Owner</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Testimonial End -->

    <!-- Footer Start -->
    <?php include('include/front_footer.php'); ?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top" aria-label="Back to top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>

