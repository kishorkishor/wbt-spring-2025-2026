<?php
session_start();

$usernameErr = $passwordErr = "";
$username = "";
$msg = "";

$file = "users.json";

if (!file_exists($file)) {
    file_put_contents($file, json_encode([]));
}

function cleanInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function loadUsers($file) {
    $data = json_decode(file_get_contents($file), true);

    if (!is_array($data)) {
        return [];
    }

    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = cleanInput($_POST["username"]);
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    }

    if ($usernameErr == "" && $passwordErr == "") {

        $password = $_POST["password"];

        if ($username == "admin" && $password == "admin12345") {
            $_SESSION["role"] = "admin";
            $_SESSION["username"] = "admin";

            header("Location: admin.php");
            exit;
        }

        $users = loadUsers($file);
        $found = false;

        foreach ($users as $user) {
            if ($user["username"] == $username && password_verify($password, $user["password"])) {
                $_SESSION["role"] = "librarian";
                $_SESSION["username"] = $user["username"];
                $_SESSION["fname"] = $user["fname"];
                $_SESSION["lname"] = $user["lname"];

                $found = true;

                header("Location: librarian.php");
                exit;
            }
        }

        if (!$found) {
            $msg = "Invalid username or password!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Arial, Helvetica, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.22), transparent 35%),
                radial-gradient(circle at bottom right, rgba(79, 70, 229, 0.20), transparent 35%),
                linear-gradient(135deg, #dbeafe, #f8fafc);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 35px;
            color: #1f2937;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: rgba(255, 255, 255, 0.95);
            padding: 35px;
            border-radius: 24px;
            box-shadow: 0 25px 70px rgba(15, 23, 42, 0.18);
        }

        h2 {
            margin: 0 0 8px;
            text-align: center;
            font-size: 34px;
            color: #111827;
        }

        .subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 25px;
        }

        label {
            font-weight: 700;
            color: #374151;
            display: inline-block;
            margin-bottom: 7px;
        }

        input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #d1d5db;
            border-radius: 14px;
            font-size: 15px;
            outline: none;
            background-color: #f9fafb;
        }

        input:focus {
            background-color: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.16);
        }

        .star {
            color: #ef4444;
        }

        .error {
            color: #dc2626;
            display: block;
            margin-top: 6px;
            font-size: 13px;
            font-weight: 600;
            min-height: 16px;
        }

        .button-row {
            display: flex;
            gap: 12px;
        }

        button {
            padding: 13px 20px;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 700;
        }

        button[type="submit"] {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: white;
            flex: 1;
        }

        button[type="button"] {
            background-color: #f3f4f6;
            color: #111827;
            flex: 1;
            border: 1px solid #e5e7eb;
        }

        .message {
            text-align: center;
            font-weight: 700;
            color: #dc2626;
            margin-top: 18px;
        }

        .signup-text {
            text-align: center;
            margin-top: 20px;
            color: #4b5563;
        }

        .hint {
            margin-top: 18px;
            padding: 12px;
            background-color: #f3f4f6;
            border-radius: 12px;
            font-size: 13px;
            color: #4b5563;
            text-align: center;
        }

        a {
            color: #2563eb;
            font-weight: 700;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="login-card">

<h2>Login</h2>

<p class="subtitle">Online Library Management System</p>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

    <label>Username <span class="star">*</span></label><br>
    <input type="text" name="username" value="<?php echo $username; ?>" placeholder="Enter username">
    <span class="error"><?php echo $usernameErr; ?></span>
    <br>

    <label>Password <span class="star">*</span></label><br>
    <input type="password" name="password" placeholder="Enter password">
    <span class="error"><?php echo $passwordErr; ?></span>
    <br>

    <div class="button-row">
        <button type="submit">Login</button>
        <button type="button" onclick="window.location.href='login.php'">Refresh</button>
    </div>

</form>

<p class="message"><?php echo $msg; ?></p>

<p class="signup-text">New librarian? <a href="reg.php">Create Account</a></p>

<div class="hint">
    Admin Login: admin / admin12345
</div>

</div>

<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>

</body>
</html>