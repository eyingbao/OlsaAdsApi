<?php
namespace Udb\Event;
class CommonEvent{
	//返回限限制次数是0并且接口使用超过50次以上的列表
	public function getSiteidCount($limit=50){
		$sql = 	'SELECT new.c1, new.c2, new.c3 ,new.count,new.siteid ,yu.limit FROM  ( 

					(SELECT  new.c1, new.c2, new.c3 , (new.c1+new.c2+new.c3) as count,new.siteid FROM (
			
					SELECT ifnull(nw.c1,0) as c1,ifnull(nw.c2,0) as c2 ,ifnull(nw.c,0) as c3,nw.siteid FROM
					
					(SELECT new.c1,new.c2,p1.c ,new.siteid FROM (
					
					SELECT c1.c as c1,c2.c as c2 ,c1.siteid FROM (
					
					(SELECT COUNT(*) AS c ,siteid FROM `yyb_udb_request` WHERE data =\'category\'   GROUP BY siteid ) as c1  
					
					LEFT JOIN 
					(SELECT COUNT(*) AS c ,siteid FROM `yyb_udb_request` WHERE data =\'news\'   GROUP BY siteid ) as c2 ON c1.siteid = c2.siteid) 
					
					) as new LEFT JOIN 
					
					(SELECT COUNT(*) AS c ,  siteid  FROM `yyb_udb_request` WHERE data = \'product\'   GROUP BY siteid ) as p1
					
					on new.siteid = p1.siteid) AS nw ) as new ) as new INNER JOIN yyb_udb as yu ON yu.siteid = new.siteid) WHERE yu.limit = 0 AND new.count >'.$limit;

		$list = M()->query($sql);
			
		return $list;
	}
	
	
	public function updateSiteidCount($v){
		M('udb_request')->where('siteid = '.$v['siteid'])->delete();
		$sql = "UPDATE yyb_udb SET category_count = category_count+{$v[c1]}, news_count = news_count +{$v[c2]} , product_count = product_count + {$v[c3]} WHERE siteid = ".$v['siteid'];
		M()->execute($sql);
	}
	
	public function checkSecret($secret){
		if(strlen($secret) == 32){
			$list = M('udb')->where("secret = '{$secret}'")->select();
			if(count($list) == 1){
				if($list[0]['status'] == 1){
					
					if($list[0]['limit'] == 0){
						
						return array(true,intval($list[0]['siteid']),$list[0]['sid']);
					
					
					}else{
						//检测限额	
						$d1  = strtotime(date('Y-m-d' , strtotime('-1 day')).'00:00:00'); 
						$d2 = strtotime(date('Y-m-d' , strtotime('now')).'23:59:59'); 
						$sql = "SELECT COUNT(*) AS c FROM yyb_udb_request WHERE rtime BETWEEN {$d1} AND {$d2}";
						$c = M('udb_request')->where("rtime BETWEEN {$d1} AND {$d2} AND siteid = {$list[0][siteid]}")->count();
						if($c >= $list[0]['limit']){
							return array(false,'接口使用次数已超额');
						}else{
							return array(true,intval($list[0]['siteid']));
						}
					}
				
				}else{
					return array(false,'该站点接口以关闭');
				}
			}else{
				return array(false,'该站点未申请接口开启或者接口错误');
			}
		}else{
			return array(false,'获取接口秘钥错误');
		}
	}
	public function aa(){
		echo 3;	
	}
	public function getLimit(){
		
		
	}
	
	public function getUid($siteid){
		$rs = M('user_site',NULL)->where('id='.$siteid)->field('userid')->find();
		return $rs['userid'];
	}
}
?>
