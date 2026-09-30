<?php
// admin.php - Cyberpunk Purple Wither Edition
session_start();

// Guard check: Ensure user is logged in and has admin access
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$user = "root";
$password = "";
$dbname = "3h";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all registered users
$sql = "SELECT id, username, role, first_name, last_name, email FROM users ORDER BY id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard // CyberTerminal</title>
    <style>
        :root {
            --bg-color: #06040d;
            --panel-bg: rgba(18, 12, 31, 0.92);
            --cyan-glow: #00f0ff;
            --purple-wither: #a855f7;
            --purple-glow: #c084fc;
            --pink-glow: #ff007f;
            --text-main: #d8b4fe;
            --border-line: #2e1065;
        }

        body {
            background-color: var(--bg-color);
            background-image: 
                linear-gradient(rgba(168, 85, 247, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(168, 85, 247, 0.05) 1px, transparent 1px);
            background-size: 30px 30px;
            color: var(--text-main);
            font-family: 'Consolas', 'Courier New', monospace;
            margin: 0;
            padding: 40px 20px;
            min-height: 100vh;
            box-sizing: border-box;
        }

        body::before {
            content: " ";
            display: block;
            position: fixed;
            top: 0; left: 0; bottom: 0; right: 0;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.35) 50%);
            background-size: 100% 4px;
            z-index: 10;
            pointer-events: none;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: var(--panel-bg);
            border: 1px solid var(--purple-wither);
            box-shadow: 0 0 30px rgba(168, 85, 247, 0.3);
            padding: 30px;
            position: relative;
            z-index: 1;
            clip-path: polygon(0 0, 100% 0, 100% calc(100% - 25px), calc(100% - 25px) 100%, 0 100%);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--purple-wither);
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        h1 {
            color: var(--purple-glow);
            margin: 0;
            font-size: 1.8rem;
            letter-spacing: 2px;
            text-shadow: 0 0 10px rgba(192, 132, 252, 0.6);
        }

        .user-info {
            font-size: 0.9rem;
            color: var(--cyan-glow);
        }

        .btn-logout {
            color: var(--pink-glow);
            border: 1px solid var(--pink-glow);
            padding: 6px 14px;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            margin-left: 15px;
        }

        .btn-logout:hover {
            background: var(--pink-glow);
            color: #000;
            box-shadow: 0 0 12px var(--pink-glow);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-line);
        }

        th {
            background: rgba(46, 16, 101, 0.5);
            color: var(--cyan-glow);
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }

        tr:hover {
            background: rgba(168, 85, 247, 0.08);
        }

        .badge-admin {
            color: var(--purple-glow);
            border: 1px solid var(--purple-glow);
            padding: 2px 8px;
            font-size: 0.75rem;
            font-weight: bold;
        }

        .badge-user {
            color: var(--cyan-glow);
            border: 1px solid var(--cyan-glow);
            padding: 2px 8px;
            font-size: 0.75rem;
        }

        .btn-delete {
            color: var(--pink-glow);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .btn-delete:hover {
            text-shadow: 0 0 8px var(--pink-glow);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>ADMIN DASHBOARD</h1>
        <div class="user-info">
            OPERATIVE: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
            <a href="logout.php" class="btn-logout">[ LOGOUT ]</a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td style="color: #fff; font-weight: bold;"><?php echo htmlspecialchars($row['username']); ?></td>
                        <td>
                            <?php if (strtolower($row['role']) === 'admin'): ?>
                                <span class="badge-admin">ADMIN</span>
                            <?php else: ?>
                                <span class="badge-user">USER</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td>
                            <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Terminate operative record?')">[ DELETE ]</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #666;">No operative records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>