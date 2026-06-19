<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - NextGen College</title>
    <link rel="stylesheet" href="./asset/css/aboutus.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="logo">NextGen College</div>
        <ul class="nav-links">
            <li><a href="Foxes.html">Home</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="courses.php">Courses</a></li>
            <li><a href="Student_Portal.php">Student Portal</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </nav>

    <!-- About Us Section -->
    <section class="about-us">
        <div class="about-container">
            <h2 class="section-title">About Us</h2>
            <p>NextGen College is a premier institution dedicated to providing quality education and fostering innovation. With state-of-the-art facilities and experienced faculty, we aim to shape the leaders of tomorrow.</p>
            <p>We offer a wide range of programs in various disciplines, ensuring our students are well-prepared for their future careers.</p>
        </div>
    </section>

    <!-- Vision, Mission, and Brief About College Section -->
    <section class="vision-mission-brief">
        <div class="vision-mission-brief-container">
            <!-- Tabs for Vision, Mission, and Brief About College -->
            <div class="tabs">
                <button class="tab-button active" onclick="openTab('vision')">
                    <i class="fas fa-eye"></i> Vision
                </button>
                <button class="tab-button" onclick="openTab('mission')">
                    <i class="fas fa-bullseye"></i> Mission
                </button>
                <button class="tab-button" onclick="openTab('brief')">
                    <i class="fas fa-info-circle"></i> Brief About College
                </button>
            </div>

            <!-- Vision Tab Content -->
            <div id="vision" class="tab-content active">
                <h3>Our Vision</h3>
                <p>To impart qualitative and value-based education in commerce and business studies, by blending creativity, curiosity, and communication, leading towards a desirable socio-economic transformation of the nation impacting the world at large.</p>
            </div>

            <!-- Mission Tab Content -->
            <div id="mission" class="tab-content">
                <h3>Our Mission</h3>
                <p>The Mission of our College aims to bring the Vision into reality by harnessing its rich and physical human resources towards the development of students, considering them as the focal point in the following ways:</p>
                <ul>
                    <li><i class="fas fa-check-circle"></i> Delivering a High Content Curriculum: To deliver a high content curriculum, which is market-oriented and contemporary, matching the requisite skill sets developed through industry-academia interface.</li>
                    <li><i class="fas fa-check-circle"></i> Value Based Learning: To include a sense of ethical, moral, and human values and social responsibility leading to the highest integrity and commitment to society.</li>
                    <li><i class="fas fa-check-circle"></i> Creativity: To impress an amalgamation of self and participative learning through innovative pedagogy tools and peer learning to hone analytical skills.</li>
                    <li><i class="fas fa-check-circle"></i> Curiosity: To foster curiosity by creating an environment that encourages openness, independent thinking, questioning, reflection, and learning together.</li>
                    <li><i class="fas fa-check-circle"></i> Communication: To promote effective communication skills by instilling divergent thinking and experimentation, embracing differences, and building confidence.</li>
                </ul>
            </div>

            <!-- Brief About College Tab Content -->
            <div id="brief" class="tab-content">
                <h3>Brief About College</h3>
                <p>NextGen College, established in 1971, is one of the leading institutions in the region, offering a wide range of undergraduate and postgraduate programs. Our college is known for its academic excellence, state-of-the-art infrastructure, and a strong focus on holistic development.</p>
                <p>We pride ourselves on our diverse student community, experienced faculty, and a vibrant campus life that fosters creativity, innovation, and leadership.</p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact">
        <h2 class="section-title">Contact Us</h2>
        <div class="contact-container">
            <div class="contact-info">
                <p><strong><i class="fas fa-phone"></i> Phone:</strong> 020 2699 0353</p>
                <p><strong><i class="fas fa-envelope"></i> Email:</strong>  info@nextgencollege.edu.in</p>
                <p><strong><i class="fas fa-map-marker-alt"></i> Address:</strong> Near 15 No Chowk, Chaitanya Colony, Mahadev Nagar,NextGen Hadapsar, Pune - 411028, Maharashtra</p>
            </div>
            <div class="map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3783.552204790782!2d73.95131017335073!3d18.503931969685897!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bc2c36f2e2d9cbf%3A0xbbb7ded39d15daf5!2sPDEA&#39;s%20Annasaheb%20Magar%20Mahavidyalaya!5e0!3m2!1sen!2sin!4v1745004109845!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 NextGen College. All rights reserved.</p>
    </footer>

    <script>
        // JavaScript for Tab Functionality
        function openTab(tabName) {
            const tabContents = document.querySelectorAll('.tab-content');
            const tabButtons = document.querySelectorAll('.tab-button');

            // Hide all tab contents
            tabContents.forEach(tab => tab.classList.remove('active'));

            // Remove active class from all buttons
            tabButtons.forEach(button => button.classList.remove('active'));

            // Show the selected tab content
            document.getElementById(tabName).classList.add('active');

            // Add active class to the clicked button
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>