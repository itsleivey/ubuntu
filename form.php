<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) header("Location: index.php");

$id = "";
$fullname = "";
$email = "";
$username = "";
$role = "User";
$isEdit = false;
$error = ""; // Store validation errors here

// --- PREFILL DATA FOR EDIT ---
// [cite: 58]
if (isset($_GET['edit'])) {
    $isEdit = true;
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM users WHERE id=$id");
    $row = $result->fetch_assoc();
    
    $fullname = $row['fullname'];
    $email = $row['email'];
    $username = $row['username'];
    $role = $row['user_role'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    
    // --- VALIDATION: CHECK UNIQUE USERNAME/EMAIL ---
    // 
    $checkSql = "SELECT * FROM users WHERE (username='$username' OR email='$email')";
    
    // If editing, exclude the current user from the check (so they can keep their own name)
    if ($isEdit) {
        $checkSql .= " AND id != $id";
    }
    
    $checkResult = $conn->query($checkSql);

    if ($checkResult->num_rows > 0) {
        $error = "Error: Username or Email already exists!";
    } else {
        // No duplicates found, proceed to save
        if ($isEdit) {
            // Update Record
            $sql = "UPDATE users SET fullname='$fullname', email='$email', username='$username', user_role='$role' WHERE id=$id";
            if($conn->query($sql)) {
                $_SESSION['message'] = "Record updated successfully!"; // [cite: 59]
                $_SESSION['msg_type'] = "success";
                header("Location: dashboard.php");
                exit();
            }
        } else {
            // Create Record
            // Note: Using plain text password as requested for now, but consider hashing later
            $password = $_POST['password']; 
            $sql = "INSERT INTO users (fullname, email, username, password, user_role) VALUES ('$fullname', '$email', '$username', '$password', '$role')";
            if($conn->query($sql)) {
                $_SESSION['message'] = "Record added successfully!"; // [cite: 49]
                $_SESSION['msg_type'] = "success";
                header("Location: dashboard.php");
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $isEdit ? "Edit" : "Add"; ?> User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2><?php echo $isEdit ? "Edit User" : "Add User"; ?></h2>
        
        <?php if($error): ?>
            <p style="color: #ef4444; font-size: 0.9rem; margin-bottom: 15px;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="fullname" value="<?php echo $fullname; ?>" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?php echo $email; ?>" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo $username; ?>" required>
            </div>
            
            <?php if (!$isEdit): ?>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label>User Role</label>
                <select name="role">
                    <option value="User" <?php if($role == 'User') echo 'selected'; ?>>User</option>
                    <option value="Staff" <?php if($role == 'Staff') echo 'selected'; ?>>Staff</option>
                    <option value="Admin" <?php if($role == 'Admin') echo 'selected'; ?>>Admin</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary"><?php echo $isEdit ? "Update Record" : "Save Record"; ?></button>
            <a href="dashboard.php" class="btn btn-sm" style="display:block; text-align:center; margin-top:10px; background:none; color:#888;">Cancel</a>
        </form>
    </div>
</body>
</html>