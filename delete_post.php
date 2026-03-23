<?php
session_start();

if (!isset($_SESSION['username'])) {
    die("Not logged in");
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Role: " . ($_SESSION['role'] ?? 'not set'));
}

include "config.php";

if (!isset($_GET['id'])) {
    die("No post ID received");
}

$id = (int) $_GET['id'];

$stmt = mysqli_prepare($conn, "DELETE FROM posts WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        header("Location: view_posts.php");
        exit();
    } else {
        mysqli_stmt_close($stmt);
        die("No post deleted. ID may not exist.");
    }
} else {
    die("Delete failed: " . mysqli_error($conn));
}
?>