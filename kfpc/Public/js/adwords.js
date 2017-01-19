function Adwords(cc1){
	
	this.children = null; //子csutomerId
	
	this.dates = null;
	
	this.lineChart = null;
	
	this.modalLock = false;
	
	this.cc1 = cc1;
	
	this.blen = 40;
	
	this.formatCustomerId = function(id){
		
		var a =[id.slice(0,3),id.slice(3,6),id.slice(6,10)];
		
		return  a[0]+'-'+a[1]+'-'+a[2];
		
	}
	
	this.conversionsContrastArr ={
		'up':null,
		'down':null,
		'dri':1
	},
	
	this.cpaContrastArr ={
		'up':null,
		'down':null,
		'dri':1
	},
	
	this.campaignCostErrContrastArr ={
		'up':null,
		'down':null,
		'dri':1
	},
	
	this.changerTr = function(obj){
		//$('tbody:last tr').on('click',function(){
			//console.log($('tbody:last').find("tr").length);
			
			$('tbody:last').find("tr").each(function(index, element) {
				//consosle.log($(this));
              	if($(this).index() % 2 ==1){
					$(this).find('td').css('background-color','#fafafc');
				}else{
					 $(this).find('td').css('background-color','transparent');
				}
            	$(this).css('background-color','transparent');
				$(this).find('td').css('color','#59595a');
			});
			var index = $(obj).index();
			$(obj).find('td').css('background-color','transparent');
			$(obj).find('td').css('color','#fff');
			$(obj).css('background-color','#8dc6f5');
		//})
	}
	
	this.changeDate = function(val){
		//console.log(val);
		
		var v = $('#accountSel').val();
		if(v != '000-000-0000'){
			window.location.href = this.cc1+'?mcc='+v+'&date='+val;
		}
		
	}
	
	//制保留2位小数，如：2，会在2后面补上00.即2.00          
	this.toDecimal2 = function(x) {              
		var f = parseFloat(x);              
		if (isNaN(f)) {   
			return false;              
		}              
		var f = Math.round(x*100)/100;              
		var s = f.toString();              
		var rs = s.indexOf('.');              
		if (rs < 0) {   
			rs = s.length;                  
			s += '.';   
		}              
		while (s.length <= rs + 2) {   
			s += '0';              
		}              
		return s;   
	}
	
	
	//保留两位小数    
	//功能：将浮点数四舍五入，取小数点后2位          

	this.toDecimal = function (x) {   
		var f = parseFloat(x);              
		if (isNaN(f)) {   
			return;              
		}              
		f = Math.round(x*100)/100;              
		return f;          
	}
	
	this.filterStatus =  function(status){
		//return '<span style="width:9px; height:9px; border-radius:50%; background:#6C6; display:block; margin:5px auto 0 auto;"></span>';	
		
		if(status == '1'){
			var str = '<span style="width:9px; height:9px; border-radius:50%; background:#6C6; display:block; margin:5px auto 0 auto;"></span>';	
		}else{
			if(arguments[1]){
				var str = '<span style="width:9px; height:9px; border-radius:50%; background:red; display:block; margin:5px auto 0 auto;"></span>';
			}else{
				var str = '<span style="width:9px; height:9px; border-radius:50%; background:#ccc; display:block; margin:5px auto 0 auto;"></span>';
			}
		}
		
		return str;
	}
	
	this.filterStatusGa =  function(status){
		if(status == '1'){
			var str = '<span title="已安装" style="width:9px; height:9px; border-radius:50%; background:#6C6; display:block; margin:5px auto 0 auto;"></span>';	
		
		
		}else{
			var str = '<span  title="未安装"  style="width:9px; height:9px; border-radius:50%; background:red; display:block; margin:5px auto 0 auto;"></span>';	
		}
		
		return str;
	}
	
	//FFC705
	this.filterStatusAds =  function(status){
		if(status == '1'){
			var str = '<span  title="48小时内未检测"   style="width:9px; height:9px; border-radius:50%; background:#FFC705; display:block; margin:5px auto 0 auto;"></span>';	
		
		}else if(status == '2'){
			var str = '<span  title="已安装"   style="width:9px; height:9px; border-radius:50%; background:#6C6; display:block; margin:5px auto 0 auto;"></span>';	
	
		}else{
			var str = '<span  title="未安装"    style="width:9px; height:9px; border-radius:50%; background:red; display:block; margin:5px auto 0 auto;"></span>';	
		}
		
		return str;
	}
	
	
	this.selects = function(url){
		///console.log(url)
		window.location.href = DOMAIN_PC+url;
	}
	
	this.showLoad = function(bool){
		if(bool){
			$('#mask')	.show();
			//$('#loading').removeClass('hide');
	$('#loading').show();
	}else{
			$('#mask')	.hide();
			$('#loading').hide();
			//$('#loading').addClass('hide');

		}
	}
	
	this.modal = function(b,tit,str){
		
		if(b){
			$('#myModal2').modal("show");
			$('#myModal2Label').html(tit);
			$('#myModal2').find('.modal-body').html(str);
			//$('.switchery:last').remove();
		
		}else{
			
			$('#myModal2').modal("hide");
			$('#myModal2').find('.modal-body').html('');
			this.ajaxLoading(false);
		}
	}
	
	this.ajaxLoading = function(b){
		if(b){
			$('#oks').attr('disabled',true).html("loading ...");
			$('#closes').hide();
		}else{
			$('#oks').attr('disabled',false).html("确定");
			$('#closes').show();
		}
	}
	
	this.isEmail = function(email){
		//var myreg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
		//var myreg =/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
		 if(email.length < 5 || email.length > 30){
			return false;
		
		}else{
			return true;	
		}
	}
	
	this.addManager = function(tit,handle){
		var _this = this;
		if(handle == 'GET'){
			data = {
				'account':'',
				'pwd':'',
				'nickname':'',
				'mcc':'',
				'status':'1',
				'email':'',
				'tel':''
				};
			var str =  this.getManagerForm(data,true);
			ads.modal('show',tit,str);
			$('#oks').unbind();
			$('#oks').click(function(e) {       
            	_this.addManager(tit,'POST');
			});
		}else{
			_this.ajaxLoading(true);
			var data = {
					'account':$('#manager').find('#account').val(),
					'pwd':$('#manager').find('#pwd').val(),
					'nickname':$('#manager').find('#nickname').val(),
					'mcc':$('#manager').find('#mcc').val().substr(0,12),
					'status':$('#manager').find('[name = status]:checked').val(),
					'email':$('#manager').find('#email').val(),
					'tel':$('#manager').find('#tel').val()
				}
				
				
					
			if(data.account.length < 5 ||  data.account.length > 30){
				alert('登录账号在5-30位之间');
				_this.ajaxLoading(false);	
			}else if(data['pwd'].length <6 || data['pwd'].length > 10){
				alert('密码长度在6-10位之间');
				_this.ajaxLoading(false);	
			}else{
				
				$.ajax({
					type: "POST",
					url: DOMAIN_PC+"/Home/Manager/add",
					data: data,
					dataType: "json",
					success: function(data){
						if(data.suc){
								window.location.reload();		
						}else{
							alert(data.msg);	
							_this.ajaxLoading(false);
						}
					}
				});
			}
			
			
		}
	
	}
	
	this.editPwd = function(id,handle){
	
		var _this = this;
		if(handle == 'GET'){
			var str = this.getPwdForm();
			ads.modal('show','密码重置',str);
			document.getElementById('pwd').focus();
			$('#oks').unbind();
			
			$('#oks').click(function(e) {
                          
           		_this.editPwd(id,'POST');
			});
		}else{
			var data = {
					'id':id,
					'pwd':$('#manager').find('#pwd').val()
				}
			if(data['pwd'].length <6 || data['pwd'].length > 10){
					alert('密码长度在6-10位之间');	
					_this.ajaxLoading(false);	
			}else{
				$.ajax({
					type: "POST",
					url: DOMAIN_PC+"/Manager/pwd",
					data: data,
					dataType: "json",
					success: function(data){
						if(data.suc){
							window.location.reload();			
						}else{
							alert(data.msg);
							_this.ajaxLoading(false);	
						}
					}
    			});		
			}
			console.log(data);	
			
		}
	}
	
	this.editManager = function(id , handle){
	
		var _this = this;
		if(handle == 'GET'){
			if(!_this.modalLock){
				$.ajax({
					type: "GET",
					url: DOMAIN_PC+"/Manager/edit",
					data: {"id":id},
					dataType: "json",
					success: function(data){
					  if(data.suc){
							var str =  _this.getManagerForm(data['msg'],false);
							ads.modal('show',data['msg']['account'],str);
							$('#oks').unbind();
							$('#oks').click(function(e) {
                                //alert(1)
                            	_this.editManager(id,'POST');
							});
								
							
						}
					}
				});
			}
		}else{
			//	
			_this.ajaxLoading(true);
			
			var data = {
					'id':id,
					//'pwd':$('#manager').find('#pwd').val(),
					'nickname':$('#manager').find('#nickname').val(),
					'mcc':$('#manager').find('#mcc').val().substr(0,12),
					'status':$('#manager').find('[name = status]:checked').val(),
					'email':$('#manager').find('#email').val(),
					'tel':$('#manager').find('#tel').val(),
				}
				
				//if(data['pwd'].length <6 || data['pwd'].length > 10){
					//alert('密码长度在6-10位之间');	
					//_this.ajaxLoading(false);	
				/*}else if(this.isMccId(data['mcc'])){
					
				}*///else{
					$.ajax({
						type: "POST",
						url: DOMAIN_PC+"/Manager/edit",
						data: data,
						dataType: "json",
						success: function(data){
						  	if(data.suc){
								window.location.reload();			
							}else{
								alert(data.msg);
								_this.ajaxLoading(false);	
							}
						}
    				});
				//}
				//console.log(data);
			
		}
	}
	
	//是否是合法的mccID
	this.isMccId = function(id){
		//id.match(/[0-9]{}/)
		
	}
	
	this.getPwdForm = function(){
		var str =  '<form id="manager">'+
             '<div class="form-group">'+
            	'<label for="account">密码重置</label>'+
            	'<input type="password" class="form-control" id="pwd"  />'+
         	'</div>'+
		'</form>';	
		
		return str;
	}
	
	this.getManagerForm = function(data,bool){
		
		var str2 = '';
		
		
		
		var str =
		 '<form id="manager">';
            
		  if(bool){
			  str+= '<div class="form-group">'+
            	'<label for="account">账号</label>'+
            	'<input type="email" class="form-control" id="account" placeholder="example@example.com"   value="'+data['account']+'">'+
         	'</div>'+
            '<div class="form-group">'+
            	'<label for="pwd">密码</label>'+
            	'<input type="password" class="form-control" id="pwd" placeholder="请填写密码"    value="'+data['pwd']+'">'+
          	'</div>';
		  }
			
			
			
           
		    str+='<div class="form-group">'+
           		' <label for="nickname">昵称</label>'+
            	'<input type="text" class="form-control" id="nickname" placeholder="请填写昵称"       value="'+data['nickname']+'">'+
          	'</div>'+
            
			'<div class="form-group">'+
            	'<label for="mcc">绑定MCC</label>'+
            	'<input type="text" class="form-control" id="mcc" placeholder="xxx-xxx-xxxx"      value="'+data['mcc']+'">'+
          	'</div>'+
			
			'<div class="form-group">'+
            	'<label for="mcc">联系邮箱</label>'+
            	'<input type="text" class="form-control" id="email" placeholder="请填写邮箱"      value="'+data['email']+'">'+
          	'</div>'+
			
			'<div class="form-group">'+
            	'<label for="mcc">联系电话</label>'+
            	'<input type="text" class="form-control" id="tel" placeholder="请填写电话"      value="'+data['tel']+'">'+
          	'</div>';
         	
		
			if(data['status'] == '1'){
				str2 = '<div class="form-group">'+
					'<div class="mb5">'+
						'<label for="exampleInputPassword1" style="position:relative; top:2px;">状态</label>&nbsp;&nbsp;'+                           
							'<label class="radio-inline" for="inlineRadio1">'+
								'<input type="radio" name="status" id="inlineRadio1" value="1" checked />启用'+
							'</label>'+ 
							
							'<label class="radio-inline"  for="inlineRadio2">'+
								'<input type="radio" name="status" id="inlineRadio2" value="0" />禁用'+
							'</label>  '+                 
					'</div>'+
          		'</div>';
			}else{
				str2 = '<div class="form-group">'+
					'<div class="mb5">'+
						'<label for="exampleInputPassword1" style="position:relative; top:2px;">状态</label>&nbsp;&nbsp;'+                           
							'<label class="radio-inline" for="inlineRadio1">'+
								'<input type="radio" name="status" id="inlineRadio1" value="1"  />启用'+
							'</label>'+ 
							
							'<label class="radio-inline"  for="inlineRadio2">'+
								'<input type="radio" name="status" id="inlineRadio2" value="0" checked />禁用'+
							'</label>  '+                 
					'</div>'+
          		'</div>';
			}
		
			
		  
      	
		str+=str2+'</form>';	
		
		return str;
	}
	
	
	
	
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	//广告系列日预算异常
	this.campaignBudgetErrTr = function(data,index,per){
		var str = '';
		str+='<tr onclick="ads.changerTr(this)">';
		str+='<td style="text-align:center">#'+index+'</td>';
		str+='<td>'+data[0]+'</td>';
		str+='<td style="text-align:center"><span class="cid" onclick="ads.getInfo(\''+this.formatCustomerId(data[1])+'\',\''+data[0]+'\')">'+this.formatCustomerId(data[1])+'</span></td>';
		str+='<td>'+data[2]+'</td>';
		var i = data[3];
		str+='<td style="text-align:center">￥'+this.toDecimal2(i)+'</td>';
		i = data[4];
		str+='<td style="text-align:center">￥'+this.toDecimal2(i)+'</td>';
		str+='<td style="text-align:center">'+per+'%</td>';
		str+='</tr>';
		return str;
	}
	this.campaignBudgetErr = function(arr){
		var _this = this;
		if(arr.length){
			this.showLoad(true);
			$.ajax({
				type: "POST",
				url: DOMAIN_PC+"/Campaign",
				data: {"ids":arr,"date":$('#date').val()},
				dataType: "json",
				success: function(data){
					console.log(data);
					_this.showLoad(false);
					if(data['list'].length){
						for(var  i = 0; i<data['list'].length; i++){
							//for(var j =0 ; j<data[i].length;j++){
								//var per = _this.toDecimal((data[i][j][4] / data[i][j][3]) * 100);
								//if(per < 60 || per > 90){
									var index = $('tr').length;
									$('#campaignBudget').find('tbody').append(_this.campaignBudgetErrTr(data['list'][i],index,data['list'][i][5]));
								//}
							//}
						}
					}
				}
			});
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	///广告系列消耗异常
	this.campaignCostErrTr = function(data,index,diff,per){
		var str = '';
		str+='<tr onclick="ads.changerTr(this)">';
		str+='<td style="text-align:center">#'+index+'</td>';
		str+='<td>'+data[0]+'</td>';
		str+='<td style="text-align:center"><span class="cid" onclick="ads.getInfo(\''+this.formatCustomerId(data[1])+'\',\''+data[0]+'\')">'+this.formatCustomerId(data[1])+'</span></td>';
		str+='<td>'+data[2]+'</td>';
		str+='<td style="text-align:center">￥'+data[3]+'</td>';
		str+='<td style="text-align:center">￥'+data[4]+'</td>';
		
		if(diff< 0){
			str+='<td style="text-align:center"><span style="color:red;">￥'+diff+'</span></td>';
		}else{
			str+='<td style="text-align:center">￥'+diff+'</td>';
		}
		
		str+='<td style="text-align:center">'+per+'%</td>';
		str+='</tr>';
		return str;
		
	}
	this.campaignCostErr = function(arr){
		var _this = this;
		if(arr.length){
			this.showLoad(true);
			$.ajax({
				type: "POST",
				url: DOMAIN_PC+"/Campaign/costErr",
				data: {"ids":arr,"date":$('#date').val()},
				dataType: "json",
				complete: function(){
					_this.showLoad(false);
				},
				success: function(data){
					if(data['up'].length){
						_this.campaignCostErrContrastArr['up'] = data['up'];
						_this.campaignCostErrContrastArr['down'] = data['down'];
						for(var  i = 0; i<_this.campaignCostErrContrastArr['up'].length; i++){
							var tr =  _this.ResolveCampaignCostErrContrastData(_this.campaignCostErrContrastArr['up'][i]);
							//console.log(tr)
							$('#campaignCostErrContrast').find('tbody').append(tr);
						}
					}
					
					/*
					if(data.length){
						for(var  i = 0; i<data.length; i++){
							for(var j =0 ; j<data[i].length;j++){
								
								var diff = _this.toDecimal2(data[i][j][4] - data[i][j][3]);
								
								//var diff = _this.toDecimal2(Math.abs(data[i][j][4] - data[i][j][3]));
								
								
								if(data[i][j][4] == 0){
									var per = 0;
								}else{
									var per = _this.toDecimal((Math.abs(diff) / data[i][j][4]) * 100);
								}
								if( per > 30){
									var index = $('tr').length;
									$('#campaignCost').find('tbody').append(_this.campaignCostErrTr(data[i][j],index,diff,per));
								}
							}
						}
					}*/
				}
			});
		}
	}
	
	this.ResolveCampaignCostErrContrastData = function(data){
		var str = '<tr onclick="ads.changerTr(this)">';
		str+='<td align="center">'+data['id']+'</td>';
		str+='<td>'+data['name']+'</td>';
		str+='<td align="center"><span class="cid" onclick="ads.getInfo(\''+data['cid']+'\',\''+data['name']+'\')">'+data['cid']+'</span></td>';
		str+='<td align="center">'+data['cost2']+'</td>';
		str+='<td align="center">'+data['cost1']+'</td>';
		if(data['diff'] < 0){
			str+='<td align="center"><span style="color:red;">'+data['diff']+'</span></td>';
		}else{
			str+='<td align="center">'+data['diff']+'</td>';
		}
		//str+='<td align="center">'+data['per']+'</td>';
		
		str+='</tr>';
		return str;
	},
	
	this.campaignCostErrContrastSorts = function(obj){
		$(obj).removeClass('bls1').removeClass('bls2');
		$(obj).parents('table').find('tbody').html('');	
		var dri = this.campaignCostErrContrastArr['dri'];
		var d = '';
		if(dri == 1){
			this.campaignCostErrContrastArr['dri'] = 0;
			d = 'down';
			$(obj).addClass('bls2');
		}else{
			$(obj).addClass('bls1');
			this.campaignCostErrContrastArr['dri'] = 1;
			d = 'up';
		}for(var  i = 0; i<this.campaignCostErrContrastArr[d].length; i++){
			var tr =  this.ResolveCampaignCostErrContrastData(this.campaignCostErrContrastArr[d][i]);
			$('#campaignCostErrContrast').find('tbody').append(tr);
		}
	}
	
	
	
	
	
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	//关键词异常
	this.keywordsErrTr = function(data1,data2,data,index){
		var str = '';
		str+='<tr onclick="ads.changerTr(this)">';
		str+='<td style="text-align:center">#'+index+'</td>';
		str+='<td>'+data[0]+'</td>';
		str+='<td style="text-align:center">'+data1 +' / ' +data2+'</td>';
		
		
		
		str+='<td style="text-align:center"><span class="cid" onclick="ads.getInfo(\''+this.formatCustomerId(data[1])+'\',\''+data[0]+'\')">'+this.formatCustomerId(data[1])+'</span></td>';
		
		str+='<td>'+data[2]+'</td>';
		str+='<td>'+data[3]+'</td>';
		str+='<td>'+data[4]+'</td>';
		str+='<td style="text-align:center">'+data[5]+'</td>';
		str+='</tr>';
		return str;
	}
	
	this.keywordsErr = function(arr){
		var _this = this;
		if(arr.length){
			this.showLoad(true);
			$.ajax({
				type: "POST",
				url: DOMAIN_PC+"/AdGroup/keywordsErr",
				data: {"ids":arr,"date":$('#date').val()},
				dataType: "json",
				success: function(data){
					var c1 = 0;
					var c2 = 0;
					_this.showLoad(false);
					//if(data.length){
						for(var  i = 0; i<data['list'].length; i++){
							var index = $('#tbd tr').length;
							var tc = data['k'][data['list'][i][1]]['c'];
							var tc1 = data['k'][data['list'][i][1]]['c1'];
							//console.log(_this.keywordsErrTr(1,2,data['list'][i],index));
							$('#tbd').append(_this.keywordsErrTr(tc,tc1,data['list'][i],index));
							console.log();
							
							//c1+= parseInt(tc);
							//c2+= parseInt(tc1);
							
							
						}
					//}
					
					$('#c1').html(data['allc1']);
					$('#c2').html(data['allc2']);
					$('#c3').html(data['allc3']+'%');
					$('#tbd1').show();
					//_this.getKeywordsCount(arr);
				}
			});
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	//广告组总量
	this.getAdGroupCount = function(arr){
		var _this = this;
		if(arr.length){
			$('#tbd1').show();
			$('#c2').html('<img src="/Public/images/32.gif" width="20" height="20" />');
			$.ajax({
				type: "POST",
				url: DOMAIN_PC+"/AdGroup/getAdGroupCount",
				data: {"ids":arr,"date":$('#date').val()},
				dataType: "json",
				success: function(data){
					var sum = 0;
					for(var i =0 ;i< data.length;i++){
						sum+= parseInt(data[i]);
					}
					$('#c2').html(sum);
					
					if(sum > 0){
						var per = _this.toDecimal2(  (  parseInt($('#c1').html()) / sum ) *100 );
						$('#c3').html(per+'%');
					}else{
						$('#c3').html(0);
					}
				}
			});
		}
	}
	
	//广告语总量
	this.getAdCount = function(arr){
		var _this = this;
		if(arr.length){
			$('#tbd1').show();
			$('#c2').html('<img src="/Public/images/32.gif" width="20" height="20" />');
			$.ajax({
				type: "POST",
				url:DOMAIN_PC+ "/AdGroup/getAdCount",
				data: {"ids":arr,"date":$('#date').val()},
				dataType: "json",
				success: function(data){
					var sum = 0;
					for(var i =0 ;i< data.length;i++){
						sum+= parseInt(data[i]);
					}
					$('#c2').html(sum);
					//console.log(sum);
					if(sum > 0){
						var per = _this.toDecimal2(  (  parseInt($('#c1').html()) / sum ) *100 );
						$('#c3').html(per+'%');
					}else{
						$('#c3').html(0);
					}
				}
			});
		}
	}
	
	
	//广告语总量
	this.getKeywordsCount = function(arr){
		var _this = this;
		if(arr.length){
			$('#tbd1').show();
			$('#c2').html('<img src="/Public/images/32.gif" width="20" height="20" />');
			$.ajax({
				type: "POST",
				url: DOMAIN_PC+"/AdGroup/getKeywordsCount",
				data: {"ids":arr,"date":$('#date').val()},
				dataType: "json",
				success: function(data){
					var sum = 0;
					for(var i =0 ;i< data.length;i++){
						sum+= parseInt(data[i]);
					}
					$('#c2').html(sum);
					if(sum > 0){
						var per = _this.toDecimal2(  (  parseInt($('#c1').html()) / sum ) *100 );
						$('#c3').html(per+'%');
					}else{
						$('#c3').html(0);
					}
				}
			});
		}
	}
	
	
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	//广告组（CTR）异常
	this.ctrErrTr = function(data,index){
		var str = '';
		str+='<tr onclick="ads.changerTr(this)">';
		str+='<td style="text-align:center">#'+index+'</td>';
		str+='<td>'+data[0]+'</td>';
		str+='<td style="text-align:center"><span class="cid" onclick="ads.getInfo(\''+this.formatCustomerId(data[1])+'\',\''+data[0]+'\')">'+this.formatCustomerId(data[1])+'</span></td>';
		//str+='<td style="text-align:center">'+this.formatCustomerId(data[1])+'</td>';
		
		
		//var i = data[3]/1000000;
		str+='<td>'+data[2]+'</td>';
		//i = data[4]/1000000;
		str+='<td>'+data[3]+'</td>';
		str+='<td style="text-align:center">'+data[4]+'</td>';
		str+='<td style="text-align:center">'+data[5]+'</td>';
		//str+='<td style="text-align:center">'+per+'%</td>';
		str+='</tr>';
		return str;
		
	}
	this.ctrErr = function(arr){
		
		var _this = this;
		if(arr.length){
			this.showLoad(true);
			$.ajax({
				type: "POST",
				url: DOMAIN_PC+"/AdGroup",
				data: {"ids":arr,"date":$('#date').val()},
				dataType: "json",
				success: function(data){
					_this.showLoad(false);
					if(data['list'].length){
						for(var j =0 ; j<data['list'].length;j++){
							//arr.push(data[i][j][1]);
							var index = $('#tbd tr').length+1;
							
							$('#tbd').append(_this.ctrErrTr(data['list'][j],index));
						}
						
					}
					$('#tbd1').show();
					$('#c1').html(data['allc1']);
					$('#c2').html(data['allc2']);
					$('#c3').html(data['allc3']+'%');
				//	$('#c1').html($('#tbd tr').length);
				//	_this.getAdGroupCount(arr);
					//console.log();
				}
			});
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	//转化对比
	this.conversionsContrast = function(arr){
		var _this = this;
		if(arr.length){
			this.showLoad(true);
			$.ajax({
				type: "POST",
				url: DOMAIN_PC+"/Campaign/conversionsContrast",
				data: {"ids":arr,"date":$('#date').val()},
				dataType: "json",
				complete: function(){
					_this.showLoad(false);
				},
				success: function(data){
					if(data['up'].length){
						_this.conversionsContrastArr['up'] = data['up'];
						_this.conversionsContrastArr['down'] = data['down'];
						for(var  i = 0; i<_this.conversionsContrastArr['up'].length; i++){
							var tr =  _this.ResolveConversionsContrastData(_this.conversionsContrastArr['up'][i]);
							//console.log(tr)
							$('#conversionsContrast').find('tbody').append(tr);
						}
					}
				}
			});
		}
	}
	this.ResolveConversionsContrastData = function(data){
		var str = '<tr onclick="ads.changerTr(this)">';
		str+='<td align="center">'+data['id']+'</td>';
		//str+='<td>'+data['name']+'</td>';
		str+='<td>'+data['name']+'</td>';
		str+='<td align="center"><span class="cid" onclick="ads.getInfo(\''+data['cid']+'\',\''+data['name']+'\')">'+data['cid']+'</span></td>';
		
		
		str+='<td align="center">'+data['last7']+'</td>';
		str+='<td align="center">'+data['last14']+'</td>';
		if(data['diff'] < 0){
			str+='<td align="center"><span style="color:red;">'+data['diff']+'</span></td>';
		}else{
			str+='<td align="center">'+data['diff']+'</td>';
		}
		str+='</tr>';
		return str;
	},
	this.conversionsContrastSorts = function(obj){
		$(obj).removeClass('bls1').removeClass('bls2');
		$(obj).parents('table').find('tbody').html('');	
		var dri = this.conversionsContrastArr['dri'];
		var d = '';
		if(dri == 1){
			this.conversionsContrastArr['dri'] = 0;
			d = 'down';
			$(obj).addClass('bls2');
		}else{
			$(obj).addClass('bls1');
			this.conversionsContrastArr['dri'] = 1;
			d = 'up';
		}for(var  i = 0; i<this.conversionsContrastArr[d].length; i++){
			var tr =  this.ResolveConversionsContrastData(this.conversionsContrastArr[d][i]);
			$('#conversionsContrast').find('tbody').append(tr);
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	//CPA对比
	this.cpaContrast = function(arr){
		var _this = this;
		if(arr.length){
			this.showLoad(true);
			$.ajax({
				type: "POST",
				url: DOMAIN_PC+"/Campaign/cpaContrast",
				data: {"ids":arr,"date":$('#date').val()},
				dataType: "json",
				complete: function(){
					_this.showLoad(false);
				},
				success: function(data){
					if(data['up'].length){
						_this.cpaContrastArr['up'] = data['up'];
						_this.cpaContrastArr['down'] = data['down'];
						for(var  i = 0; i<_this.cpaContrastArr['up'].length; i++){
							var tr =  _this.ResolveCpaContrastData(_this.cpaContrastArr['up'][i]);
							//console.log(tr)
							$('#cpaContrast').find('tbody').append(tr);
						}
					}
				}
			});
		}
	}
	
	this.ResolveCpaContrastData = function(data){
		var str = '<tr onclick="ads.changerTr(this)">';
		str+='<td align="center">'+data['id']+'</td>';
		
		//str+='<td>'+data['name']+'</td>';
		str+='<td>'+data['name']+'</td>';
		str+='<td align="center"><span class="cid" onclick="ads.getInfo(\''+data['cid']+'\',\''+data['name']+'\')">'+data['cid']+'</span></td>';
		
		
		
		str+='<td align="center">'+data['last7']+'</td>';
		str+='<td align="center">'+data['last14']+'</td>';
		if(data['diff'] < 0){
			str+='<td align="center"><span style="color:red;">'+data['diff']+'</span></td>';
		}else{
			str+='<td align="center">'+data['diff']+'</td>';
		}
		str+='</tr>';
		return str;
	},
	this.cpaContrastSorts = function(obj){
		$(obj).removeClass('bls1').removeClass('bls2');
		$(obj).parents('table').find('tbody').html('');	
		var dri = this.cpaContrastArr['dri'];
		var d = '';
		if(dri == 1){
			this.cpaContrastArr['dri'] = 0;
			d = 'down';
			$(obj).addClass('bls2');
		}else{
			$(obj).addClass('bls1');
			this.cpaContrastArr['dri'] = 1;
			d = 'up';
		}for(var  i = 0; i<this.cpaContrastArr[d].length; i++){
			var tr =  this.ResolveCpaContrastData(this.cpaContrastArr[d][i]);
			$('#cpaContrast').find('tbody').append(tr);
		}
	}
	
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	//广告异常
	this.adErrTr = function(data,index){
		var str = '';
		str+='<tr onclick="ads.changerTr(this)">';
		str+='<td style="text-align:center">'+'#'+index+'</td>';
		str+='<td>'+data[0]+'</td>';
	
		str+='<td style="text-align:center"><span class="cid" onclick="ads.getInfo(\''+this.formatCustomerId(data[1])+'\',\''+data[0]+'\')">'+this.formatCustomerId(data[1])+'</span></td>';
		
		
		str+='<td>'+data[2]+'</td>';
		str+='<td>'+data[3]+'</td>';
		str+='<td>'+data[4]+'</td>';
		str+='<td style="text-align:center; color:red;">已拒登</td>';
		<!--str+='<td>'+data[6]+'</td>';-->
		str+='</tr>';
		return str;
	}
	
	this.adErr = function(arr){
		var _this = this;
		if(arr.length){
			this.showLoad(true);
			$.ajax({
				type: "POST",
				url: DOMAIN_PC+"/AdGroup/adErr",
				data: {"ids":arr,"date":$('#date').val()},
				dataType: "json",
				success: function(data){
					_this.showLoad(false);
					if(data['list'].length){
						
						//for(var  i = 0; i<data.length; i++){
							for(var j =0 ; j<data['list'].length;j++){
								var index = $('tr').length;
								$('#ad').find('tbody').append(_this.adErrTr(data['list'][j],index));
							}
						}
					//}
				}
			});
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	this.updateMcc = function(obj){
		var _this = this;
		var r=confirm("确定要更新MCC账户吗？");
		if(r==true){
			$.ajax({
					type: "POST",
					beforeSend: function(){
						_this.showLoad(true);
					},
					url: DOMAIN_PC+"/Home/Manager/updateMcc",
					complete: function(){
						//_this.showLoad(false);
						alert('同步成功');
					    window.location.href=DOMAIN_PC;
					}
			})
		}
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	//olsa日报
	this.getOlsa = function(){
		var _this = this;
		$.ajax({
			type: "POST",
			url: DOMAIN_PC+"/Mcc/olsa",
			beforeSend: function(){
				_this.showLoad(true);
			},
			dataType: "json",
			success: function(data){
				console.log(data['list'])
				//if(data['list'].length){
					var tr='';
					tr+='<tr>';
					tr+='<td style="text-align:center;border-color:#000;" colspan="2">公司合计</td>';
					
					
					tr+='<td style="text-align:center;border-color:#000;">'+data['sum']['now']+'</td>';
					tr+='<td style="text-align:center;border-color:#000;">'+data['sum']['yesterday']+'</td>';
					tr+='<td style="text-align:center;border-color:#000;">'+data['sum']['beforeyesterday']+'</td>';
					
					if(data['sum']['per1'] > 0 ){
						tr+='<td style="text-align:center;color:#40DC40;border-color:#000;">'+data['sum']['per1']+'%</td>';
					}else{
						tr+='<td style="text-align:center;color:red;border-color:#000;">'+data['sum']['per1']+'%</td>';
					}
					
					
					tr+='<td style="text-align:center;border-color:#000;">'+data['sum']['lastweek']+'</td>';
					
					if(data['sum']['per2'] > 0 ){
						tr+='<td style="text-align:center;color:#40DC40;border-color:#000;">'+data['sum']['per2']+'%</td>';
					}else{
						tr+='<td style="text-align:center;color:red;border-color:#000;">'+data['sum']['per2']+'%</td>';
					}
					
				
					tr+='</tr>';
					
					for(var x in data['list']){
						var rs = data['list'][x];
						tr+='<tr>';
						//for(var x in data['list'][i].length; i++){
							
						//}
						tr+='<td style="text-align:left;border-color: #000;">'+rs.info.name+'</td>';
						tr+='<td style="text-align:center;border-color:#000;">'+rs.info.cid+'</td>';
					
						tr+='<td style="text-align:center;border-color:#000;">'+rs.cost.now+'</td>';
						tr+='<td style="text-align:center;border-color:#000;">'+rs.cost.yesterday+'</td>';
						tr+='<td style="text-align:center;border-color:#000;">'+rs.cost.beforeyesterday+'</td>';
					
						if(rs.per1 > 0 ){
							tr+='<td style="text-align:center;color:#40DC40;border-color:#000;">'+rs.per1+'%</td>';
						}else{
							tr+='<td style="text-align:center;color:red;border-color:#000;">'+rs.per1+'%</td>';
						}
						
						
						
						tr+='<td style="text-align:center;border-color:#000;">'+rs.cost.lastweek+'</td>';
						
						if(rs.per2 > 0 ){
							tr+='<td style="text-align:center;color:#40DC40;border-color:#000;">'+rs.per2+'%</td>';
						}else{
							tr+='<td style="text-align:center;color:red;border-color:#000;">'+rs.per2+'%</td>';
						}
						
						tr+='</tr>';
					}
					
					$('#schedule1').html(data['schedule'][0]+'%');
					
					$('#schedule2').html(data['schedule'][1]+'%');
					
					$('#olsa').html(tr);
				//}
				/*
				<tr>
                      <td style="text-align:left">{$v.info.name}</td>
                      <td style="text-align:center">{$v.info.cid}</td>
                      <td style="text-align:center">{$v.cost.now}</td>
                      <td style="text-align:center">{$v.cost.yesterday}</td>
                       <td style="text-align:center">{$v.cost.beforeyesterday}</td>
                        
                        <td style="text-align:center">{$v.per1}</td>
                       
                        <td style="text-align:center">{$v.cost.lastweek}</td>
                         
                         
                         <td style="text-align:center">{$v.per2}</td>
                    </tr>
					*/
					
				$('#olsa_d1').html(data['date'][0]);
				$('#olsa_d2').html(data['date'][1]);
				$('#olsa_d3').html(data['date'][2]);
			
			},
			complete: function(){
				_this.showLoad(false);
			
			}
		})
	
	}
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	//账户等级
	this.getAlertedList = function(arr){
		var _this = this;
		if(arr.length){
			this.showLoad(true);
			$.ajax({
				type: "POST",
				url: DOMAIN_PC+"/Mcc/alertedList",
				data: {"ids":arr,"date":$('#date').val()},
				dataType: "json",
				success: function(data2){
					
					_this.showLoad(false);
					var tops = data2['top'];
					var data =  data2['list'];
					var top = '';
					for(var x in tops){
						top+='<tr height="35">';
							top+='<td style="text-align:left">'+x+'</td>';
							top+='<td>'+tops[x]['c']+'</td>';
							if(tops[x]['ag'] == 'N/A'){
								top+='<td><span class="lc">'+tops[x]['ag']+'</span></td>';
							}else{
								top+='<td>'+tops[x]['ag']+'</td>';
							}
							
						top+='<tr>';
					}
					$('#alertedListTop').html(top);
					
					var tr='';
					var i = 0;
					for(var i = 0; i < data.length; i++ ){
						tr+='<tr>';
							tr+='<td style="vertical-align:middle; text-align:left;text-decoration:underline;cursor:pointer"  onclick="waring_view(\''+data[i]['cid']+'\')">'+data[i]['name']+'</td>';
							tr+='<td style="vertical-align:middle;text-decoration:underline;cursor:pointer" onclick="suggest_view(\''+data[i]['cid']+'\')">'+data[i]['cid']+'</td>';
						//tr+='<td style="vertical-align:middle;text-align:center;">优</td>';
							
							tr+='<td style="text-align:center;">'+data[i]['costerr']+'</td>';
							tr+='<td style="text-align:center;">'+data[i]['renewals']+'</td>';
							
							tr+='<td style="text-align:center;">'+data[i]['campaignctr']+'</td>';
							tr+='<td style="text-align:center;">'+data[i]['convers']+'</td>';
							
							tr+='<td style="text-align:center;">'+data[i]['ref']+'</td>';
							tr+='<td style="text-align:center;">'+data[i]['kwpoint']+'</td>';
							
							tr+='<td style="text-align:center;">'+data[i]['bouncerate']+'</td>';
							tr+='<td style="text-align:center;">'+data[i]['loadtime']+'</td>';
							
							
							<!--tr+='<td style="vertical-align:middle;text-align:center;">'+data[i]['crmservercount']+'</td>';-->
							tr+='<td style="vertical-align:middle;text-align:center;">'+data[i]['point']+'</td>';
							
							tr+='</tr>';
					}
					
					$('.TabCon').find('tbody').html(tr);
				}
				
			})
		}
		
	}
	
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	//工具检测
	this.getTools = function(arr){
		var _this = this;
		if(arr.length){
			this.showLoad(true);
			$.ajax({
				type: "POST",
				url: DOMAIN_PC+"/Mcc/tools",
				data: {"ids":arr,"date":$('#date').val()},
				dataType: "json",
				complete: function(){
					_this.showLoad(false);
				
				},
				success: function(data){
					if(data['list'].length){
						for(var  i = 0; i<data['list'].length; i++){
							var tr =  _this.ResolveToolsData(data['list'][i]);
							console.log(tr)
							$('#tools').find('tbody').append(tr);
						}
						
						$('#c1').html(data['list'].length);
						$('#c2').html(data['count'][1]);
						$('#c3').html(data['count'][2]);
					}
					
				}
			});
		}
	}
	this.ResolveToolsData = function(data){
		var str = '<tr onclick="ads.changerTr(this)">';
		
		//for(var x in data){
			//str+='<td>a</td>';
		//}
		
		str+='<td align="center">'+data['id']+'</td>';
		str+='<td>'+data['name']+'</td>';

		str+='<td align="center"><span class="cid" onclick="ads.getInfo(\''+data['cid']+'\',\''+data['name']+'\')">'+data['cid']+'</span></td>';
		<!--str+='<td align="center">'+data['gaid']+'</td>';-->
		str+='<td align="center">'+this.filterStatusGa(data['ga_status'])+'</td>';
		<!--str+='<td align="center">'+data['conversions_name']+'</td>';-->
		str+='<td align="center">'+this.filterStatusAds(data['conversions'])+'</td>';
		str+='<td align="center">'+data['conversions_value']+'</td>';
		str+='<td align="center">'+data['cpa']+'</td>';

	
		
		str+='</tr>';
		return str;
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	//投放渠道
	this.deliveryChannel = function(arr){
		var _this = this;
		if(arr.length){
			this.showLoad(true);
			$.ajax({
				type: "POST",
				url: DOMAIN_PC+"/Mcc/deliveryChannel",
				data: {"ids":arr,"date":$('#date').val()},
				dataType: "json",
				success: function(data){
					_this.showLoad(false);
					if(data['list'].length){
						//console.log(data);
						for(var  i = 0; i<data['list'].length; i++){
							var index = $('tr').length;
							var tr =  _this.ResolveDeliveryChannelData(data['list'][i],index);
							$('#deliveryChannel').find('tbody').append(tr);
						}
					}
				}
			});
		}
	}
	this.ResolveDeliveryChannelData = function(data,index){
		var str = '';
		str+='<tr onclick="ads.changerTr(this)">';
		str+='<td style="text-align:center">'+'#'+index+'</td>';
		str+='<td>'+data[0]+'</td>'; //0
		str+='<td style="text-align:center"><span class="cid" onclick="ads.getInfo(\''+data[1]+'\',\''+data[0]+'\')">'+data[1]+'</span></td>';  //1
		str+='<td  style="text-align:center">'+this.filterStatus(data[2],1)+'</td>'; //2
		
		str+='<td style="text-align:center">'+this.filterStatus(data[3],1)+'</td>'; //3
		
		//if(data[3] == 1 && data[6] == 0){ //4
			//str+='<td style="text-align:center">'+this.filterStatus(data[6],1)+'</td>';
		
		//}else{
			str+='<td style="text-align:center">'+this.filterStatus(data[4],1)+'</td>';
		//}
		
		
		
		str+='<td style="text-align:center">'+this.filterStatus(data[5],1)+'</td>';
		str+='<td style="text-align:center">'+this.filterStatus(data[6],1)+'</td>';
		str+='</tr>';
		return str;
	}
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	this.adCtrErrTr = function(data,index){
		var str = '';
		str+='<tr onclick="ads.changerTr(this)">';
		str+='<td style="text-align:center">'+'#'+index+'</td>';
		str+='<td>'+data[0]+'</td>';
		str+='<td style="text-align:center"><span class="cid" onclick="ads.getInfo(\''+this.formatCustomerId(data[1])+'\',\''+data[0]+'\')">'+this.formatCustomerId(data[1])+'</span></td>';
		str+='<td>'+data[2]+'</td>';
		str+='<td>'+data[3]+'</td>';
		str+='<td>'+data[4]+'</td>';
		str+='<td style="text-align:center">'+data[5]+'</td>';
		str+='<td style="text-align:center">'+data[6]+'</td>';
		str+='</tr>';
		return str;
	}
	
	this.adCtrErr = function(arr){
		var _this = this;
		if(arr.length){
			this.showLoad(true);
			$.ajax({
				type: "POST",
				url: DOMAIN_PC+"/AdGroup/adCtrErr",
				data: {"ids":arr,"date":$('#date').val()},
				dataType: "json",
				success: function(data){
					_this.showLoad(false);
					//if(data.length){
						//var arr = new Array();
						for(var j =0 ; j<data['list'].length;j++){
							var index = $('#tbd tr').length+1;
							$('#tbd').append(_this.adCtrErrTr(data['list'][j],index));
						}
						
						
						
					//}
					$('#tbd1').show();
					$('#c1').html(data['allc1']);
					$('#c2').html(data['allc2']);
					$('#c3').html(data['allc3']);
					
					//_this.getAdCount(arr);
				}
			});
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	this.adCountErrTr = function(data,index){
		var str = '';
		str+='<tr onclick="ads.changerTr(this)">';
		str+='<td style="text-align:center">'+'#'+index+'</td>';
		str+='<td>'+data[0]+'</td>';
		
		str+='<td style="text-align:center"><span class="cid" onclick="ads.getInfo(\''+this.formatCustomerId(data[1])+'\',\''+data[0]+'\')">'+this.formatCustomerId(data[1])+'</span></td>';
	
		str+='<td>'+data[2]+'</td>';
		str+='<td>'+data[3]+'</td>';
		str+='<td >'+data[4]+'</td>';
		str+='<td style="text-align:center">1</td>';
		<!--str+='<td style="text-align:center">'+data[6]+'</td>';-->
		str+='</tr>';
		return str;
	}
	
	this.adCountErr = function(arr){
		var _this = this;
		if(arr.length){
			this.showLoad(true);
			$.ajax({
				type: "POST",
				url: DOMAIN_PC+"/AdGroup/adCountErr",
				data: {"ids":arr,"date":$('#date').val()},
				dataType: "json",
				success: function(data){
					_this.showLoad(false);
					if(data['list'].length){
						
						for(var j =0 ; j<data['list'].length;j++){
							var index = $('tr').length;
							$('#adCountErr').find('tbody').append(_this.adCountErrTr(data['list'][j],index));
						}
					
					}
				}
			});
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	this.getBalance2 = function(arr){
		var _this = this;
		var c = arr.length;
		if(c){
			this.showLoad(true);
			$.ajax({
				type: "POST",
				url: DOMAIN_PC+"/Mcc/index2",
				//async:false,
				data: {"ids":arr,"date":$('#date').val()},
				dataType: "json",
				success: function(data){
					_this.showLoad(false);
					var origin = new Array();
					if(data.length){
						for(var  i = 0; i<data.length; i++){
							index = $('tr').length;
							var tr =  _this.ResolveBalanceData(data[i],index);
							origin.push(data[i][4]);
							//console.log(tr);
							
							//origin.push(data[i][4]);
							$('#budget').find('tbody').append(tr);
						}
						
						
						
						origin.sort(downNumber);
			var trs = $('#budget').children('tbody').children('tr');
			if(trs.length){
				var clone = $('#budget').children('tbody').clone();
				for(var i = 0; i<origin.length;i++){
					clone.find('tr').each(function(index, element) {
						if(origin[i] == $(this).children('td:eq(6)').html()){
							down.appendChild($(this)[0]);
							return false;
						}
					});
				}
				
			}
			origin.sort(upNumber);
			if(trs.length){
				var clone = $('#budget').children('tbody').clone();
				for(var i = 0; i<origin.length;i++){
					clone.find('tr').each(function(index, element) {
						if(origin[i] == $(this).children('td:eq(6)').html()){
							up.appendChild($(this)[0]);
							return false;
						}
					});
				}
			}
			if(trs.length){
				blsSorts=true;
				$('.blsa').addClass('bls1');
				$('#budget').children('tbody')[0].innerHTML= down.innerHTML;
			}
			
			_this.showLoad(false);	
						
						
						
						
						
						
						
						
						
						/*$('.cname').each(function(index, element) {
							var _this2 = this;
							$.ajax({
								url: "/Mcc/getMcc?customerId="+$(_this2).attr('data-id'),
								async:false,
								success: function(data){
								 $(_this2).html(data);
								}
							});
					  
						});*/
						//console.log(origin);
						//_this.rdg(f,y,origin);
						
						
						
					}
				}
			})
			
		}
			
		
		
	}
	
	
	
	
	
	
	//余额
	this.getBalance = function(arr){
		var _this = this;
		var c = arr.length;
		var fs = new Array();
		if(c){
			var  m = Math.ceil(arr.length / this.blen);	
			
			for(var j = 0;j<m;j++){
				fs.push(arr.slice(this.blen*j,this.blen*(j+1)));
			
			}
			this.showLoad(true);
			if(fs.length){
				var origin = new Array();
				//console.log(fs[8]);
				this.rdg(fs,-1,origin);
			}
		
		}
	}
	
	this.rdg = function(f,y,origin){
		var _this = this;
		y++;
		if(f[y]!=undefined){
			$.ajax({
				type: "POST",
				url: DOMAIN_PC+"/Mcc",
				//async:false,
				data: {"ids":f[y],"date":$('#date').val()},
				dataType: "json",
				success: function(data){
					
					if(data.length){
						for(var  i = 0; i<data.length; i++){
							index = $('tr').length;
							var tr =  _this.ResolveBalanceData(data[i],index);
							origin.push(data[i][4]);
							$('#budget').find('tbody').append(tr);
						}
						$('.cname').each(function(index, element) {
							var _this2 = this;
							$.ajax({
								url: DOMAIN_PC+"/Mcc/getMcc?customerId="+$(_this2).attr('data-id'),
								async:false,
								success: function(data){
								 $(_this2).html(data);
								}
							});
					  
						});
						//console.log(origin);
						_this.rdg(f,y,origin);
						
						
						
					}
				}
			});
		}else{
			
			
			origin.sort(downNumber);
			var trs = $('#budget').children('tbody').children('tr');
			if(trs.length){
				var clone = $('#budget').children('tbody').clone();
				for(var i = 0; i<origin.length;i++){
					clone.find('tr').each(function(index, element) {
						if(origin[i] == $(this).children('td:eq(6)').html()){
							down.appendChild($(this)[0]);
							return false;
						}
					});
				}
				
			}
			origin.sort(upNumber);
			if(trs.length){
				var clone = $('#budget').children('tbody').clone();
				for(var i = 0; i<origin.length;i++){
					clone.find('tr').each(function(index, element) {
						if(origin[i] == $(this).children('td:eq(6)').html()){
							up.appendChild($(this)[0]);
							return false;
						}
					});
				}
			}
			if(trs.length){
				blsSorts=true;
				$('.blsa').addClass('bls1');
				$('#budget').children('tbody')[0].innerHTML= down.innerHTML;
			}
			
			_this.showLoad(false);	
			
		}
	}
	//解析余额数据
	this.ResolveBalanceData = function(data,f){
		if(data.length){
			var str='';
			str+='<tr onclick="ads.changerTr(this)">';
			for(var  i = 0; i< data.length; i++){
				switch(i){
					case 0:
						str+='<td style="text-align:center">'+'#'+f+'</td>';
						
						str+='<td data-id="'+data[0]['cid']+'" class="cname">'+data[0]['cname']+'</td>';
						
						str+='<td style="text-align:center"><span class="cid" onclick="ads.getInfo(\''+data[0]['cid']+'\',\'余额提醒\',true)">'+data[0]['cid']+'</span></td>';
						
					break;
					case 1:
						if(data[i]<=0){
							var color = "color:red";
						}else{
							var color = "";
						}
					
						str+='<td style="text-align:center;'+color+'">'+data[i]+'</td>';
					break;
					case 2:
					
					case 3:
						str+='<td style="text-align:center">'+data[i]+'</td>';
					break;
				}
				if( i == data.length-1){
					if(data[i]<=0){
							var color = "color:red";
						}else{
							var color = "";
						}
					
					str+='<td style="text-align:center;'+color+'">'+data[i]+'</td>';
					
					str+='</tr>';
					return str;
				}
			}
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	//月消耗曲线图
	this.getMonthCost = function(arr){
		var final = new Object();
		var _this = this;
		if(arr.length){
			this.showLoad(true);
			$.ajax({
				type: "POST",
				url: DOMAIN_PC+"/Mcc/monthCost",
				data: {"ids":arr,"date":$('#date').val()},
				dataType: "json",
				success: function(data){
					_this.showLoad(false);
					ctx = document.getElementById("canvas_time").getContext("2d");
					var data = {
					labels: data['labels'],
					datasets: [
						{
							data: data['vals'],
							label: "消耗",
							fill: true,
							lineTension: 0.05,
							backgroundColor: "rgba(164,228,255,0.1)",
							borderColor: "#49c9f8",
							borderCapStyle: 'butt',
							borderDash: [],
							borderDashOffset: 0.0,
							borderJoinStyle: 'miter',
							pointBorderColor: "#009fda",//实心点
							pointBackgroundColor: "#fff", //实心点
							pointBorderWidth: 0.2,
							pointHoverRadius: 5,
							pointHoverBackgroundColor: "rgba(75,192,192,1)",
							pointHoverBorderColor: "rgba(220,220,220,1)",
							pointHoverBorderWidth: 1,
							pointRadius: 4,
							pointHitRadius: 20,
							spanGaps: false,
						}
					]
				}
					
					var chartInstance = new Chart(ctx, {
						type: 'line',
						data: data,
						options: {
							title: {
								display: false,
								text: 'Custom Chart Title'
							},
							legend: {
								display: false,
								labels: {
									fontColor: 'rgb(255, 99, 132)'
								}
							}
						}
					})
				}
				//success: function(data){
					
					//if(data.length){
						
						//console.log(data);
						
						/*for(var i =0 ;i < _this.dates.length;i++){
							final[_this.dates[i]] = 0;
							for(var j =0 ;j < data.length;j++){
								for(var f =0 ;f < data[j].length;f++){
									if(data[j][f][1] == _this.dates[i]){
										final[_this.dates[i]]+=parseFloat(data[j][f][0]/1000000);
										break;
									}
								}
							}
						}
					
						var f =  new Array();
						var d = new Array();
						for(var x in final){
							f.push(_this.toDecimal2(final[x]));
						}
						
						for(var i = 0;i<date.length;i++){
							d.push(date[i].replace(/2015-/,''));
						}*/
						//alert(1)
						
						
						/*for(var j = 0; j<data.length;j++){
							var index = $('tr').length;
							$('tbody').append(_this.monthCostTr(data[j][0],index));
						}*/
						
					//}
				//}
			});
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	//月活跃账户曲线图表格
	this.monthCostTr = function(data,index){
		var str = '<tr>';

		str+='<td style="text-align:center">'+'#'+index+'</td>';
		str+='<td>'+data[2]+'</td>';
		str+='<td style="text-align:center">'+this.formatCustomerId(data[3])+'</td>';
		
		
		str+='</tr>';
		return str;
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	//月活跃账户曲线图
	this.getMonthActiveMcc= function(arr){
		var final = new Object();
		var _this = this;
		if(arr.length){
			this.showLoad(true);
			$.ajax({
				type: "POST",
				url: DOMAIN_PC+"/Mcc/active",
				data: {"ids":arr,"date":$('#date').val()},
				dataType: "json",
				success: function(data){
					_this.showLoad(false);
					ctx = document.getElementById("canvas_time").getContext("2d");
					var data = {
					labels: data['labels'],
					datasets: [
						{
							data: data['vals'],
							label: "消耗",
							fill: true,
							lineTension: 0.05,
							backgroundColor: "rgba(164,228,255,0.1)",
							borderColor: "#49c9f8",
							borderCapStyle: 'butt',
							borderDash: [],
							borderDashOffset: 0.0,
							borderJoinStyle: 'miter',
							pointBorderColor: "#009fda",//实心点
							pointBackgroundColor: "#fff", //实心点
							pointBorderWidth: 0.2,
							pointHoverRadius: 5,
							pointHoverBackgroundColor: "rgba(75,192,192,1)",
							pointHoverBorderColor: "rgba(220,220,220,1)",
							pointHoverBorderWidth: 1,
							pointRadius: 4,
							pointHitRadius: 20,
							spanGaps: false,
						}
					]
				}
					
					var chartInstance = new Chart(ctx, {
						type: 'line',
						data: data,
						options: {
							title: {
								display: false,
								text: 'Custom Chart Title'
							},
							legend: {
								display: false,
								labels: {
									fontColor: 'rgb(255, 99, 132)'
								}
							}
						}
					})
					
					
				}
			});
		}
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	
	this.editPwd2 = function(handle){
	
		var _this = this;
		if(handle == 'GET'){
			var str = this.getPwdForm();
			ads.modal('show','密码重置',str);
			window.setTimeout(function(){
				//console.log(document.getElementById('pwd'))
				document.getElementById('pwd').focus();
			},10);
			$('#oks').unbind();
			$('#oks').click(function(e) {
                          
           		_this.editPwd2('POST');
			});
		}else{
			var data = {
					'pwd':$('#manager').find('#pwd').val()
				}
			if(data['pwd'].length <6 || data['pwd'].length > 10){
					alert('密码长度在6-10位之间');	
					_this.ajaxLoading(false);	
			}else{
				$.ajax({
					type: "POST",
					url: DOMAIN_PC+"/User/pwd2",
					data: data,
					dataType: "json",
					success: function(data){
						if(data.suc){
							alert('修改成功，请重新登录');
							window.location.href = '/Login/logout';			
						}else{
							alert(data.msg);
							_this.ajaxLoading(false);	
						}
					}
    			});		
			}
			console.log(data);	
			
		}
	}
	
	
	
	this.customerInfo = function(cid){
		var _this = this;
		var ctrArr = new Array();
		var ctrIndex = new Array();
		$.ajax({
			type: "GET",
			url: DOMIAN_PC+"/Customer",
			data:{'mcc':cid},
			dataType: "json",
			success: function(data){
				
				if(!data.suc){
					alert(data.msg);
				}else{
					var tr = '';
					var table = '';
						//
						//console.log(data.length);
						var data = data.list;
						
						for(var i = 0; i<data.length;i++){
							if(data[i]['name'] == 'ctrErr'){
								//var index = i;
								ctrIndex.push(i);
								//break;
							}
						}
						
						//console.log(ctrIndex);
						ctrArr = data[ctrIndex[0]]['list'].concat(data[ctrIndex[1]]['list']); 
						console.log(ctrArr);
						
						for(var i = 0; i<data.length;i++){
							//console.log(data[0]['name']);
							//console.log(data[i]['name']);
							
							
							if(data[i]['name'] == 'ctrErr'){
								
								if(ctrArr.length){
									var str = '';
									for(var j = 0; j<ctrArr.length;j++){
										 str +='<tr>'+
												  '<td>'+ctrArr[j][2]+'</td>'+
												  '<td>'+ctrArr[j][3]+'</td>'+
												  '<td style="text-align:center">'+ctrArr[j][5]+'</td>'+
											'</tr>';
									
									}
									//console.log(str);
									$('#ctr_info').find('tbody').html(str);
								
								
								}else{
									$('#ctr_info').hide();
									$('#ctr_info').prev().show();
								}
								
							}else if(data[i]['name'] == 'kwCount'){	
								var str = '';
								if(data[i]['list'].length){
									
									for(var x in data[i]['list'][0]){
											for(var y in data[i]['list'][0][x]){
												//console.log(x+'----'+y+'-----'+data[i]['list'][0][x][y]);
												if( data[i]['list'][0][x][y]  < 5 || data[i]['list'][0][x][y]  > 35){
													str +='<tr>'+
													  '<td>'+x+'</td>'+
													  '<td>'+y+'</td>'+
													  '<td style="text-align:center">'+data[i]['list'][0][x][y]+'</td>'+
													'</tr>';
												}
											}
											
											//console.log(x+'----'+data[i]['list'][0][x]);
									}
									/*var str = '';
									for(var j = 0; j<data[i]['list'].length;j++){
										 str +='<tr>'+
												  '<td>'+data[i]['list'][j][0]+'</td>'+
												  '<td>'+data[i]['list'][j][1]+'</td>'+
												  '<td style="text-align:center">'+data[i]['list'][j][2]+'</td>'+
											'</tr>';
									
									}*/
									$('#kwCount_info').find('tbody').html(str);
								
								}else{
									
									$('#kwCount_info').hide();
									$('#kwCount_info').prev().show();
								}
							
							
							}else if(data[i]['name'] == 'keywordsErr'){
								
								
								if(data[i]['list'].length){
									var str = '';
									for(var j = 0; j<data[i]['list'].length;j++){
										 str +='<tr>'+
												  '<td>'+data[i]['list'][j][2]+'</td>'+
												  '<td>'+data[i]['list'][j][3]+'</td>'+
												  '<td>'+data[i]['list'][j][4]+'</td>'+
												  '<td style="text-align:center">'+data[i]['list'][j][5]+'</td>'+
											'</tr>';
									
									}
									$('#keywords_info').find('tbody').html(str);
								
								}else{
									
									$('#keywords_info').hide();
									$('#keywords_info').prev().show();
								}
								
								
							
								
							}else if(data[i]['name'] == 'adCtrErr'){
							
								if(data[i]['list'].length){
									var str = '';
									for(var j = 0; j<data[i]['list'].length;j++){
										 str +='<tr>'+
												  '<td>'+data[i]['list'][j][2]+'</td>'+
												  '<td>'+data[i]['list'][j][3]+'</td>'+
												  '<td>'+data[i]['list'][j][4]+'</td>'+
												  '<td style="text-align:center">'+data[i]['list'][j][6]+'</td>'+
											'</tr>';
									
									}
									$('#adCtr_info').find('tbody').html(str);
								
								}else{
									
									$('#adCtr_info').hide();
									$('#adCtr_info').prev().show();
								}
							
							
							
							}else if(data[i]['name'] == 'adCountErr'){
								
								if(data[i]['list'].length){
									var str = '';
									for(var j = 0; j<data[i]['list'].length;j++){
										 str +='<tr>'+
												  '<td>'+data[i]['list'][j][2]+'</td>'+
												  '<td>'+data[i]['list'][j][3]+'</td>'+
												  '<td style="text-align:center">1</td>'+
											'</tr>';
									
									}
									//console.log(str);
									$('#adCount_info').find('tbody').html(str);
								
								
								}else{
									$('#adCount_info').hide();
									$('#adCount_info').prev().show();
								}
							
							
							}else if(data[i]['name'] == 'adErr'){
								
								if(data[i]['list'].length){
									var str = '';
									for(var j = 0; j<data[i]['list'].length;j++){
										 str +='<tr>'+
												  '<td>'+data[i]['list'][j][2]+'</td>'+
												  '<td>'+data[i]['list'][j][3]+'</td>'+
												  '<td style="text-align:center; color:red;">已拒登</td>'+
												  '<td style="text-align:center">'+data[i]['list'][j][6]+'</td>'+
											'</tr>';
									
									}
									$('#adErr_info').find('tbody').html(str);
								
								}else{
									
									$('#adErr_info').hide();
									$('#adErr_info').prev().show();
								}
							
							
							
							
							
							}else if(data[i]['name'] == 'campaignBudgetErr'){
								var farr = new Array();
								
								if(data[i]['list'].length){
									
									for(var j =0 ; j<data[i]['list'].length; j++){
										var per = _this.toDecimal((data[i]['list'][j][4] / data[i]['list'][j][3]) * 100);
										//console.log(per+'-----'+j);
										
										if(per < 60 || per > 90){
											var ar = new Array();
											ar[0] = data[i]['list'][j][2];
											ar[1] = data[i]['list'][j][3];
											ar[2] = data[i]['list'][j][4];
											ar[3] = per;
											//console.log(ar);
											farr.push(ar);
										
										}
									}
									
									
										//console.log(farr);
										if(farr.length){
										
										var str = '';
										for(var m =0 ; m<farr.length;m++){
											//console.log(farr[m][0])
												var a1 = farr[m][1]/1000000;
												var a2 = farr[m][2]/1000000;
											
											str+='<tr>'+
													  '<td>'+farr[m][0]+'</td>'+
														'<td style="text-align:center">￥'+_this.toDecimal2(a1)+'</td>'+
													  '<td style="text-align:center">￥'+_this.toDecimal2(a2)+'</td>'+
													 '<td style="text-align:center">'+farr[m][3]+'%</td>'+
												'</tr>';
										}
										//console.log(str);
										$('#campaignBudget_info').find('tbody').html(str);
									}else{
										$('#campaignBudget_info').hide();
										$('#campaignBudget_info').prev().show();
									}
										
								}else{
									$('#campaignBudget_info').hide();
									$('#campaignBudget_info').prev().show();
								}
								
							
							}else if(data[i]['name'] == 'campaignCostErr'){
								var farr = new Array();
								
								if(data[i]['list'].length){
									
									for(var j =0 ; j<data[i]['list'].length;j++){
										//for(var j =0 ; j<data[i]['list'].length;j++){
										
										var diff = _this.toDecimal2(data[i]['list'][j][4] - data[i]['list'][j][3]);
										//console.log(data[i]['list'][j][4]);
										
										//var diff = _this.toDecimal2(Math.abs(data[i][j][4] - data[i][j][3]));
										
										
										if(data[i]['list'][j][4] == 0.00 || data[i]['list'][j][4] == 0){
											var per = 0;
										}else{
											var per = _this.toDecimal((Math.abs(diff) / data[i]['list'][j][4]) * 100);
										}
									//	console.log(per);
										if( per > 30){
											var ar = new Array();
											//var index = $('tr').length;
											
											ar[0] = data[i]['list'][j][2];
											ar[1] = data[i]['list'][j][3];
											
											ar[2] = data[i]['list'][j][4];
											ar[3] = diff;
											ar[4] = per;
											
											farr.push(ar);
											
										}
									}
									
									
									if(farr.length){
										
										var str = '';
										for(var m =0 ; m<farr.length;m++){
										//	console.log(farr[m][0])
											str+='<tr>'+
													  '<td>'+farr[m][0]+'</td>'+
													  '<td style="text-align:center">￥'+farr[m][1]+'</td>'+
													  '<td style="text-align:center">￥'+farr[m][2]+'</td>'+
													  '<td style="text-align:center">￥'+farr[m][3]+'</td>'+
													  '<td style="text-align:center">'+farr[m][4]+'%</td>'+
												'</tr>';
										}
										//console.log(str);
										$('#campaignCost_info').find('tbody').html(str);
									}else{
										$('#campaignCost_info').hide();
										$('#campaignCost_info').prev().show();
									}
								}else{
									
									$('#campaignCost_info').hide();
									$('#campaignCost_info').prev().show();
								}
								
								
							}else if(data[i]['name'] == 'budget'){
								
								if(data[i]['list'].length){
									
									if(data[i]['list'][1]<0){
										var color = "color:red";
									}else{
										var color = "";
									}
									
		
									if(data[i]['list'][4]<0){
										var color2 = "color:red";
									}else{
										var color2 = "";
									}
									
									var str ='<tr>'+
												  '<td style="text-align:center;'+color+'">￥'+data[i]['list'][1]+'</td>'+
												  '<td style="text-align:center">￥'+data[i]['list'][3]+'</td>'+
												  '<td style="text-align:center;'+color+'">'+data[i]['list'][4]+'</td>'+
											'</tr>';
								
									$('#budget_info').find('tbody').html(str);
									$('#budget_info').show();
									$('#budget_info').prev().hide();
								
								
								}else{
									
									$('#budget_info').hide();
									$('#budget_info').prev().show();
								}
							
							}else if(data[i]['name'] == 'deliveryChannel'){
								
								if(data[i]['list'][3] == 1 && data[i]['list'][6] == 0){ //4
									var strs ='<td style="text-align:center">'+_this.filterStatus(data[i]['list'][6],1)+'</td>';
					
								}else{
									var strs ='<td style="text-align:center">'+_this.filterStatus(data[i]['list'][6])+'</td>';
								}	
								
								var str ='<tr align="center">'+
												  '<td>'+_this.filterStatus(data[i]['list'][2])+'</td>'+ //2
												  '<td>'+_this.filterStatus(data[i]['list'][3])+'</td>'+ //3
												 strs+														   //4
												  '<td>'+_this.filterStatus(data[i]['list'][4])+'</td>'+ //5
												  '<td>'+_this.filterStatus(data[i]['list'][5])+'</td>'+ //6
											'</tr>';
								$('#deliveryChannel_info').find('tbody').html(str);
								//$('#deliveryChannel').find('tbody').html(str);
							}else if(data[i]['name'] == 'monthCost2'){
								$('#collect').prev().remove();
								$('#collect').before('<canvas  height="50"></canvas>');
								var dataOrigin = new Array();
								var dateOrigin = new Array();
								var finalDate = new Array();
								var date = new Array();
								var cost = new Array();
								
								for(var j =0;j<data[i]['list'].length;j++){
									
									dateOrigin.push(data[i]['list'][j][1]);
									
									//date.push(data[i]['list'][j][1].replace(/2015-/,''));
									//cost.push(data[i]['list'][j][0] / 1000000);
								}
								
								var st = dateOrigin.sort();
								for(var x = 0; x <st.length;x++ ){
									for(var x2 =0; x2<data[i]['list'].length; x2++){
										if(data[i]['list'][x2][1] == st[x]){
											dataOrigin.push(data[i]['list'][x2][0] / 1000000);
											break;
										}
									}
								}
								
								
								
								console.log(st);
								//console.log(dataOrigin);
								var lineChart = {
									   labels : st,
										datasets : [
											{
												label: "My First dataset",
												fillColor : "rgba(21,130,220,0.1)",
												strokeColor : "rgba(128,198,255,1)",
												pointColor : "rgba(0,140,255,1)",
												pointStrokeColor : "#fff",
												pointHighlightFill : "#fff",
												pointHighlightStroke : "rgba(21,130,220,1)",
												data:dataOrigin
											}
										]
								}	
								ctx = $('#month_cost_info').find('canvas')[0].getContext("2d");
								//ctx = document.getElementById("month_cost_info").getContext("2d");
								window.myLine = new Chart(ctx).Line(lineChart, {
									//success:true,
									responsive: true,
									bezierCurve:false,
									datasetStroke:false
								});	
								
								var w2 = (w-45);
								//alert(w2);
								$('#month_cost_info').css('width',w2+'px');
							}
								
						}
						$('.info_tit,.mains').show();
						$('#load2').addClass('hide');
					//	$('#infoModal').find('#collect_loading').hide();
						$('#infoModal').find('#collect').show();
						$('#infoModal').find('#closes2').show();
					}
				}
    	});
		
	}
	
	this.getInfo2 = function(cid,name){
		var _this = this;
		
		$('#infoModal').modal("show");
		//$('#infoModal').find('#collect_loading').show();
		$('#load2').removeClass('hide');
		$('#infoModal').find('#closes2').hide();
		
		$('.info_tit,.mains').hide();
		$('#budget_info,#deliveryChannel_info').find('tbody').html('');
		$('#infoModal').find('#myModal2Label2').html(name);
		_this.customerInfo(cid);	
	}
	
	this.getInfo = function(cid,cname){
		$('.search').val(cid);
		btnSearch($('#search_cid').get(0));
		console.log(cid,cname);
		/*var _this = this;
		
		$('#infoModal').modal("show");
		//$('#infoModal').find('#collect_loading').show();
		$('#load2').removeClass('hide');
		$('#infoModal').find('#closes2').hide();
		
		$('.info_tit,.mains').hide();
		$('#budget_info,#deliveryChannel_info').find('tbody').html('');
		
		if(arguments[2] === true){
			
			$.ajax({
				type: "GET",
				url: "/Search",
				data: {"mcc":cid},
				dataType: "json",
				
				success: function(data){
				  if(data.suc){
						$('#infoModal').find('#myModal2Label2').html(data.msg);
						_this.customerInfo(cid);	
				
					 
				  }else{
					  alert(data.msg);
				  }
				}
    		});
		
		}else{
			$('#infoModal').find('#myModal2Label2').html(cname);
			_this.customerInfo(cid);
		}*/
		
		
		
		
	}
	var cida = 0;
	this.iptcustomer = function(m){
		if(m == 'get'){
			cida = arguments[1];
			$('#iptcustomerModal').modal('show');
		}else{
			
			var obj = arguments[1];
			
			var lock = $(obj).attr('data-lock');
			
			//console.log(lock);
			//return;
			
			if(lock==0){
				$(obj).attr('data-lock',1);
				$(obj).html('正在导入...');
				var f = $('#csvfile').get(0).files[0];
				var urls = DOMAIN_PC+"/Home/Manager/iptcustomer";
				var xhr = new XMLHttpRequest();
				var fd = new FormData();
				fd.append('csv',f); 
				fd.append('cid',cida);
				xhr.addEventListener("load", function(evt){
				$(obj).attr('data-lock',0);
				$(obj).html('开始导入');
				$('#iptcustomerModal').modal('hide');
				},false);
				xhr.open("POST", urls); 
				xhr.send(fd); 
			}
			
		}
		
	}
	
	
}

function downNumber(a, b){
return a - b
}

function upNumber(a, b){
return b - a
}

//去重复数组 
function unique(data){ 
	data = data || []; 
	var a = {}; 
	len = data.length; 
	for (var i=0; i<len;i++){ 
		var v = data[i]; 
		if (typeof(a[v]) == 'undefined'){ 
			a[v] = 1; 
		} 
	}; 
	data.length=0; 
	for (var i in a){ 
		data[data.length] = i; 
	} 
	return data; 
} 

var width = document.documentElement.clientWidth;
var w = Math.ceil(width * 0.95);
//alert(w);

$(function(){
	$('#myModal2,#infoModal').modal({backdrop: 'static', keyboard: false,show:false});
	
	
	var height = document.documentElement.clientHeight;
	
	
	
	/*if(height <= 1075 && height >= 768){
		var h = Math.ceil(height * 0.85) ;
	}else if(height < 768){
		var h = Math.ceil(height * 0.82) ;
	}else{
		var h = Math.ceil(height * 0.84) ;
	}*/
	
	//alert(height)
	//console.log(w+'-----'+h);
	
	//2:4:4
	var h = Math.ceil(height * 0.8) ;
	
	//alert(h)
	
	$('#infoModal').find('[role = document]').css('width',w+'px');
	
	
	$('#infoModal').find('.modal-body').css('height',h+'px');
	var hh = Math.floor(h*0.2);
	var ih =[Math.floor(h*0.12),Math.floor(h*0.22),Math.ceil(h*0.22),Math.ceil(h*0.22),Math.ceil(h*0.22)];
	
	$('#collect').find('.mains').each(function(index, element) {
		//$(this).css('height',(hh-50)+'px');
      /* if(index == 0 || index ==1){
			$(this).css('height',(ih[0]-45)+'px');
		}else{
			$(this).css('height',(ih[1]-45)+'px');
		}	*/
		 /*}else if(index == 2 || index ==3){
			$(this).css('height',(ih[1]-50)+'px');
		}else if(index == 4 || index ==5){
			$(this).css('height',(ih[1]-50)+'px');
		}else{
			$(this).css('height',(ih[1]-50)+'px');
		}*/
    });
	
	//console.log(ih);
	
	
	//$('#infoModal').modal("show");
})