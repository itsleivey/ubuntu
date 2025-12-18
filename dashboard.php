<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// --- DELETE FUNCTIONALITY ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$id");
    
    $_SESSION['message'] = "Record deleted successfully.";
    $_SESSION['msg_type'] = "success";
    
    header("Location: dashboard.php");
    exit();
}

// Fetch all users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function confirmDelete(id) {
            if(confirm("Are you sure you want to delete this record?")) {
                window.location.href = "dashboard.php?delete=" + id;
            }
        }
    </script>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <div>
                <h2>Record Management System</h2>
                <span class="subtitle">Welcome, <?php echo $_SESSION['fullname']; ?></span>
            </div>
            <a href="logout.php" class="btn btn-sm" style="background:#ef4444;">Logout</a>
        </div>

        <?php if(isset($_SESSION['message'])): ?>
            <div id="msg-box" style="padding: 10px; border-radius: 6px; margin-bottom: 15px; 
                background-color: <?php echo $_SESSION['msg_type'] == 'success' ? '#22c55e' : '#ef4444'; ?>; 
                color: white; text-align: center; transition: opacity 0.5s ease;">
                <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']); 
                    unset($_SESSION['msg_type']);
                ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="header" style="margin-bottom:0;">
                <div>
                    <h3>User Management</h3>
                    <p class="subtitle" style="margin-bottom:0;">Manage system users and records</p>
                </div>
                <a href="form.php" class="btn btn-primary btn-sm">Add User</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['fullname']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['user_role']; ?></td>
                            <td>
                                <a href="form.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm" style="background:#eab308;">Edit</a>
                                <button onclick="confirmDelete(<?php echo $row['id']; ?>)" class="btn btn-sm btn-danger">Delete</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="empty-state">No users found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Check if the message box exists
        const msgBox = document.getElementById('msg-box');
        if (msgBox) {
            // Wait 3 seconds (3000 milliseconds)
            setTimeout(() => {
                // Smoothly fade out
                msgBox.style.opacity = '0';
                
                // Remove from the page completely after the fade (0.5s)
                setTimeout(() => {
                    msgBox.style.display = 'none';
                }, 500);
            }, 3000);
        }
    </script>
</body>
</html>