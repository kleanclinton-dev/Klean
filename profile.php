<?php
session_start();
include 'db.php';

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = $conn->query($sql);
$user_data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; margin: 0; }
        .navbar { background-color: #17a2b8; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h2 { margin: 0; font-size: 20px; }
        .navbar a { color: #ddd; text-decoration: none; margin-left: 20px; font-size: 14px; transition: 0.3s; }
        .navbar a:hover { color: white; }
        .container { padding: 30px; display: flex; justify-content: center; }
        .profile-card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); width: 400px; }
        .profile-card h2 { margin-top: 0; color: #333; text-align: center; border-bottom: 2px solid #17a2b8; padding-bottom: 15px; }
        .profile-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee; }
        .profile-item strong { color: #555; }
        .profile-item span { color: #333; }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>My Profile</h2>
        <div>
            <a href="<?php echo ($_SESSION['role'] == 'admin') ? 'admin.php' : 'user.php'; ?>">Back to Dashboard</a>
            <a href="logout.php">Log Out</a>
        </div>
    </div>

    <div class="container">
        <div class="profile-card">
            <h2>Account Details</h2>
            <div class="profile-item">
                <strong>User ID:</strong> <span><?php echo $user_data['id']; ?></span>
            </div>
            <div class="profile-item">
                <strong>Username:</strong> <span><?php echo $user_data['username']; ?></span>
            </div>
            <div class="profile-item">
                <strong>Full Name:</strong> <span><?php echo $user_data['first_name'] . ' ' . $user_data['last_name']; ?></span>
            </div>
            <div class="profile-item">
                <strong>Email:</strong> <span><?php echo $user_data['email']; ?></span>
            </div>
            <div class="profile-item">
                <strong>Role:</strong> <span><?php echo ucfirst($user_data['role']); ?></span>
            </div>
        </div>
    </div>
</body>
</html>