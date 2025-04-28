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
// for single deletion
if(isset($_GET['id'])){
    $url_id = $_GET['id'];
    $delete_statement = $pdo->prepare("DELETE FROM products WHERE product_id = :id");
    $delete_statement->execute([":id" => $url_id]);
    if($delete_statement){
        header("location: index.php");
    }
}

if(isset($_POST['delete_selected'])){
    if(isset($_POST['product_ids']) && count($_POST['product_ids']) > 0){
        $product_ids = $_POST['product_ids'];
        $placeholder = implode(",",array_fill(0,count($product_ids),"?"));
        $multi_delete = $pdo->prepare("DELETE FROM products WHERE product_id IN ($placeholder)");
        $multi_delete->execute($product_ids);
        if($multi_delete){
            header("location: table.php");
        }
    }
    else{
        header("location: table.php");
    }
}
 
?>
