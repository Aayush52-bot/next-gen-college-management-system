<?php
// admission_form.php
session_start();
include('db.php');

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit;
}

// Get course_id from URL
$course_id = $_GET['course_id'] ?? 0;

// Fetch course details
$course_query = "SELECT * FROM courses WHERE course_id = ?";
$stmt = $conn->prepare($course_query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course_result = $stmt->get_result();
$course = $course_result->fetch_assoc();

// If the course doesn't exist, redirect to courses page
if (!$course) {
    header('Location: courses.php');
    exit;
}

// Fetch user details
$user_id = $_SESSION['userid'];
$user_query = "SELECT full_name FROM users_register WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $_SESSION['userid']);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $upload_dir = "uploads/";
    $folderName = $_SESSION['userid'];
    $upload_dir = "uploads/" . $folderName;
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $upload_dir = $upload_dir . "/";

    // Initialize file paths
    $ssc_marksheet_path = $upload_dir . basename($_FILES['ssc_marksheet']['name'] ?? '');
    $ssc_certificate_path = $upload_dir . basename($_FILES['ssc_certificate']['name'] ?? '');
    $hsc_marksheet_path = $upload_dir . basename($_FILES['hsc_marksheet']['name'] ?? '');
    $caste_certificate_path = $upload_dir . basename($_FILES['caste_certificate']['name'] ?? '');
    $aadhar_card_path = $upload_dir . basename($_FILES['aadhar_card']['name'] ?? '');
    $previous_marksheet_path = $upload_dir . basename($_FILES['previous_marksheet']['name'] ?? '');
    $applicant_photo_path = $upload_dir . basename($_FILES['applicant_photo']['name'] ?? '');
    $transfer_certificate_path = $upload_dir . basename($_FILES['transfer_certificate']['name'] ?? '');
    $previous_degree_certificate_path = $upload_dir . basename($_FILES['previous_degree_certificate']['name'] ?? '');

    // Move uploaded files
    if (!empty($_FILES['ssc_marksheet']['tmp_name'])) move_uploaded_file($_FILES['ssc_marksheet']['tmp_name'], $ssc_marksheet_path);
    if (!empty($_FILES['ssc_certificate']['tmp_name'])) move_uploaded_file($_FILES['ssc_certificate']['tmp_name'], $ssc_certificate_path);
    if (!empty($_FILES['hsc_marksheet']['tmp_name'])) move_uploaded_file($_FILES['hsc_marksheet']['tmp_name'], $hsc_marksheet_path);
    if (!empty($_FILES['caste_certificate']['tmp_name'])) move_uploaded_file($_FILES['caste_certificate']['tmp_name'], $caste_certificate_path);
    if (!empty($_FILES['aadhar_card']['tmp_name'])) move_uploaded_file($_FILES['aadhar_card']['tmp_name'], $aadhar_card_path);
    if (!empty($_FILES['previous_marksheet']['tmp_name'])) move_uploaded_file($_FILES['previous_marksheet']['tmp_name'], $previous_marksheet_path);
    if (!empty($_FILES['applicant_photo']['tmp_name'])) move_uploaded_file($_FILES['applicant_photo']['tmp_name'], $applicant_photo_path);
    if (!empty($_FILES['transfer_certificate']['tmp_name'])) move_uploaded_file($_FILES['transfer_certificate']['tmp_name'], $transfer_certificate_path);
    if (!empty($_FILES['previous_degree_certificate']['tmp_name'])) move_uploaded_file($_FILES['previous_degree_certificate']['tmp_name'], $previous_degree_certificate_path);

    $values = [
        $_SESSION['userid'],
        $course_id,
        $_POST['full_name'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['permanent_address'],
        $_POST['correspondence_address'],
        $_POST['nationality'],
        $_POST['area'],
        $_POST['marital_status'],
        $_POST['category'],
        $_POST['religion'],
        $_POST['caste'],
        $_POST['sub_caste'],
        $_POST['state'],
        $_POST['district'],
        $_POST['taluka'],
        $_POST['city'],
        $_POST['pincode'],
        $_POST['home_number'],
        $_POST['domicile'],
        $_POST['language'],
        $_POST['blood_group'],
        $_POST['physically_handicapped'],
        $_POST['medium'],
        $_POST['last_institute'],
        $_POST['abc_id'] ?? '',
        $_POST['father_name'],
        $_POST['father_income'],
        $_POST['father_occupation'],
        $_POST['father_phone'],
        $_POST['mother_name'],
        $_POST['mother_income'],
        $_POST['mother_occupation'],
        $_POST['mother_phone'],
        $_POST['guardian_address'],
        $_POST['guardian_relation'],
        $_POST['guardian_contact'],
        $_POST['ssc_passing'],
        $_POST['ssc_board'],
        $_POST['ssc_school'],
        $_POST['ssc_seat_no'],
        $_POST['ssc_class_div'],
        $_POST['ssc_total_marks'],
        $_POST['ssc_obtained_marks'],
        $_POST['ssc_percentage'],
        $_POST['hsc_passing'] ?? '',
        $_POST['hsc_board'] ?? '',
        $_POST['hsc_stream'] ?? '',
        $_POST['hsc_school'] ?? '',
        $_POST['hsc_seat_no'] ?? '',
        $_POST['hsc_class_div'] ?? '',
        $_POST['hsc_total_marks'] ?? 0,
        $_POST['hsc_obtained_marks'] ?? 0,
        $_POST['hsc_percentage'] ?? 0,
        $ssc_marksheet_path,
        $ssc_certificate_path,
        $hsc_marksheet_path,
        $caste_certificate_path,
        $aadhar_card_path,
        'Pending',
        date('Y-m-d H:i:s'),
        $_POST['academic_year'],
        $_POST['selected_class'],
        $_POST['previous_course_name'] ?? '',
        $_POST['previous_institute_name'] ?? '',
        $_POST['previous_marks'] ?? 0,
        $previous_marksheet_path,
        $applicant_photo_path,
        $transfer_certificate_path,
        $_POST['previous_passing_year'] ?? '',
        $_POST['previous_enrollment_no'] ?? '',
        $_POST['previous_cgpa'] ?? '',
        $_POST['previous_class_division'] ?? '',
        $_POST['previous_marksheet_no'] ?? '',
        $_POST['previous_mode_of_study'] ?? '',
        $previous_degree_certificate_path
    ];

    $types = str_repeat("s", count($values));
    
    $query = "INSERT INTO admissions (
        user_id, course_id, full_name, email, phone, permanent_address, correspondence_address, 
        nationality, area, marital_status, category, religion, caste, sub_caste, state, district, 
        taluka, city, pincode, home_number, domicile, language, blood_group, physically_handicapped, 
        medium, last_institute, abc_id, father_name, father_income, father_occupation, father_phone, 
        mother_name, mother_income, mother_occupation, mother_phone, guardian_address, guardian_relation, 
        guardian_contact, ssc_passing, ssc_board, ssc_school, ssc_seat_no, ssc_class_div, ssc_total_marks, 
        ssc_obtained_marks, ssc_percentage, hsc_passing, hsc_board, hsc_stream, hsc_school, hsc_seat_no, 
        hsc_class_div, hsc_total_marks, hsc_obtained_marks, hsc_percentage, ssc_marksheet, ssc_certificate, 
        hsc_marksheet, caste_certificate, aadhar_card, status, applied_at, academic_year, selected_class, 
        previous_course_name, previous_institute_name, previous_marks, previous_marksheet_path,
        applicant_photo, transfer_certificate, previous_passing_year, previous_enrollment_no,
        previous_cgpa, previous_class_division, previous_marksheet_no, previous_mode_of_study,
        previous_degree_certificate
    ) VALUES (" . str_repeat("?,", count($values) - 1) . "?)";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo "<script>alert('An error occurred. Please try again later.');</script>";
    } else {
        $stmt->bind_param($types, ...$values);
        if ($stmt->execute()) {
            echo "<script>
                alert('Your application has been received. You can check your status later.');
                window.location.href = 'courses.php';
            </script>";
            exit;
        } else {
            error_log("Execute failed: " . $stmt->error);
            echo "<script>alert('An error occurred. Please try again later.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Form - College Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            max-width: 1200px;
            margin: 2rem auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .profile-section {
            flex: 1;
            padding: 2rem;
            background: #2c3e50;
            color: #fff;
            border-radius: 8px 0 0 8px;
            text-align: center;
        }
        .profile-section img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 1rem;
        }
        .profile-section .logout-btn {
            background: #e74c3c;
            color: #fff;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 1rem;
        }
        .profile-section .logout-btn:hover {
            background: #c0392b;
        }
        .admission-form {
            flex: 2;
            padding: 2rem;
        }
        h2, .section-title {
            color: #2c3e50;
        }
        .section-title {
            margin-top: 2rem;
            border-bottom: 2px solid #3498db;
            padding-bottom: 0.5rem;
        }
        label {
            display: block;
            margin: 1rem 0 0.5rem;
        }
        input, textarea, select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #3498db;
            color: #fff;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 1rem;
        }
        button:hover {
            background: #2980b9;
        }
        .declaration input[type="checkbox"] {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <?php 
    include('nav.php');
    $sql = "SELECT * FROM users_register WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $fullname = $user['full_name'];
    ?>
    <div class="container">
        <div class="profile-section">
            <img src="./asset/img/user.jpg" alt="Profile Picture">
            <h3><?php echo htmlspecialchars($fullname); ?></h3>
            <form action="logout.php" method="POST">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
        
        <div class="admission-form">
            <h2>Admission Form for <?php echo htmlspecialchars($course['course_name']); ?></h2>
            <form method="POST" enctype="multipart/form-data">
                <h3 class="section-title">Personal Details</h3>
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone" required>

                <label for="permanent_address">Permanent Address:</label>
                <textarea id="permanent_address" name="permanent_address" rows="4" required></textarea>

                <label for="correspondence_address">Correspondence Address:</label>
                <textarea id="correspondence_address" name="correspondence_address" rows="4" required></textarea>

                <label for="nationality">Nationality:</label>
                <input type="text" id="nationality" name="nationality" required>

                <label for="area">Area:</label>
                <select id="area" name="area" required>
                    <option value="" disabled selected>Select Area</option>
                    <option value="Urban">Urban</option>
                    <option value="Rural">Rural</option>
                    <option value="Semi-Urban">Semi-Urban</option>
                </select>

                <label for="marital_status">Marital Status:</label>
                <select id="marital_status" name="marital_status" required>
                    <option value="" disabled selected>Select</option>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Divorced">Divorced</option>
                    <option value="Widowed">Widowed</option>
                </select>

                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <option value="" disabled selected>Select</option>
                    <option value="General">General</option>
                    <option value="OBC">OBC</option>
                    <option value="SC">SC</option>
                    <option value="ST">ST</option>
                    <option value="EWS">EWS</option>
                </select>

                <label for="religion">Religion:</label>
                <select id="religion" name="religion" required>
                    <option value="" disabled selected>Select</option>
                    <option value="Hindu">Hindu</option>
                    <option value="Muslim">Muslim</option>
                    <option value="Christian">Christian</option>
                    <option value="Sikh">Sikh</option>
                    <option value="Buddhist">Buddhist</option>
                    <option value="Jain">Jain</option>
                    <option value="Other">Other</option>
                </select>

                <label for="caste">Caste:</label>
                <input type="text" id="caste" name="caste" required>

                <label for="sub_caste">Sub-Caste:</label>
                <input type="text" id="sub_caste" name="sub_caste" required>

                <label for="state">State:</label>
                <input type="text" id="state" name="state" required>

                <label for="district">District:</label>
                <input type="text" id="district" name="district" required>

                <label for="taluka">Taluka:</label>
                <input type="text" id="taluka" name="taluka" required>

                <label for="city">City:</label>
                <input type="text" id="city" name="city" required>

                <label for="pincode">Pincode:</label>
                <input type="text" id="pincode" name="pincode" required>

                <label for="home_number">Home Number:</label>
                <input type="tel" id="home_number" name="home_number" required>

                <label for="domicile">Domicile:</label>
                <input type="text" id="domicile" name="domicile" required>

                <label for="language">Mother Tongue:</label>
                <input type="text" id="language" name="language" required>

                <label for="blood_group">Blood Group:</label>
                <select id="blood_group" name="blood_group" required>
                    <option value="" disabled selected>Select</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                </select>

                <label for="physically_handicapped">Physically Handicapped:</label>
                <select id="physically_handicapped" name="physically_handicapped" required>
                    <option value="" disabled selected>Select</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>

                <label for="medium">Medium:</label>
                <select id="medium" name="medium" required>
                    <option value="" disabled selected>Select</option>
                    <option value="Marathi">Marathi</option>
                    <option value="English">English</option>
                    <option value="Hindi">Hindi</option>
                </select>

                <label for="last_institute">Institute Last Attended:</label>
                <input type="text" id="last_institute" name="last_institute" required>

                <label for="abc_id">ABC ID:</label>
                <input type="text" id="abc_id" name="abc_id">

                <h3 class="section-title">Parents Details</h3>
                <label for="father_name">Father Name:</label>
                <input type="text" id="father_name" name="father_name" required>

                <label for="father_income">Father's Income:</label>
                <select id="father_income" name="father_income" required>
                    <option value="" disabled selected>Select</option>
                    <option value="Less than 1 Lakh">Less than 1 Lakh</option>
                    <option value="1-3 Lakhs">1-3 Lakhs</option>
                    <option value="3-5 Lakhs">3-5 Lakhs</option>
                    <option value="5-10 Lakhs">5-10 Lakhs</option>
                    <option value="More than 10 Lakhs">More than 10 Lakhs</option>
                </select>

                <label for="father_occupation">Father's Occupation:</label>
                <select id="father_occupation" name="father_occupation" required>
                    <option value="" disabled selected>Select</option>
                    <option value="Government Employee">Government Employee</option>
                    <option value="Service">Service</option>
                    <option value="Business">Business</option>
                    <option value="Farmer">Farmer</option>
                    <option value="Retired">Retired</option>
                    <option value="Other">Other</option>
                </select>

                <label for="father_phone">Father Contact:</label>
                <input type="tel" id="father_phone" name="father_phone" required>

                <label for="mother_name">Mother Name:</label>
                <input type="text" id="mother_name" name="mother_name" required>

                <label for="mother_income">Mother's Income:</label>
                <select id="mother_income" name="mother_income" required>
                    <option value="" disabled selected>Select</option>
                    <option value="Less than 1 Lakh">Less than 1 Lakh</option>
                    <option value="1-3 Lakhs">1-3 Lakhs</option>
                    <option value="3-5 Lakhs">3-5 Lakhs</option>
                    <option value="5-10 Lakhs">5-10 Lakhs</option>
                    <option value="More than 10 Lakhs">More than 10 Lakhs</option>
                </select>

                <label for="mother_occupation">Mother's Occupation:</label>
                <select id="mother_occupation" name="mother_occupation" required>
                    <option value="" disabled selected>Select</option>
                    <option value="Government Employee">Government Employee</option>
                    <option value="Private Employee">Private Employee</option>
                    <option value="Business">Business</option>
                    <option value="Homemaker">Homemaker</option>
                    <option value="Retired">Retired</option>
                    <option value="Other">Other</option>
                </select>

                <label for="mother_phone">Mother Contact:</label>
                <input type="tel" id="mother_phone" name="mother_phone" required>

                <label for="guardian_address">Local Guardian's Address:</label>
                <textarea id="guardian_address" name="guardian_address" required></textarea>

                <label for="guardian_relation">Relation with Local Guardian:</label>
                <select id="guardian_relation" name="guardian_relation" required>
                    <option value="" disabled selected>Select</option>
                    <option value="Father">Father</option>
                    <option value="Mother">Mother</option>
                    <option value="Uncle">Uncle</option>
                    <option value="Aunt">Aunt</option>
                    <option value="Brother">Brother</option>
                    <option value="Sister">Sister</option>
                    <option value="Other">Other</option>
                </select>

                <label for="guardian_contact">Local Guardian's Contact Number:</label>
                <input type="tel" id="guardian_contact" name="guardian_contact" required>

                <h3 class="section-title">S.S.C Details</h3>
                <label for="ssc_passing">Month/Year of Passing:</label>
                <input type="text" id="ssc_passing" name="ssc_passing" placeholder="MM/YYYY" required>

                <label for="ssc_board">Board:</label>
                <input type="text" id="ssc_board" name="ssc_board" required>

                <label for="ssc_school">School Name:</label>
                <input type="text" id="ssc_school" name="ssc_school" required>

                <label for="ssc_seat_no">Seat No:</label>
                <input type="text" id="ssc_seat_no" name="ssc_seat_no" required>

                <label for="ssc_class_div">Class/Division:</label>
                <input type="text" id="ssc_class_div" name="ssc_class_div" required>

                <label for="ssc_total_marks">Total Marks:</label>
                <input type="number" id="ssc_total_marks" name="ssc_total_marks" required>

                <label for="ssc_obtained_marks">Obtained Marks:</label>
                <input type="number" id="ssc_obtained_marks" name="ssc_obtained_marks" required>

                <label for="ssc_percentage">Percentage:</label>
                <input type="text" id="ssc_percentage" name="ssc_percentage" required>

                <?php if ($course['level'] != 'Junior'): ?>
                <h3 class="section-title">H.S.C Details</h3>
                <label for="hsc_passing">Month/Year of Passing:</label>
                <input type="text" id="hsc_passing" name="hsc_passing" placeholder="MM/YYYY" required>

                <label for="hsc_board">Board:</label>
                <input type="text" id="hsc_board" name="hsc_board" required>

                <label for="hsc_stream">Stream:</label>
                <input type="text" id="hsc_stream" name="hsc_stream" required>

                <label for="hsc_school">School Name:</label>
                <input type="text" id="hsc_school" name="hsc_school" required>

                <label for="hsc_seat_no">Seat No:</label>
                <input type="text" id="hsc_seat_no" name="hsc_seat_no" required>

                <label for="hsc_class_div">Class/Division:</label>
                <input type="text" id="hsc_class_div" name="hsc_class_div" required>

                <label for="hsc_total_marks">Total Marks:</label>
                <input type="number" id="hsc_total_marks" name="hsc_total_marks" required>

                <label for="hsc_obtained_marks">Obtained Marks:</label>
                <input type="number" id="hsc_obtained_marks" name="hsc_obtained_marks" required>

                <label for="hsc_percentage">Percentage:</label>
                <input type="text" id="hsc_percentage" name="hsc_percentage" required>
                <?php endif; ?>

                <h3 class="section-title">Course Confirmation</h3>
                <label>Selected Course:</label>
                <input type="text" value="<?php echo htmlspecialchars($course['course_name']); ?>" readonly>

                <label for="academic_year">Academic Year:</label>
                <select id="academic_year" name="academic_year" required>
                    <option value="" disabled selected>Select Academic Year</option>
                    <?php
                    $current_year = date('Y');
                    $next_year = $current_year + 1;
                    $year_range = ($course['level'] == 'Junior') ? 5 : 3;
                    for ($i = 0; $i < $year_range; $i++) {
                        $year = ($current_year + $i) . '-' . ($next_year + $i);
                        echo "<option value='$year'>$year</option>";
                    }
                    ?>
                </select>

                <?php if ($course['level'] == 'Senior'): ?>
                <label for="selected_class">Select Class/Year:</label>
                <select id="selected_class" name="selected_class" required onchange="togglePreviousAcademicDetails()">
                    <option value="" disabled selected>Select Class</option>
                    <?php
                    $duration = (int)preg_replace('/[^0-9]/', '', $course['duration']);
                    if ($duration <= 2) {
                        echo '<option value="First Year Masters">First Year Masters</option>';
                        if ($duration >= 2) {
                            echo '<option value="Second Year Masters">Second Year Masters</option>';
                        }
                    } else {
                        $class_labels = [
                            1 => 'First Year',
                            2 => 'Second Year',
                            3 => 'Third Year',
                            4 => 'Fourth Year'
                        ];
                        for ($year = 1; $year <= $duration; $year++) {
                            echo "<option value='{$class_labels[$year]}'>{$class_labels[$year]}</option>";
                        }
                    }
                    ?>
                </select>
                <?php endif; ?>

                <div id="previous_academic_details" style="display: none;">
                    <h3 class="section-title">Previous Academic Details</h3>
                    <label for="previous_course_name">Course Name:</label>
                    <input type="text" id="previous_course_name" name="previous_course_name">

                    <label for="previous_institute_name">Institute Name:</label>
                    <input type="text" id="previous_institute_name" name="previous_institute_name">

                    <label for="previous_marks">Marks:</label>
                    <input type="number" id="previous_marks" name="previous_marks">

                    <label for="previous_marksheet">Upload Marksheet:</label>
                    <input type="file" id="previous_marksheet" name="previous_marksheet" accept=".pdf, .jpg, .jpeg, .png">

                    <?php if ($course['level'] == 'Senior' && preg_replace('/[^0-9]/', '', $course['duration']) == 2): ?>
                    <h3 class="section-title">Bachelor's Degree Details</h3>
                    <label for="previous_passing_year">Year of Passing:</label>
                    <input type="text" id="previous_passing_year" name="previous_passing_year" placeholder="YYYY" required>

                    <label for="previous_enrollment_no">Enrollment Number:</label>
                    <input type="text" id="previous_enrollment_no" name="previous_enrollment_no" required>

                    <label for="previous_cgpa">CGPA/Percentage:</label>
                    <input type="text" id="previous_cgpa" name="previous_cgpa" required>

                    <label for="previous_class_division">Class/Division:</label>
                    <select id="previous_class_division" name="previous_class_division" required>
                        <option value="" disabled selected>Select</option>
                        <option value="Distinction">Distinction</option>
                        <option value="First Class">First Class</option>
                        <option value="Second Class">Second Class</option>
                        <option value="Pass">Pass</option>
                    </select>

                    <label for="previous_marksheet_no">Marksheet Number:</label>
                    <input type="text" id="previous_marksheet_no" name="previous_marksheet_no" required>

                    <label for="previous_mode_of_study">Mode of Study:</label>
                    <select id="previous_mode_of_study" name="previous_mode_of_study" required>
                        <option value="" disabled selected>Select</option>
                        <option value="Regular">Regular</option>
                        <option value="Distance">Distance</option>
                    </select>

                    <label for="previous_degree_certificate">Degree Certificate:</label>
                    <input type="file" id="previous_degree_certificate" name="previous_degree_certificate" accept=".pdf, .jpg, .jpeg, .png">
                    <?php endif; ?>
                </div>

                <h3 class="section-title">Documents Upload</h3>
                <label for="ssc_marksheet">S.S.C Marksheet:</label>
                <input type="file" id="ssc_marksheet" name="ssc_marksheet" accept=".pdf, .jpg, .jpeg, .png" required>

                <label for="ssc_certificate">S.S.C Certificate:</label>
                <input type="file" id="ssc_certificate" name="ssc_certificate" accept=".pdf, .jpg, .jpeg, .png" required>

                <?php if ($course['level'] != 'Junior'): ?>
                <label for="hsc_marksheet">H.S.C Marksheet:</label>
                <input type="file" id="hsc_marksheet" name="hsc_marksheet" accept=".pdf, .jpg, .jpeg, .png" required>
                <?php endif; ?>

                <label for="caste_certificate">Caste Certificate:</label>
                <input type="file" id="caste_certificate" name="caste_certificate" accept=".pdf, .jpg, .jpeg, .png" required>

                <label for="aadhar_card">Aadhar Card:</label>
                <input type="file" id="aadhar_card" name="aadhar_card" accept=".pdf, .jpg, .jpeg, .png" required>

                <label for="applicant_photo">Applicant Photo:</label>
                <input type="file" id="applicant_photo" name="applicant_photo" accept=".jpg, .jpeg, .png" required>

                <label for="transfer_certificate">Transfer Certificate:</label>
                <input type="file" id="transfer_certificate" name="transfer_certificate" accept=".pdf, .jpg, .jpeg, .png" required>

                <h3 class="section-title">Declaration</h3>
                <div class="declaration">
                    <p>I hereby declare that the information provided above is true and correct to the best of my knowledge.</p>
                    <input type="checkbox" id="declaration" name="declaration" required>
                    <label for="declaration">I agree to the terms and conditions</label>
                </div>

                <button type="submit">Submit Application</button>
            </form>
        </div>
    </div>

    <script>
    function togglePreviousAcademicDetails() {
        const selectedClass = document.getElementById('selected_class')?.value;
        const previousAcademicDetails = document.getElementById('previous_academic_details');
        const courseLevel = '<?php echo $course['level']; ?>';
        const courseDuration = '<?php echo preg_replace('/[^0-9]/', '', $course['duration']); ?>';

        if (selectedClass === 'Second Year' || 
            selectedClass === 'Third Year' || 
            (courseLevel === 'Senior' && courseDuration === '2')) {
            previousAcademicDetails.style.display = 'block';
        } else {
            previousAcademicDetails.style.display = 'none';
        }
    }
    </script>
</body>
</html>