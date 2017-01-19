<?php
require_once '../config.php';
$con['51'] = mysqli_connect($db['51']['host'],$db['51']['user'],$db['51']['pwd'],$db['51']['db']);
//$con = mysql_connect("localhost","root","");
if (!$con['51'])
  {
  die('Could not connect: ' . mysql_error());
  }
//mysql_select_db($db['115']['db'], $con['115']);
mysqli_query($con['51'],"SET NAMES utf-8");
$result = mysqli_query($con['51'],"SELECT `account_id` FROM `ads_mcc` WHERE `canManageClients` = 0 AND status = 1");



$list = array();
$dirs = TASK_JAVA_DIR."\accountids.txt";
if(file_exists($dirs)){
	unlink($dirs);
}
$myfile = fopen($dirs, "w") or die("Unable to open file!");
while($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
{
   //$rs[0] = $row['account_id'];
  // array_push($list,$row['account_id']);
  if($row['account_id']!='' && isset($row['account_id'])){
  	$txt = $row['account_id']."\r\n";
  }
  fwrite($myfile, $txt);
  
}
 
 
//生成文件  
  



fclose($myfile);

 



?>