<?php
$b = true;
//print_r($_SERVER);
?>
<?php require_once 'header.php'; ?>

<section id="agreement">
	<?php require_once 'left.php'; ?>
    
    <div class="agright">
    	<div class="agtit">
        	<div class="agrtitle ritit"><p>服务器信息</p></div>
        </div>
        <div class="agrCon">
        	<div class="agrtwo">
              <table class="table table-bordered">
                <tbody>
                  <tr>
                  	<td style="text-align:left;">参数</td>
                    <td>值</td>
                  </tr>
                   <tr>
                  	<td>服务器版本</td>
                  	<td><?php echo apache_get_version(); ?></td>
                  </tr>
                  
                  <tr>
                  	<td>php版本</td>
                  	<td><?php 
						echo PHP_VERSION;
						
						if(substr(PHP_VERSION,0,3) >= 5.6){
							//echo '&nbsp;<span style="color:#07e210;font-size:15px;">√</span>'; 
						}else{
							$b = false;
							echo '&nbsp;<b style="color:red;font-size:15px;">×</b> (需要版本号 > 5.6)'; 
						}
					
					
					?></td>
                  </tr>
                  <tr>
                  	<td>域名</td>
                  	<td><?php echo $_SERVER['HTTP_HOST'] ?></td>
                  </tr>
                  <tr>
                  	<td>时区</td>
                  	<td><?php echo date_default_timezone_get(); ?></td>
                  </tr>
                  
                   <tr>
                  	<td>CURL模块</td>
                  	<td><?php 
							if(function_exists('curl_init')){
								echo '<span style="color:#07e210;font-size:15px;">√</span>'; 
							}else{
								$b = false;
								echo '<b style="color:red;font-size:15px;">×</b>'; 
							}
						?>
                    </td>
                  </tr>
                  
                  <tr>
                  	<td>SOAP模块</td>
                  	<td><?php 
							if(class_exists('SoapClient')){
								echo '<span style="color:#07e210;font-size:15px;">√</span>'; 
							}else{
								$b = false;
								echo '<b style="color:red;font-size:15px;">×</b>'; 
							}
						?>
                    </td>
                  </tr>
                  
                  
                 
                  
                  
                  <tr>
                  	<td>OPENSSL模块</td>
                  	<td><?php 
					
							//print_r($_SERVER);
					
							if(function_exists('openssl_sign')){
								echo '<span style="color:#07e210;font-size:15px;">√</span>'; 
							}else{
								$b = false;
								echo '<b style="color:red;font-size:15px;">×</b>'; 
							}
						?>
                    </td>
                  </tr>

                </tbody>
              </table>
            </div>
        </div>
        <br/>
        <p class="line"></p>
        <form class="formcon">
            <button type="button" class="upperbut" onClick="window.location.href='index.php'">上一步</button>
           
           <?php if($b){ ?>
            <button type="button" class="nextbut" onClick="next();">下一步</button>
        <?php }else{ ?>
        	<button type="button" class="nextbut" style="background:#ccc;"  disabled>下一步</button>
        <?php } ?>
        
        </form>
        </div>
        
    </div>
</section>

<script type="text/javascript">
$('.agrlist').find('li:eq(1)').addClass('selected');
function next(){

	window.location.href = 'step3.php';
}
</script>
</body>
</html>
