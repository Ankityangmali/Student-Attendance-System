<?php 
include 'Includes/dbcon.php';
session_start();

$success = $error = "";

// Forgot Password Logic
if (isset($_POST['send_reset'])) {
    $email = $_POST['email'];
    $tables = ['tbladmin', 'tblclassteacher'];
    $found = false;

    foreach ($tables as $table) {
        $query = "SELECT * FROM $table WHERE emailAddress='$email'";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            $token = bin2hex(random_bytes(16));
            $expiry = date("Y-m-d H:i:s", strtotime("+30 minutes"));
            $conn->query("UPDATE $table SET reset_token='$token', token_expiry='$expiry' WHERE emailAddress='$email'");
            $success = "Reset link generated: <a href='resetPassword.php?token=$token'>Click here to reset</a>";
            $found = true;
            break;
        }
    }

    if (!$found) $error = "Email not found in our records.";
}

// Change Password Logic
if (isset($_POST['change_password'])) {
    $email = $_POST['change_email'];
    $current = md5($_POST['current_password']);
    $new = md5($_POST['new_password']);
    $tables = ['tbladmin', 'tblclassteacher'];
    $updated = false;

    foreach ($tables as $table) {
        $query = "SELECT * FROM $table WHERE emailAddress='$email' AND password='$current'";
        $check = $conn->query($query);
        if ($check->num_rows > 0) {
            $conn->query("UPDATE $table SET password='$new' WHERE emailAddress='$email'");
            $success = "Password updated successfully!";
            $updated = true;
            break;
        }
    }

    if (!$updated) $error = "Incorrect email or current password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot / Change Password</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <style>
        .tab-button {
            width: 50%;
            padding: 10px;
            cursor: pointer;
            font-weight: bold;
            text-align: center;
            background-color: #f1f1f1;
            border: 1px solid #ccc;
        }
        .tab-button.active {
            background-color: #007bff;
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body class="bg-gradient-login">
    <div class="container-login">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-9">
                <div class="card shadow-sm my-5">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <img src="img/logo/attnlg.jpg" style="width:80px;height:80px" class="mb-3">
                            <h4 class="text-gray-900 mb-2">Forgot / Change Password</h4>
                        </div>

                        <?php if ($success): ?>
                            <div class="alert alert-success text-center"><?php echo $success; ?></div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <!-- Tab buttons -->
                        <div class="d-flex mb-3">
                            <div class="tab-button active" onclick="showTab('forgot')">Forgot Password</div>
                            <div class="tab-button" onclick="showTab('change')">Change Password</div>
                        </div>

                        <!-- Forgot Password Tab -->
                        <div class="tab-content active" id="forgot">
                            <form method="POST">
                                <div class="form-group">
                                    <input type="email" name="email" required class="form-control" placeholder="Enter Your Email">
                                </div>
                                <button class="btn btn-primary btn-block" type="submit" name="send_reset">Send Reset Link</button>
                            </form>
                        </div>

                        <!-- Change Password Tab -->
                        <div class="tab-content" id="change">
                            <form method="POST">
                                <div class="form-group">
                                    <input type="email" name="change_email" required class="form-control" placeholder="Enter Email">
                                </div>
                                <div class="form-group">
                                    <input type="password" name="current_password" required class="form-control" placeholder="Enter Current Password">
                                </div>
                                <div class="form-group">
                                    <input type="password" name="new_password" required class="form-control" placeholder="Enter New Password">
                                </div>
                                <button class="btn btn-success btn-block" type="submit" name="change_password">Update Password</button>
                            </form>
                        </div>

                        <div class="text-center mt-3">
                            <a href="index.php" class="small">← Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function showTab(tab) {
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(tabDiv => tabDiv.classList.remove('active'));

            document.getElementById(tab).classList.add('active');
            if (tab === 'forgot') {
                document.querySelectorAll('.tab-button')[0].classList.add('active');
            } else {
                document.querySelectorAll('.tab-button')[1].classList.add('active');
            }
        }
    </script>
</body>
</html>
