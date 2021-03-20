<?php 
  require_once '../functions.php';
  xiu_get_current_user();

  /*分页数据和大小*/
  $page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
  
  $size = 20;
  $offset = ($page - 1) * $size;
  /*分页数据和大小*/

  $where = '1 = 1';
  $search = '';
  if (isset($_GET['category']) && $_GET['category'] !== 'all') {
      $where .= ' and posts.category_id =' . $_GET['category'];
      $search .= '&category=' . $_GET['category'];
  }
  if (isset($_GET['status']) && $_GET['status'] !== 'all') {
      $where .= " and posts.status = '{$_GET['status']}'" ;
      $search .= '&status=' . $_GET['status'];
  }
  

  if ($page < 1) {
    header('Location: /admin/posts.php?page=1'.$search);
  }
  // if (!empty($_GET['status'])) {
  //     $where .=  " and posts.status ='{$_GET['status']}'";
  // }
  /*数据库查询关联数据*/
  $posts = xiu_db_fetch("select 
    posts.id,
    posts.title,
    users.nickname as user_name,
    categories.name as category_name,
    posts.created,
    posts.status
    from posts
    inner join categories on posts.category_id = categories.id
    inner join users on posts.user_id = users.id
    where {$where}
    order by posts.created desc
    limit {$offset}, {$size};");
  /*数据库查询关联数据*/
  $categories = xiu_db_fetch('select * from categories;');
  /*设置分页页码*/

  //获取最大页码数
  $total_count = xiu_db_fetch("select 
    count(1) as count
    from posts
    inner join categories on posts.category_id = categories.id
    inner join users on posts.user_id = users.id
    where {$where}");
  $total_pages = (int)ceil($total_count[0]['count'] / $size);

  //判断用户输入的页码数是否大于$total_pages
  if ($page > $total_pages) {
    header('Location: /admin/posts.php?page='.$total_pages . $search);
  }
  // var_dump($total_pages);
  // var_dump($total_count);
  
  



  //显示的分页页码
  $visible = 9;

  //计算最大和最小的页码
  $begin = $page - ($visible - 1) / 2;
  $end = $begin + $visible - 1;

  //判断begin小于0 和 end大于总页码数的问题
  $begin = $begin<1 ? 1 : $begin;  //保证$begin不小于1
  $end = $begin + $visible - 1;               //因为上一行的$begin数值改变了，所以end也需要改变
  $end = $end > $total_pages ? $total_pages : $end;  //判断end是否超出最大值
  $begin = $end - $visible + 1;               //同上
  $begin = $begin<1 ? 1 : $begin;  //再次确保begin不小于1

  /*设置分页页码*/


  /*
    出版状态转化函数
    input: string
    return : string
   */
  function convert_charset($status){
      $staName =  array(
        'published' => '已发表', 
        'drafted' => '草稿',
        'trashed' => '回收站'
      );

      return isset($staName[$status]) ? $staName[$status] : '未知'; 
  }

  /*
    出版时间转化函数
    input: string
    return : string
   */
  // echo "1111";
  function convert_date($createdTime){
    // echo "1111";
    // var_dump($createdTime);
    $timeStmp = strtotime($createdTime);

    $time = date('Y年m月d日<b\r>H:i:s',$timeStmp);
    return $time;
  }


?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
     <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="/admin/post-delete.php" style="display: none" id="deleteAll">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF'] ?>">
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach ($categories as $item): ?>
                <option value="<?php echo $item['id'] ?>" <?php echo isset($_GET['category']) && $_GET['category'] === $item['id'] ? 'selected':''; ?>><?php echo $item['name'] ?></option>
            <?php endforeach ?>
          </select>

          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="drafted"<?php echo isset($_GET['status']) && $_GET['status'] == 'drafted' ? ' selected' : ''; ?>>草稿</option>
            <option value="published"<?php echo isset($_GET['status']) && $_GET['status'] == 'published' ? ' selected' : ''; ?>>已发布</option>
            <option value="trashed"<?php echo isset($_GET['status']) && $_GET['status'] == 'trashed' ? ' selected' : ''; ?>>回收站</option>
          </select>

          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="?page=<?php echo ($page-1); ?>">上一页</a></li>
          <?php for ($i = $begin; $i <= $end; $i++): ?>
            <li <?php echo $i===$page ? 'class="active"':''; ?>><a href="?page=<?php echo $i . $search; ?>"><?php echo $i ; ?></a></li>
          <?php endfor ?>
          
          <li><a href="?page=<?php echo ($page+1); ?>">下一页</a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"  id="checkAllBox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody id="tb">
          <?php foreach ($posts as $item): ?>
            <tr>
              <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id'];?>"></td>
              <td><?php echo $item['title'];  ?></td>
              <td><?php echo $item['user_name'];  ?></td>
              <td><?php echo $item['category_name'];  ?></td>
              <td class="text-center"><?php echo  convert_date($item['created']);?></td>
              <td class="text-center"><?php echo convert_charset($item['status']);  ?></td>
              <td class="text-center">
                <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
                <a href="/admin/post-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
              </td>
          </tr>
          <?php endforeach ?>
         
        </tbody>
      </table>
    </div>
  </div>
  <?php $current_page ='posts'; ?>
  <?php include 'inc/silderbar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
  <script type="text/javascript">
     $(function(){

    let checkAllBox = $('#checkAllBox');  //全选复选框
    var tbody_checkBox = $('#tb').find('input'); //所有的单选复选框
    let deleteAll = $('#deleteAll');

    var checkedInputId = [];
    tbody_checkBox.on('change',function(){

      //H5新特性：自定义属性统一使用 data-xxx来命名，添加的自定义属性可以通过dataSet来访问
      var id = $(this).data('id');
      // console.log(id);
      // 根据是否选中复选框来添加id
      if ($(this).prop('checked') && checkedInputId.length < 10) {
        //判断是否存在id
         checkedInputId.includes(id) || checkedInputId.push(id);
      }else{
        checkedInputId.splice(checkedInputId.indexOf(id),1);
      }
      // console.log(checkedInputId);
      checkedInputId.length ? deleteAll.fadeIn() : deleteAll.fadeOut();

      deleteAll.prop('search','?id=' + checkedInputId);
    });

    //全选和全不选
    checkAllBox.click(function(){
      tbody_checkBox.prop('checked',checkAllBox.prop('checked')).trigger('change'); //指定触发事件
    });

  });
  </script>
    
</body>
</html>