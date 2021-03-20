<?php 
	require_once 'config.php';
	session_start();


	//获取当前用户登录信息，没用则自动跳转到登录页面
	function xiu_get_current_user(){
		if (empty($_SESSION['current_login_user'])) {
			//没有登录信息
			header('Location: /admin/login.php');
			exit();
		}
		return $_SESSION['current_login_user'];
	}


	

	//数据库查询函数
	
	function xiu_db_fetch($sql){
		$con = mysqli_connect(WX_DB_HOST,WX_DB_USER,WX_DB_PASS,WX_DB_NAME);
		if (!$con) {
			exit('链接失败');
		}
		$query = mysqli_query($con,$sql);
		if (!$query) {
			return false;
		}
		$result = array();
		while ($row = mysqli_fetch_assoc($query)) {
			$result[] = $row;
		}

		mysqli_free_result($query);
		mysqli_close($con);

		return $result;
	}


	//数据库新增函数
	function xiu_db_execute($sql){
		$con = mysqli_connect(WX_DB_HOST,WX_DB_USER,WX_DB_PASS,WX_DB_NAME);
		if (!$con) {
			exit('链接失败');
		}
		$query = mysqli_query($con,$sql);
		if (!$query) {
			return false;
		}

		$affected_row = mysqli_affected_rows($con);



		// mysqli_free_result($query);
		mysqli_close($con);

		return $affected_row;
	}