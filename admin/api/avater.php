<?php 
	require_once '../../config.php';
	if (empty($_GET['email'])) {
		exit('<h1>无</h1>');
	}

	$email = $_GET['email'];

	//链接数据库
    $con  = mysqli_connect(WX_DB_HOST,WX_DB_USER,WX_DB_PASS,WX_DB_NAME);

    if(!$con){
      exit("<h1>链接失败</h1>");
    }
    $query = mysqli_query($con,"select avatar from users where email = '{$email}' limit 1;");
    // var_dump($query);
    if (!$query) {
      exit('<h1>查询失败</h1>');
    }
    //取出数据
    $res = mysqli_fetch_assoc($query);
    
    echo $res['avatar'];


