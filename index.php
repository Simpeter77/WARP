<?php 
session_start();
include "dbconfig.php";
include "style.php"; // Make sure this includes Bootstrap 5 CSS

if(isset($_SESSION["USER"])){
    $user_session = $_SESSION['USER'];
    if($user_session['user_role'] == "Admin"){
        header("location: Admin/");
    } else {
        header("location: Users/");
    }
}

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $fetch_user = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $fetch_user->execute([":username" => $username]);

    if($fetch_user->rowCount() < 1){
        echo "<script>alert('Login Failed');</script>";
    } else {
        $user = $fetch_user->fetch();
        if($user['user_role'] == "Admin"){
            if($user['user_password'] == $password){
                $_SESSION['USER'] = $user;
                header("location: Admin/");
            } else {
                echo "<script>alert('Login Failed');</script>";
            }
        } else {
            if($user['user_password'] == md5($password)){
                $_SESSION['USER'] = $user;
                header("location: Users/");
            } else {
                echo "<script>alert('Login Failed');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mira's</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f9fafb;
        }

        .login-card {
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
            padding: 2rem;
            width: 100%;
            max-width: 420px;
        }

        .login-header {
            font-weight: bold;
            color: #1d4ed8;
        }

        .form-label {
            font-weight: 500;
        }

        .btn-primary {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            border-color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <form method="POST" class="login-card">
            <h1 class="text-center mb-3 login-header">Welcome to Mira's</h1>
            <p class="text-center mb-4 text-muted">Login to continue</p>
            
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter username" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>

            <button name="login" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</body>
</html>
