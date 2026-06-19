<?php
// nav.php
if (!isset($_SESSION)) {
    session_start();
}
?>

<nav>
    <div class="logo">NextGen College</div>
    <ul class="nav-links">
        <li><a href="Foxes.html">Home</a></li>
        <li><a href="aboutus.php">About</a></li>
        <li><a href="courses.php">Courses</a></li>
        <li><a href="Student_Portal.php">Student Portal</a></li>
        <li><a href="contact.php">Contact</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Display if user is logged in -->
            <li class="dropdown">
                <a href="#" class="dropdown-toggle">My Account</a>
                <ul class="dropdown-menu">
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </li>
        <?php else: ?>
            
            
        <?php endif; ?>
    </ul>
    <div class="hamburger">☰</div>
</nav>

<style>
    /* Navigation Styles */
    nav {
        background: #2c3e50;
        color: #fff;
        padding: 1rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .logo {
        font-size: 1.5rem;
        font-weight: bold;
    }

    .nav-links {
        display: flex;
        gap: 2rem;
        list-style: none;
    }

    .nav-links a {
        color: #fff;
        transition: color 0.3s;
    }

    .nav-links a:hover {
        color: #3498db;
    }

    .dropdown {
        position: relative;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background: #2c3e50;
        list-style: none;
        padding: 0.5rem;
        border-radius: 4px;
    }

    .dropdown:hover .dropdown-menu {
        display: block;
    }

    .dropdown-menu li {
        padding: 0.5rem 0;
    }

    .dropdown-menu a {
        color: #fff;
        text-decoration: none;
    }

    .hamburger {
        display: none;
        font-size: 1.5rem;
        cursor: pointer;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .nav-links {
            display: none;
            flex-direction: column;
            position: absolute;
            top: 60px;
            left: 0;
            right: 0;
            background: #2c3e50;
            padding: 1rem;
        }

        .nav-links.active {
            display: flex;
        }

        .hamburger {
            display: block;
        }
    }
</style>

<script>
    // Hamburger menu functionality
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');

    hamburger.addEventListener('click', () => {
        navLinks.classList.toggle('active');
    });
</script>