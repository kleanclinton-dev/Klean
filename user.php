<?php
// user.php - Cyberpunk Purple Wither Edition
session_start();

// Database Connection Settings
$host = "localhost";
$user = "root";
$password = "";
$dbname = "3h";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Session Check (Fallback for development/testing)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = "user1234";
}

$current_user_id = $_SESSION['user_id'];
$message = "";

// 1. ADD TASK LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_task'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if (!empty($title)) {
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, status) VALUES (?, ?, ?, 'pending')");
        $stmt->bind_param("iss", $current_user_id, $title, $description);

        if ($stmt->execute()) {
            $message = "WITHER_NET: Task Directive Initialized.";
        } else {
            $message = "ERROR: " . $conn->error;
        }
        $stmt->close();
    }
}

// 2. TOGGLE STATUS LOGIC (Completed / Pending)
if (isset($_GET['toggle_status'])) {
    $task_id = intval($_GET['toggle_status']);
    
    $stmt = $conn->prepare("SELECT status FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $current_user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($row = $res->fetch_assoc()) {
        $new_status = ($row['status'] === 'completed') ? 'pending' : 'completed';
        $update_stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
        $update_stmt->bind_param("sii", $new_status, $task_id, $current_user_id);
        $update_stmt->execute();
        $update_stmt->close();
    }
    $stmt->close();
    header("Location: user.php");
    exit();
}

// 3. REMOVE TASK LOGIC ([X] Remove / Purge)
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $remove_id, $current_user_id);
    
    if ($stmt->execute()) {
        header("Location: user.php");
        exit();
    }
    $stmt->close();
}

// FETCH USER TASKS
$stmt = $conn->prepare("SELECT id, title, description, status FROM tasks WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$tasks_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberTerminal Workspace // Wither Protocol</title>
    <style>
        :root {
            --bg-color: #06040d;
            --panel-bg: rgba(18, 12, 31, 0.88);
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
            padding: 30px;
            box-sizing: border-box;
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

        .container {
            max-width: 850px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .header-panel {
            border: 1px solid var(--purple-wither);
            box-shadow: 0 0 20px rgba(168, 85, 247, 0.3);
            background: var(--panel-bg);
            padding: 20px;
            margin-bottom: 25px;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            clip-path: polygon(0 0, 100% 0, 100% calc(100% - 15px), calc(100% - 15px) 100%, 0 100%);
        }

        h2 {
            color: var(--purple-glow);
            margin: 0;
            font-size: 1.4rem;
            text-shadow: 0 0 10px rgba(192, 132, 252, 0.6);
            letter-spacing: 1px;
        }

        .nav-links {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .nav-links a.nav-item {
            color: var(--cyan-glow);
            text-decoration: none;
            font-weight: bold;
            font-size: 0.9rem;
            transition: 0.2s;
        }

        .nav-links a.nav-item:hover {
            text-shadow: 0 0 8px var(--cyan-glow);
        }

        /* PURPLE WITHER LOGOUT BUTTON */
        .btn-wither-logout {
            background: rgba(168, 85, 247, 0.15);
            color: #fff;
            border: 1px solid var(--purple-wither);
            padding: 8px 16px;
            font-family: inherit;
            font-size: 0.85rem;
            font-weight: bold;
            text-decoration: none;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 0 10px rgba(168, 85, 247, 0.4), inset 0 0 10px rgba(168, 85, 247, 0.2);
            transition: all 0.3s ease;
            position: relative;
        }

        .btn-wither-logout:hover {
            background: var(--purple-wither);
            color: #000;
            box-shadow: 0 0 20px var(--purple-glow), 0 0 30px var(--purple-wither);
            text-shadow: none;
        }

        .message {
            color: var(--purple-glow);
            background: rgba(168, 85, 247, 0.1);
            border-left: 3px solid var(--purple-wither);
            padding: 8px 12px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .section-box {
            border: 1px solid var(--border-line);
            background: var(--panel-bg);
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.7);
        }

        h3 {
            color: #fff;
            border-bottom: 1px solid var(--purple-wither);
            padding-bottom: 5px;
            margin-top: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 1.1rem;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            color: var(--purple-glow);
            font-size: 0.85rem;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        input[type="text"], textarea {
            width: 100%;
            background: rgba(6, 4, 13, 0.8);
            border: 1px solid var(--border-line);
            color: #fff;
            padding: 10px;
            font-family: inherit;
            box-sizing: border-box;
            transition: 0.3s;
        }

        input[type="text"]:focus, textarea:focus {
            outline: none;
            border-color: var(--purple-wither);
            box-shadow: 0 0 10px rgba(168, 85, 247, 0.5);
        }

        textarea {
            height: 70px;
            resize: vertical;
        }

        button.btn-cyber {
            background: transparent;
            color: var(--cyan-glow);
            border: 1px solid var(--cyan-glow);
            padding: 10px 20px;
            font-family: inherit;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            transition: 0.3s;
        }

        button.btn-cyber:hover {
            background: var(--cyan-glow);
            color: #000;
            box-shadow: 0 0 12px var(--cyan-glow);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid var(--border-line);
            padding: 12px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: rgba(168, 85, 247, 0.08);
            color: var(--purple-glow);
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .btn-status {
            display: inline-block;
            padding: 6px 10px;
            border: 1px solid var(--pink-glow);
            color: var(--pink-glow);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-status.completed {
            border-color: var(--green-glow);
            color: var(--green-glow);
            box-shadow: 0 0 6px rgba(0, 255, 102, 0.3);
        }

        .remove-link {
            color: var(--pink-glow);
            text-decoration: none;
            font-weight: bold;
            font-size: 0.85rem;
            transition: 0.2s;
        }

        .remove-link:hover {
            text-shadow: 0 0 8px var(--pink-glow);
        }
    </style>
</head>
<body>

<div class="container">

    <div class="header-panel">
        <h2>WELCOME, OPERATIVE <?php echo htmlspecialchars($_SESSION['username'] ?? 'user1234'); ?>!</h2>
        <div class="nav-links">
            <a href="user.php" class="nav-item">[ MY TASKS ]</a>
            <a href="logout.php" class="btn-wither-logout">&#9763; PURGE SESSION</a>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="section-box">
        <h3>Initialize New Task Directive</h3>
        <form action="user.php" method="POST">
            <div class="form-group">
                <label for="title">Task Protocol Title</label>
                <input type="text" id="title" name="title" required placeholder="Enter directive title...">
            </div>
            
            <div class="form-group">
                <label for="description">Task Details / Specs (Optional)</label>
                <textarea id="description" name="description" placeholder="Enter additional directive notes..."></textarea>
            </div>

            <button type="submit" name="add_task" class="btn-cyber">+ EXECUTE TASK</button>
        </form>
    </div>

    <div class="section-box">
        <h3>Active Operative Workspace</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Execution Status</th>
                    <th style="width: 55%;">Directive Logs</th>
                    <th style="width: 20%;">Purge</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tasks_result && $tasks_result->num_rows > 0): ?>
                    <?php while ($task = $tasks_result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <a href="user.php?toggle_status=<?php echo $task['id']; ?>" class="btn-status <?php echo ($task['status'] === 'completed') ? 'completed' : ''; ?>">
                                    <?php if ($task['status'] === 'completed'): ?>
                                        &#10004; COMPLETED
                                    <?php else: ?>
                                        &#9654; PENDING
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td>
                                <strong style="color: #fff;"><?php echo htmlspecialchars($task['title']); ?></strong><br>
                                <span style="color: #a78bfa; font-size: 0.9rem;"><?php echo nl2br(htmlspecialchars($task['description'])); ?></span>
                            </td>
                            <td>
                                <a href="user.php?remove=<?php echo $task['id']; ?>" 
                                   class="remove-link" 
                                   onclick="return confirm('Purge this task record permanently?');">[X] PURGE</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: #666;">No active directives detected in database queue.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>