<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include "config.php";

$search = "";
$limit = 3;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;

if (isset($_GET['search']) && $_GET['search'] != "") {
    $search = mysqli_real_escape_string($conn, $_GET['search']);

    $count_sql = "SELECT COUNT(*) AS total FROM posts 
                  WHERE title LIKE '%$search%' OR content LIKE '%$search%'";

    $sql = "SELECT * FROM posts 
            WHERE title LIKE '%$search%' OR content LIKE '%$search%' 
            ORDER BY created_at DESC 
            LIMIT $limit OFFSET $offset";
} else {
    $count_sql = "SELECT COUNT(*) AS total FROM posts";

    $sql = "SELECT * FROM posts 
            ORDER BY created_at DESC 
            LIMIT $limit OFFSET $offset";
}

$count_result = mysqli_query($conn, $count_sql);

if (!$count_result) {
    die("Count Query Failed: " . mysqli_error($conn));
}

$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center mb-4">All Blog Posts</h2>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            Welcome, <strong><?php echo $_SESSION['username']; ?></strong> (<?php echo $_SESSION['role']; ?>)
        </div>
        <div>
            <a href="add_post.php" class="btn btn-success btn-sm">Add Post</a>
            <a href="logout.php" class="btn btn-secondary btn-sm">Logout</a>
        </div>
    </div>

    <form method="GET" class="d-flex mb-4">
        <input 
            type="text" 
            name="search" 
            class="form-control me-2" 
            placeholder="Search posts"
            value="<?php echo htmlspecialchars($search); ?>"
        >
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <?php if (mysqli_num_rows($result) > 0) { ?>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h4 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h4>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                    <small class="text-muted"><?php echo $row['created_at']; ?></small>
                    <br><br>

                    <?php if ($_SESSION['role'] == 'admin') { ?>
                        <a class="btn btn-warning btn-sm" href="edit_post.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <a class="btn btn-danger btn-sm" href="delete_post.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                   <?php } ?>
</div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div class="alert alert-info">No posts found.</div>
    <?php } ?>

    <div class="text-center mt-4">
        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
            <a class="btn btn-outline-primary btn-sm <?php echo ($i == $page) ? 'active' : ''; ?>"
               href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                <?php echo $i; ?>
            </a>
        <?php } ?>
    </div>
</div>

</body>
</html>