<?php
// view_application.php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Fetch all applications for the current user
$query = "SELECT a.*, c.course_name 
          FROM admissions a 
          LEFT JOIN courses c ON a.course_id = c.course_id 
          WHERE a.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['userid']);
$stmt->execute();
$result = $stmt->get_result();
$applications = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #fff;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18pt;
        }
        .header p {
            margin: 5px 0;
            font-size: 12pt;
        }
        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0;
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 12pt;
        }
        th {
            background-color: #f0f0f0;
            text-align: left;
        }
        .documents {
            margin: 20px 0;
        }
        .documents table {
            width: 50%;
        }
        .declaration {
            margin: 20px 0;
            font-size: 12pt;
        }
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            width: 80%;
        }
        .signature div {
            width: 45%;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            margin-top: 20px;
        }
        @media print {
            body {
                margin: 0;
            }
            .container {
                border: none;
                box-shadow: none;
            }
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <?php if (empty($applications)): ?>
        <div class="container">
            <p>No applications found.</p>
        </div>
    <?php else: ?>
        <?php foreach ($applications as $application): ?>
            <div class="container">
            <div class="header">
            <img src="./asset/img/NextGenLogobg.png" alt="College Logo" style="width: 240px; height: 200px; display: block; margin: 0 auto 10px;">
            <h1>Savitribai Phule Pune University</h1>
            <h1>NextGen College</h1>
            <p>Plot No. 42, Innovator Park,Sector 21, Cyber City, Pune – 411057, Maharashtra, India</p>
            <p>Ph.no. +91 98230 45678 / 020 6678 8899 Email ID: info@nextgencollege.edu.in</p>
            <p>NGU Reg. No. NGU/MU/ESC:042(2020) | AISHE: C-98765 | Jr Col. Code: 22-10-008</p>
            <p>Website: http://www.nextgencollege.edu.in/<
</div>

                <div class="title">
                    Application Form (<?php echo htmlspecialchars($application['academic_year']); ?>)
                </div>

                <table>
                    <tr><th>Course Name</th><td><?php echo htmlspecialchars($application['course_name']); ?></td></tr>
                    <tr><th>Full Name</th><td><?php echo htmlspecialchars($application['full_name']); ?></td></tr>
                    <tr><th>Email</th><td><?php echo htmlspecialchars($application['email']); ?></td></tr>
                    <tr><th>Phone</th><td><?php echo htmlspecialchars($application['phone']); ?></td></tr>
                    <tr><th>Permanent Address</th><td><?php echo htmlspecialchars($application['permanent_address']); ?></td></tr>
                    <tr><th>Correspondence Address</th><td><?php echo htmlspecialchars($application['correspondence_address']); ?></td></tr>
                    <tr><th>Nationality</th><td><?php echo htmlspecialchars($application['nationality']); ?></td></tr>
                    <tr><th>Area</th><td><?php echo htmlspecialchars($application['area']); ?></td></tr>
                    <tr><th>Marital Status</th><td><?php echo htmlspecialchars($application['marital_status']); ?></td></tr>
                    <tr><th>Category</th><td><?php echo htmlspecialchars($application['category']); ?></td></tr>
                    <tr><th>Religion</th><td><?php echo htmlspecialchars($application['religion']); ?></td></tr>
                    <tr><th>Caste</th><td><?php echo htmlspecialchars($application['caste']); ?></td></tr>
                    <tr><th>Sub-Caste</th><td><?php echo htmlspecialchars($application['sub_caste']); ?></td></tr>
                    <tr><th>State</th><td><?php echo htmlspecialchars($application['state']); ?></td></tr>
                    <tr><th>District</th><td><?php echo htmlspecialchars($application['district']); ?></td></tr>
                    <tr><th>Taluka</th><td><?php echo htmlspecialchars($application['taluka']); ?></td></tr>
                    <tr><th>City</th><td><?php echo htmlspecialchars($application['city']); ?></td></tr>
                    <tr><th>Pincode</th><td><?php echo htmlspecialchars($application['pincode']); ?></td></tr>
                    <tr><th>Home Number</th><td><?php echo htmlspecialchars($application['home_number']); ?></td></tr>
                    <tr><th>Domicile</th><td><?php echo htmlspecialchars($application['domicile']); ?></td></tr>
                    <tr><th>Mother Tongue</th><td><?php echo htmlspecialchars($application['language']); ?></td></tr>
                    <tr><th>Blood Group</th><td><?php echo htmlspecialchars($application['blood_group']); ?></td></tr>
                    <tr><th>Physically Handicapped</th><td><?php echo htmlspecialchars($application['physically_handicapped']); ?></td></tr>
                    <tr><th>Medium</th><td><?php echo htmlspecialchars($application['medium']); ?></td></tr>
                    <tr><th>Last Institute</th><td><?php echo htmlspecialchars($application['last_institute']); ?></td></tr>
                    <tr><th>ABC ID</th><td><?php echo htmlspecialchars($application['abc_id'] ?? 'N/A'); ?></td></tr>
                    <tr><th>Father Name</th><td><?php echo htmlspecialchars($application['father_name']); ?></td></tr>
                    <tr><th>Father's Income</th><td><?php echo htmlspecialchars($application['father_income']); ?></td></tr>
                    <tr><th>Father's Occupation</th><td><?php echo htmlspecialchars($application['father_occupation']); ?></td></tr>
                    <tr><th>Father Contact</th><td><?php echo htmlspecialchars($application['father_phone']); ?></td></tr>
                    <tr><th>Mother Name</th><td><?php echo htmlspecialchars($application['mother_name']); ?></td></tr>
                    <tr><th>Mother's Income</th><td><?php echo htmlspecialchars($application['mother_income']); ?></td></tr>
                    <tr><th>Mother's Occupation</th><td><?php echo htmlspecialchars($application['mother_occupation']); ?></td></tr>
                    <tr><th>Mother Contact</th><td><?php echo htmlspecialchars($application['mother_phone']); ?></td></tr>
                    <tr><th>Guardian Address</th><td><?php echo htmlspecialchars($application['guardian_address']); ?></td></tr>
                    <tr><th>Guardian Relation</th><td><?php echo htmlspecialchars($application['guardian_relation']); ?></td></tr>
                    <tr><th>Guardian Contact</th><td><?php echo htmlspecialchars($application['guardian_contact']); ?></td></tr>
                    <tr><th>SSC Passing</th><td><?php echo htmlspecialchars($application['ssc_passing']); ?></td></tr>
                    <tr><th>SSC Board</th><td><?php echo htmlspecialchars($application['ssc_board']); ?></td></tr>
                    <tr><th>SSC School</th><td><?php echo htmlspecialchars($application['ssc_school']); ?></td></tr>
                    <tr><th>SSC Seat No</th><td><?php echo htmlspecialchars($application['ssc_seat_no']); ?></td></tr>
                    <tr><th>SSC Class/Div</th><td><?php echo htmlspecialchars($application['ssc_class_div']); ?></td></tr>
                    <tr><th>SSC Total Marks</th><td><?php echo htmlspecialchars($application['ssc_total_marks']); ?></td></tr>
                    <tr><th>SSC Obtained Marks</th><td><?php echo htmlspecialchars($application['ssc_obtained_marks']); ?></td></tr>
                    <tr><th>SSC Percentage</th><td><?php echo htmlspecialchars($application['ssc_percentage']); ?></td></tr>
                    <?php if ($application['hsc_board']): ?>
                    <tr><th>HSC Passing</th><td><?php echo htmlspecialchars($application['hsc_passing']); ?></td></tr>
                    <tr><th>HSC Board</th><td><?php echo htmlspecialchars($application['hsc_board']); ?></td></tr>
                    <tr><th>HSC Stream</th><td><?php echo htmlspecialchars($application['hsc_stream']); ?></td></tr>
                    <tr><th>HSC School</th><td><?php echo htmlspecialchars($application['hsc_school']); ?></td></tr>
                    <tr><th>HSC Seat No</th><td><?php echo htmlspecialchars($application['hsc_seat_no']); ?></td></tr>
                    <tr><th>HSC Class/Div</th><td><?php echo htmlspecialchars($application['hsc_class_div']); ?></td></tr>
                    <tr><th>HSC Total Marks</th><td><?php echo htmlspecialchars($application['hsc_total_marks']); ?></td></tr>
                    <tr><th>HSC Obtained Marks</th><td><?php echo htmlspecialchars($application['hsc_obtained_marks']); ?></td></tr>
                    <tr><th>HSC Percentage</th><td><?php echo htmlspecialchars($application['hsc_percentage']); ?></td></tr>
                    <?php endif; ?>
                    <tr><th>Selected Class</th><td><?php echo htmlspecialchars($application['selected_class']); ?></td></tr>
                    <tr><th>Status</th><td><?php echo htmlspecialchars($application['status']); ?></td></tr>
                    <tr><th>Applied At</th><td><?php echo htmlspecialchars($application['applied_at']); ?></td></tr>
                    <?php if ($application['previous_course_name'] || $application['previous_passing_year']): ?>
                    <tr><th>Previous Course</th><td><?php echo htmlspecialchars($application['previous_course_name'] ?? 'N/A'); ?></td></tr>
                    <tr><th>Previous Institute</th><td><?php echo htmlspecialchars($application['previous_institute_name'] ?? 'N/A'); ?></td></tr>
                    <tr><th>Previous Marks</th><td><?php echo htmlspecialchars($application['previous_marks'] ?? 'N/A'); ?></td></tr>
                    <?php if ($application['previous_passing_year']): ?>
                    <tr><th>Year of Passing</th><td><?php echo htmlspecialchars($application['previous_passing_year']); ?></td></tr>
                    <tr><th>Enrollment No</th><td><?php echo htmlspecialchars($application['previous_enrollment_no']); ?></td></tr>
                    <tr><th>CGPA/Percentage</th><td><?php echo htmlspecialchars($application['previous_cgpa']); ?></td></tr>
                    <tr><th>Class/Division</th><td><?php echo htmlspecialchars($application['previous_class_division']); ?></td></tr>
                    <tr><th>Marksheet No</th><td><?php echo htmlspecialchars($application['previous_marksheet_no']); ?></td></tr>
                    <tr><th>Mode of Study</th><td><?php echo htmlspecialchars($application['previous_mode_of_study']); ?></td></tr>
                    <?php endif; ?>
                    <?php endif; ?>
                </table>

                <div class="documents">
                    <strong>Documents Submitted:</strong>
                    <table>
                        <tr><th>Sr. No.</th><th>Document Name</th></tr>
                        <tr><td>1</td><td>SSC Mark Sheet</td></tr>
                        <tr><td>2</td><td>SSC Certificate</td></tr>
                        <?php if ($application['hsc_marksheet']): ?>
                        <tr><td>3</td><td>HSC Mark Sheet</td></tr>
                        <tr><td>4</td><td>Caste Certificate</td></tr>
                        <tr><td>5</td><td>Aadhar Card</td></tr>
                        <?php else: ?>
                        <tr><td>3</td><td>Caste Certificate</td></tr>
                        <tr><td>4</td><td>Aadhar Card</td></tr>
                        <?php endif; ?>
                        <?php if ($application['previous_marksheet_path']): ?>
                        <tr><td><?php echo $application['hsc_marksheet'] ? '6' : '5'; ?></td><td>Previous Mark Sheet</td></tr>
                        <?php endif; ?>
                        <?php if ($application['previous_degree_certificate']): ?>
                        <tr><td><?php echo $application['hsc_marksheet'] ? '7' : '6'; ?></td><td>Degree Certificate</td></tr>
                        <?php endif; ?>
                        <tr><td><?php echo $application['hsc_marksheet'] ? ($application['previous_degree_certificate'] ? '8' : '7') : ($application['previous_degree_certificate'] ? '7' : '6'); ?></td><td>Transfer Certificate</td></tr>
                    </table>
                </div>

                <div class="declaration">
                    <strong>DECLARATION</strong>
                    <p>1. I, the applicant, declare that all information contained on this application for admission is true and complete and no information has been withheld to the best of my knowledge.</p>
                    <p>2. I agree to abide by the rules, regulations and policies of the college.</p>
                </div>

                <div class="signature">
                    <div>
                        <p>Signature of Applicant</p>
                        <div class="signature-line"></div>
                        <p>Date: ______</p>
                    </div>
                    <div>
                        <p>Signature of Parent</p>
                        <div class="signature-line"></div>
                        <p>Date: ______</p>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 20px;">
    <button class="print-btn" onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; font-size: 14pt; cursor: pointer; transition: background 0.3s ease;">Print Application</button>
</div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>