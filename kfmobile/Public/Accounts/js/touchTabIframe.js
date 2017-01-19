//2015-04-24 by wyq 触屏滑动选项卡
var touchTab = function() {
var otab=document.querySelectorAll("[data-etcontrol=e_selectTab]"); //获取元素


	if (otab) { //判断元素是否存在
		var oelement = document.getElementById("all_element");
		$(oelement).attr("data-snap-ignore", "true")
		var dataEtId = [];
		var etDiv = "";
		var etLi = [];
		var boxLeft = [];
		var evtX = 0;
		var evtY = 0;
		var etDivWidth = 0;
		var leftIndex = 0;
		var rightIndex = 0;
//		var yqHeight = [];
		var thisIndex = 0;
		var odiv = null;
		var boxWidth = [];
		var boxPadding = [];
		var etidValue = null;
		var downPageX = 0;
		var downPageY = 0;
		var bolen =  true;
		var pagesX = 0;
		var pagesY = 0;
		var kaoIndex=0; //记录点击选项卡的次数
		var odivAndDocument = null;
		var heightValue=0;
     	var tabBgColor=null;
		var etLiStyle = "background-color: rgb(230, 230,230) !important;background-image:none !important;color:rgba(80,80,80,1) !important;text-shadow:none !important;";

		var eventType = window.ontouchstart === undefined ||(window.navigator.userAgent.indexOf("Firefox") > 0 &&window.navigator.userAgent.indexOf("Android") < 0 ) ? "mousedown" : "touchstart"; //判断浏览器是否支持触屏事件
		
	
		
			for (var i = 0,len=otab.length; i <len; i++) { //循环选项卡
               
              
          
				etDiv=$(otab[i]).find("[et_role='selectTabDiv']") //元素添加到数组
				if(etDiv.length>1)
				{
						var oul=$(otab[i]).find("[et_role='selectTabUl']"),
						    borderWidth=parseInt(etDiv.css("border-left-width")) * 2,
	                        oclass,bgcolor,color;
                    
					if (parseInt($(otab[i]).css("padding-left")) > 0) {
						boxPadding[i] = parseInt($(otab[i]).css("padding-left"))
	
					} else {
						boxPadding[i] = 0;
					}
					
				    etLi=oul.children("li") //元素添加到数组
					oclass=oul.children("li:not([style*='rgba(80,80,80,1)'])").attr("class");
					bgcolor=oul.attr("e_bgcolor");
					color=oul.attr("e_color");
	                tabBgColor = {
					"background-color":bgcolor ,
					"text-shadow": "none",
					"background-image": "none",
					"color":color
				     };
				     
				    if(!oclass&&bgcolor)
				    {
				        oul.children("li").removeAttr("style").css(tabBgColor);
				    }
				    else
				    {
				    	oul.children("li").removeAttr("style").addClass(oclass).css("color",color)
				    }
				    
	
			
	                oul.children("li:first-child").attr("style", etLiStyle);
	       
	               	etDiv.css({
						"width": Math.floor($(otab[i]).innerWidth() - 
						 borderWidth - parseInt(etDiv.css("padding-left")) * 2 - boxPadding[i] * 2),
						"margin-right": boxPadding[i]
	
					}); 
					$(otab[i]).append("<div id='yq_Box" + i + "'><div et_role='ul_box'></div></div>") //创建父元素
					boxLeft["yq_Box" + i] = 0;
					boxWidth["yq_Box" + i] = 0;
					$("#yq_Box" + i).children("div").append(etDiv) //向父元素中追加子元素
				var odivWidth = Math.ceil((etDiv.outerWidth() + boxPadding[i]) * etDiv.size()); //获取元素的总宽度
				 heightValue=$("#yq_Box" + i).parent().attr("data-height")||100;
				  $("#yq_Box" + i).css({
						"overflow":"hidden",
						"position": "relative"
				 }).children("div").css({
						width:odivWidth,
						height:"auto",//yqHeight["yq_Box" + i],
						overflow:"hidden"
					})
				tabClick(etLi);
				etDiv.css({
						"display": "block",
						"float": "left",
						"height":"auto",
						"overflow-y":"auto",
						"overflow-x":"hidden"
					}).on(eventType, function(e) { //绑定事件
						var evt = e || event;
						oyqId = $(this).parent().attr("id");
	
						odiv = $(this).parent().find("[et_role='selectTabDiv']")
						olis = $(this).parent().parent().parent().find("ul[et_role='selectTabUl']").find("li")
		
						if (eventType == "mousedown") { //判断事件类型
							var touchOrMouse = "mousemove";
							var endOrup = "mouseup"
							var touchX = evt.clientX;
							var touchY = evt.clientY;
							odivAndDocument = odiv
						} else {
							var touchX = evt.originalEvent.targetTouches[0].clientX;
							var touchY = evt.originalEvent.targetTouches[0].clientY;
							var touchOrMouse = "touchmove";
							var endOrup = "touchend"
							odivAndDocument = odiv;
	
						};
						$(this).each(function(index) { //遍历元素
							thisIndex = $(this).index();
							var etDivLeft = $(this).offset().left;
							var etDivTop = $(this).offset().top;
							etDivWidth = $(this).outerWidth();
							leftIndex = thisIndex - 1;
							rightIndex = thisIndex + 1;
							evtX = touchX - etDivLeft;
							evtY = touchY - etDivTop;
							downPageX = touchX - evtX //获取鼠标当前的位置
							downPageY = touchY - evtY //获取鼠标当前的位置
							touchOrMove(touchOrMouse);
							touchOrUp(touchOrMouse, endOrup);
	          
	    
						})
					
					    if(eventType=="touchstart")
	                    {
	                       evt.stopPropagation();
	                    }
	
					});
					
				}
				else
				{
					 heightValue=otab[i].dataset.height||100;
					 etDiv.eq(0).css({
						"display": "block",
						"height":heightValue+"px",
						"overflow-y":"auto",
						"overflow-x":"hidden"
					 })
				}
				  
                
		};
		


        			    				//点击事件
	    function tabClick(obj) {
					//etLi[i].eq(0).css(etLiStyle);
					obj.on("click", function(e) {
						var evt = e || event;
						var thisObj = $(this).parent();
       	
						odiv = $(this).parent().parent().parent().find("div[et_role='selectTabDiv']");
						var thisIndex = $(this).index();
						var oyqId = $(this).parent().parent().next().attr("id");
						var marginRight = parseInt(odiv.eq(0).css("margin-right"))
						var odivOuterW = odiv.eq(0).outerWidth() + marginRight;
					    
						oimgLoad(odiv.eq(thisIndex));
						boxLeft[oyqId] = odivOuterW * thisIndex;
                        tab($(this).parent().children("li"),thisIndex)
//						yqHeight[oyqId] = odiv.eq(thisIndex).outerHeight();

						boxWidth[oyqId] = "translate(" + -(odivOuterW * thisIndex) + "px,0px)"
						etDivStyle("0.2s", boxWidth[oyqId]);

						evt.stopPropagation();
						evt.preventDefault();
						
					})

		}
        function tab(obj,index)
        {
        	var  oul=obj.eq(index).parent(),
        	     olis= oul.children("li"),
			     oclass=obj.eq(index).attr("class"),
			     bgcolor=oul.attr("e_bgcolor"),
			     color=oul.attr("e_color"),
				 etLiStyle = "background-color: rgb(230, 230,230) !important;"+
				 "background-image:none !important;color:rgba(80,80,80,1) !important;"+
				 "text-shadow:none !important;";
				 tabBgColor = {
				             "background-color":bgcolor, 
				             "text-shadow": "none",
				             "background-image": "none",
				             "color":color
			                 }
			    if(!oclass&&bgcolor)
				{
				    olis.removeAttr("style").css(tabBgColor);
				}
				else
			    {
				    olis.removeAttr("style").addClass(oclass).css("color",color);
				}
				    
		        obj.eq(index).attr("style", etLiStyle);
			     
			     
        }
        
        function oimgLoad(obj) {
			var eImg = obj.find("img");
			if (eImg.length > 0 && eImg.attr("data-original") != undefined) {
				eImg.each(function(index) {
					eImg.eq(index).attr("src", eImg.eq(index).attr("data-original"))
				})
			}
		}
        
        function touchOrMove(touchOrMouse) {

			odiv.on(touchOrMouse, function(e) {


				var evt = e || event;

				$("div[id='all_element']").removeAttr("style")
				if (eventType == "mousedown") {
					var touchX = evt.clientX;
					var touchY = evt.clientY;
				} else {
					var touchX = evt.originalEvent.targetTouches[0].clientX;
					var touchY = evt.originalEvent.targetTouches[0].clientY;
				}
				oimgLoad(odiv.eq(leftIndex))
				
				var duration = "0s"
				pagesX = touchX - evtX; //获取元素滑动的位置
				pagesY = touchY - evtY;
				var hX = Math.abs(pagesX - downPageX);
				var sY = Math.abs(pagesY - downPageY);
				var deg = 180 * Math.atan2(hX, sY) / Math.PI; //根据x轴y轴求角度


				if (hX < sY && deg < 70) {
					bolen = true;
				

				} else if (hX > sY && deg > 70) {
					boxWidth[oyqId] = "translate(" + (pagesX - parseInt(boxLeft[oyqId])) + "px,0px)";
					etDivStyle(duration, boxWidth[oyqId]);
                
					bolen = false;
				}




				evt.stopPropagation();

				return bolen;
			})
		}

		function etDivStyle(duration, boxWidth) { //动画样式
			odiv.parent().css({
				"TransitionDelay": "0s",
				"TransitionDuration": duration,
				"TransitionProperty": "transform",
				"Transform": boxWidth,
				"MozTransitionDelay": "0s",
				"MozTransitionDuration": duration,
				"MozTransitionProperty": "transform",
				"MozTransform": boxWidth,
			});
		}

		function touchOrUp(touchOrMouse, endOrup) { //取消事件

			odivAndDocument.on(endOrup, function(e) {
					var evt = e || event;
					if (endOrup == "mouseup") {
						var touchX = evt.clientX;
						var touchY = evt.clientY;
					} else {
						var touchX = evt.originalEvent.changedTouches[0].clientX;
						var touchY = evt.originalEvent.changedTouches[0].clientY;

					}
				
	           
					odiv.off(touchOrMouse, "");
					odiv.off(endOrup, "");
					duration = "0.2s";
					var endPageX = touchX - evtX //获取鼠标当前的位置
					var endPageY = touchY - evtY //获取鼠标当前的位置
					var hX = Math.abs(endPageX - downPageX);
					var sY = Math.abs(endPageY - downPageY);
					var marginRight = parseInt(odiv.eq(0).css("margin-right"))
					var odivOuterW = odiv.eq(0).outerWidth() + marginRight;
					var deg = 180 * Math.atan2(hX, sY) / Math.PI; //根据x轴y轴求角度

					if (hX > sY && deg > 70) {
						if (pagesX < -(etDivWidth / 3)) {
							if (leftIndex < 0) {
								leftIndex = odiv.size() - 1;

							}
							boxLeft[oyqId] = odivOuterW * leftIndex
							tab(olis, leftIndex);
//							yqHeight[oyqId] = odiv.eq(leftIndex).outerHeight();
							boxWidth[oyqId] = "translate(" + -(odivOuterW * leftIndex) + "px,0px)";
		                	etDivStyle(duration, boxWidth[oyqId]);
						} else if (pagesX > etDivWidth / 3) {
							if (rightIndex >= odiv.size()) {
								rightIndex = 0;
							}
							tab(olis, rightIndex);
							boxLeft[oyqId] = (odivOuterW * rightIndex)
//							yqHeight[oyqId] = odiv.eq(rightIndex).outerHeight();
							boxWidth[oyqId] = "translate(" + -(odivOuterW * rightIndex) + "px,0px)";
						} else {
							boxLeft[oyqId] = (odivOuterW * thisIndex)
							boxWidth[oyqId] = "translate(" + -(odivOuterW * thisIndex) + "px,0px)"
						}
			etDivStyle(duration, boxWidth[oyqId]);
					} else {
						boxLeft[oyqId] = (odivOuterW * thisIndex);
			
						boxWidth[oyqId] = "translate(" + -(odivOuterW * thisIndex) + "px,0px)";
				var speed=navigator.
				userAgent.match(/(iPad|iPod|iPhone)/g)?800:400;

		
		       etDivStyle("0.2", boxWidth[oyqId]);

					}
	  
			
	
			
			
	          if(endOrup=="touchend")
	          {
	            evt.stopPropagation();
	          }

		   })


		}

	    
		$(document).on("dragstart", function() {

			return false;
		})

	}




};


!function(){
		var windowWidth=$(window).width();
	$(window).on("resize", function() {
		var windowWidth1=$(window).width()
		//public();
		if(parseInt(windowWidth)!=parseInt(windowWidth1))
		{
				if (window.navigator.userAgent.indexOf("Firefox") > 0) {
			window.location.href = window.location.href
		} else {
			window.location.reload(true);
		}	
		}

	})
	

	
}();
//$(window).on("load",function(){
//	
	touchTab(); //执行方法
//})


