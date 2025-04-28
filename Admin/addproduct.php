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
#end of essesntials


if(isset($_POST['add'])){
    $filename = $_FILES['product_image']['name'];
    $temploc = $_FILES['product_image']['tmp_name'];
    $dir = "../img/";
    $target = $dir.$filename;
    $type = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
    $types = ["jpg", "jpeg", "png", "gif"];
    if(!in_array($type,$types)){
        echo "<script>alert('Invalid image type'); window.history.back()</script>";
    }
    else{
        if(file_exists($target)){
            $counter = 1;
            $file_base = pathinfo($filename, PATHINFO_FILENAME);
            do {
                $new_filename = $file_base . "($counter)." . $type;
                $target = $dir . $new_filename;
                $counter++;
            } while (file_exists($target));
            $filename = $new_filename;
        }
        move_uploaded_file($temploc,$target);
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];

        $insert = $pdo->prepare("INSERT INTO products(product_name, product_price, product_image) VALUES(:name, :price, :img)");
        $insert->execute([
            ":name" => $product_name,
            ":price" => $product_price,
            ":img" => $filename,
        ]);
        if($insert){
            echo "<script>alert('Product inserted'); window.location.href='index.php';</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Add Product</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="productImage" class="form-label">Upload Product Image</label>
                <input type="file" class="form-control" id="productImage" name="product_image" required>
            </div>

            <div class="mb-4">
                <label for="productName" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="productName" name="product_name" required>
            </div>


            <div class="mb-4">
                <label for="productPrice" class="form-label">Product Price</label>
                <input type="number" class="form-control" id="productPrice" name="product_price" required>
            </div>
            <div class="row justify-content-between">
                <div class="col-auto">
                    <button type="submit" class="btn btn-success" name="add">Add Product</button>
                </div>
                <div class="col-auto">
                    <a href="index.php" class="btn btn-danger">Back</a>
                </div>
            </div>
            
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>