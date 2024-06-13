<?php
// index.php

// Include config file
require_once 'config.php';

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect them to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Query to fetch all posts
$sql = "SELECT p.id, p.title, p.content, p.created_at, p.user_id, u.username 
        FROM posts p 
        INNER JOIN users u ON p.user_id = u.id 
        ORDER BY p.created_at DESC";

$result = mysqli_query($link, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Blog</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            padding-top: 20px;
        }
        header {
            background: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }
        header h1 {
            margin: 0;
        }
        nav {
            margin: 10px 0;
            text-align: center;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
        }
        nav a:hover {
            text-decoration: underline;
        }
        .button-container {
            text-align: right;
            margin-bottom: 20px;
        }
        .button-container a {
            background-color: #04AA6D;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }
        .button-container a:hover {
            background-color: #039f5e;
        }
        .post {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .post h2 {
            margin-top: 0;
            color: #333;
        }
        .post p {
            margin-bottom: 10px;
            color: #555;
        }
        .post .meta {
            font-size: 12px;
            color: #999;
            margin-bottom: 10px;
        }
        .post a {
            color: #04AA6D;
            text-decoration: none;
            margin-right: 10px;
        }
        .post a:hover {
            text-decoration: underline;
        }
        footer {
            background: #333;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>My Blog</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <div class="button-container">
            <a href="new_post.php">Create New Post</a>
        </div>
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>
        <div class="posts">
            <?php
            if (mysqli_num_rows($result) > 0) {
                // Output data of each row
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="post">';
                    echo '<h2>' . htmlspecialchars($row["title"]) . '</h2>';
                    echo '<p>' . nl2br(htmlspecialchars($row["content"])) . '</p>';
                    echo '<div class="meta">Posted by ' . htmlspecialchars($row["username"]) . ' on ' . htmlspecialchars(date("M d, Y H:i:s", strtotime($row["created_at"]))) . '</div>';
                    
                    // Only show edit and delete buttons if the logged-in user is the author
                    if ($_SESSION["id"] == $row["user_id"]) {
                        echo '<p><a href="edit_post.php?id=' . $row["id"] . '">Edit</a> | <a href="delete_post.php?id=' . $row["id"] . '">Delete</a></p>';
                    }
                    
                    echo '</div>';
                }
            } else {
                echo '<p>Tidak ada postingan.</p>';
            }
            ?>
        </div>
    </div>

    <footer>
        <p>My Blog &copy; 2024</p>
    </footer>
</body>
</html>

<?php
// Close connection
mysqli_close($link);
?>
