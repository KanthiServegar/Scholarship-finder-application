<?php
session_start();

// Assuming the user is logged in and their profile data is stored in the session.
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Database connection
$con = mysqli_connect("localhost", "root", "", "user_database");

// Fetch user profile data (example fields)
$user_id = $_SESSION['user_id'];
$query_profile = "SELECT * FROM user_form WHERE id = '$user_id'";
$result_profile = mysqli_query($con, $query_profile);
$user_profile = mysqli_fetch_assoc($result_profile);

// Extract profile details
$marks = $user_profile['marks'];
$age = $user_profile['age'];
$income = $user_profile['family_income'];
$gender = $user_profile['gender'];
$admission_type = $user_profile['admission_type'];
$class = $user_profile['class'];

// Query for scholarships based on the user's profile
$query = "SELECT * FROM scholarship 
          WHERE (marks_min <= $marks) 
          AND (age_min <= $age AND age_max >= $age) 
          AND (income_min <= $income AND income_max >= $income)
          AND (gender = 'All' OR gender = '$gender') 
          AND (class_eligibility = 'any' OR class_eligibility = '$class')
          AND (admission_type = 'All' OR admission_type = '$admission_type')";

$result = mysqli_query($con, $query);
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags and Bootstrap CSS -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Scholarship Finder</title>
    <style>
        body {
            background-image: url('css/bg.jpeg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card mt-4">
            <div class="card-header">
                <h4>Applicable Scholarships for You</h4>
            </div>
            <div class="card-body">
                <hr>
                <!-- Results Table -->
                <h4>Applicable Scholarships</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Sl.No</th>
                            <th>Scholarship Name</th>
                            <th>Description</th>
                            <th>Class</th>
                            <th>Income Limit</th>
                            <th>Minimum Marks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            $serial_number = 1; // Initialize serial number
                            while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <tr>
                                    <td><?php echo $serial_number++; ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td><?php echo htmlspecialchars($row['class_eligibility']); ?></td>
                                    <td><?php echo htmlspecialchars($row['income_min']) . " - " . htmlspecialchars($row['income_max']); ?></td>
                                    <td><?php echo htmlspecialchars($row['marks_min']); ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='6'>No applicable scholarships found based on your profile.</td></tr>";
                        }

                        // Close database connection
                        mysqli_close($con);
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <a href="home.php" class="delete-btn">Go back to Home Page</a>
</body>
</html>
