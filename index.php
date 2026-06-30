<?php
/*
  index.php
 Public landing page. No login 
 */
?>
<html>
  <head>
    <title>SmileCare Dental Clinic</title>
    <link rel="stylesheet" href="assets/css/index.css" />
  </head>

  <body>
    <?php require_once __DIR__ . '/includes/navbar-guest.php'; ?>

    <main>
      <!-- HERO SECTION -->
      <section id="hero">
        <div class="heroContent">
          <span class="heroLocation"> Puerto Princesa City, Palawan</span>

          <h1>Your <em>Smile</em>,<br />Our Priority</h1>

          <p>
            Book your dental appointment online — fast, easy, and hassle-free.
            Trusted by 2,000+ patients.
          </p>

          <div class="heroBtns">
            <button class="heroBtnPrimary" onclick="window.location.href='register.php'">Book Now</button>
            <a class="heroBtnSecondary" href="#services">▶ Learn More</a>
          </div>
        </div>

        <div class="heroImage">
          <img src="assets/images/heroImageSample.jpg" alt="Dental Clinic" />
        </div>
      </section>

      <!-- HOW IT WORKS -->
      <section class="howItWorks">
        <div class="sectionHeader">
          <div class="sectionTag">How It Works</div>
          <h2 class="sectionTitle">Book in 3 Simple Steps</h2>
        </div>

        <div class="stepsGrid">
          <div class="stepCard">
            <div class="stepNum">1</div>
            <h3>Create an Account</h3>
            <p>Register using your name, email, and contact number. It only takes a minute.</p>
            <div class="stepConnector">→</div>
          </div>

          <div class="stepCard">
            <div class="stepNum">2</div>
            <h3>Book an Appointment</h3>
            <p>Choose your preferred service, dentist, date, and time slot that works for you.</p>
            <div class="stepConnector">→</div>
          </div>

          <div class="stepCard">
            <div class="stepNum">3</div>
            <h3>Visit the Clinic</h3>
            <p>Come in on your scheduled date and our dentists will take great care of you.</p>
          </div>
        </div>
      </section>

      <!-- services from datbase  -->
      <section id="services">
        <div class="sectionHeader">
          <div class="sectionTag">What We Offer</div>
          <h2 class="sectionTitle">Our Services</h2>
        </div>

        <div class="servicesGrid">
          <?php
          require_once __DIR__ . '/config/db.php';
          $result = $conn->query("SELECT name, description, starting_price FROM services ORDER BY service_id");
          while ($row = $result->fetch_assoc()):
          ?>
            <div class="serviceCard">
              
              <h3><?php echo htmlspecialchars($row['name']); ?></h3>
              <p><?php echo htmlspecialchars($row['description']); ?></p>
              <div class="servicePrice">From ₱<?php echo number_format($row['starting_price'], 0); ?></div>
            </div>
          <?php endwhile; ?>
        </div>
      </section>

      <!-- doctors from db  -->
      <section id="doctors">
        <div class="sectionHeader">
          <div class="sectionTag">Meet Our Team</div>
          <h2 class="sectionTitle">Our Dentists</h2>
        </div>

        <div class="doctorsGrid">
          <?php
          $result = $conn->query("SELECT full_name, specialty, years_experience FROM dentists ORDER BY dentist_id");
          while ($row = $result->fetch_assoc()):
          ?>
            <div class="doctorCard">
              <div class="doctorInfo">
                <h3><?php echo htmlspecialchars($row['full_name']); ?></h3>
                <div class="doctorSpecialty"><?php echo htmlspecialchars($row['specialty']); ?></div>
                <p class="doctorExperience"><?php echo (int)$row['years_experience']; ?>+ years of experience</p>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      </section>

      <!-- about section -->
      <section id="about" class="aboutSection">
        <div class="aboutContent aboutContentCentered">
          <div class="sectionTag">About Us</div>
          <h2>Always Caring for Your Smile</h2>
          <p>
            SmileCare Dental Clinic is dedicated to providing high-quality, affordable
            dental care for patients of all ages. Our team of licensed and experienced
            dentists ensures that every visit is comfortable, safe, and stress-free.
          </p>
          <p>We believe that a healthy smile starts with regular care and the right dental team by your side.</p>
        </div>
      </section>
    </main>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
  </body>
</html>
