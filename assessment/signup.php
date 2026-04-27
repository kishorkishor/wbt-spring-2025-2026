<?php

$fnameErr = $lnameErr = $contactErr = $emailErr = $usernameErr = $passwordErr = "";

$fname = $lname = $contact = $email = $username = "";

$msg = "";

function cleanInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST["fname"])) {
        $fnameErr = "First name is required";
    } else {
        $fname = strtoupper(cleanInput($_POST["fname"]));

        if (!preg_match("/^[A-Z-' ]*$/", $fname)) {
            $fnameErr = "Only letters and white space allowed";
        }
    }

    if (empty($_POST["lname"])) {
        $lnameErr = "Last name is required";
    } else {
        $lname = strtoupper(cleanInput($_POST["lname"]));

        if (!preg_match("/^[A-Z-' ]*$/", $lname)) {
            $lnameErr = "Only letters and white space allowed";
        }
    }

    if (empty($_POST["contact"])) {
        $contactErr = "Contact is required";
    } else {
        $contact = cleanInput($_POST["contact"]);

        if (!preg_match("/^[0-9+ ]*$/", $contact)) {
            $contactErr = "Only numbers, + and space allowed";
        }
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = strtolower(cleanInput($_POST["email"]));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = cleanInput($_POST["username"]);

        if (strlen($username) < 4) {
            $usernameErr = "Username must be at least 4 characters";
        } else if (!preg_match("/^[a-zA-Z0-9_]*$/", $username)) {
            $usernameErr = "Username can contain only letters, numbers and underscore";
        }
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else if (strlen($_POST["password"]) < 8) {
        $passwordErr = "Password must be at least 8 characters";
    }

    if ($fnameErr == "" && $lnameErr == "" && $contactErr == "" && $emailErr == "" && $usernameErr == "" && $passwordErr == "") {

        if ($username == "admin") {
            $msg = "Username already exists!";
        } else {
            $hashedPassword = password_hash($_POST["password"], PASSWORD_DEFAULT);
            $msg = "Registration successful!";

            $fname = "";
            $lname = "";
            $contact = "";
            $email = "";
            $username = "";
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #e0e7ff, #f8fafc);
            color: #1f2937;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }

        .signup-card {
            width: 100%;
            max-width: 480px;
            background-color: white;
            padding: 32px;
            border-radius: 18px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.15);
            border: 1px solid #e5e7eb;
        }

        h2 {
            margin: 0 0 8px;
            text-align: center;
            color: #111827;
            font-size: 30px;
        }

        .required-text {
            text-align: center;
            margin-bottom: 22px;
            color: #6b7280;
            font-size: 14px;
        }

        label {
            font-weight: bold;
            color: #374151;
            display: inline-block;
            margin-bottom: 7px;
        }

        input {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 15px;
            outline: none;
            transition: 0.2s;
        }

        input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        .star {
            color: #ef4444;
        }

        .error {
            color: #dc2626;
            display: block;
            margin-top: 5px;
            font-size: 13px;
            font-weight: 500;
        }

        .button-row {
            display: flex;
            gap: 10px;
            margin-top: 5px;
        }

        button {
            padding: 11px 18px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 15px;
            font-weight: bold;
            transition: 0.2s;
        }

        button[type="submit"] {
            background-color: #4f46e5;
            color: white;
            flex: 1;
        }

        button[type="submit"]:hover {
            background-color: #4338ca;
        }

        button[type="button"] {
            background-color: #e5e7eb;
            color: #111827;
            flex: 1;
        }

        button[type="button"]:hover {
            background-color: #d1d5db;
        }

        .message {
            text-align: center;
            font-weight: bold;
            color: #16a34a;
            margin-top: 18px;
        }

        .login-text {
            text-align: center;
            margin-top: 18px;
            color: #4b5563;
        }

        a {
            color: #4f46e5;
            font-weight: bold;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 520px) {
            body {
                padding: 15px;
            }

            .signup-card {
                padding: 22px;
            }

            .button-row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<div class="signup-card">

<h2>Signup</h2>

<p class="required-text"><span class="star">*</span> required field</p>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

    <label>First Name <span class="star">*</span></label><br>

    <input type="text" name="fname" id="fname" value="<?php echo $fname; ?>" placeholder="Enter first name">

    <span class="error"><?php echo $fnameErr; ?></span>

    <br><br>

    <label>Last Name <span class="star">*</span></label><br>

    <input type="text" name="lname" id="lname" value="<?php echo $lname; ?>" placeholder="Enter last name">

    <span class="error"><?php echo $lnameErr; ?></span>

    <br><br>

    <label>Contact <span class="star">*</span></label><br>

    <input type="text" name="contact" value="<?php echo $contact; ?>" placeholder="Enter contact number">

    <span class="error"><?php echo $contactErr; ?></span>

    <br><br>

    <label>Email <span class="star">*</span></label><br>

    <input type="email" name="email" id="email" value="<?php echo $email; ?>" placeholder="example@gmail.com">

    <span class="error"><?php echo $emailErr; ?></span>

    <br><br>

    <label>Username <span class="star">*</span></label><br>

    <input type="text" name="username" value="<?php echo $username; ?>" placeholder="letters_numbers_underscore">

    <span class="error"><?php echo $usernameErr; ?></span>

    <br><br>

    <label>Password <span class="star">*</span></label><br>

    <input type="password" name="password" placeholder="Minimum 8 characters">

    <span class="error"><?php echo $passwordErr; ?></span>

    <br><br>

    <div class="button-row">
        <button type="submit">Register</button>

        <button type="button" onclick="window.location.href='reg.php'">Refresh</button>
    </div>

</form>

<p class="message"><?php echo $msg; ?></p>

<p class="login-text">Already have an account? <a href="login.php">Login</a></p>

</div>

<script>
    const fname = document.getElementById("fname");
    const lname = document.getElementById("lname");
    const email = document.getElementById("email");

    fname.addEventListener("input", function () {
        fname.value = fname.value.toUpperCase();
    });

    lname.addEventListener("input", function () {
        lname.value = lname.value.toUpperCase();
    });

    email.addEventListener("input", function () {
        email.value = email.value.toLowerCase();
    });

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>

</body>
</html>