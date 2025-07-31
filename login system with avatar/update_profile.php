
<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if form fields are set and not empty
    $update_name = isset($_POST['update_name']) ? mysqli_real_escape_string($conn, $_POST['update_name']) : '';
    $update_email = isset($_POST['update_email']) ? mysqli_real_escape_string($conn, $_POST['update_email']) : '';
    $age = isset($_POST['age']) ? mysqli_real_escape_string($conn, $_POST['age']) : '';
    $class = isset($_POST['class']) ? mysqli_real_escape_string($conn, $_POST['class']) : '';
    $family_income = isset($_POST['family_income']) ? mysqli_real_escape_string($conn, $_POST['family_income']) : '';
    $marks = isset($_POST['marks']) ? mysqli_real_escape_string($conn, $_POST['marks']) : '';
    $caste = isset($_POST['caste']) ? mysqli_real_escape_string($conn, $_POST['caste']) : '';
    $gender = isset($_POST['gender']) ? mysqli_real_escape_string($conn, $_POST['gender']) : '';
    $admission_type = isset($_POST['admission_type']) ? mysqli_real_escape_string($conn, $_POST['admission_type']) : '';
    $parent_occupation = isset($_POST['parent_occupation']) ? mysqli_real_escape_string($conn, $_POST['parent_occupation']) : '';
    
    // Handle password fields
    $old_pass = isset($_POST['old_pass']) ? $_POST['old_pass'] : '';
    $new_pass = isset($_POST['new_pass']) ? $_POST['new_pass'] : '';
    $confirm_pass = isset($_POST['confirm_pass']) ? $_POST['confirm_pass'] : '';
 
    // Update password logic
    if (!empty($new_pass) && !empty($confirm_pass)) {
        // Fetch current password from the database
        $select = mysqli_query($conn, "SELECT password FROM user_form WHERE id = '$user_id'") or die('query failed');
        $fetch = mysqli_fetch_assoc($select);
        $current_password = $fetch['password']; // Get current password
        
        if (!password_verify($old_pass, $current_password)) {
            echo "Old password does not match!";
        } elseif ($new_pass != $confirm_pass) {
            echo "New passwords do not match!";
        } else {
            $hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE user_form SET password='$hashed_new_pass' WHERE id='$user_id'");
            echo "Password updated successfully!";
        }
    }

    if (isset($_POST['update_profile'])) {
        // Update query for all profile fields
        $query = "UPDATE user_form SET 
                  name = '$update_name', 
                  email = '$update_email', 
                  age = '$age', 
                  class = '$class',
                  family_income = '$family_income', 
                  marks = '$marks',
                  admission_type = '$admission_type',
                  parent_occupation = '$parent_occupation',
                  caste = '$caste',
                  gender = '$gender' 
                  WHERE id = '$user_id'";
        mysqli_query($conn, $query) or die('Update query failed');

        // Image upload logic
        $update_image = $_FILES['update_image']['name'];
        $update_image_size = $_FILES['update_image']['size'];
        $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
        $update_image_folder = 'uploaded_img/' . $update_image;

        if (!empty($update_image)) {
            // Validate image size
            if ($update_image_size > 2000000) {
                echo 'Image is too large.';
            } else {
                // Validate image type
                $allowed_types = ['image/jpg', 'image/jpeg', 'image/png'];
                if (in_array($_FILES['update_image']['type'], $allowed_types)) {
                    $image_update_query = mysqli_query($conn, "UPDATE user_form SET image = '$update_image' WHERE id = '$user_id'");
                    if ($image_update_query) {
                        move_uploaded_file($update_image_tmp_name, $update_image_folder);
                        echo 'Image updated successfully!';
                    }
                } else {
                    echo 'Invalid image type.';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="update-profile">
    <?php
    $select = mysqli_query($conn, "SELECT * FROM user_form WHERE id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($select) > 0) {
        $fetch = mysqli_fetch_assoc($select);
    }
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <?php
        if ($fetch['image'] == '') {
            echo '<img src="images/default-avatar.png">';
        } else {
            echo '<img src="uploaded_img/'.$fetch['image'].'">';
        }
        ?>
        <div class="flex">
            <div class="inputBox">
                <span>Username:</span>
                <input type="text" name="update_name" value="<?php echo $fetch['name']; ?>" class="box">
                <span>Your Email:</span>
                <input type="email" name="update_email" value="<?php echo $fetch['email']; ?>" class="box">

                <span>Update Your Pic:</span>
                <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" class="box">

                <span>Age:</span>
                <input type="number" name="age" value="<?php echo isset($fetch['age']) ? $fetch['age'] : ''; ?>" class="box">

                <span>Class:</span>
                <select name="class" class="box">
                    <option value="sslc" <?php echo ($fetch['class'] == 'sslc') ? 'selected' : ''; ?>>sslc</option>
                    <option value="puc" <?php echo ($fetch['class'] == 'puc') ? 'selected' : ''; ?>>Puc</option>
                    <option value="undergraduate" <?php echo ($fetch['class'] == 'undergraduate') ? 'selected' : ''; ?>>Undergraduate</option>
                </select>
                
                <span>Family Income:</span>
                <input type="text" name="family_income" value="<?php echo isset($fetch['family_income']) ? $fetch['family_income'] : ''; ?>" class="box">
                <span>Marks:</span>
                <input type="number" name="marks" value="<?php echo isset($fetch['marks']) ? $fetch['marks'] : ''; ?>" class="box">

            </div>

            <div class="inputBox">
                
                <span>Caste:</span>
                <input type="text" name="caste" value="<?php echo isset($fetch['caste']) ? $fetch['caste'] : ''; ?>" class="box">

                <span>Gender:</span>
                <select name="gender" class="box">
                    <option value="Male" <?php echo ($fetch['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($fetch['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo ($fetch['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>

                <span>Admission Type:</span>
                <select name="admission_type" class="box">
                   <option value="regular" <?php echo ($fetch['admission_type'] == 'regular') ? 'selected' : ''; ?>>Regular</option>
                </select>
               

                <span>Parent Occupation:</span>
                <input type="text" name="parent_occupation" value="<?php echo isset($fetch['parent_occupation']) ? $fetch['parent_occupation'] : ''; ?>" class="box">

                <span>Old Password:</span>
                <input type="password" name="old_pass" placeholder="Enter previous password" class="box">
                <span>New Password:</span>
                <input type="password" name="new_pass" placeholder="Enter new password" class="box">
                <span>Confirm Password:</span>
                <input type="password" name="confirm_pass" placeholder="Confirm new password" class="box">
            </div>
        </div>

        <input type="submit" value="Update Profile" name="update_profile" class="btn">
        <a href="browse_scholarships.php" class="delete-btn">Browse for Scholarship</a>
    </form>
</div>
</body>
</html>