(function(window,undefined){
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
	};
	
	function get_select_value(id){
		var options=$T('option',$(id));
		for(var i=0;i<options.length;i++){
			if(options[i].selected==true){
				return options[i].value;
			}
		}
		return false;
	}
	function getDate(){
		var date=new Date,
			year=date.getFullYear(),
			month=date.getMonth()+1,
			day=date.getDate();
		month=month<10?'0'+month:month;
		day=day<10?'0'+day:day;
		return year+'-'+month+'-'+day;
	}
	addListener($('choosetheme'),'change',function(){
		var area=$('new-theme-area'),
			value=get_select_value('choosetheme'),
			inputs=$T('input', area),
			date=getDate();
		if(value.indexOf('/')>-1){
			value=value.substring(value.indexOf('/')+1);
		}
		inputs[0].value=value+'-';
		inputs[1].value=date;
		$('copy-save').value='Copy '+value;
		area.style.display='table-row';
		if(!this['lisTimer']){
			lisTimer=setInterval(function(){
				$('copy-info-area').innerHTML='You will copy <code>'+get_select_value('choosetheme')+'</code> to <code>'+inputs[0].value+inputs[1].value+'</code>';
			},800);
		}
	},false);

	
})(window,undefined);