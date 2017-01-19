// JavaScript Document


		//console.log(window.location.href);
	if(window.location.href == 'http://www.eyingbao.com/Form/Accounts'){
		//alert(document.getElementById('back').onclick);
		//document.getElementById('back').onclick = function(){
			//window.location.href='/Form/Accounts/login';
		//}
		//document.getElementById('back').style.display = 'none';
		
	}	

function exit(){
	var r=confirm("确定要退出当前账户？")
	if (r==true){
		window.location.href = '/Form/Logout';
	}
}

function selTab(obj,index){
	$(obj).siblings('.tabs').removeClass('col');
	$(obj).siblings('.tabs').addClass('col2');
	$(obj).removeClass('col2');
	$(obj).addClass('col');
	
	$('.cons').hide();
	$('.cons:eq('+index+')').show();
	
}