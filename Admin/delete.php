<?php 
include "adminsession.php";

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
            exit();
        }
    }
    else{
        header("location: table.php");
        exit();
    }
}

if(isset($_POST['delete_selected_users'])){
    if(isset($_POST['user_ids']) && count($_POST['user_ids']) > 0){
        $user_ids = $_POST['user_ids'];
        $placeholder = implode(",",array_fill(0,count($user_ids),"?"));
        try{
            $pdo->beginTransaction();
            $multi_delete = $pdo->prepare("DELETE FROM users WHERE user_id IN ($placeholder)");
            $multi_delete->execute($user_ids);
            $pdo->commit();
            if($multi_delete){
                header("location: manageuser.php");
                exit();
            }
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
    else{
        header("location: manageuser.php");
        exit();
    }
}
 
?>
