<?php
namespace Udb\Event;
class NewsEvent extends CommonEvent{ 
	private $public_fields = array(
		'select' => array('id','title','thumb','inputtime','updatetime','minthumb','sanpShot','content','description','catid'),
		'listorder' => array('listorder','inputtime','updatetime'),
		'sort'=>array('ASC','DESC'),
		'page'=>array(),
		'limit'=>array(),
		'data' => array(
			'news'=>'user_news'
		),
		'config'=>array('siteid','uid'),
		'dataType' =>array(
			'list'=>array(
				'where' => array(
					'title' =>array('like'=>'vchar'),
					'status'=>array('='=>array(0,1)),
					'catid'=> array('='=>'int'),
					'id'=>array('in'=>'int_array'),
				)
			),
			'info'=>array(
				'where'=>array(
					'id'=>array('='=>'int')
				)
			)
		),
	);
	
	public function __construct(){

	}
	
	//检测是否有缩略图路径
	public function isThumbPath($data){
		$thumbArr = array('thumb','minthumb');
		$aiSelThumbPath = array_intersect($thumbArr,$data['select']);
		if(count($aiSelThumbPath)){ //选择了查询的缩略图字段
			if(isset($data['other']['thumbPath'])){
				if($data['other']['thumbPath'] == 0  || $data['other']['thumbPath']==1){
					return array(true,333);
				}else{
					return array(false,'缩略图路径类型取值为0或者1');
				}
			}else{
				return array(false,'选取了缩略图字段，就要对应选取缩略图路径类型');
			}
		}else{
			return array(true,333);	
		}
	}
	
	public function isDateType($data){
		
		if(is_array($data['other']['dateType']) && !empty($data['other']['dateType'])){
			$typeArrCheck = true; //有时间类型
			$typeArr = array_keys($data['other']['dateType']);
		}else{
			$typeArrCheck = false;	 //无时间类型
		}
		
		$time = $timeType = array('inputtime','updatetime');
		
		$aiSelTime = array_intersect($time,$data['select']);
		
		if(count($aiSelTime)){ //选择了查询的时间字段
			if($typeArrCheck){
				if (   count(array_intersect($aiSelTime ,$typeArr)) == count($aiSelTime)   ){
					return array(true,333);
				}else{
					return array(false,'时间字段与时间字段类型要各自对应');
				}
			
			}else{
				return array(false,'选取了时间类型，就要对应选取时间字段');
			}
		}else{
			return array(true,333);
		}
	}
	
	//获取分页
	public function getPage($data){
		if(isset($data) && $data >=0){
			return array(true," LIMIT {$data} ,");
		}else{
			return array(false,'获取分页错误');
		}
	}
	
	//获取条数
	public function getLimit($data){
		if(isset($data) && $data >0){
			return array(true,$data);
		}else{
			return array(false,'获取条数错误');
		}
	}
	
	//获取排序
	public function getListorder($data){
		if(in_array($data,array_keys($this->public_fields['listorder']))){
			return array(true," ORDER BY ".$data);
		}else{
			return array(false,'获取排序错误');
		}
	}
	
	//获取排序
	public function getSort($data){
		if(in_array($data,array_keys($this->public_fields['sort']))){
			return array(true,$data);
		}else{
			return array(false,'获取排序顺序错误');
		}
	}
	
	//获取选取表
	public function getFromTable($data){
		if(in_array($data,array_keys($this->public_fields['data']))){
			return array(true," FROM ".$this->public_fields['data'][$data]);
		}else{
			return array(false,'获取表名错误');
		}
	}
	
	//获取要查询的字段
	public function getSelectFields($data){
		if(is_array($data) && !empty($data)){
			$arr = array_intersect($data,$this->public_fields['select']);
			if(count($arr)){
				return array(true,"SELECT ".implode(',',$arr));
			}else{
				return array(false,'选取字段错误');
			}
		}else{
			return array(false,'选取字段错误');
		}
	}
	
	//获取要查询的条件
	public function getFromWhere($dataType,$where,$siteid){
		$str = '';
		if(in_array($dataType,array_keys($this->public_fields['dataType']))){
			foreach($where as $k => $v){
				if(in_array($k,array_keys($this->public_fields['dataType'][$dataType]['where']))){
					foreach($v as $k2=>$v2){
						if(in_array($k2, array_keys(  $this->public_fields['dataType'][$dataType]['where'][$k] ))){
							if(is_array($this->public_fields['dataType'][$dataType]['where'][$k][$k2])){
								if(in_array($v2,   $this->public_fields['dataType'][$dataType]['where'][$k][$k2])){
									if($v2 == -1){
									
									}else{
										$str.=" AND {$k} {$k2} {$v2}";
									}
								}else{
									return array(false,$k.' 取值错误');
								}
							}else{
								if($this->public_fields['dataType'][$dataType]['where'][$k][$k2] == 'int_array'){
									if(is_array($v2) && !empty($v2)){
										if(is_intarray($v2)){
											$str.=" AND {$k} {$k2} (".implode(',',$v2).")";
										}else{
											return array(false,$k.'使用运算符 '.$k2.' 的值是数字的数组格式');
										}
									}else{
										return array(false,$k.'使用运算符 '.$k2.' 的值是数组格式');
									}
								}else{
									if($k2 == 'like'){
										$str.=" AND {$k} {$k2} '%{$v2}%'";
									}else{
										if($k == 'catid' && $k2 == '='){
											$list = M('user_category',NULL)->where("U_siteID = {$siteid}")->field('id,parentid')->select();
											$arr = array();
											if(intval($v2)){
												findCid($v2,$list,$arr);
												
											}else{
												$f = array();
												foreach($list as $v){
													array_push($arr,$v['id']);
												}
											}
											
											$in = implode(',',$arr);
											$str.=" AND {$k} in ({$in})";
										}else{
											$str.=" AND {$k} {$k2} {$v2}";
										}
									}
								}
							}
						}else{
							return array(false,$k.' 的运算符不允许是 '.$k2);
						}
					}
				}
			}
			$str.=' AND U_siteID = '.$siteid;
			$str = ltrim($str,' AND');
			return array(true,' WHERE '.$str.' ');
		}else{
			return array(false,'获取where条件错误');
		}
	}
}
?>
