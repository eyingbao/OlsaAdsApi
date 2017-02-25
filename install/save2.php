<?php

$refreshToken = $_POST['refreshToken'];

if($refreshToken == ''){
	echo '{"suc":false,"msg":"refreshToken不能为空"}';
	exit;
}
$configs = parse_ini_file('../kf_task/config2.ini',true);
$configs['OAUTH2']['refreshToken'] = $refreshToken;

/*foreach($configs  as $k=>$v){
	$k2 = explode('-',$k);
	$b[$k2[0]][$k2[1]] = $v;
}*/
$str = '';
foreach($configs as $k=>$v){
	$str.="[{$k}]\n";
	foreach($v as $k2=>$v2){
		$str.="$k2=\"{$v2}\"\n";
	}
}
file_put_contents("../kf_task/config.ini",$str);
file_put_contents("../lock.txt",5);
echo '{"suc":true,"msg":""}';
	exit;
//print_r($configs);
?>