<?php 

//接受数据

if(empty($_FILES['avatar'])){
	exit('必须上传文件');
}

$avatar = $_FILES['avatar'];
//判断是否上传成功

if($avatar['error'] !== UPLOAD_ERR_OK)
{
	exit('上传失败');
}

//校验文件的大小
if ($avatar['size'] < 1 * 1024 ) {
	exit('过小');
}

if ($avatar['size'] > 10 * 1024 * 1024) {
	exit('过大');
}

//移动到网站的目录下
$ext = pathinfo($avatar['name'],PATHINFO_EXTENSION);

$target = '../../static/uploads/img-' . uniqid() . '.' . $ext;

if (!move_uploaded_file($avatar['tmp_name'], $target)) {
	exit('上传失败');
}

echo  substr($target,5);	