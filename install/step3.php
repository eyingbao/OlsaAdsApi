<?php
$configs = parse_ini_file('../kf_task/config2.ini',true);

?>
<?php require_once 'header.php'; ?>
<section id="agreement">
 <?php require_once 'left.php'; ?>
 <div class="agright">
 
 <div class="agtit">
   <div class="agrtitle ritit">
    <p>APi设置</p>
   </div>
  </div>
  
  <div class="agrCon">
   <div class="agrthree">
    <form>
     <div class="fo-group">
      <label for="inputEmail3">开发者令牌：</label>
      <div class="colma">
       <input data-k="ADWORDS-developerToken"  value="<?php echo $configs['ADWORDS']['developerToken']?>" />
      </div>
      <span>（developerToken）</span>
      <!--<p>只能用‘0-9'、‘a-Z'、‘A-Z'、‘.'、‘@'、‘_'、‘-'、‘!'以内范围的字符</p>-->
     </div>
     <div class="fo-group">
      <label for="inputPassword3">顶级mcc账户ID：</label>
      <div class="colma">
       <input data-k="ADWORDS-clientCustomerId" value="<?php echo $configs['ADWORDS']['clientCustomerId']?>" />
      </div>
     </div>
     <div class="fo-group">
      <label for="inputPassword3">API项目ID：</label>
      <div class="colma">
       <input data-k="OAUTH2-clientId" value="<?php echo $configs['OAUTH2']['clientId']?>" />
      </div>
     </div>
     
     <div class="fo-group">
      <label for="inputPassword3">API项目秘钥：</label>
      <div class="colma">
       <input data-k="OAUTH2-clientSecret" value="<?php echo $configs['OAUTH2']['clientSecret']?>" />
      </div>
     </div>
     
     <!--<div class="fo-group">
      <label for="inputPassword3">API项目ID：</label>
      <div class="colma">
       <input >
      </div>
     </div>-->
     
     
    </form>
   </div>
  </div>
 
 <div class="agtit">
   <div class="agrtitle ritit">
    <p>硬盘路径</p>
   </div>
  </div>
  
  <div class="agrCon">
   <div class="agrthree">
    <form>
     <div class="fo-group">
      <label for="inputEmail3">【后台任务】硬盘路径：</label>
      <div class="colma">
       <input data-k="DIR-TASK_JAVA_DIR"  value="<?php echo $configs['DIR']['TASK_JAVA_DIR']?>" />
      </div>
       </div>
       
       <div class="fo-group">
      <label for="inputEmail3">【PC管理】硬盘路径：</label>
      <div class="colma">
       <input data-k="DIR-TASK_PHP_DIR"  value="<?php echo $configs['DIR']['TASK_PHP_DIR']?>" />
      </div>
       </div>
       
     </div>
    </form>
   </div>
   
   <div class="agtit">
   <div class="agrtitle ritit">
    <p>域名访问</p>
   </div>
  </div>
  
  <div class="agrCon">
   <div class="agrthree">
    <form>
     <div class="fo-group">
      <label for="inputEmail3">【后台任务】域名：</label>
      <div class="colma">
       <input data-k="DOMAIN-DOMAIN_TASK"  value="<?php echo $configs['DOMAIN']['DOMAIN_TASK']?>" />
      </div>
       </div>
       
       <div class="fo-group">
      <label for="inputEmail3">【PC管理】域名：</label>
      <div class="colma">
       <input   data-k="DOMAIN-DOMAIN_PC"  value="<?php echo $configs['DOMAIN']['DOMAIN_PC']?>" />
      </div>
       </div>
       
       <div class="fo-group">
      <label for="inputEmail3">【移动端】域名：</label>
      <div class="colma">
       <input data-k="DOMAIN-DOMAIN_MOBILE"  value="<?php echo $configs['DOMAIN']['DOMAIN_MOBILE']?>" />
      </div>
       </div>
       
     </div>
    </form>
   </div>

  
  
  <div class="agtit">
   <div class="agrtitle ritit">
    <p>【后台任务】数据库设定</p>
   </div>
  </div>
 <div class="agrCon">
   <div class="agrthree">
    <form>
     <div class="fo-group">
      <label for="inputEmail3">数据库主机：</label>
      <div class="colma">
       <input data-k="DATABASE_KFTASK-host"  value="<?php echo $configs['DATABASE_KFTASK']['host']?>" />
      </div>
      <span>一般为localhost</span>
     </div>
     <div class="fo-group">
      <label for="inputPassword3">数据库用户：</label>
      <div class="colma">
       <input data-k="DATABASE_KFTASK-user" value="<?php echo $configs['DATABASE_KFTASK']['user']?>"   />
      </div>
     </div>
     <div class="fo-group">
      <label for="inputPassword3">数据库密码：</label>
      <div class="colma">
       <input data-k="DATABASE_KFTASK-pwd" value="<?php echo $configs['DATABASE_KFTASK']['pwd']?>"  />
      </div>
     </div>
     
     <div class="fo-group">
      <label for="inputPassword3">数据库名称：</label>
      <div class="colma">
       <input data-k="DATABASE_KFTASK-db"   value="awreports" disabled style="cursor:not-allowed">
      </div>
      <span>采用google官方数据库名称</span>
     </div>
     
    </form>
   </div>
  </div>
  
  
  
  
  
  <div class="agtit">
   <div class="agrtitle ritit">
    <p>【PC管理】数据库设定</p>
   </div>
  </div>
 <div class="agrCon">
   <div class="agrthree">
    <form>
     <div class="fo-group">
      <label for="inputEmail3">数据库主机：</label>
      <div class="colma">
       <input data-k="DATABASE_KFPC-host" value="<?php echo $configs['DATABASE_KFPC']['host']?>" />
      </div>
     </div>
     <div class="fo-group">
      <label for="inputPassword3">数据库用户：</label>
      <div class="colma">
       <input data-k="DATABASE_KFPC-user" value="<?php echo $configs['DATABASE_KFPC']['user']?>"  />
      </div>
     </div>
     <div class="fo-group">
      <label for="inputPassword3">数据库密码：</label>
      <div class="colma">
       <input data-k="DATABASE_KFPC-pwd" value="<?php echo $configs['DATABASE_KFPC']['pwd']?>"  />
      </div>
     </div>
     
     <div class="fo-group">
      <label for="inputPassword3">数据库名称：</label>
      <div class="colma">
       <input data-k="DATABASE_KFPC-db" value="<?php echo $configs['DATABASE_KFPC']['db']?>"  />
      </div>
     </div>
     
    </form>
   </div>
  </div>
  
    
  
  <div class="agtit">
   <div class="agrtitle ritit">
    <p>【后台PC管理】管理员设定</p>
   </div>
  </div>
  
  <div class="agrCon">
   <div class="agrthree">
    <form>
     <div class="fo-group">
      <label for="inputEmail3">用户名：</label>
      <div class="colma">
       <input  id="admin"  value="admin@admin.com" />
      </div>
      <p>默认为邮箱格式</p>
     </div>
     <div class="fo-group">
      <label for="inputPassword3">密 码：</label>
      <div class="colma">
       <input type="password" id="pwd" />
      </div>
     </div>
     
    </form>
   </div>
  </div>
  
  <br/>
  <p class="line"></p>
  <form class="formcon">
   <button type="button" class="upperbut" onClick="window.location.href='step2.php'">上一步</button>
   <button type="button" class="nextbut" data-lock="0" id="nexts"  onClick="next(this)">下一步</button>
  </form>
 </div>
 </div>
</section>




<script type="text/javascript">

function next(obj){
	if($(obj).attr('data-lock') == 0){
		var b ={};
		$('[data-k]').each(function(index, element) {
			var k = $(this).attr('data-k');
			b[k] = $(this).val();
		  
		});
		
		
		$.ajax({
			type: "POST",
			url: "save.php",
			data: {"kk":b,"admin":$('#admin').val(),"pwd":$('#pwd').val()},
			dataType: "json",
			beforeSend: function(){
				$(obj).attr('data-lock',1);
			},
			success: function(data){
				if(data.suc){
					
					window.location.href = 'step4.php';
				
				}else{
					$(obj).attr('data-lock',0);
					alert(data.msg);	
				}
			}
		});
	}
	  console.log(b);
	//
}

$('.agrlist').find('li:eq(2)').addClass('selected');
</script>
</body>
</html>
