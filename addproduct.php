<?php   
    include("../sss/dbconfig.php");
    if(isset($_POST['add'])){
        $filename = $_FILES['product_image']['name'];
        $temploc = $_FILES['product_image']['tmp_name'];
        $dir = "img/";
        $target = $dir.$filename;
        $type = strtolower(basename(pathinfo($temploc,PATHINFO_EXTENSION)));
        $types = ["jpg", "jpeg", "png", "gif"];
        if(!in_array($type,$types)){
            echo "<script>alert('Invalid image type') window.history.back()</script>";
        }
        else{
            if(!file_exists($target)){
                move_uploaded_file($temploc,$target);
            }
        }
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];

        $insert = $pdo->prepare("INSERT INTO products(product_name, product_price, product_img) VALUES(:name, :price, :img)");
        $insert->execute([
            ":name" => $product_name,
            ":price" => $product_price,
            ":img" => $target,
        ]);
        if($insert){
            echo "
                <script>alert('product inserted') window.location.href='index.php'</script>
            ";
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
                <input type="file" class="form-control" id="productImage" name="product_image">
            </div>

            <div class="mb-4">
                <label for="productName" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="productName" name="product_name" required>
            </div>


            <div class="mb-4">
                <label for="productPrice" class="form-label">Product Price</label>
                <input type="number" class="form-control" id="productPrice" name="product_price" required>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary" name = "add">Add Product</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>
