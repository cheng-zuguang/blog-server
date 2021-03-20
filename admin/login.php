<?php 
  require_once '../config.php';

  session_start();

  function login(){

    //1.校验
    if(empty($_POST['email'])){
      $GLOBALS['message'] = "用户名不能为空";

      return;
    }

    if(empty($_POST['password'])){
       $GLOBALS['message'] = "用户密码不能为空";
       return;

    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    //链接数据库
    $con  = mysqli_connect(WX_DB_HOST,WX_DB_USER,WX_DB_PASS,WX_DB_NAME);

    if(!$con){
      exit("<h1>链接失败</h1>");
    }
    $query = mysqli_query($con,"select * from users where email = '{$email}' limit 1;");
    // var_dump($query);
    if (!$query) {
      $GLOBALS['message'] = "登录失败，try again";
      return;
    }
    //取出数据
    $user = mysqli_fetch_assoc($query);
    if (!$user) {
      $GLOBALS['message'] = "用户名与密码不匹配";
      return;
    }
    

    if ($user['email'] !== $email) {
         $GLOBALS['message'] = "用户名或密码error11";
        return;
    }

    if ($user['password'] !== $password) {
         $GLOBALS['message'] = "用户名或密码error";
        return;
    }


    // if($_POST['email'] !== "admin@qq.com"){
    //     $GLOBALS['message'] = "用户名错误";
    //    return;
    // }
    //  if($_POST['password'] !== "admin"){
    //     $GLOBALS['message'] = "用户密码错误";
    //    return;
    // }

    
    //存放这当前登录后的用户信息
    $_SESSION['current_login_user'] = $user;

    header('Location: index.php');

  }
  
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    login();

  }

  if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'action'){

      unset($_SESSION['current_login_user']);
      
  }
 ?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/baixiu/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/baixiu/static/assets/css/admin.css">
  <link rel="stylesheet" href="/baixiu/static/assets/vendors/animate/animate.css">
</head>
<body>
  <div class="login">
    <form class="login-wrap<?php echo isset($message) ? ' shake animated' : ''; ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" novalidate autocomplete="off"><!--  novalidate 取消系统的邮件校验 -->
      <img class="avatar" src="/baixiu/static/assets/img/default.png">
      <?php if (isset($message)): ?>
        <!-- 有错误信息时展示 -->
      <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $message; ?>
      </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo empty($_POST['email'])? '' : $_POST['email']; ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
     <button class="btn btn-primary btn-block">登 录</button> 
    </form>
  </div>

  <script src="/baixiu/static/assets/vendors/jquery/jquery.min.js"></script>

  <script>
  /*
  目标：在用户输入完邮箱过后进行校验，并用ajax发送请求，获取头像地址，显示在头像栏

   */
   $(function ($){
      //1.单独作用域
      //2.页面加载函数
      
      //正则表达式
      var emailFormat = /^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/;

      $('#email').on('blur',function(){
        var value = $(this).val();
        //用正则表达式校验邮箱的格式是否正确
        if (!value || !emailFormat.test(value)) return;

        //对于已经输入的合格的邮箱地址，我们将其保存并向服务端发送请求
        //使用Jquery 中的ajax来发送请求
        //$.get(请求的地址，传过去的参数， 函数);
        $.get('/admin/api/avater.php', {email : value}, function(res){
            if (!res) return;
          
            $(".avatar").fadeOut(function(){
              $(this).attr('src',res);

              $(this).on('load',function(){
                $(this).fadeIn();
              });
            });
        });  
        
      });
   });


  </script>
</body>
</html>
