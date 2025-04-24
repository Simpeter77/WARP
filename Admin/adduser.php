<?php 
#essentials
session_start();
include "../dbconfig.php";
include "../style.php";
#sessions
if(!isset($_SESSION['USER'])){
    header("location: logout.php");
}

if(isset($_SESSION['USER'])){
    $user_session = $_SESSION['USER'];
    if($user_session['user_role'] != "Admin"){
        header("location: ../Users/");
    }
}
#end of essentials

if(isset($_POST['add'])){
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $insert_user = $pdo->prepare("INSERT INTO users(username, user_password, user_role) VALUES(:username, :password, :role)");
    $insert_user->execute([
        ":username" =>$username,
        ":password" => $password,
        ":role" => "User",
    ]);
    if($insert_user){
        echo" 
            <script>
                alert('Added User {$username}');
                window.location.href='../';
            </script>
        ";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add a user</title>
</head>
<body>
    <div class="container my-5">
        <h2 class="text-center mb-4">Add a User</h2>
        <form action="" method="post" class="bg-white p-4 rounded shadow-sm">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter Username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter Password" id="password" required>
            </div>
            <div class="mb-3">
                <label for="confirmpassword" class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" id="confirmpassword" required>
            </div>
            <div class="row justify-content-between">
                <button id="adduser" name="add" class="btn btn-primary col-md-3">Add User</button>
                <a href="../" class="btn btn-secondary ms-2 col-md-3">Back</a>
            </div>
        </form>
    </div>

    <script>
        const adduser = document.getElementById('adduser');
        adduser.addEventListener("click", function(event) {
            const pass = document.getElementById('password').value;
            const confpass = document.getElementById('confirmpassword').value;
            if (pass !== confpass) {
                alert("Password Does Not Match");
                event.preventDefault();
            }
        });
    </script>
</body>
</html>
