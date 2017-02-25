<?php
$configs = parse_ini_file('../kf_task/config.ini',true);

?>


<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>环境检测</title>
<link rel="stylesheet" type="text/css" href="css/apistyle.css">
</head>

<body>
<section id="header">
 <div>
  <div class="agrlogo">
   <h1>Google AdWords API-Olsa</h1>
        <p>▶ 管理系统<span>安装程序</span></p>
  </div>
 </div>
</section>
<section id="agreement">
 <?php require_once 'left.php'; ?>
 <div class="agright">
 
 <div class="agtit">
   <div class="agrtitle ritit">
    <p>oauth2设置</p>
   </div>
  </div>
  
  <div class="agrCon">
   <div class="agrthree">
    <div style="text-align:center; padding:30px 0; font-size:15px; color:#07e210;">√已安装完成</div>
    </div> 
    
  
  <br/>
  <p class="line"></p>
  <form class="formcon">
   <button type="button" class="upperbut" onClick="window.location.href='step4.php'">上一步</button>

  </form>
 </div>
 </div>
</section>
<script type="text/javascript">
$('.agrlist').find('li:eq(3)').addClass('selected');
</script>
</body>
</html>
