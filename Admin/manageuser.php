<?php 
include "adminsession.php";

try {
    $pdo->beginTransaction();
    $fetch_user = $pdo->query("SELECT * FROM users WHERE user_role = 'User'");
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo $e->getMessage();
}

if (isset($_POST['reset_password'])) {
    $newpass_raw = $_POST['newpass'];
    $confpass_raw = $_POST['confpass'];
    $user_id = $_POST['user_id'];
    $oldpass = $_POST['oldpass'];

    $newpass = md5($newpass_raw);
    $confpass = md5($confpass_raw);

    if ($newpass === $oldpass) {
        echo "<script>alert('New password cannot be the same as the old password.');</script>";
    } elseif ($newpass !== $confpass) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        try {
            $pdo->beginTransaction();
            $update_password = $pdo->prepare("UPDATE users SET user_password = :password WHERE user_id = :user_id");
            $update_password->execute([
                ":password" => $newpass,
                ":user_id" => $user_id
            ]);
            $pdo->commit();

            echo "<script>
                alert('Password updated successfully.');
                window.location.href = 'manageuser.php';
            </script>";
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Miras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

    <style>
        th, td {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container my-4 d-flex justify-content-between flex-wrap gap-2">
    <div class="d-flex flex-wrap gap-2">
        <a href="index.php" class="btn btn-success">Sales View</a>
        <a href="history.php" class="btn btn-info text-white">Sales History</a>
        <a href="manageuser.php" class="btn btn-primary">Manage User</a>
        <a href="table.php" class="btn btn-warning">All Products</a>
        <a href="shopview.php" class="btn btn-danger">Shop View</a>
    </div>
    <div>
        <a href="../logout.php" class="btn btn-dark">Logout</a>
    </div>
</div>

<div class="container my-4">
    <h1 class="text-center fw-bold text-primary mb-4">MIRA'S USERS</h1>
    
    <div class="table-responsive">
        <form action="delete.php" method="post">
            <div class="d-flex justify-content-between mb-2">
                <a href="adduser.php" class="btn btn-success">Add User</a>
                <button name="delete_selected_users" class="btn btn-danger">Delete Selected</button>
            </div>
            <table class="table table-bordered table-striped">
                <?php if ($fetch_user->rowCount() < 1): ?>
                    <hr>
                    <h2 class="text-center">No Users Found</h2>
                <?php else: ?>
                    <thead>
                        <tr>
                            <th style = "text-align:center">ID</th>
                            <th style = "text-align:center">Username</th>
                            <th style = "text-align:center">Role</th>
                            <th style = "text-align:center">Actions</th>
                            <th style = "text-align:center">
                                <button type="button" class="btn btn-warning btn-sm" id="select-all">Select All</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $fetch_user->fetch()) { ?>
                            <tr>
                                <td><?= $user['user_id'] ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['user_role']) ?></td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" 
                                            data-user-id="<?= $user['user_id'] ?>" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#resetPasswordModal">
                                        <i class="bi bi-key"></i>
                                    </button>
                                </td>
                                <td>
                                    <input type="checkbox" class="delete-checkbox" name="user_ids[]" value="<?= $user['user_id'] ?>">
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                <?php endif; ?>
            </table>
        </form>
    </div>

    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="reset-password"></div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    document.getElementById('resetPasswordModal').addEventListener("show.bs.modal", function(event) {
        const button = event.relatedTarget;
        const userId = button.getAttribute("data-user-id");
        const target = document.getElementById("reset-password");

        let xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                target.innerHTML = xhr.responseText;
                attachResetValidation(); 
            }
        };
        xhr.open("GET", "resetpassword.php?userid=" + userId, true);
        xhr.send();
    });

    function attachResetValidation() {
        const resetBtn = document.getElementById('reset_password');
        if (!resetBtn) return;

        resetBtn.addEventListener("click", function(e) {
            const oldpass = document.getElementById('oldpass').value;
            const newpassRaw = document.getElementById('newpass').value;
            const confpassRaw = document.getElementById('confpass').value;

            const newpass = CryptoJS.MD5(newpassRaw).toString();
            const confpass = CryptoJS.MD5(confpassRaw).toString();

            if (oldpass === newpass) {
                alert("New password cannot be the same as the old password!");
                e.preventDefault();
            } else if (newpass !== confpass) {
                alert("Passwords do not match!");
                e.preventDefault();
            }
        });
    }


    document.getElementById('select-all').addEventListener("click", () => {
        const checkboxes = document.querySelectorAll('.delete-checkbox');
        const shouldCheck = Array.from(checkboxes).some(checkbox => !checkbox.checked);
        checkboxes.forEach(checkbox => checkbox.checked = shouldCheck);
    });
</script>
</body>
</html>
