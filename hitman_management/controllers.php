<?php
// ================================================================
// CONTROLLERS - request handling + role-based logic
// ================================================================

/* ============== Login ============== */
function loginCtrl($conn) {
    $error = '';
    $prefill = $_COOKIE['remember_user'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $u = trim($_POST['username'] ?? '');
        $p = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        if ($u === '' || $p === '') {
            $error = 'Please fill in both fields.';
        } else {
            // Try admin first
            $admin = authAdmin($conn, $u, $p);
            if ($admin) {
                $_SESSION['user'] = [
                    'id' => $admin['id'], 'username' => $admin['username'],
                    'name' => 'Administrator', 'role' => 'admin'
                ];
                if ($remember) setcookie('remember_user', $u, time() + 86400 * 30, '/');
                else setcookie('remember_user', '', time() - 3600, '/');
                header('Location: index.php?page=admin');
                exit;
            }
            // Then handler
            $handler = authHandler($conn, $u, $p);
            if ($handler) {
                $_SESSION['user'] = [
                    'id' => $handler['id'], 'username' => $handler['username'],
                    'name' => $handler['name'], 'role' => 'handler'
                ];
                if ($remember) setcookie('remember_user', $u, time() + 86400 * 30, '/');
                else setcookie('remember_user', '', time() - 3600, '/');
                header('Location: index.php?page=handler');
                exit;
            }
            $error = 'Invalid username or password.';
        }
    }

    require 'views/login.php';
}

/* ============== Register (handler self-registration) ============== */
function registerCtrl($conn) {
    $error = $success = '';
    $old = ['name' => '', 'contact' => '', 'username' => ''];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name     = trim($_POST['name'] ?? '');
        $contact  = trim($_POST['contact'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $old = compact('name', 'contact', 'username');

        if ($name === '' || $contact === '' || $username === '' || $password === '') {
            $error = 'All fields are required.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } elseif (handlerUsernameExists($conn, $username)) {
            $error = 'Username is already taken.';
        } else {
            if (addHandler($conn, $name, $contact, $username, $password)) {
                $success = 'Account created! You can now log in.';
                $old = ['name' => '', 'contact' => '', 'username' => ''];
            } else {
                $error = 'Registration failed. Try again.';
            }
        }
    }

    require 'views/register.php';
}

/* ============== Admin Dashboard (manages handlers) ============== */
function adminCtrl($conn) {
    $action = $_GET['action'] ?? 'list';
    $error = '';
    $editing = null;  // when set, view shows Edit form instead of Add form

    /* --- Add (POST) --- */
    if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $name     = trim($_POST['name'] ?? '');
        $contact  = trim($_POST['contact'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($name === '' || $contact === '' || $username === '' || $password === '') {
            $error = 'All fields are required.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif (handlerUsernameExists($conn, $username)) {
            $error = 'Username is already taken.';
        } else {
            if (addHandler($conn, $name, $contact, $username, $password)) {
                header('Location: index.php?page=admin&msg=added');
                exit;
            }
            $error = 'Failed to add handler.';
        }
    }

    /* --- Update (POST) --- */
    if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id       = intval($_GET['id'] ?? 0);
        $name     = trim($_POST['name'] ?? '');
        $contact  = trim($_POST['contact'] ?? '');
        $username = trim($_POST['username'] ?? '');

        // ===== NULL VALIDATION on UPDATE =====
        if ($name === '' || $contact === '' || $username === '') {
            $error = 'No field can be empty (NULL). All fields are required.';
            $editing = ['id' => $id, 'name' => $name, 'contact' => $contact, 'username' => $username];
        } elseif (handlerUsernameExists($conn, $username, $id)) {
            $error = 'That username is used by another handler.';
            $editing = ['id' => $id, 'name' => $name, 'contact' => $contact, 'username' => $username];
        } else {
            if (updateHandler($conn, $id, $name, $contact, $username)) {
                header('Location: index.php?page=admin&msg=updated');
                exit;
            }
            $error = 'Update failed.';
            $editing = ['id' => $id, 'name' => $name, 'contact' => $contact, 'username' => $username];
        }
    }

    /* --- Show edit form (GET) --- */
    if ($action === 'edit' && !$editing) {
        $id = intval($_GET['id'] ?? 0);
        $editing = getHandler($conn, $id);
    }

    /* --- Delete (GET) --- */
    if ($action === 'delete') {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) deleteHandler($conn, $id);
        header('Location: index.php?page=admin&msg=deleted');
        exit;
    }

    $handlers = getHandlers($conn);
    require 'views/admin.php';
}

/* ============== Handler Dashboard (manages contracts) ============== */
function handlerCtrl($conn) {
    $action = $_GET['action'] ?? 'list';
    $error = '';
    $editing = null;

    /* --- Add (POST) --- */
    if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $target_name = trim($_POST['target_name'] ?? '');
        $location    = trim($_POST['location'] ?? '');
        $bounty      = trim($_POST['bounty'] ?? '');
        $fee         = trim($_POST['fee'] ?? '');

        if ($target_name === '' || $location === '' || $bounty === '' || $fee === '') {
            $error = 'All fields are required.';
        } elseif (!ctype_digit($bounty) || intval($bounty) < 0) {
            $error = 'Bounty must be a non-negative whole number.';
        } elseif (!is_numeric($fee) || floatval($fee) < 0) {
            $error = 'Fee must be a non-negative number.';
        } else {
            $handlerId = $_SESSION['user']['id'];
            if (addContract($conn, $target_name, $location, intval($bounty), floatval($fee), $handlerId)) {
                header('Location: index.php?page=handler&msg=added');
                exit;
            }
            $error = 'Failed to add contract.';
        }
    }

    /* --- Update (POST) --- */
    if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id          = intval($_GET['id'] ?? 0);
        $target_name = trim($_POST['target_name'] ?? '');
        $location    = trim($_POST['location'] ?? '');
        $bounty      = trim($_POST['bounty'] ?? '');
        $fee         = trim($_POST['fee'] ?? '');

        // ===== NULL VALIDATION on UPDATE =====
        if ($target_name === '' || $location === '' || $bounty === '' || $fee === '') {
            $error = 'No field can be empty (NULL). All fields are required.';
            $editing = ['id' => $id, 'target_name' => $target_name, 'location' => $location,
                        'bounty' => $bounty, 'fee' => $fee];
        } elseif (!ctype_digit($bounty) || intval($bounty) < 0) {
            $error = 'Bounty must be a non-negative whole number.';
            $editing = ['id' => $id, 'target_name' => $target_name, 'location' => $location,
                        'bounty' => $bounty, 'fee' => $fee];
        } elseif (!is_numeric($fee) || floatval($fee) < 0) {
            $error = 'Fee must be a non-negative number.';
            $editing = ['id' => $id, 'target_name' => $target_name, 'location' => $location,
                        'bounty' => $bounty, 'fee' => $fee];
        } else {
            if (updateContract($conn, $id, $target_name, $location, intval($bounty), floatval($fee))) {
                header('Location: index.php?page=handler&msg=updated');
                exit;
            }
            $error = 'Update failed.';
            $editing = ['id' => $id, 'target_name' => $target_name, 'location' => $location,
                        'bounty' => $bounty, 'fee' => $fee];
        }
    }

    /* --- Show edit form --- */
    if ($action === 'edit' && !$editing) {
        $id = intval($_GET['id'] ?? 0);
        $editing = getContract($conn, $id);
    }

    /* --- Delete --- */
    if ($action === 'delete') {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) deleteContract($conn, $id);
        header('Location: index.php?page=handler&msg=deleted');
        exit;
    }

    $contracts = getContracts($conn);
    require 'views/handler.php';
}
?>
