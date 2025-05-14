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
        .nav-row {
            background-color: #ffffff;
            border-radius: 12px;
        }

        .nav-button {
            padding: 0.45rem 1.1rem;
            background-color: #f2f2f2;
            color: #333;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .nav-button:hover,
        .nav-button:focus {
            background-color: #e0e0e0;
            color: #111;
            transform: translateY(-1px);
        }

        .nav-button.active {
            background-color: #dbeafe;
            color: #1d4ed8;
            font-weight: 600;
        }

        .logout {
            background-color: #ffe5e5;
            color: #c0392b;
        }

        .logout:hover,
        .logout:focus {
            background-color: #ffd6d6;
            color: #922b21;
        }
        .form-check-input {
            width: 20px;
            height: 20px;
            border: 2px solid black;
            border-radius: 4px;
            appearance: none; /* Remove default styling */
            -webkit-appearance: none;
            outline: none;
            cursor: pointer;
            position: relative;
            background-color: white;
            transition: all 0.2s ease-in-out;
        }

        .form-check-input:checked {
            background-color: #198754; /* Bootstrap success green */
            border-color: #198754;
        }
    </style>
</head>
<body>
<!-- Header Navigation -->
<div class="container my-4 mt-0">
  <div class="nav-row d-flex flex-wrap justify-content-between align-items-center gap-3 p-3 rounded">
    
    <div class="d-flex flex-wrap gap-2 align-items-center">
      <a href="index.php" class="nav-button <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Sales View</a>
      <a href="history.php" class="nav-button <?= basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : '' ?>">Sales History</a>
      <a href="manageuser.php" class="nav-button <?= basename($_SERVER['PHP_SELF']) == 'manageuser.php' ? 'active' : '' ?>">Manage User</a>
      <a href="table.php" class="nav-button <?= basename($_SERVER['PHP_SELF']) == 'table.php' ? 'active' : '' ?>">All Products</a>
      <a href="shopview.php" class="nav-button <?= basename($_SERVER['PHP_SELF']) == 'shopview.php' ? 'active' : '' ?>">Shop View</a>
    </div>

    <div>
      <a href="../logout.php" class="nav-button logout">Logout</a>
    </div>

  </div>
</div>

<div class="container my-4">
    <h1 class="text-center fw-bold text-primary mb-4">MIRA'S USERS</h1>
    
    <div class="table-responsive" style="overflow-x: hidden; width: 100%;">
        <form action="delete.php" method="post">
            <div class="row justify-content-between mb-3">
                <div class="col-auto">
                    <a href="adduser.php" class="btn btn-success">Add User</a>
                </div>
                <div class="col-auto">
                    <?php if ($fetch_user->rowCount() !== 0): ?>
                        <button name="delete_selected_users" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete the selected users?')">Delete Selected</button>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($fetch_user->rowCount() === 0): ?>
                <div class="text-center my-4">
                    <h2>No Users Found</h2>
                </div>
            <?php else: ?>
                <table class="table table-bordered align-middle" style="width: 100%; table-layout: auto;">
                    <thead class="table-dark text-center">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Username</th>
                            <th scope="col">Role</th>
                            <th scope="col">Actions</th>
                            <th scope="col">
                                <button type="button" class="btn btn-warning btn-sm" id="select-all">Select All</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $fetch_user->fetch()): ?>
                            <tr>
                                <td class="text-center"><?= $user['user_id'] ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($user['user_role']) ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-sm" 
                                            data-user-id="<?= $user['user_id'] ?>" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#resetPasswordModal">
                                        <i class="bi bi-key"></i>
                                    </button>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input delete-checkbox" name="user_ids[]" value="<?= $user['user_id'] ?>">
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
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
