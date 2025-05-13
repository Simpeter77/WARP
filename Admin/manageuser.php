<?php 
include "adminsession.php";
try{
    $pdo->beginTransaction();
    $fetch_user = $pdo->query("SELECT * FROM users WHERE user_role = 'User'");
}catch(Exception $e){
    $pdo->rollBack();
    echo $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Miras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<!-- Header Buttons -->
<div class="container my-4 d-flex justify-content-between flex-wrap gap-2">
    <div class="d-flex flex-wrap gap-2">
        <a href="index.php" class="btn btn-success btn-custom">Sales View</a>
        <a href="history.php" class="btn btn-info text-white btn-custom">Sales History</a>
        <a href="manageuser.php" class="btn btn-primary btn-custom">Manage User</a>
        <a href="table.php" class="btn btn-warning btn-custom">All Products</a>
        <a href="shopview.php" class="btn btn-danger btn-custom">Shop View</a>
    </div>
    <div>
        <a href="../logout.php" class="btn btn-dark btn-custom">Logout</a>
    </div>
</div>

<div class="container my-4">
    <h1 class="text-center fw-bold text-primary mb-4">MIRA'S USERS</h1>
    
    <div class="table-responsive">
        <form action="delete.php" method="post">
            <div class="justify-content-between ">
                <a href="adduser.php" class="btn btn-success">Add User</a>
                <button name="delete_selected" class="btn btn-danger mb-2 float-end">Delete Selected</button>
            </div>
            
            <table class="table table-bordered table-striped">
                <?php if($fetch_user->rowCount()<1):?>
                    <center><h2>No Users Found</h2></center>
                <?php else:?>
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Actions</th>
                            <th>
                                <button type="button" class="btn btn-warning btn-sm mx-auto" id="select-all">Select All</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = $fetch_user->fetch()){?>
                            <tr>
                                <td><?= $user['user_id']?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['user_role']) ?></td>
                                <td>
                                    <a href="edit.php?id=<?= $user['user_id']?>" class="btn btn-primary btn-sm">Edit</a>
                                </td>
                                <td>
                                    <input type="checkbox" class="delete-checkbox" name="user_ids[]" value="<?= $user['user_id']?>">
                                </td>
                            </tr>
                        <?php };?>
                    </tbody>
                <?php endif?>
            </table>
        </form>
    </div>
</div>

<script>
    const selectAll = document.getElementById('select-all');
    selectAll.addEventListener("click", () => {
        let checkboxes = document.querySelectorAll('.delete-checkbox');
        let isChecked = Array.from(checkboxes).some(checkbox => !checkbox.checked);
        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
    });
</script>
</body>
</html>
