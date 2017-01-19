<?php
namespace Udb\Event;
class CategoryEvent  extends CommonEvent{
	
	private $public_fields = array(
		'select' => array('id','catname','module','parentid','listorder'),
		'listorder' => array('listorder'),
		'sort'=>array('ASC','DESC'),
		'level'=>array('='=>array(0,1)), //0无限分类,1单一分类
		'data' => array(
			'category'=>'user_category'
		),
		
		'dataType' =>array(
			'list'=>array(
				'where' => array(
					'catname' =>array('like'=>'vchar'),
					'ismenu'=>array('='=>array(0,1)),
					'module'=>array('='=>array(-1,0,1)),
					'parentid'=> array('='=>'int'),
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
	
	
	//检测是否是单一分类
	public function isSingleCategory($data){
		if($data['other']['level'] == 1 || $data['other']['level'] == 0){ //单一分类
			if( isset($data['where']['parentid']['=']) && isset($data['where']['id']['in']) ){
				//$data['where']['parentid']['='] = intval($data['where']['parentid']['=']);
				return array(false,'parentid 或 in 不能共存');
			}else if(!isset($data['where']['parentid']['=']) && !isset($data['where']['id']['in'])){
				return array(false,'parentid 或 in 必须传其中一个');
			}else{
				return array(true,$data['where']['parentid']['=']);	
			}
		}else{
				return array(false,'level获取错误');
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
		if(is_array($data)){
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
		if(in_array($dataType,array_keys($this->public_fields['dataType']))){  //检测最外层键值
			foreach($where as $k => $v){   //第二层键值 
				if(in_array($k,array_keys($this->public_fields['dataType'][$dataType]['where']))){
					foreach($v as $k2=>$v2){
						if(in_array($k2,array_keys($this->public_fields['dataType'][$dataType]['where'][$k]))){
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
										$str.=" AND {$k} {$k2} {$v2}";
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
