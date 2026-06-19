<?php
session_start();
include 'db.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['userid'];

// Fetch user application
$query = "SELECT * FROM admissions WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();

// If no application, redirect to admission form
if (!$application) {
    header("Location: admission.php");
    exit();
}

// Fetch user details
$user_query = "SELECT full_name FROM users_register WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$fullname = $user['full_name'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect all form data
    $academic_year = $_POST['academic_year'];
    $selected_class = $_POST['selected_class'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $permanent_address = $_POST['permanent_address'];
    $correspondence_address = $_POST['correspondence_address'];
    $nationality = $_POST['nationality'];
    $area = $_POST['area'];
    $marital_status = $_POST['marital_status'];
    $category = $_POST['category'];
    $religion = $_POST['religion'];
    $caste = $_POST['caste'];
    $sub_caste = $_POST['sub_caste'];
    $state = $_POST['state'];
    $district = $_POST['district'];
    $taluka = $_POST['taluka'];
    $city = $_POST['city'];
    $pincode = $_POST['pincode'];
    $home_number = $_POST['home_number'];
    $domicile = $_POST['domicile'];
    $language = $_POST['language'];
    $blood_group = $_POST['blood_group'];
    $physically_handicapped = $_POST['physically_handicapped'];
    $medium = $_POST['medium'];
    $last_institute = $_POST['last_institute'];
    $abc_id = $_POST['abc_id'];
    $father_name = $_POST['father_name'];
    $father_income = $_POST['father_income'];
    $father_occupation = $_POST['father_occupation'];
    $father_phone = $_POST['father_phone'];
    $mother_name = $_POST['mother_name'];
    $mother_income = $_POST['mother_income'];
    $mother_occupation = $_POST['mother_occupation'];
    $mother_phone = $_POST['mother_phone'];
    $guardian_address = $_POST['guardian_address'];
    $guardian_relation = $_POST['guardian_relation'];
    $guardian_contact = $_POST['guardian_contact'];
    $ssc_passing = $_POST['ssc_passing'];
    $ssc_board = $_POST['ssc_board'];
    $ssc_school = $_POST['ssc_school'];
    $ssc_seat_no = $_POST['ssc_seat_no'];
    $ssc_class_div = $_POST['ssc_class_div'];
    $ssc_total_marks = $_POST['ssc_total_marks'];
    $ssc_obtained_marks = $_POST['ssc_obtained_marks'];
    $ssc_percentage = $_POST['ssc_percentage'];
    $hsc_passing = $_POST['hsc_passing'];
    $hsc_board = $_POST['hsc_board'];
    $hsc_stream = $_POST['hsc_stream'];
    $hsc_school = $_POST['hsc_school'];
    $hsc_seat_no = $_POST['hsc_seat_no'];
    $hsc_class_div = $_POST['hsc_class_div'];
    $hsc_total_marks = $_POST['hsc_total_marks'];
    $hsc_obtained_marks = $_POST['hsc_obtained_marks'];
    $hsc_percentage = $_POST['hsc_percentage'];

    // Update query
    $update_query = "UPDATE admissions SET 
        academic_year = ?, 
        selected_class = ?, 
        full_name = ?, 
        email = ?, 
        phone = ?, 
        permanent_address = ?, 
        correspondence_address = ?, 
        nationality = ?, 
        area = ?, 
        marital_status = ?, 
        category = ?, 
        religion = ?, 
        caste = ?, 
        sub_caste = ?, 
        state = ?, 
        district = ?, 
        taluka = ?, 
        city = ?, 
        pincode = ?, 
        home_number = ?, 
        domicile = ?, 
        language = ?, 
        blood_group = ?, 
        physically_handicapped = ?, 
        medium = ?, 
        last_institute = ?, 
        abc_id = ?, 
        father_name = ?, 
        father_income = ?, 
        father_occupation = ?, 
        father_phone = ?, 
        mother_name = ?, 
        mother_income = ?, 
        mother_occupation = ?, 
        mother_phone = ?, 
        guardian_address = ?, 
        guardian_relation = ?, 
        guardian_contact = ?, 
        ssc_passing = ?, 
        ssc_board = ?, 
        ssc_school = ?, 
        ssc_seat_no = ?, 
        ssc_class_div = ?, 
        ssc_total_marks = ?, 
        ssc_obtained_marks = ?, 
        ssc_percentage = ?, 
        hsc_passing = ?, 
        hsc_board = ?, 
        hsc_stream = ?, 
        hsc_school = ?, 
        hsc_seat_no = ?, 
        hsc_class_div = ?, 
        hsc_total_marks = ?, 
        hsc_obtained_marks = ?, 
        hsc_percentage = ? 
        WHERE user_id = ?";

    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssssssssssssssssssssssssssssssssssssssssssssssssssssssssi", 
        $academic_year, $selected_class, $full_name, $email, $phone, $permanent_address, $correspondence_address, 
        $nationality, $area, $marital_status, $category, $religion, $caste, $sub_caste, $state, $district, 
        $taluka, $city, $pincode, $home_number, $domicile, $language, $blood_group, $physically_handicapped, 
        $medium, $last_institute, $abc_id, $father_name, $father_income, $father_occupation, $father_phone, 
        $mother_name, $mother_income, $mother_occupation, $mother_phone, $guardian_address, $guardian_relation, 
        $guardian_contact, $ssc_passing, $ssc_board, $ssc_school, $ssc_seat_no, $ssc_class_div, $ssc_total_marks, 
        $ssc_obtained_marks, $ssc_percentage, $hsc_passing, $hsc_board, $hsc_stream, $hsc_school, $hsc_seat_no, 
        $hsc_class_div, $hsc_total_marks, $hsc_obtained_marks, $hsc_percentage, $user_id);

    if ($update_stmt->execute()) {
        header("Location: dashboard.php?success=Application Updated");
        exit();
    } else {
        echo "Error updating application.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Application</title>
    <style>
/* General Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

/* Navigation Bar (Same theme as admission_form.php) */
nav {
    background-color: #2c3e50;
    color: #fff;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

nav h3 {
    margin: 0;
    font-size: 1.2rem;
}

nav .logout-btn {
    background: #e74c3c;
    color: #fff;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

nav .logout-btn:hover {
    background: #c0392b;
}

/* Update Form Container */
.container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

/* Form Styles */
h2 {
    text-align: center;
    color: #2c3e50;
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

.section-title {
    margin-top: 2rem;
    color: #2c3e50;
    border-bottom: 2px solid #3498db;
    padding-bottom: 0.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    nav {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .container {
        padding: 1rem;
    }
}
        </style>
</head>
<body>
<div class="user-info">
<nav>
    <h3><?php echo htmlspecialchars($fullname); ?></h3>
    <form action="logout.php" method="POST">
        <button type="submit" class="logout-btn">Logout</button>
    </form>
</nav>

        <!-- Update Application Form -->
        <div class="container">
        <h2>Update Application</h2>
        <form method="POST" enctype="multipart/form-data">
                <!-- Personal Details -->
                <h3 class="section-title">Personal Details</h3>
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($application['full_name']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($application['email']); ?>" required>

                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($application['phone']); ?>" required>

                <label for="permanent_address">Permanent Address:</label>
                <textarea id="permanent_address" name="permanent_address" rows="4" required><?php echo htmlspecialchars($application['permanent_address']); ?></textarea>

                <label for="correspondence_address">Correspondence Address:</label>
                <textarea id="correspondence_address" name="correspondence_address" rows="4" required><?php echo htmlspecialchars($application['correspondence_address']); ?></textarea>

                <label for="area">Area:</label>
<select id="area" name="area" required>
    <option value="" disabled selected>Select Area</option>
    <option value="Urban" <?php if ($application['area'] == 'Urban') echo 'selected'; ?>>Urban</option>
    <option value="Rural" <?php if ($application['area'] == 'Rural') echo 'selected'; ?>>Rural</option>
    <option value="Semi-Urban" <?php if ($application['area'] == 'Semi-Urban') echo 'selected'; ?>>Semi-Urban</option>
</select>

<label for="marital_status">Marital Status:</label>
<select id="marital_status" name="marital_status" required>
    <option value="" disabled selected>Select</option>
    <option value="Single" <?php if ($application['marital_status'] == 'Single') echo 'selected'; ?>>Single</option>
    <option value="Married" <?php if ($application['marital_status'] == 'Married') echo 'selected'; ?>>Married</option>
    <option value="Divorced" <?php if ($application['marital_status'] == 'Divorced') echo 'selected'; ?>>Divorced</option>
    <option value="Widowed" <?php if ($application['marital_status'] == 'Widowed') echo 'selected'; ?>>Widowed</option>
</select>

<label for="category">Category:</label>
<select id="category" name="category" required>
    <option value="" disabled selected>Select</option>
    <option value="General" <?php if ($application['category'] == 'General') echo 'selected'; ?>>General</option>
    <option value="OBC" <?php if ($application['category'] == 'OBC') echo 'selected'; ?>>OBC</option>
    <option value="SC" <?php if ($application['category'] == 'SC') echo 'selected'; ?>>SC</option>
    <option value="ST" <?php if ($application['category'] == 'ST') echo 'selected'; ?>>ST</option>
    <option value="EWS" <?php if ($application['category'] == 'EWS') echo 'selected'; ?>>EWS</option>
</select>

<label for="religion">Religion:</label>
<select id="religion" name="religion" required>
    <option value="" disabled selected>Select</option>
    <option value="Hindu" <?php if ($application['religion'] == 'Hindu') echo 'selected'; ?>>Hindu</option>
    <option value="Muslim" <?php if ($application['religion'] == 'Muslim') echo 'selected'; ?>>Muslim</option>
    <option value="Christian" <?php if ($application['religion'] == 'Christian') echo 'selected'; ?>>Christian</option>
    <option value="Sikh" <?php if ($application['religion'] == 'Sikh') echo 'selected'; ?>>Sikh</option>
    <option value="Buddhist" <?php if ($application['religion'] == 'Buddhist') echo 'selected'; ?>>Buddhist</option>
    <option value="Jain" <?php if ($application['religion'] == 'Jain') echo 'selected'; ?>>Jain</option>
    <option value="Other" <?php if ($application['religion'] == 'Other') echo 'selected'; ?>>Other</option>
</select>

<label for="caste">Caste:</label>
<input type="text" id="caste" name="caste" value="<?php echo htmlspecialchars($application['caste']); ?>" required>

<label for="sub_caste">Sub-Caste:</label>
<input type="text" id="sub_caste" name="sub_caste" value="<?php echo htmlspecialchars($application['sub_caste']); ?>" required>

<label for="pincode">Pincode:</label>
<input type="text" id="pincode" name="pincode" value="<?php echo htmlspecialchars($application['pincode']); ?>" required>

<label for="home_number">Home Number:</label>
<input type="tel" id="home_number" name="home_number" value="<?php echo htmlspecialchars($application['home_number']); ?>" required>

<label for="domicile">Domicile:</label>
<input type="text" id="domicile" name="domicile" value="<?php echo htmlspecialchars($application['domicile']); ?>" required>

<label for="language">Mother Tongue:</label>
<input type="text" id="language" name="language" value="<?php echo htmlspecialchars($application['language']); ?>" required>

<label for="blood_group">Blood Group:</label>
<select id="blood_group" name="blood_group" required>
    <option value="" disabled selected>Select</option>
    <option value="A+" <?php if ($application['blood_group'] == 'A+') echo 'selected'; ?>>A+</option>
    <option value="A-" <?php if ($application['blood_group'] == 'A-') echo 'selected'; ?>>A-</option>
    <option value="B+" <?php if ($application['blood_group'] == 'B+') echo 'selected'; ?>>B+</option>
    <option value="B-" <?php if ($application['blood_group'] == 'B-') echo 'selected'; ?>>B-</option>
    <option value="O+" <?php if ($application['blood_group'] == 'O+') echo 'selected'; ?>>O+</option>
    <option value="O-" <?php if ($application['blood_group'] == 'O-') echo 'selected'; ?>>O-</option>
    <option value="AB+" <?php if ($application['blood_group'] == 'AB+') echo 'selected'; ?>>AB+</option>
    <option value="AB-" <?php if ($application['blood_group'] == 'AB-') echo 'selected'; ?>>AB-</option>
</select>

<label for="physically_handicapped">Physically Handicapped:</label>
<select id="physically_handicapped" name="physically_handicapped" required>
    <option value="" disabled selected>Select</option>
    <option value="Yes" <?php if ($application['physically_handicapped'] == 'Yes') echo 'selected'; ?>>Yes</option>
    <option value="No" <?php if ($application['physically_handicapped'] == 'No') echo 'selected'; ?>>No</option>
</select>

<label for="medium">Medium:</label>
<select id="medium" name="medium" required>
    <option value="" disabled selected>Select</option>
    <option value="Marathi" <?php if ($application['medium'] == 'Marathi') echo 'selected'; ?>>Marathi</option>
    <option value="English" <?php if ($application['medium'] == 'English') echo 'selected'; ?>>English</option>
    <option value="Hindi" <?php if ($application['medium'] == 'Hindi') echo 'selected'; ?>>Hindi</option>
</select>

<label for="last_institute">Institute Last Attended:</label>
<input type="text" id="last_institute" name="last_institute" value="<?php echo htmlspecialchars($application['last_institute']); ?>" required>

<label for="abc_id">ABC ID:</label>
<input type="text" id="abc_id" name="abc_id" value="<?php echo htmlspecialchars($application['abc_id']); ?>">

            <!-- Parents Details -->
            <h3 class="section-title">Parents Details</h3>
            <label for="father_name">Father Name:</label>
            <input type="text" id="father_name" name="father_name" value="<?php echo htmlspecialchars($application['father_name'] ?? ''); ?>" required>

            <label for="father_income">Father's Income:</label>
            <select id="father_income" name="father_income" required>
                <option value="" disabled>Select</option>
                <?php 
                $incomes = ["Less than 1 Lakh", "1-3 Lakhs", "3-5 Lakhs", "5-10 Lakhs", "More than 10 Lakhs"];
                foreach ($incomes as $income) {
                    $selected = ($application['father_income'] ?? '') === $income ? 'selected' : '';
                    echo "<option value='$income' $selected>$income</option>";
                }
                ?>
            </select>

            <label for="father_occupation">Father's Occupation:</label>
            <select id="father_occupation" name="father_occupation" required>
                <option value="" disabled>Select</option>
                <?php 
                $occupations = ["Government Employee", "Service", "Business", "Farmer", "Retired", "Other"];
                foreach ($occupations as $occupation) {
                    $selected = ($application['father_occupation'] ?? '') === $occupation ? 'selected' : '';
                    echo "<option value='$occupation' $selected>$occupation</option>";
                }
                ?>
            </select>

            <label for="father_phone">Father Contact:</label>
            <input type="tel" id="father_phone" name="father_phone" value="<?php echo htmlspecialchars($application['father_phone'] ?? ''); ?>" required>

            <label for="mother_name">Mother Name:</label>
            <input type="text" id="mother_name" name="mother_name" value="<?php echo htmlspecialchars($application['mother_name'] ?? ''); ?>" required>

            <label for="mother_income">Mother's Income:</label>
            <select id="mother_income" name="mother_income" required>
                <option value="" disabled>Select</option>
                <?php 
                foreach ($incomes as $income) {
                    $selected = ($application['mother_income'] ?? '') === $income ? 'selected' : '';
                    echo "<option value='$income' $selected>$income</option>";
                }
                ?>
            </select>

            <label for="mother_occupation">Mother's Occupation:</label>
            <select id="mother_occupation" name="mother_occupation" required>
                <option value="" disabled>Select</option>
                <?php 
                $mother_occupations = ["Government Employee", "Private Employee", "Business", "Homemaker", "Retired", "Other"];
                foreach ($mother_occupations as $occupation) {
                    $selected = ($application['mother_occupation'] ?? '') === $occupation ? 'selected' : '';
                    echo "<option value='$occupation' $selected>$occupation</option>";
                }
                ?>
            </select>

            <label for="mother_phone">Mother Contact:</label>
            <input type="tel" id="mother_phone" name="mother_phone" value="<?php echo htmlspecialchars($application['mother_phone'] ?? ''); ?>" required>

            <label for="guardian_address">Local Guardian's Address:</label>
            <textarea id="guardian_address" name="guardian_address" required><?php echo htmlspecialchars($application['guardian_address'] ?? ''); ?></textarea>

            <label for="guardian_relation">Relation with Local Guardian:</label>
            <select id="guardian_relation" name="guardian_relation" required>
                <option value="" disabled>Select</option>
                <?php 
                $relations = ["Father", "Mother", "Uncle", "Aunt", "Brother", "Sister", "Other"];
                foreach ($relations as $relation) {
                    $selected = ($application['guardian_relation'] ?? '') === $relation ? 'selected' : '';
                    echo "<option value='$relation' $selected>$relation</option>";
                }
                ?>
            </select>

            <label for="guardian_contact">Local Guardian's Contact Number:</label>
            <input type="tel" id="guardian_contact" name="guardian_contact" pattern="[0-9]{10}" value="<?php echo htmlspecialchars($application['guardian_contact'] ?? ''); ?>" required>

            <h3 class="section-title">S.S.C Details</h3>
<label for="ssc_passing">Month/Year of Passing:</label>
<input type="text" id="ssc_passing" name="ssc_passing" placeholder="MM/YYYY" value="<?php echo htmlspecialchars($application['ssc_passing'] ?? ''); ?>" required>

<label for="ssc_board">Board:</label>
<input type="text" id="ssc_board" name="ssc_board" value="<?php echo htmlspecialchars($application['ssc_board'] ?? ''); ?>" required>

<label for="ssc_school">School Name:</label>
<input type="text" id="ssc_school" name="ssc_school" value="<?php echo htmlspecialchars($application['ssc_school'] ?? ''); ?>" required>

<label for="ssc_seat_no">Seat No:</label>
<input type="text" id="ssc_seat_no" name="ssc_seat_no" value="<?php echo htmlspecialchars($application['ssc_seat_no'] ?? ''); ?>" required>

<label for="ssc_class_div">Class/Division:</label>
<input type="text" id="ssc_class_div" name="ssc_class_div" value="<?php echo htmlspecialchars($application['ssc_class_div'] ?? ''); ?>" required>

<label for="ssc_total_marks">Total Marks:</label>
<input type="number" id="ssc_total_marks" name="ssc_total_marks" value="<?php echo htmlspecialchars($application['ssc_total_marks'] ?? ''); ?>" required>

<label for="ssc_obtained_marks">Obtained Marks:</label>
<input type="number" id="ssc_obtained_marks" name="ssc_obtained_marks" value="<?php echo htmlspecialchars($application['ssc_obtained_marks'] ?? ''); ?>" required>

<label for="ssc_percentage">Percentage:</label>
<input type="text" id="ssc_percentage" name="ssc_percentage" value="<?php echo htmlspecialchars($application['ssc_percentage'] ?? ''); ?>" required>

<h3 class="section-title">H.S.C Details</h3>
<label for="hsc_passing">Month/Year of Passing:</label>
<input type="text" id="hsc_passing" name="hsc_passing" placeholder="MM/YYYY" value="<?php echo htmlspecialchars($application['hsc_passing'] ?? ''); ?>" required>

<label for="hsc_board">Board:</label>
<input type="text" id="hsc_board" name="hsc_board" value="<?php echo htmlspecialchars($application['hsc_board'] ?? ''); ?>" required>

<label for="hsc_stream">Stream:</label>
<input type="text" id="hsc_stream" name="hsc_stream" placeholder="Science/Commerce/Arts" value="<?php echo htmlspecialchars($application['hsc_stream'] ?? ''); ?>" required>

<label for="hsc_school">School Name:</label>
<input type="text" id="hsc_school" name="hsc_school" value="<?php echo htmlspecialchars($application['hsc_school'] ?? ''); ?>" required>

<label for="hsc_seat_no">Seat No:</label>
<input type="text" id="hsc_seat_no" name="hsc_seat_no" value="<?php echo htmlspecialchars($application['hsc_seat_no'] ?? ''); ?>" required>

<label for="hsc_class_div">Class/Division:</label>
<input type="text" id="hsc_class_div" name="hsc_class_div" value="<?php echo htmlspecialchars($application['hsc_class_div'] ?? ''); ?>" required>

<label for="hsc_total_marks">Total Marks:</label>
<input type="number" id="hsc_total_marks" name="hsc_total_marks" value="<?php echo htmlspecialchars($application['hsc_total_marks'] ?? ''); ?>" required>

<label for="hsc_obtained_marks">Obtained Marks:</label>
<input type="number" id="hsc_obtained_marks" name="hsc_obtained_marks" value="<?php echo htmlspecialchars($application['hsc_obtained_marks'] ?? ''); ?>" required>

<label for="hsc_percentage">Percentage:</label>
<input type="text" id="hsc_percentage" name="hsc_percentage" value="<?php echo htmlspecialchars($application['hsc_percentage'] ?? ''); ?>" required>

<h3 class="section-title">Documents Upload</h3>
<label for="ssc_marksheet">S.S.C Marksheet (PDF/JPG/PNG):</label>
<input type="file" id="ssc_marksheet" name="ssc_marksheet" accept=".pdf, .jpg, .jpeg, .png" value="<?php echo htmlspecialchars($application['ssc_marksheet'] ?? ''); ?>" required>

<label for="ssc_certificate">S.S.C Certificate (PDF/JPG/PNG):</label>
<input type="file" id="ssc_certificate" name="ssc_certificate" accept=".pdf, .jpg, .jpeg, .png" value="<?php echo htmlspecialchars($application['ssc_certificate'] ?? ''); ?>" required>

<label for="hsc_marksheet">H.S.C Marksheet (PDF/JPG/PNG):</label>
<input type="file" id="hsc_marksheet" name="hsc_marksheet" accept=".pdf, .jpg, .jpeg, .png" value="<?php echo htmlspecialchars($application['hsc_marksheet'] ?? ''); ?>" required>

<label for="caste_certificate">Caste Certificate (PDF/JPG/PNG):</label>
<input type="file" id="caste_certificate" name="caste_certificate" accept=".pdf, .jpg, .jpeg, .png" value="<?php echo htmlspecialchars($application['caste_certificate'] ?? ''); ?>" required>

<label for="aadhar_card">Aadhar Card (PDF/JPG/PNG):</label>
<input type="file" id="aadhar_card" name="aadhar_card" accept=".pdf, .jpg, .jpeg, .png" value="<?php echo htmlspecialchars($application['aadhar_card'] ?? ''); ?>" required>


                <!-- Submit Button -->
                <button type="submit">Update Application</button>
            </form>
        </div>
    </div>
</body>
</html>