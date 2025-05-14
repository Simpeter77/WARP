<?php 
include "adminsession.php";

$user_id = $_GET['userid'] ?? null;

if (!$user_id) {
    echo "Invalid user.";
    exit;
}

$fetch_user = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
$fetch_user->execute([":user_id" => $user_id]);
$user = $fetch_user->fetch();

if (!$user) {
    echo "User not found.";
    exit;
}
?>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<form action="" method="post">
    <label for="username" class="form-label">Username:</label>
    <input type="text" class="form-control mb-2" value="<?= htmlspecialchars($user['username']) ?>" disabled>

    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
    <input type="hidden" name="oldpass" value="<?= $user['user_password'] ?>">

    <label for="newpass" class="form-label">Enter New Password:</label>
    <input type="password" id="newpass" name="newpass" class="form-control mb-2" required placeholder="Enter New Password">

    <label for="confpass" class="form-label">Confirm Password:</label>
    <input type="password" id="confpass" name="confpass" class="form-control mb-2" required placeholder="Confirm New Password">

    <button type="submit" name="reset_password" class="btn btn-success mt-2">Save</button>
</form>
