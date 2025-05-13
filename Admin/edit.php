<?php   
include "adminsession.php";


// URL ID from GET
$url_id = $_GET['id'];

// Fetch product details to edit
$fetch_product_details = $pdo->prepare("SELECT * FROM products WHERE product_id = :product_id");
$fetch_product_details->execute([":product_id" => $url_id]);
$product_details = $fetch_product_details->fetch();

if(isset($_POST['update'])){
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_status = $_POST['product_status'];

    if(isset($_FILES['product_image']) && $_FILES['product_image']['name'] != ''){
        $filename = $_FILES['product_image']['name'];
        $temploc = $_FILES['product_image']['tmp_name'];
        $dir = "../img/";
        $target = $dir.$filename;
        $type = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $types = ["jpg", "jpeg", "png", "gif"];

        if(!in_array($type, $types)){
            echo "<script>alert('Invalid image type'); window.history.back()</script>";
            exit;
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

        $update_product = $pdo->prepare("UPDATE products SET product_name = :name, product_price = :price, product_status = :product_status, product_image = :img WHERE product_id = :product_id");
        $update_product->execute([
            ":name" => $product_name,
            ":price" => $product_price,
            ":product_status" => $product_status,
            ":img" => $filename,
            ":product_id" => $url_id,
        ]);
    } else {
        $update_product = $pdo->prepare("UPDATE products SET product_name = :name, product_price = :price, product_status = :product_status WHERE product_id = :product_id");
        $update_product->execute([
            ":name" => $product_name,
            ":price" => $product_price,
            ":product_status" => $product_status,
            ":product_id" => $url_id,
        ]);
    }

    if($update_product){
        echo "<script>alert('Product Update Successful'); window.location.href='table.php';</script>";
        exit;
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
            <div class="image-container mb-4">
                <img id="productImagePreview" src="../img/<?php echo $product_details['product_image']?>" alt="<?= htmlspecialchars($product_details['product_image']) ?>" class="img-fluid product-image">
            </div>

            <div class="mb-4">
                <label for="productImage" class="form-label">Upload Product Image</label>
                <input type="file" class="form-control" id="productImage" name="product_image" onchange="previewImage()">
            </div>

            <div class="mb-4">
                <label for="productName" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="productName" name="product_name" value="<?= htmlspecialchars($product_details['product_name']) ?>" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Product Status</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="product_status" id="available" value="Available" <?= ($product_details['product_status'] == "Available") ? "checked" : "" ?>>
                    <label class="form-check-label" for="available">Available</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="product_status" id="unavailable" value="Unavailable" <?= ($product_details['product_status'] == "Unavailable") ? "checked" : "" ?>>
                    <label class="form-check-label" for="unavailable">Unavailable</label>
                </div>
            </div>

            <div class="mb-4">
                <label for="productPrice" class="form-label">Product Price</label>
                <input type="number" class="form-control" id="productPrice" name="product_price" value="<?= htmlspecialchars($product_details['product_price']) ?>" required>
            </div>

            <div class="row justify-content-between">
                <div class="col-auto">
                    <a href="table.php" class="btn btn-danger">Back</a>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary" name="update">Update Product</button>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
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