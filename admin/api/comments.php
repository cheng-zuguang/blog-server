<?php 
	require_once '../../functions.php';

	$page = empty($_GET['page']) ? 1 : $_GET['page'];
	$length = 30;
	$offset = ($page - 1) * $length;

	$sql = sprintf('select 
	comments.*, 
	posts.title as post_title
	from comments
	inner join posts on comments.post_id = posts.id
	order by comments.created desc
	limit %d, %d',$offset, $length);

	$comments = xiu_db_fetch($sql);

	$totalCount = xiu_db_fetch('select 
	count(1) as count
	from comments
	inner join posts on comments.post_id = posts.id')[0]['count'];
	$totalPages = ceil($totalCount / $length);

	$json = json_encode(array(
		'totalPages' => $totalPages,
		'comments' => $comments

		));

	header('Content-Type: application/json');

	echo $json;

