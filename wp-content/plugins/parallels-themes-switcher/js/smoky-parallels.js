(function(window,undefined) {
var $=function(id){
		return document.getElementById(id);
	},
	
	$T=function(t,p) {
		p = p ? p : document;
		return p.getElementsByTagName(t);
	},
	
	$$=$C=function(c, t, p) {
		var at = $T(t,p),
			ms = new Array();
		for (var i = 0; i < at.length; i++)
			if (hasClassName(at[i],c))
				ms.push(at[i]);
		return ms;
	},
	
	bind=function(obj,func){
		return function(){
			func.apply(obj,arguments);
		}
	},
	
	insertBe=function(obj,tar){
		tar.parentNode.insertBefore(obj,tar);
	},
	
	removeNode=function(node){
		node.parentNode.removeChild(node);
	},
	
	addListener=function(e, n, o, u){
		if(e.addEventListener) {
			e.addEventListener(n, o, u);
			return true;
		} else if(e.attachEvent) {
			e['e' + n + o] = o;
			e[n + o] = function() {
				e['e' + n + o](window.event);
			};
			e.attachEvent('on' + n, e[n + o]);
			return true;
		}
		return false;
	},
	
	removeListener=function(e,n,o){
		if (e.removeEventListener) {
			e.removeEventListener(n, o, true);
		} else if (e.detachEvent) {
			e.detachEvent('on' + n, o);
		}
	},
	
	hasClassName=function(o,c) {
		return new RegExp("(?:^|\\s+)" + c + "(?:\\s+|$)").test(o.className);
	},
	
	addClassName=function(o,c) {
		if (!hasClassName(o,c)) {
			o.className = [o.className, c].join(" ");
		}
	},
	
	removeClassName=function(o,c) {
		if (hasClassName(o,c)) {
			var a = o.className;
			o.className = a.replace(new RegExp("(?:^|\\s+)" + c + "(?:\\s+|$)", "g"), " ");
		}
	},
	
	toggleClassName=function(o,c) {
		hasClassName(o,c) ? removeClassName(o,c) : addClassName(o,c);
	},
	
	IE=function(){
		if(/msie (\d+\.\d)/i.test(navigator.userAgent)){
			return document.documentMode || parseFloat(RegExp.$1);
		}
		return 0;
	},
	
	getObjPoint=function(o){
		var x=y=0;
		do {
			x += o.offsetLeft || 0;
			y += o.offsetTop  || 0;
			o = o.offsetParent;
		} while (o);

		return {'x':x,'y':y};
	},
	
	getScrollSize=function(){
		var x=y=0,
			doc=document.documentElement,
			body = document.body;
		x = (doc && doc.scrollLeft || body && body.scrollLeft || 0) - (doc && doc.clientLeft || body && body.clientLeft || 0);
		y = (doc && doc.scrollTop  || body && body.scrollTop  || 0) - (doc && doc.clientTop  || body && body.clientTop  || 0);
		return {'x':x,'y':y}
	},
	
	getWindowSize=function(){
		return {'x':document.documentElement.clientWidth || document.body.clientWidth,'y':document.documentElement.clientHeight || document.body.clientHeight};
	},
	
	getDocSize=function(){
		return {'x':document.documentElement.scrollWidth || document.body.scrollWidth,'y':document.documentElement.scrollHeight || document.body.scrollHeight};
	},
	
	documentReady=(function(){
		var load_events = [],load_timer,script,done,exec,old_onload,init = function () {done = true;clearInterval(load_timer);while (exec = load_events.shift())exec(); if (script) script.onreadystatechange = '';};
		return function (func) {
			if (done) return func();
			if (!load_events[0]) {
				if (document.addEventListener)
					document.addEventListener("DOMContentLoaded", init, false);
				else if (/MSIE/i.test(navigator.userAgent)){
					document.write("<script id=__ie_onload defer src=//0><\/scr"+"ipt>");
					script = document.getElementById("__ie_onload");
					script.onreadystatechange = function() {
						if (this.readyState == "complete")
							init();
					};
				}else
				if (/WebKit/i.test(navigator.userAgent)) {
					load_timer = setInterval(function() {
						if (/loaded|complete/.test(document.readyState))
							init();
					}, 10);
				}else{
					old_onload = window.onload;
					window.onload = function() {
						init();
						if (old_onload) old_onload();
					};
				}
			}
			load_events.push(func);
		}
	})(),	
	
	createxmlHttp=function() {
		var xmlHttp;
		try {
			xmlHttp = new XMLHttpRequest()
		} catch(e) {
			try {
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP")
			} catch(e) {
				try {
					xmlHttp = new ActiveXObject("Msxml2.XMLHTTP")
				} catch(e) {
					myAlert("Your browser does not support ajax!");
					return false
				}
			}
		}
		return xmlHttp
	};

	var SP={
		start:function(){
			SP.frame=$('sp-float-cover');
			if(!SP.frame){
				SP.frame=document.createElement('div');
				SP.frame.id='sp-float-cover';
				SP.frame.innerHTML='<div class="SP_content">'+
					'<div class="SP-current-theme"></div><div class="SP-other-theme"></div></div><div class="plugin-info">Powered by &#169; Parallels Theme Switcher</div>'
				document.body.appendChild(SP.frame);
			}
			SP.frame.style.display='block';
			SP.requestInfo();
		},
		requestInfo:function(){
			var xmlHttp = createxmlHttp(),
				url = '?action=WPXW_getAllThemes';
			xmlHttp.open("GET", url, true);
			xmlHttp.setRequestHeader("Content-type", "charset=UTF-8");
			xmlHttp.onreadystatechange = function() {
				if (xmlHttp.readyState == 4 || xmlHttp.readyState=="complete") {
					var curr=$$('SP-current-theme','div',SP.frame)[0],
						other=$$('SP-other-theme','div',SP.frame)[0];
					if (xmlHttp.status == 200) {
						var json;
						try{
							json=eval("("+xmlHttp.responseText+")");
						}catch(e){
							curr.innerHTML='Sorry, some error!';
						}
						if(json){
							curr.innerHTML='You are previewing <span style="color:red;">'+json.current+'</span> theme';
							other.innerHTML='<a href="#1" class="SP-btn" title="List all your themes"></a>';
							SP.bindAction(json.allthemes);
						}
					}else{
						curr.innerHTML=xmlHttp.responseText;
					}
				}
			}
			xmlHttp.send(null);
		},
		bindAction:function(json){
			var btn=$$('SP-btn','a',SP.frame)[0],
				data='<ol class="SP_list">';
			for(var i=0;i<json.length;i++){
				data+='<li><a class="SP_theme" name="'+json[i].template+'" title="author: '+json[i].author+', version: '+json[i].version+'" href="#1">'+json[i].template+'</a></li>';
			}
			data+='</ol>';
			SP.UI=new UI(btn,data);
			addListener(window,'scroll',bind(SP.UI,SP.UI.hide),false);
			addListener(btn,'click',function(){
				SP.UI.toggle();
			},false);
			SP.bindSwitch(SP.UI.frame);
		},
		bindSwitch:function(el){
			var themes=$$('SP_theme','a',el);
			for(var i=0;i<themes.length;i++){
				addListener(themes[i],'click',bind({'theme':themes[i]},SP.switchTheme),false);
			}
		},
		switchTheme:function(){
			var name=this.theme.getAttribute('name'),
				xmlHttp = createxmlHttp(),
				url = '?action=WPXW_switchTheme&theme='+name;
			addClassName(this.theme,'loading');
			xmlHttp.open("GET", url, true);
			xmlHttp.setRequestHeader("Content-type", "charset=UTF-8");
			xmlHttp.onreadystatechange = bind(this,function() {
				if (xmlHttp.readyState == 4 || xmlHttp.readyState=="complete") {
					if (xmlHttp.status == 200) {
						var res=xmlHttp.responseText;
						if(res.trim()==='success!'){
							alert('Theme Switching Succeed! The new theme is being loaded.');
							window.location.href=window.location.href.replace(/(\?preview_theme=|#|\?wptheme=).*/i,'');
						}else{
							alert('Theme Switching Failed! Please contact author for support');
						}
					}else{
						alert(xmlHttp.responseText);
					}
					removeClassName(this.theme,'loading');
				}
			})
			xmlHttp.send(null);
		}
	}
	String.prototype.trim=function(){
		return this.replace(/(^\s*)|(\s*$)/g, "");
	}
	/* UI Class */
	function UI(el,str){
		this.el=el;
		this.content=str;
		this.init();
	}
	UI.prototype={
		init:function(){
			this.frame=document.createElement('div');
			this.frame.className='SP_the_frame';
			var data='<table class="SP_dialog_table">'+
						'<tbody>'+
							'<tr>'+
								'<td class="SP_t_topleft"></td>'+
								'<td class="SP_t_topborder"></td>'+
								'<td class="SP_t_topright"></td>'+
							'</tr>'+
							'<tr>'+
								'<td class="SP_t_leftborder"></td>'+
								'<td class="SP_t_content">'+
									'<div><div class="SP_content">'+this.content+'</div></div>'+
								'</td>'+
								'<td class="SP_t_rightborder"></td>'+
							'</tr>'+
							'<tr>'+
								'<td class="SP_t_bottomleft"></td>'+
								'<td class="SP_t_bottomborder"></td>'+
								'<td class="SP_t_bottomright"></td>'+
							'</tr>'+
						'</tbody></table><div class="SP_close"></div><div class="SP_arrow"></div>';
				
			this.frame.innerHTML=data;
			document.body.appendChild(this.frame);
			addListener($$('SP_close','div',this.frame)[0],'click',bind(this,this.hide),false);
		},
		show:function(){
			this.setPos();
			this.frame.style.display='block';
		},
		hide:function(){
			this.frame.style.display='none';
		},
		toggle:function(){
			this.frame.style.display=='block'?this.hide():this.show();
		},
		setPos:function(){
			var ep=getObjPoint(this.el),
				ds=getDocSize(),ss=getScrollSize(),
				ws=getWindowSize(),
				right,top;
			top=IE()&&IE()<=6?ep.y+40:ss.y+ep.y+40;
			right=ds.x-ep.x-50;
			this.frame.style.top=top+'px';
			this.frame.style.right=right+'px';
		}
	}

	documentReady(function(){
		SP.start();
	});
})(window,undefined);