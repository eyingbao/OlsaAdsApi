<?php
	require_once  '../config.php';
	require_once  'public.php';
	require_once  'fun.php';
	$list = $l = array();
	$con['51'] = mysqli_connect($db['51']['host'],$db['51']['user'],$db['51']['pwd'],$db['51']['db']);
	//$con['115'] = mysqli_connect($db['115']['host'],$db['115']['user'],$db['115']['pwd'],$db['115']['db']);
	if (!$con['51'])
	  {
	  die('Could not connect: ' . mysqli_error());
	  }
	mysqli_query($con['51'],"SET NAMES GB2312");
	$result = mysqli_query($con['51'],"SELECT DISTINCT account_id , name FROM ads_mcc WHERE 
	(canManageClients  = 0 AND status = 1 ) OR (canManageClients  = 0 AND accountLabels = 1 )");
	while($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	  {
		   $rs[0] = cut($row['account_id']);
		   $rs[1] = $row['name'];
		   array_push($list,$rs);
	  }
	$i=0;
	if(is_array($list) && !empty($list)){
		foreach($list as $v){
			$i++;
			echo $v[0].'---'.$v[1];
			echo "\n";
		}
	}
	//pr(array($list));
	/*echo "\n";
	echo "--------------------------------------------------------------------------------";
	echo "\n";
	echo  '                           PHP mcc blance downloading...';
	echo "\n";
	echo "\n";
	echo  "--------------------------------------------------------------------------------";*/
?>