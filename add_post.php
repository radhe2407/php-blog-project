<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include "config.php";

$message = "";

if (isset($_POST['submit'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if ($title == "" || $content == "") {
        $message = "All fields are required!";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO posts (title, content) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $title, $content);

        if (mysqli_stmt_execute($stmt)) {
            $message = "Post added successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
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
    <title>Add Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Add New Post</h2>

    <?php if ($message != "") { ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php } ?>

    <form method="POST">
        <input class="form-control mb-3" type="text" name="title" placeholder="Enter Title" required>
        <textarea class="form-control mb-3" name="content" placeholder="Enter Content" rows="5" required></textarea>
        <button class="btn btn-primary" type="submit" name="submit">Add Post</button>
        <a href="view_posts.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>