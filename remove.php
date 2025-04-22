<?php   
    include("../sss/dbconfig.php");

	$id = $_GET['ID'];

	$delete = $pdo->prepare("DELETE FROM products WHERE product_id = :id");
	$delete->execute([
		":id" => $id
	]);

	if($id){
		header("location:index.php");
	}
?>

