<?php 
	require_once '../functions.php';

	if(empty($_GET['id']) ){
		exit('none');
	}

	$id = $_GET['id'];
	

	$row = xiu_db_execute('delete from categories where id in (' . $id . ');');
	
	// if ($row > 0) {
	// 	echo "删除成功";
	// }
	header('Location: categories.php');

