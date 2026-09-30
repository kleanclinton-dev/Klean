<?php
session_start();
// If the user is already logged in, send them to the correct dashboard
if (isset($_SESSION['logged_in'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: user.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome to the Anime System</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Poppins:wght@300;400;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            /* Starry Anime Night Background */
            background: radial-gradient(circle at center, #2a0845 0%, #0f0f1a 100%);
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            overflow: hidden;
            position: relative;
            color: white;
        }

        /* Floating magical particles */
        .particle {
            position: absolute;
            background: rgba(255, 105, 180, 0.8);
            border-radius: 50%;
            box-shadow: 0 0 10px #ff69b4, 0 0 20px #ff69b4;
            animation: floatUp 10s infinite linear;
            opacity: 0;
        }

        @keyframes floatUp {
            0% { transform: translateY(100vh) scale(0); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: translateY(-10vh) scale(1.5); opacity: 0; }
        }

        /* Glowing magic circle behind the text */
        .magic-circle {
            position: absolute;
            width: 600px;
            height: 600px;
            border: 2px solid rgba(0, 255, 255, 0.2);
            border-radius: 50%;
            box-shadow: 0 0 40px rgba(0, 255, 255, 0.3), inset 0 0 40px rgba(0, 255, 255, 0.2);
            animation: spin 20s linear infinite;
            z-index: 1;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Welcome Card */
        .welcome-container { 
            background: rgba(255, 255, 255, 0.05); 
            backdrop-filter: blur(20px); 
            -webkit-backdrop-filter: blur(20px);
            padding: 60px 50px; 
            border-radius: 30px; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.6), inset 0 0 0 1px rgba(255,255,255,0.1); 
            width: 450px; 
            text-align: center; 
            z-index: 10;
            animation: fadeIn 1.2s ease-out;
            position: relative;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .anime-icon {
            font-size: 60px;
            margin-bottom: 10px;
            filter: drop-shadow(0 0 15px rgba(255, 105, 180, 0.8));
        }

        .anime-title {
            font-family: 'Cinzel', serif;
            font-size: 32px;
            font-weight: 900;
            color: #fff;
            text-shadow: 0 0 20px rgba(255, 105, 180, 0.9), 0 0 40px rgba(255, 105, 180, 0.5);
            margin-bottom: 5px;
            letter-spacing: 2px;
        }

        .anime-subtitle {
            font-size: 14px;
            color: #00ffff;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 30px;
            font-weight: 600;
            text-shadow: 0 0 15px rgba(0, 255, 255, 0.8);
        }

        .welcome-text {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            margin-bottom: 40px;
        }

        /* Glowing Buttons */
        .btn-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .btn { 
            width: 100%; 
            padding: 15px; 
            border: none; 
            border-radius: 15px; 
            font-size: 16px; 
            font-weight: 700;
            cursor: pointer; 
            transition: 0.4s;
            letter-spacing: 2px;
            text-decoration: none;
            display: block;
            text-transform: uppercase;
        }

        .btn-login {
            background: linear-gradient(90deg, #ff758c, #ff7eb3); 
            color: white; 
            box-shadow: 0 0 20px rgba(255, 117, 140, 0.5);
        }

        .btn-login:hover {
            box-shadow: 0 0 40px rgba(255, 117, 140, 0.9);
            transform: translateY(-3px);
        }

        .btn-register {
            background: rgba(255, 255, 255, 0.1);
            color: #00ffff; 
            border: 1px solid #00ffff;
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.3);
        }

        .btn-register:hover {
            background: #00ffff;
            color: #000;
            box-shadow: 0 0 40px rgba(0, 255, 255, 0.9);
            transform: translateY(-3px);
        }

    </style>
</head>
<body>
    <!-- Floating Magic Particles -->
    <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
    <div class="particle" style="left: 30%; animation-delay: 2s;"></div>
    <div class="particle" style="left: 50%; animation-delay: 4s;"></div>
    <div class="particle" style="left: 70%; animation-delay: 1s;"></div>
    <div class="particle" style="left: 90%; animation-delay: 3s;"></div>

    <!-- Glowing Magic Circle -->
    <div class="magic-circle"></div>

    <div class="welcome-container">
        <div class="anime-icon">✨</div>
        <div class="anime-title">ようこそ</div>
        <div class="anime-subtitle">Welcome to the System</div>
        
        <p class="welcome-text">
            Your journey begins here. Please log in to your account or create a new one to enter the system.
        </p>

        <div class="btn-container">
            <a href="login.php" class="btn btn-login">ログイン (Login)</a>
            <a href="register.php" class="btn btn-register">新規登録 (Register)</a>
        </div>
    </div>
</body>
</html>