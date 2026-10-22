<?php
session_start();
require_once __DIR__ . '/dbConfig.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    $stmt = mysqli_prepare($conn, "SELECT id, email, password, name, role, team_id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $matchedUser = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($matchedUser && $matchedUser["password"] === $password) {
        $_SESSION["user_id"] = (int) $matchedUser["id"];
        $_SESSION["username"] = $matchedUser["email"];
        $_SESSION["name"] = $matchedUser["name"];
        $_SESSION["role"] = $matchedUser["role"];
        $_SESSION["team_id"] = $matchedUser["team_id"] !== null ? (int) $matchedUser["team_id"] : null;
        $_SESSION["loggedIn"] = true;

        // TODO: isn't it project_manager? //Good catch - alex
        if ($matchedUser["role"] === "project_manager" || $matchedUser["role"] === "team_leader") {
            header("Location: /managerAnalytics.php");
            exit();
        } else {
            header("Location: /employeeAnalytics.php");
            exit();
        }
    } else {
        $error = "Invalid Email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Make-It-All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(255, 255, 255, 0.8), transparent 30%),
                linear-gradient(135deg, #f5f5f5 0%, #e5e7eb 55%, #d1d5db 100%);
        }

        .login-wrapper {
            min-height: 100vh;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.86);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.10);
            padding: 2.2rem;
            animation: fadeUp 0.7s ease;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-title {
            text-align: center;
            font-weight: 700;
            font-size: 1.75rem;
            color: #111827;
            margin-bottom: 0.3rem;
        }

        .login-subtitle {
            text-align: center;
            font-size: 1.2rem;
            color: #6b7280;
            margin-bottom: 1.8rem;
            letter-spacing: 0.04em;
        }

        .form-floating>.form-control {
            height: 58px;
            border-radius: 12px;
            border: 1px solid #d1d5db;
            box-shadow: none;
        }

        .form-floating>label {
            color: #6b7280;
        }

        .form-control:focus {
            border-color: #6b7280;
            box-shadow: 0 0 0 0.2rem rgba(107, 114, 128, 0.12);
        }

        .signin-btn {
            height: 52px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            background: linear-gradient(135deg, #111827 0%, #374151 100%);
            border: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
        }

        .signin-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(17, 24, 39, 0.18);
            opacity: 0.98;
        }

        .helper-links {
            text-align: center;
            font-size: 0.93rem;
            margin-top: 0.3rem;
        }

        .helper-links a {
            color: #4b5563;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .helper-links a:hover {
            color: #111827;
            text-decoration: underline;
        }

        .sample-users {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
            font-size: 0.88rem;
            color: #374151;
        }

        .sample-users h6 {
            font-weight: 700;
            margin-bottom: 0.7rem;
            color: #111827;
        }

        .sample-users p {
            margin-bottom: 0.35rem;
        }

        .role-label {
            display: inline-block;
            padding: 0.15rem 0.4rem;
            border-radius: 5px;
            font-size: 0.72rem;
            font-weight: 600;
        }

        .role-pm {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
        }

        .role-tl {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .role-emp {
            background: rgba(107, 114, 128, 0.1);
            color: #4b5563;
        }

        .alert {
            border-radius: 12px;
            font-size: 0.95rem;
        }
    </style>
</head>

<body>
    <div class="container login-wrapper d-flex justify-content-center align-items-center">
        <div class="login-card">
            <h1 class="welcome-title">Welcome to Make-It-All</h1>
            <p class="login-subtitle">Log In</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter username"
                        required>
                    <label for="username">Email</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Enter password" required>
                    <label for="password">Password</label>
                </div>
                <button type="submit" class="btn btn-dark w-100 signin-btn mb-3">Sign In</button>
            </form>

            <div class="helper-links">
                <p class="mb-1">
                    <a href="#">Forgot Email / Password?</a>
                </p>
                <p class="mb-0">
                    Don’t have an account? <a href="#">Sign Up</a>
                </p>
            </div>

            <div class="sample-users">
                <h6>Sample users for testing</h6>
                <p><span class="role-label role-pm">Project Manager</span> manager@gmail.com / manager123</p>
                <p><span class="role-label role-tl">Team Leader</span> leader1@gmail.com / leader123
                    <small>(Alpha)</small></p>
                <p><span class="role-label role-tl">Team Leader</span> leader2@gmail.com / leader123
                    <small>(Beta)</small></p>
                <p><span class="role-label role-emp">Employee</span> employee@gmail.com / employee123
                    <small>(Alpha)</small></p>
                <p><span class="role-label role-emp">Employee</span> emp2@gmail.com / employee123 <small>(Alpha)</small>
                </p>
                <p><span class="role-label role-emp">Employee</span> emp3@gmail.com / employee123 <small>(Beta)</small>
                </p>
                <p><span class="role-label role-emp">Employee</span> emp4@gmail.com / employee123 <small>(Beta)</small>
                </p>
            </div>
        </div>
    </div>
</body>

</html>
