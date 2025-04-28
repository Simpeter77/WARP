<?php   
# Essentials
session_start();
include "../dbconfig.php";
include "../style.php";

# Sessions
if(!isset($_SESSION['USER'])){
    header("location: logout.php");
}

if(isset($_SESSION['USER'])){
    $user_session = $_SESSION['USER'];
    if($user_session['user_role'] != "Admin"){
        header("location: ../Users/");
    }
}
# End of essentials

// URL ID from GET
$url_id = $_GET['id'];

// Fetch product details to edit
$fetch_product_details = $pdo->prepare("SELECT * FROM products WHERE product_id = :product_id");
$fetch_product_details->execute([":product_id" => $url_id]);
$product_details = $fetch_product_details->fetch();

if(isset($_POST['update'])){
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];

    if(isset($_FILES['product_image']) && $_FILES['product_image']['name'] != ''){
        $filename = $_FILES['product_image']['name'];
        $temploc = $_FILES['product_image']['tmp_name'];
        $dir = "../img/";
        $target = $dir.$filename;
        $type = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $types = ["jpg", "jpeg", "png", "gif"];

        if(!in_array($type, $types)){
            echo "<script>alert('Invalid image type'); window.history.back()</script>";
        } else {
            if(file_exists($target)){
                $counter = 1;
                $file_base = pathinfo($filename, PATHINFO_FILENAME);
                do {
                    $new_filename = $file_base . "($counter)." . $type;
                    $target = $dir . $new_filename;
                    $counter++;
                } while(file_exists($target));
                $filename = $new_filename;
            }
            move_uploaded_file($temploc, $target);
        }
        $update_product = $pdo->prepare("UPDATE products SET product_name = :name, product_price = :price, product_image = :img WHERE product_id = :product_id");
        $update_product->execute([
            ":name" => $product_name,
            ":price" => $product_price,
            ":img" => $filename,
            ":product_id" => $url_id,
        ]);
    } else {
        $update_product = $pdo->prepare("UPDATE products SET product_name = :name, product_price = :price WHERE product_id = :product_id");
        $update_product->execute([
            ":name" => $product_name,
            ":price" => $product_price,
            ":product_id" => $url_id,
        ]);
    }

    if($update_product){
        echo "<script>alert('Product Update Successful'); window.location.href='index.php';</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .image-container {
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 400px;
            max-height: 300px;
            overflow: hidden;
            margin: 0 auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Edit Product</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="image-container">
                <img id="productImagePreview" src="../img/<?php echo $product_details['product_image']?>" alt="<?= $product_details['product_image']?>" class="img-fluid product-image">
            </div>

            <div class="mb-4">
                <label for="productImage" class="form-label">Upload Product Image</label>
                <input type="file" class="form-control" id="productImage" name="product_image" onchange="previewImage()">
            </div>

            <div class="mb-4">
                <label for="productName" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="productName" name="product_name" value="<?= $product_details['product_name'] ?>" required>
            </div>

            <div class="mb-4">
                <label for="productPrice" class="form-label">Product Price</label>
                <input type="number" class="form-control" id="productPrice" name="product_price" value="<?= $product_details['product_price'] ?>" required>
            </div>

            <div class="row justify-content-between">
                <div class="col-auto">
                    <a href="index.php" class="btn btn-danger">Back</a>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary" name="update">Update Product</button>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <script>
        function previewImage() {
            const file = document.getElementById("productImage").files[0];
            const reader = new FileReader();

            reader.onloadend = function () {
                const imgElement = document.getElementById("productImagePreview");
                imgElement.src = reader.result;
            }

            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
