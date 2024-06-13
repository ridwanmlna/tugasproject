<?php
// edit_post.php

// Include config file
require_once 'config.php';

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect them to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check if the post ID is set in the URL
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $post_id = trim($_GET["id"]);

    // Prepare a select statement
    $sql = "SELECT * FROM posts WHERE id = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_id);

        // Set parameters
        $param_id = $post_id;

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                // Fetch the result row as an associative array
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                // Retrieve individual field values
                $title = $row["title"];
                $content = $row["content"];

                // Check if the logged-in user is the author of the post
                if ($_SESSION["id"] != $row["user_id"]) {
                    header("location: index.php");
                    exit;
                }
            } else {
                // URL doesn't contain a valid ID
                header("location: error.php");
                exit();
            }
        } else {
            echo "Oops! Ada yang salah. Coba lagi nanti.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }
} else {
    // URL doesn't contain an ID
    header("location: error.php");
    exit();
}

// Define variables and initialize with empty values
$title_err = $content_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title
    if (empty(trim($_POST["title"]))) {
        $title_err = "Masukkan judul.";
    } else {
        $title = trim($_POST["title"]);
    }

    // Validate content
    if (empty(trim($_POST["content"]))) {
        $content_err = "Masukkan konten.";
    } else {
        $content = trim($_POST["content"]);
    }

    // Check input errors before updating in database
    if (empty($title_err) && empty($content_err)) {
        // Prepare an update statement
        $sql = "UPDATE posts SET title = ?, content = ? WHERE id = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssi", $param_title, $param_content, $param_id);

            // Set parameters
            $param_title = $title;
            $param_content = $content;
            $param_id = $post_id;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to homepage
                header("location: index.php");
                exit();
            } else {
                echo "Ada yang salah, coba lagi nanti.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Postingan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
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
        form {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form h2 {
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group input[type="submit"] {
            background-color: #04AA6D;
            color: #fff;
            border: none;
            cursor: pointer;
            padding: 10px 20px;
        }
        .form-group input[type="submit"]:hover {
            background-color: #039f5e;
        }
        .form-group .error {
            color: #d9534f;
        }
    </style>
</head>
<body>
    <header>
        <h1>Edit Postingan</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $post_id; ?>" method="post">
            <h2>Edit Postingan Blog Anda</h2>
            <div class="form-group <?php echo (!empty($title_err)) ? 'has-error' : ''; ?>">
                <label>Title</label>
                <input type="text" name="title" value="<?php echo $title; ?>">
                <span class="error"><?php echo $title_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($content_err)) ? 'has-error' : ''; ?>">
                <label>Content</label>
                <textarea name="content" rows="10"><?php echo $content; ?></textarea>
                <span class="error"><?php echo $content_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" value="Submit">
            </div>
        </form>
    </div>

    <footer>
        <p style="text-align:center; padding: 10px 0; background: #333; color: #fff; margin-top: 20px;">My Blog &copy; 2024</p>
    </footer>
</body>
</html>
