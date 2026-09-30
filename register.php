<?php
// register.php - Cyberpunk Purple Wither Edition
session_start();

$host = "localhost";
$user = "root";
$password = "";
$dbname = "3h";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username   = trim($_POST['username']);
    $pass       = trim($_POST['password']);
    $role       = trim($_POST['role']);
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);

    if (!empty($username) && !empty($pass) && !empty($email)) {
        // Check if user already exists
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "CONFLICT: Operative ID or Email already registered.";
        } else {
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (username, password, role, first_name, last_name, email) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $username, $hashed_password, $role, $first_name, $last_name, $email);

            if ($stmt->execute()) {
                $success = "SYSTEM: Operative identity registered successfully!";
            } else {
                $error = "ERROR: Registration failed. " . $conn->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register // CyberTerminal</title>
    <style>
        :root {
            --bg-color: #06040d;
            --panel-bg: rgba(18, 12, 31, 0.92);
            --cyan-glow: #00f0ff;
            --purple-wither: #a855f7;
            --purple-glow: #c084fc;
            --pink-glow: #ff007f;
            --green-glow: #00ff66;
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
            padding: 40px 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
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

        .register-card {
            width: 100%;
            max-width: 440px;
            background: var(--panel-bg);
            border: 1px solid var(--purple-wither);
            box-shadow: 0 0 25px rgba(168, 85, 247, 0.35);
            padding: 30px 35px;
            position: relative;
            z-index: 1;
            clip-path: polygon(0 0, 100% 0, 100% calc(100% - 20px), calc(100% - 20px) 100%, 0 100%);
        }

        h2 {
            color: var(--purple-glow);
            margin: 0 0 20px 0;
            font-size: 1.5rem;
            text-align: center;
            letter-spacing: 2px;
            text-shadow: 0 0 10px rgba(192, 132, 252, 0.6);
            border-bottom: 1px solid var(--purple-wither);
            padding-bottom: 10px;
        }

        .error {
            color: var(--pink-glow);
            background: rgba(255, 0, 127, 0.1);
            border-left: 3px solid var(--pink-glow);
            padding: 8px 12px;
            font-size: 0.85rem;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .success {
            color: var(--green-glow);
            background: rgba(0, 255, 102, 0.1);
            border-left: 3px solid var(--green-glow);
            padding: 8px 12px;
            font-size: 0.85rem;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 14px;
        }

        label {
            display: block;
            color: var(--cyan-glow);
            font-size: 0.8rem;
            margin-bottom: 5px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        input[type="text"], input[type="password"], input[type="email"], select {
            width: 100%;
            background: rgba(6, 4, 13, 0.85);
            border: 1px solid var(--border-line);
            color: #fff;
            padding: 10px;
            font-family: inherit;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        select option {
            background: #06040d;
            color: #fff;
        }

        input[type="text"]:focus, input[type="password"]:focus, input[type="email"]:focus, select:focus {
            outline: none;
            border-color: var(--purple-wither);
            box-shadow: 0 0 10px rgba(168, 85, 247, 0.5);
        }

        button.btn-register {
            width: 100%;
            background: rgba(168, 85, 247, 0.15);
            color: #fff;
            border: 1px solid var(--purple-wither);
            padding: 12px;
            font-family: inherit;
            font-weight: bold;
            font-size: 0.95rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(168, 85, 247, 0.3);
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        button.btn-register:hover {
            background: var(--purple-wither);
            color: #000;
            box-shadow: 0 0 20px var(--purple-glow), 0 0 30px var(--purple-wither);
        }

        .login-link {
            text-align: center;
            margin-top: 18px;
            font-size: 0.85rem;
        }

        .login-link a {
            color: var(--cyan-glow);
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-shadow: 0 0 8px var(--cyan-glow);
        }
    </style>
</head>
<body>

<div class="register-card">
    <h2>NEW OPERATIVE REGISTRATION</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autocomplete="off" placeholder="Enter operative handle...">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Enter secure passcode...">
        </div>

        <div class="form-group">
            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" placeholder="Enter first name...">
        </div>

        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" placeholder="Enter last name...">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="Enter comms address...">
        </div>

        <button type="submit" class="btn-register">INITIALIZE IDENTITY</button>
    </form>

    <div class="login-link">
        <span style="color: #666;">Already registered?</span> <a href="login.php">[ LOGIN HERE ]</a>
    </div>
</div>

</body>
</html>