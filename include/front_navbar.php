<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Shepherd Animal Clinic - Navbar</title>
<!-- Bootstrap CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<!-- FontAwesome CDN for icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
<style>
  /* Navbar background and shadow */
  .navbar {
    background-color: #121212;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.8);
  }

  /* Brand styling */
  .navbar-brand {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .navbar-brand img {
    height: 50px; /* Increased image height to 50px */
    width: auto;
  }
  .navbar-brand h1 {
    color: #ffffff;
    font-weight: 700;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 1.98rem; /* Increased font size by 10% */
    margin: 0;
  }

  /* Navbar links default with white text */
  .navbar-nav .nav-link {
    color: #ffffff !important;
    font-weight: 500;
    padding: 10px 15px;
    border-radius: 6px;
    transition: color 0.3s ease; /* Only transition color */
    text-shadow: none;
  }
  .navbar-nav .nav-link.active,
  .navbar-nav .nav-link.hover {
    color: #28a745 !important; /* Active state color (green) */
    font-weight: 700;
  }
  .navbar-nav .nav-link:hover {
    color: #28a745 !important; /* Change text color to green on hover */
    text-decoration: none;
  }

  /* Dropdown menu styling */
  .dropdown-menu {
    background-color: #1f1f1f;
    border-radius: 8px;
    border: none;
    box-shadow: 0 8px 16px rgba(255, 193, 7, 0.3);
    transition: opacity 0.3s ease;
  }
  .dropdown-item {
    color: #ffffff !important;
    font-weight: 500;
    transition: background-color 0.3s ease, color 0.3s ease;
    text-shadow: none;
  }
  .dropdown-item:hover {
    background-color: #28a745; /* Change dropdown item background to green on hover */
    color: #121212 !important;
  }

  .nav-link.dropdown-toggle {
    position: relative;
  }
  .nav-link.dropdown-toggle::after {
    border-top: 0.3em solid #ffffff; /* Change dropdown arrow color to white */
    border-right: 0.3em solid transparent;
    border-left: 0.3em solid transparent;
    content: "";
    display: inline-block;
    margin-left: .255em;
    vertical-align: 0.255em;
  }

  /* Increased height Login button */
  .btn-primary {
    background: linear-gradient(135deg, #28a745 0%, #218838 100%); /* Green gradient */
    border: none;
    font-weight: 600;
    border-radius: 4px;
    padding: 8px 42px; /* Increased left and right padding by 10px */
    margin-right: 15px; /* Added right margin */
    box-shadow: 0 6px 12px rgba(40, 167, 69, 0.5); /* Shadow color adjusted to green */
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background 0.3s ease, box-shadow 0.3s ease;
    color: #fff;
    font-size: 1rem;
    line-height: normal;
  }
  .btn-primary:hover, .btn-primary:focus {
    background: linear-gradient(135deg, #218838 0%, #1e7e34 100%); /* Darker green on hover */
    box-shadow: 0 8px 16px rgba(0, 86, 179, 0.7);
    color: #fff;
    text-decoration: none;
  }

  /* Navbar toggler color */
  .navbar-dark .navbar-toggler-icon {
    filter: invert(100%);
  }

  @media (max-width: 991.98px) {
    .navbar-nav .nav-link {
      padding: 8px 12px;
      font-weight: 600;
    }
    .btn-primary {
      padding: 6px 34px; /* Adjusted for smaller screens */
      font-size: 0.9rem;
    }
  }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top p-0 wow fadeIn" data-wow-delay="0.1s">
  <a href="index.html" class="navbar-brand px-4 px-lg-5" aria-label="Homepage">
    <img src="img/shepherd.png" alt="Shepherd Animal Clinic Logo" />
    <h1>Shepherd Animal Clinic</h1>
  </a>
  <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-label="Toggle navigation" aria-expanded="false">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarCollapse">
    <div class="navbar-nav ms-auto p-4 p-lg-0">
      <a href="index.php" class="nav-item nav-link active" aria-current="page" onclick="setActive(this)">Home</a> <!-- Home button -->
      <a href="about.php" class="nav-item nav-link" onclick="setActive(this)">About</a>
      <a href="services.php" class="nav-item nav-link" onclick="setActive(this)">Service</a>
      <a href="appointment.php" class="nav-item nav-link" onclick="setActive(this)">Appointment</a>
      <div class="nav-item dropdown">
        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false" onclick="setActive(this)">Pages</a>
        <ul class="dropdown-menu rounded-0 rounded-bottom m-0">
          <li><a href="feature.php" class="dropdown-item">Feature</a></li>
          <li><a href="team.php" class="dropdown-item">Our Doctor</a></li>
          <li><a href="testimonial.php" class="dropdown-item">Testimonial</a></li>
        </ul>
      </div>
      <a href="contact.php" class="nav-item nav-link" onclick="setActive(this)">Contact</a>
    </div>
    <a href="login.php" class="btn btn-primary rounded d-none d-lg-flex align-items-center" role="button" aria-label="Login">
      LOGIN
    </a>
  </div>
</nav>

<!-- Bootstrap JS and Popper.js CDN for dropdowns and toggler -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<script>
  function setActive(element) {
    // Remove active class from all nav links
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    navLinks.forEach(link => {
      link.classList.remove('active');
    });
    
    // Add active class to the clicked link
    element.classList.add('active');
    
    // Optionally, navigate to the link after a short delay
    setTimeout(() => {
      window.location.href = element.href;
    }, 200); // Adjust the delay as needed
  }
</script>

</body>
</html>
