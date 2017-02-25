
<?php
$b = file_get_contents('../lock.txt');

//print_r($_SERVER);
if($b == '4' && strpos($_SERVER['PHP_SELF'],'step4')===false){
	header("Location:step4.php");	
}

if($b == '5' && strpos($_SERVER['PHP_SELF'],'step5')===false){
	header("Location:step5.php");	
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>许可协议</title>
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