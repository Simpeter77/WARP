<?php 
    session_start();
    include "dbconfig.php";
    include "style.php";

    if(isset($_SESSION["USER"])){
        $user_session = $_SESSION['USER'];
        if($user_session['user_role'] == "Admin"){
            header("location: Admin/");
        }
        else{
            header("location: Users/");
        }
    }

    if(isset($_POST['login'])){
        $username = $_POST['username'];
        $password = $_POST['password'];

        $fetch_user = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $fetch_user->execute([":username" => $username]);

        if($fetch_user->rowCount()<1){
            echo "
                <script>
                    alert('Login Failed');
                </script>
            ";
        }
        else{
            $user = $fetch_user->fetch();
            if($user['user_role'] == "Admin"){
                if($user['user_password'] == $password){
                    $_SESSION['USER'] = $user;
                    header("location: Admin/");
                }
                else{
                    echo "
                        <script>
                            alert('Login Failed');
                        </script>
                    ";
                }
            }
            else{
                if($user['user_password'] == md5($password)){
                    $_SESSION['USER'] = $user;
                    header("location: Users/");
                }
                else{
                    echo "
                        <script>
                            alert('Login Failed');
                        </script>
                    ";
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
    <title>Login - Miras</title>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <form method="POST" class="w-100" style="max-width: 400px;">
            <h1 class="text-center mb-4">Welcome to Mira's</h1>
            <h2 class="text-center mb-4">Login</h2>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter Username" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
            </div>
            <button name="login" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</body>
</html>
