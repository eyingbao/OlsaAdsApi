<?php
namespace Ads\Event;
class AccEvent{
	
	//获取服务记录
	public function getServerLog($mccID,$connectCrm){
		
 		$map['account_Id']=$mccID;
		$fromUserId= M('n_accountinfo',NULL,$connectCrm)->where($map)->getfield("customer_Id");	
		if(!$fromUserId){
			return "未绑定谷歌账号";
		}
		$sql = "select sl.id,sl.timeOfApplication,sl.serviceItem,se.appellation as serveSource,q.appellation as questionType,
				c.appellation as contacter,sl.contactPhone,sl.mobilePhone,sl.responsibleTaxOfficer,
				p.appellation as prductName,s.appellation as serviceState
				from n_servicelog sl 
				left join n_contacts c on sl.linkman = c.id
				left join n_productname p on sl.productName = p.id
				left join n_servicestate s on sl.serviceState = s.id
				left join n_servicesource se on sl.serveSource = se.id
				left join n_questiontype q on sl.questionType = q.id
				
				
				where sl.fromUserId = ".$fromUserId." and timeOfApplication>".(time()-30*24*3600)."
				order by sl.id desc
				";	 
		$rs= M('',NULL,$connectCrm)->query($sql);	
		foreach($rs as $item){
 
			$str.='<li>
            <div class="header">
              <p><span class="paddingr"style="width:140px;display:inline-block;">日 期 : '.date("Y/m/d",$item['timeofapplication']) .'</span>联系人: '.$item['contacter'].'</p>
              <p>服务类别:'.$item['questiontype'].'</p>
			 <p>联系方式:'.$item['servesource'].'</p>
            </div>
            <div style="clear:both"></div>
            <ul class="menu">
				<div class="recorddiv">
				<p class="tit">服务<br />记录</p>
				<p class="con"> 
				'.$item['serviceitem'].'
				</p></div>
            </ul>
          </li>';
		} 
		return $str;
		 
		//$rs= M('servicelog','n_',$connectCrm)->where("ACCOUNT_ID = '{$mccid[1]}' AND DAY BETWEEN '{$data[7][0]}' AND '{$data[7][0]}'")-> field('COST,IMPRESSIONS,ALL_CONVERSIONS,CLICKS,DAY')->order('COST DESC')->select();
	}
	//获取客服联系方式
	public function getServerUserInfo($mccID,$connectCrm){
 		$map['account_Id']=$mccID;
		$salesOrder_Id= M('n_accountinfo',NULL,$connectCrm)->where($map)->getfield("salesOrder_Id");
		$w['id']=$salesOrder_Id;
		$responsibleForCustomerService= M('n_salesorder',NULL,$connectCrm)->where($w)->getfield("responsibleForCustomerService");
		$w['id']=$responsibleForCustomerService;
		$userInfo= M('n_userprofile',NULL,$connectCrm)->where($w)->find();
		return $userInfo; 
		//Array ( [id] => 100060 [username] => 渠道 [office] => [gender] => 1 [departmentname] => 11 [jobtitle] => 3 [phone] => [homephone] => [email] => [education] => [idnumber] => [dateofbirth] => [entrydate] => [address] => [terminationdate] => [departmentmanger] => -1-2 [usergroupid] => 44 [whetherdisabled] => 0 [loginpassword] => 232425 [loginname] => 渠道 [sms_balance] => 0 [educationbackground] => 7 [graduateinstitutions] => [emergencytelephone] => [major] => [customersnumber] => 0 [qq] => ) 		
	}
	
	//获取CRM账户信息  账户状态、产品类型
	public function getAccInfo($mccID,$connectCrm){
 		$map['account_Id']=$mccID;
		$contacts_Id= M('n_accountinfo',NULL,$connectCrm)->where($map)->getfield("id");
		$w['accId']=$contacts_Id;
		$orderInfo= M('n_salesorder',NULL,$connectCrm)->where($w)->field("acctype,productName,productTypes")->order("id desc")->find();
		//print_r($orderInfo);
		if($orderInfo['acctype']==16){
			$arr['acctype']="活跃";
		}else{
			$arr['acctype']="暂停";
		}
		$arr['productName']=$this->getCrmProductName($orderInfo['productname'],$connectCrm);
		
		$arr['productTypes']=$this->getCrmProductTypes($orderInfo['producttypes'],$connectCrm);
		
		return $arr; 
		 		
	}
	//获取CRM客户信息
	public function getCustomerInfo($mccID,$connectCrm){
 		$map['account_Id']=$mccID;
		$fromUserId= M('n_accountinfo',NULL,$connectCrm)->where($map)->getfield("customer_Id");	
		$w['id']=$fromUserId;
		$orderInfo= M('n_customerinformation',NULL,$connectCrm)->where($w)->find();
		return $orderInfo; 
		 		
	}	
	//获取CRM产品类型
	public function getCrmProductTypes($id,$connectCrm){
 		if(empty($id)){
			return;
		}
		$w['id']=$id;
		$orderInfo= M('n_categoryproduct',NULL,$connectCrm)->where($w)->getfield("appellation");
		return $orderInfo; 
		 		
	}
	//获取CRM产品名称
	public function getCrmProductName($id,$connectCrm){
 		if(empty($id)){
			return;
		}
		$w['id']=$id;
		$orderInfo= M('n_productname',NULL,$connectCrm)->where($w)->getfield("appellation");
		return $orderInfo; 
		 		
	}
	//获取CRM产品名称
	public function verificationMccInfo($mccUserName,$mccUserPass,$connectCrm){
		$arr['errcode']=0;
 		if(empty($mccUserName) or empty($mccUserPass)){
			return $arr;
		}
		$w['loginName']=$mccUserName;
		$w['loginPassword']=$mccUserPass; 
		
//		echo $mccUserName."__".$mccUserPass."__".$connectCrm;
//		exit;
		$orderInfo= M('n_accountinfo',NULL,$connectCrm)->where($w)->find();
		if($orderInfo){
			$arr['errcode']=1;
			$arr['info']=$orderInfo;
		}
		return $arr; 
		 		
	}	
}
?>
