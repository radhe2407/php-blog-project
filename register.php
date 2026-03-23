<?php
include "config.php";

$message = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username == "" || $password == "") {
        $message = "All fields are required!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = "user";

        $check_stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($check_stmt, "s", $username);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {
            $message = "Username already exists!";
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $username, $hashed_password, $role);

            if (mysqli_stmt_execute($stmt)) {
                $message = "Registration successful!";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($check_stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Register</h2>

    <?php if ($message != "") { ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php } ?>

    <form method="POST">
        <input class="form-control mb-3" type="text" name="username" placeholder="Enter Username" required>
        <input class="form-control mb-3" type="password" name="password" placeholder="Enter Password" required>
        <button class="btn btn-primary" type="submit" name="register">Register</button>
        <a href="login.php" class="btn btn-secondary">Login</a>
    </form>
</div>
</body>
</html>