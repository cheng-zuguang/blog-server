<?php 
	require_once '../functions.php';

	if(empty($_GET['id']) ){
		exit('none');
	}

	$id = $_GET['id'];
	

	$row = xiu_db_execute('delete from posts where id in (' . $id . ');');
	
	// if ($row > 0) {
	// 	echo "删除成功";
	// }
	// http中的 referer 用来标识当前页面请求来源
	header('Location: '. $_SERVER['HTTP_REFERER']);

