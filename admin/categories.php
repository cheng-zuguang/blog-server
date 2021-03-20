<?php 

  require_once '../functions.php';
  xiu_get_current_user();

  /*新增功能（需在查询语句的上面，这样在新增后就可以将新的数据显示在页面）
    1.表单校验
    2.数据库新增
    3.持久化
    4.响应
  */  
   //判断是否为编辑模式
  if (!empty($_GET['id'])) {
      //根据id查询数据库
      //
      $cureent_edit_category = xiu_db_fetch('select * from categories where id=' . $_GET['id']);
      // var_dump($cureent_edit_category);
  }

  function category_add(){
    if(empty($_POST['name']) || empty($_POST['slug'])){
      $GLOBALS['message'] = '请完整填写表单';
      $GLOBALS['success'] = false;  
      return;
    }

    $name = $_POST['name'];
    $slug = $_POST['slug'];

    //调用新增函数并返回受影响的行数
    $row = xiu_db_execute("insert into categories values (null,'{$name}','{$slug}');");
    
    $GLOBALS['message'] = $row <= 0 ? "添加失败": "添加成功";
    $GLOBALS['success'] = $row > 0;  
  }

  function category_edit(){
    global $cureent_edit_category;


    $id = $cureent_edit_category[0]['id'];
    $name = empty($_POST['name']) ? $cureent_edit_category[0]['name'] : $_POST['name'];
    $cureent_edit_category[0]['name'] = $name;
    $slug = empty($_POST['slug']) ? $cureent_edit_category[0]['slug'] : $_POST['slug'];
    $cureent_edit_category[0]['slug'] = $slug;
    //调用新增函数并返回受影响的行数
    $row = xiu_db_execute("update categories set slug='{$slug}', name='{$name}' where id = {$id};");
    
    $GLOBALS['message'] = $row <= 0 ? "修改失败": "修改成功";
    $GLOBALS['success'] = $row > 0;  
  }


   

  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    //一旦表单提交请求并判断是否通过URL提交ID，如果有则是编辑装填，无则是正常的新增
    if (!empty($_GET['id'])) {
      //调用编辑函数
      category_edit();

    }else{
      //正常新增
      category_add(); 
    }
     
    
  }



  $category = xiu_db_fetch('select * from categories;');

  

 ?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>

      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
        <?php if ($success): ?>
          <div class="alert alert-success">
          <strong>成功！</strong><?php echo $message ?>
          </div>
        <?php else: ?>
          <div class="alert alert-danger">
          <strong>错误！</strong><?php echo $message ?>
          </div>
        <?php endif ?>
      <?php endif ?>

      <div class="row">
        <div class="col-md-4">
          <?php if (isset($cureent_edit_category)): ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $cureent_edit_category[0]['id']; ?>" method="post" accept="multipart/form-data">
            <h2>编辑《<?php echo $cureent_edit_category[0]['name']; ?>》 </h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo $cureent_edit_category[0]['name']; ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $cureent_edit_category[0]['slug']; ?>">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">保存</button>
            </div>
          </form>
          <?php else: ?>
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" accept="multipart/form-data">
            <h2>添加新分类目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
          <?php endif ?>
          

        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" href="/admin/categories-delete.php" style="display: none" id="deleteAll">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox" id="checkAllBox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody id="tb">
              <?php foreach ($category as $item): ?>
              <tr>
                <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id'];?>"></td>
                <td><?php echo $item['name'] ?></td>
                <td><?php echo $item['slug'] ?></td>
                <td class="text-center">
                  <a href="/admin/categories.php?id=<?php echo $item['id']; ?>" class="btn btn-info btn-xs">编辑</a>
                  <a href="/admin/categories-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
                            
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  
  <?php $current_page ='category'; ?>
  <?php include 'inc/silderbar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.min.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
  <script>

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
      if ($(this).prop('checked')) {
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


    // vesion1:全选
    // checkAllBox.click( function() {
    //   tbody_checkBox.prop('checked',checkAllBox.prop('checked'));
      
    //   // deleteAll.attr('style','display:block');
    // });
  
    // //为单个复选框注册点击事件，当他们全选将全选按钮勾上
    // tbody_checkBox.click(function(){
    //   //获取当前的复选框的总个数
    //   var checkBoxLength = tbody_checkBox.length;
  
    //   //获取已经勾选的复选框的个数
    //   var checkedLength = $('#tb :checked').length;
      
    //   //判断是否复选框个数相等
    //   checkAllBox.prop("checked",checkBoxLength === checkedLength);
  
    // });


  });

  
  </script>
</body>
</html>
