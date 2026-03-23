<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['role'] != 'admin') {
    header("Location: view_posts.php");
    exit();
}
include "config.php";

$message = "";

// Fetch post
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM posts WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
}

// Update post
if (isset($_POST['update'])) {
    $id = (int) $_POST['id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if ($title == "" || $content == "") {
        $message = "All fields are required!";
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE posts SET title=?, content=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssi", $title, $content, $id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: view_posts.php");
            exit();
        } else {
            $message = "Error updating post";
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Post</h2>

    <?php if ($message != "") { ?>
        <div class="alert alert-danger"><?php echo $message; ?></div>
    <?php } ?>

    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

        <input class="form-control mb-3" type="text" name="title"
               value="<?php echo htmlspecialchars($row['title']); ?>" required>

        <textarea class="form-control mb-3" name="content" rows="5" required><?php echo htmlspecialchars($row['content']); ?></textarea>

        <button class="btn btn-primary" type="submit" name="update">Update</button>
        <a href="view_posts.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>