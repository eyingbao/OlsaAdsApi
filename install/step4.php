<?php
$configs = parse_ini_file('../kf_task/config.ini',true);

?>
<?php require_once 'header.php'; ?>
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
    <form>
     <div class="fo-group">
      <label for="inputEmail3">刷新令牌：</label>
      <div class="colma">
       <input id="refreshToken" value="<?php echo $configs['OAUTH2']['refreshToken']?>" />
      </div>
      <span>（refreshToken）</span>
      <!--<p>只能用‘0-9'、‘a-Z'、‘A-Z'、‘.'、‘@'、‘_'、‘-'、‘!'以内范围的字符</p>-->
     </div>
     
    </form>
    </div> 
    
  
  <br/>
  <p class="line"></p>
  <form class="formcon">
   <button type="button" class="upperbut" onClick="window.location.href='step3.php'">上一步</button>
   <button type="button" class="nextbut" data-lock="0" onclick="next(this);">下一步</button>
  </form>
 </div>
 </div>
</section>
<script type="text/javascript">

function next(obj){
	if($(obj).attr('data-lock') == 0){
		$.ajax({
			type: "POST",
			url: "save2.php",
			data: {"refreshToken":$('#refreshToken').val()},
			dataType: "json",
			beforeSend: function(){
				$(obj).attr('data-lock',1);
			},
			success: function(data){
				if(data.suc){
					
					window.location.href = 'step5.php';
				
				}else{
					$(obj).attr('data-lock',0);
					alert(data.msg);	
				}
			}
		});
	}
	
}

$('.agrlist').find('li:eq(3)').addClass('selected');
</script>
</body>
</html>
