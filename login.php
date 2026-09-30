<?php
// login.php - Cyberpunk Purple Wither Edition
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            
            // Verifies password against hashed OR plain text password
            if (password_verify($password, $row['password']) || $password === $row['password']) {
                
                // Store required session keys
                $_SESSION['user_id']  = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role']     = strtolower(trim($row['role']));

                // Redirect based on role
                if ($_SESSION['role'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: user.php");
                }
                exit();
            } else {
                $error = "Invalid username or password";
            }
        } else {
            $error = "Invalid username or password";
        }
        $stmt->close();
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login // CyberTerminal</title>
    <style>
        :root {
            --bg-color: #06040d;
            --panel-bg: rgba(18, 12, 31, 0.90);
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
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Scanline Overlay */
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

        .login-card {
            width: 100%;
            max-width: 380px;
            background: var(--panel-bg);
            border: 1px solid var(--purple-wither);
            box-shadow: 0 0 25px rgba(168, 85, 247, 0.35);
            padding: 35px 30px;
            position: relative;
            z-index: 1;
            clip-path: polygon(0 0, 100% 0, 100% calc(100% - 20px), calc(100% - 20px) 100%, 0 100%);
        }

        h2 {
            color: var(--purple-glow);
            margin: 0 0 25px 0;
            font-size: 1.6rem;
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
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: var(--cyan-glow);
            font-size: 0.85rem;
            margin-bottom: 8px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            background: rgba(6, 4, 13, 0.85);
            border: 1px solid var(--border-line);
            color: #fff;
            padding: 12px;
            font-family: inherit;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: var(--purple-wither);
            box-shadow: 0 0 12px rgba(168, 85, 247, 0.5);
        }

        button.btn-login {
            width: 100%;
            background: rgba(168, 85, 247, 0.15);
            color: #fff;
            border: 1px solid var(--purple-wither);
            padding: 12px;
            font-family: inherit;
            font-weight: bold;
            font-size: 1rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(168, 85, 247, 0.3);
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        button.btn-login:hover {
            background: var(--purple-wither);
            color: #000;
            box-shadow: 0 0 20px var(--purple-glow), 0 0 30px var(--purple-wither);
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 0.85rem;
        }

        .register-link a {
            color: var(--cyan-glow);
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-shadow: 0 0 8px var(--cyan-glow);
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>SYSTEM ACCESS</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autocomplete="off" placeholder="Enter identity...">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Enter passcode...">
        </div>

        <button type="submit" class="btn-login">AUTHENTICATE</button>
    </form>

    <div class="register-link">
        <span style="color: #666;">No access code?</span> <a href="register.php">[ REGISTER ]</a>
    </div>
</div>

</body>
</html>