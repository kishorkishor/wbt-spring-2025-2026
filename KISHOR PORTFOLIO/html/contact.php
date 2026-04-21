<?php
$fnameErr = $lnameErr = $genderErr = $emailErr = $cdateErr = "";
$fname = $lname = $gender = $email = $company = $cdate = "";
$reasons = [];
$topics = [];

function cleanInput($data) {
    return htmlspecialchars(trim($data));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["fname"])) {
        $fnameErr = "First name is required";
    } else {
        $fname = cleanInput($_POST["fname"]);
    }

    if (empty($_POST["lname"])) {
        $lnameErr = "Last name is required";
    } else {
        $lname = cleanInput($_POST["lname"]);
    }

    if (empty($_POST["gender"])) {
        $genderErr = "Gender is required";
    } else {
        $gender = cleanInput($_POST["gender"]);
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = cleanInput($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    $company = cleanInput($_POST["company"] ?? "");

    if (empty($_POST["cdate"])) {
        $cdateErr = "Consultation date is required";
    } else {
        $cdate = cleanInput($_POST["cdate"]);
    }

    if (!empty($_POST["reason"]) && is_array($_POST["reason"])) {
        $reasons = array_map("cleanInput", $_POST["reason"]);
    }

    if (!empty($_POST["topics"]) && is_array($_POST["topics"])) {
        $topics = array_map("cleanInput", $_POST["topics"]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Kishor Tarafder</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/contact.css">
</head>
<body>
    <header>
        <div class="header-content">
            <a href="../index.html" class="logo">KT</a>
            <nav>
                <ul>
                    <li><a href="../index.html">Home</a></li>
                    <li><a href="educations.html">Education</a></li>
                    <li><a href="experience.html">Experience</a></li>
                    <li><a href="projects.html">Projects</a></li>
                    <li><a href="contact.php" class="active">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <h1>Contact</h1>

        <div class="contact-form">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <fieldset>
                    <legend>Contact Form</legend>

                    <table>
                        <tr>
                            <td><label for="fname">First Name:</label></td>
                            <td>
                                <input type="text" id="fname" placeholder="Enter your first name" name="fname" value="<?php echo $fname; ?>">
                                <span style="color:red;"><?php echo $fnameErr; ?></span>
                            </td>
                        </tr>

                        <tr>
                            <td><label for="lname">Last Name:</label></td>
                            <td>
                                <input type="text" id="lname" placeholder="Enter your last name" name="lname" value="<?php echo $lname; ?>">
                                <span style="color:red;"><?php echo $lnameErr; ?></span>
                            </td>
                        </tr>

                        <tr>
                            <td><label>Gender:</label></td>
                            <td>
                                <input type="radio" name="gender" value="male" <?php if ($gender == "male") echo "checked"; ?>> Male
                                <input type="radio" name="gender" value="female" <?php if ($gender == "female") echo "checked"; ?>> Female
                                <input type="radio" name="gender" value="other" <?php if ($gender == "other") echo "checked"; ?>> Other
                                <span style="color:red;"><?php echo $genderErr; ?></span>
                            </td>
                        </tr>

                        <tr>
                            <td><label for="email">Email:</label></td>
                            <td>
                                <input type="email" id="email" placeholder="kishortarafder@gmail.com" name="email" value="<?php echo $email; ?>">
                                <span style="color:red;"><?php echo $emailErr; ?></span>
                            </td>
                        </tr>

                        <tr>
                            <td><label for="company">Company:</label></td>
                            <td>
                                <input type="text" id="company" placeholder="Enter your company name" name="company" value="<?php echo $company; ?>">
                            </td>
                        </tr>

                        <tr>
                            <td><label>Reason of Contact:</label></td>
                            <td>
                                <input type="checkbox" name="reason[]" value="projects" <?php if (in_array("projects", $reasons)) echo "checked"; ?>> Projects
                                <input type="checkbox" name="reason[]" value="thesis" <?php if (in_array("thesis", $reasons)) echo "checked"; ?>> Thesis
                                <input type="checkbox" name="reason[]" value="job" <?php if (in_array("job", $reasons)) echo "checked"; ?>> Job
                            </td>
                        </tr>

                        <tr>
                            <td><label>Topics:</label></td>
                            <td>
                                <input type="checkbox" name="topics[]" value="web" <?php if (in_array("web", $topics)) echo "checked"; ?>> Web Development
                                <input type="checkbox" name="topics[]" value="mobile" <?php if (in_array("mobile", $topics)) echo "checked"; ?>> Mobile Development
                                <input type="checkbox" name="topics[]" value="aiml" <?php if (in_array("aiml", $topics)) echo "checked"; ?>> AI/ML Development
                            </td>
                        </tr>

                        <tr>
                            <td><label for="cdate">Consultation Date:</label></td>
                            <td>
                                <input type="date" id="cdate" name="cdate" value="<?php echo $cdate; ?>">
                                <span style="color:red;"><?php echo $cdateErr; ?></span>
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <input type="submit" value="Submit">
                                <input type="reset">
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </form>
        </div>

        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !$fnameErr && !$lnameErr && !$genderErr && !$emailErr && !$cdateErr) { ?>
            <div class="contact-form" style="margin-top: 20px;">
                <fieldset>
                    <legend>Submitted Details</legend>
                    <p><strong>First Name:</strong> <?php echo $fname; ?></p>
                    <p><strong>Last Name:</strong> <?php echo $lname; ?></p>
                    <p><strong>Gender:</strong> <?php echo $gender; ?></p>
                    <p><strong>Email:</strong> <?php echo $email; ?></p>
                    <p><strong>Company:</strong> <?php echo $company; ?></p>
                    <p><strong>Reason of Contact:</strong> <?php echo !empty($reasons) ? implode(", ", $reasons) : "None"; ?></p>
                    <p><strong>Topics:</strong> <?php echo !empty($topics) ? implode(", ", $topics) : "None"; ?></p>
                    <p><strong>Consultation Date:</strong> <?php echo $cdate; ?></p>
                </fieldset>
            </div>
        <?php } ?>
    </main>

    <footer>
        <div class="footer-content">
            <p class="footer-name">Kishor Tarafder</p>
            <p class="footer-title">Quant Engineer</p>
            <p>222/A North Shahjahanpur, Dhaka</p>
            <p><a href="mailto:kishortarafder@gmail.com">kishortarafder@gmail.com</a></p>
            <div class="social-icons">
                <a href="https://github.com" target="_blank"><img src="../images/github.png" alt="GitHub"></a>
                <a href="https://linkedin.com" target="_blank"><img src="../images/linkedin.png" alt="LinkedIn"></a>
            </div>
            <p class="footer-copy">&copy; 2026 Kishor Tarafder</p>
        </div>
    </footer>
</body>
</html>
