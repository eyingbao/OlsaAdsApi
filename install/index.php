<?php require_once 'header.php'; ?>

<section id="agreement">
	
    <?php require_once 'left.php'; ?>
    
    <div class="agright">
    	<div class="agtit">
        	<div class="agrtitle ritit"><p>阅读许可协议</p></div>
        </div>
        <div class="agrCon">
        	<div class="agrfirst">
        	<p><strong>在使用本网站系统前，请阅读以下服务条款</strong></p>
<br />
<p>Google AdWords API-Olsa属于一款开源软件，方便为您的adwords账户进行数据分析。</p>
<p>配置之前请先仔细阅读下《配置说明书.pptx》。</p>
        </div>
        <br/>
        <p class="line"></p>
        <form class="formcon">
        <label>
          <input type="checkbox" name="ck" id="ck"> <strong>我已经阅读并同意此协议</strong>
        </label>
        <button type="button" class="nextbut" onClick="next()">下一步</button>
        </form>
        </div>
     </div>
</section>
<script type="text/javascript">
$('.agrlist').find('li:eq(0)').addClass('selected');

function next(){
	var b = document.getElementById('ck').checked;
	//console.log(b);
	if(!b){
		alert('请确认此协议');	
	}else{
		window.location.href = 'step2.php';
	}
}
</script>
</body>
</html>
