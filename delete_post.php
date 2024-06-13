<?php
// delete_post.php

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

    // Prepare a select statement to verify ownership
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

                // Check if the logged-in user is the author of the post
                if ($_SESSION["id"] != $row["user_id"]) {
                    header("location: index.php");
                    exit;
                }

                // If confirmed to delete
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $delete_sql = "DELETE FROM posts WHERE id = ?";

                    if ($delete_stmt = mysqli_prepare($link, $delete_sql)) {
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($delete_stmt, "i", $param_id);

                        // Set parameters
                        $param_id = $post_id;

                        // Attempt to execute the prepared statement
                        if (mysqli_stmt_execute($delete_stmt)) {
                            // Redirect to homepage
                            header("location: index.php");
                            exit();
                        } else {
                            echo "Ada yang salah. Coba lagi nanti.";
                        }

                        // Close statement
                        mysqli_stmt_close($delete_stmt);
                    }
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

// Close connection
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Postingan</title>
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
        .confirm {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .confirm h2 {
            margin-top: 0;
        }
        .confirm p {
            margin-bottom: 15px;
        }
        .confirm form {
            display: inline-block;
        }
        .confirm form input[type="submit"] {
            background-color: #d9534f;
            color: #fff;
            border: none;
            cursor: pointer;
            padding: 10px 20px;
        }
        .confirm form input[type="submit"]:hover {
            background-color: #c9302c;
        }
        .confirm form button {
            background-color: #337ab7;
            color: #fff;
            border: none;
            cursor: pointer;
            padding: 10px 20px;
            margin-left: 10px;
        }
        .confirm form button:hover {
            background-color: #286090;
        }
    </style>
</head>
<body>
    <header>
        <h1>Hapus Postingan</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <div class="confirm">
            <h2>Apakah anda yakin ingin menghapus postingan blog ini??</h2>
            <p>Judul: <?php echo htmlspecialchars($row["title"]); ?></p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $post_id; ?>" method="post">
                <input type="submit" value="Ya, Hapus" class="delete">
            </form>
            <button onclick="location.href='index.php';">Cancel</button>
        </div>
    </div>

    <footer>
        <p style="text-align:center; padding: 10px 0; background: #333; color: #fff; margin-top: 20px;">My Blog &copy; 2024</p>
    </footer>
</body>
</html>
