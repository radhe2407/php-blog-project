<?php
session_start();
include "config.php";

$message = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username == "" || $password == "") {
        $message = "All fields are required!";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];

                header("Location: view_posts.php");
                exit();
            } else {
                $message = "Wrong password!";
            }
        } else {
            $message = "User not found!";
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Login</h2>

    <?php if ($message != "") { ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php } ?>

    <form method="POST">
        <input class="form-control mb-3" type="text" name="username" placeholder="Enter Username" required>
        <input class="form-control mb-3" type="password" name="password" placeholder="Enter Password" required>
        <button class="btn btn-primary" type="submit" name="login">Login</button>
        <a href="register.php" class="btn btn-secondary">Register</a>
    </form>
</div>
</body>
</html>