<?php
class Criteo
{
	var $scripts;
	var $criteo;
	var $commonScript="var c=document.createElement('iframe');
			c.width='100px';
			c.height='200px';
			c.style.display='none';
			c.id='ifcr';
			newdiv = document.createElement('div');
			newdiv.id='divcr';
			document.body.appendChild(newdiv);
			mydiv=document.getElementById('divcr');
			mydiv.appendChild(c);";

	function Criteo(){
		$l = dirname(__FILE__)."/../conf/criteo.cfg.php";
		if(file_exists($l))@require_once $l; 
		if($criteo) $this->criteo = $criteo;
	}

	function begin() {
		$l = dirname(__FILE__)."/../conf/criteo.cfg.php";
		if(file_exists($l))
			$content=file_get_contents($l);
		if(empty($content))
			return false;
		return true;	
	}

	########### 메인 main/index.php 태그생성 ########### 
	function get_main(){ 
			$scripts="<script>addOnloadEvent(function(){ 
			".$this->commonScript." 
			document.getElementById('ifcr').src='../partner/criteoscript.php?mode=get_main';
			});</script>";
		$this->scripts = $scripts; 
	}

	function get_main_scripts(){ 
		$scripts = "<script type=\"text/javascript\">
		var cto_conf = 't1=sendEvent&c=2&p=".$this->criteo['p_code']."';
		var cto_conf_event = 'v=2&wi=".$this->criteo['wi_code1']."&pt1=0&pt2=1';
		var CRITEO = function(){var b ={Load:function(d){var c = window.onload;window.onload = function(){if(c){c()}d()}}};function a(e){if(document.createElement){var c = document.createElement((typeof(cto_container)!= 'undefined' && cto_container == 'img')? 'img' : 'iframe');if(c){c.width='1px';c.height='1px';c.style.display='none';c.src = e;var d = document.getElementById('cto_mg_div');if(d!=null&&d.appendChild){d.appendChild(c)}}}}return{Load:function(c){document.write(\"<div id='cto_mg_div' style='display:none;'></div>\");c+='&'+cto_conf;var f='';if(typeof(cto_conf_event)!='undefined')f=cto_conf_event;if(typeof(cto_container)!='undefined'){if(cto_container=='img')c+='&resptype=gif';}if(typeof(cto_params)!='undefined'){for(var key in cto_params){if(key.substring(0,2)!='kw'&&(typeof(cto_params[key])=='string'))f+='&'+key+'='+encodeURIComponent(cto_params[key]);}if(cto_params['kw']!=undefined)c+='&kw='+encodeURIComponent(cto_params['kw']); }c+='&p1='+encodeURIComponent(f);c+='&cb='+Math.floor(Math.random()*99999999999);try{c+='&ref='+encodeURIComponent(document.referrer);}catch(e){}try{c+='&sc_r='+encodeURIComponent(screen.width+'x'+screen.height);}catch(e){}try{c+='&sc_d='+encodeURIComponent(screen.colorDepth);}catch(e){}b.Load(function(){a(c.substring(0,2000))})}}}();CRITEO.Load(document.location.protocol+'//dis.as.criteo.com/dis/dis.aspx?');
		</script>";
		return $scripts;
	}

	
	########### 상품리스트 goods_list.php 태그생성 ########### 
	########### parameter : 상품인덱스 배열        ###########
	function get_list($arr_goodsno){
		$scripts="<script>addOnloadEvent(function(){ 
			".$this->commonScript." 
			document.getElementById('ifcr').src='../partner/criteoscript.php?mode=get_list&arr_goodsno=".urlencode(serialize($arr_goodsno))."';
			});</script>";
		$this->scripts = $scripts;
	}


	function get_list_scripts($arr_goodsno) {
		$scripts = "<script type=\"text/javascript\">
		//cto listing tag
		var cto_params = [];
		//CONFIGURE THE FOLLOWING VARIABLES 
		cto_params[\"i1\"] = \"".$arr_goodsno[0]."\";
		cto_params[\"i2\"] = \"".$arr_goodsno[1]."\";
		//DO NOT MODIFY AFTER THIS LINE
		var cto_conf = 't1=sendEvent&c=2&p=".$this->criteo['p_code']."';
		var cto_conf_event = 'v=2&wi=".$this->criteo['wi_code1']."&pt1=3';
		var CRITEO = function(){var b ={Load:function(d){var c = window.onload;window.onload = function(){if(c){c()}d()}}};function a(e){if(document.createElement){var c = document.createElement((typeof(cto_container)!= 'undefined' && cto_container == 'img')? 'img' : 'iframe');if(c){c.width='1px';c.height='1px';c.style.display='none';c.src = e;var d = document.getElementById('cto_mg_div');if(d!=null&&d.appendChild){d.appendChild(c)}}}}return{Load:function(c){document.write(\"<div id='cto_mg_div' style='display:none;'></div>\");c+='&'+cto_conf;var f='';if(typeof(cto_conf_event)!='undefined')f=cto_conf_event;if(typeof(cto_container)!='undefined'){if(cto_container=='img')c+='&resptype=gif';}if(typeof(cto_params)!='undefined'){for(var key in cto_params){if(key.substring(0,2)!='kw'&&(typeof(cto_params[key])=='string'))f+='&'+key+'='+encodeURIComponent(cto_params[key]);}if(cto_params['kw']!=undefined)c+='&kw='+encodeURIComponent(cto_params['kw']); }c+='&p1='+encodeURIComponent(f);c+='&cb='+Math.floor(Math.random()*99999999999);try{c+='&ref='+encodeURIComponent(document.referrer);}catch(e){}try{c+='&sc_r='+encodeURIComponent(screen.width+'x'+screen.height);}catch(e){}try{c+='&sc_d='+encodeURIComponent(screen.colorDepth);}catch(e){}b.Load(function(){a(c.substring(0,2000))})}}}();CRITEO.Load(document.location.protocol+'//dis.as.criteo.com/dis/dis.aspx?');
		</script>"; 
		return $scripts;
	}

	########### 상품정보 goods_view.php 태그생성 ########### 
	########### parameter : 상품인덱스           ###########
	function get_detail($goodsno) {
		$scripts="<script>addOnloadEvent(function(){ 
			".$this->commonScript." 
			document.getElementById('ifcr').src='../partner/criteoscript.php?mode=get_detail&goodsno=".$goodsno."';
			});</script>";
		$this->scripts = $scripts;
	}

	function get_detail_scripts($goodsno) {
		$scripts="<script type=\"text/javascript\">
		//cto product tag
		var cto_params = [];
		//CONFIGURE THE FOLLOWING VARIABLES
		cto_params[\"i\"] = \"".$goodsno."\";
		//DO NOT MODIFY AFTER THIS LINE
		var cto_conf = 't1=sendEvent&c=2&p=".$this->criteo['p_code']."';
		var cto_conf_event = 'v=2&wi=".$this->criteo['wi_code1']."&pt1=2';
		var CRITEO = function(){var b ={Load:function(d){var c = window.onload;window.onload = function(){if(c){c()}d()}}};function a(e){if(document.createElement){var c = document.createElement((typeof(cto_container)!= 'undefined' && cto_container == 'img')? 'img' : 'iframe');if(c){c.width='1px';c.height='1px';c.style.display='none';c.src = e;var d = document.getElementById('cto_mg_div');if(d!=null&&d.appendChild){d.appendChild(c)}}}}return{Load:function(c){document.write(\"<div id='cto_mg_div' style='display:none;'></div>\");c+='&'+cto_conf;var f='';if(typeof(cto_conf_event)!='undefined')f=cto_conf_event;if(typeof(cto_container)!='undefined'){if(cto_container=='img')c+='&resptype=gif';}if(typeof(cto_params)!='undefined'){for(var key in cto_params){if(key.substring(0,2)!='kw'&&(typeof(cto_params[key])=='string'))f+='&'+key+'='+encodeURIComponent(cto_params[key]);}if(cto_params['kw']!=undefined)c+='&kw='+encodeURIComponent(cto_params['kw']); }c+='&p1='+encodeURIComponent(f);c+='&cb='+Math.floor(Math.random()*99999999999);try{c+='&ref='+encodeURIComponent(document.referrer);}catch(e){}try{c+='&sc_r='+encodeURIComponent(screen.width+'x'+screen.height);}catch(e){}try{c+='&sc_d='+encodeURIComponent(screen.colorDepth);}catch(e){}b.Load(function(){a(c.substring(0,2000))})}}}();CRITEO.Load(document.location.protocol+'//dis.as.criteo.com/dis/dis.aspx?');
		</script>";
		return  $scripts;
	}

	########### 장바구니 goods_cart.php 태그생성         ########### 
	########### parameter : 장바구니상품정보 인덱스 배열 ###########
	function get_cart($arr_cart) {
		$scripts="<script>addOnloadEvent(function(){ 
			".$this->commonScript." 
			document.getElementById('ifcr').src='../partner/criteoscript.php?mode=get_cart&arr_cart=".urlencode(serialize($arr_cart))."';
			});</script>";
		$this->scripts = $scripts; 
	}

	function get_cart_scripts($arr_cart) {
		$scripts="<script type=\"text/javascript\">
		//cto basket tag
		var cto_params = [];
		//CONFIGURE THE FOLLOWING VARIABLES
		cto_params[\"i1\"] = \"".$arr_cart[0]['goodsno']."\";
		cto_params[\"p1\"] = \"".$arr_cart[0]['price']."\";
		cto_params[\"q1\"] = \"".$arr_cart[0]['ea']."\";
		cto_params[\"i2\"] = \"".$arr_cart[1]['goodsno']."\";
		cto_params[\"p2\"] = \"".$arr_cart[1]['price']."\";
		cto_params[\"q2\"] = \"".$arr_cart[1]['ea']."\";
		//DO NOT MODIFY AFTER THIS LINE
		var cto_conf = 't1=transaction&c=2&p=".$this->criteo['p_code']."';
		var cto_conf_event = 'v=2&wi=".$this->criteo['wi_code2']."&s=0';
		var CRITEO = function(){var b ={Load:function(d){var c = window.onload;window.onload = function(){if(c){c()}d()}}};function a(e){if(document.createElement){var c = document.createElement((typeof(cto_container)!= 'undefined' && cto_container == 'img')? 'img' : 'iframe');if(c){c.width='1px';c.height='1px';c.style.display='none';c.src = e;var d = document.getElementById('cto_mg_div');if(d!=null&&d.appendChild){d.appendChild(c)}}}}return{Load:function(c){document.write(\"<div id='cto_mg_div' style='display:none;'></div>\");c+='&'+cto_conf;var f='';if(typeof(cto_conf_event)!='undefined')f=cto_conf_event;if(typeof(cto_container)!='undefined'){if(cto_container=='img')c+='&resptype=gif';}if(typeof(cto_params)!='undefined'){for(var key in cto_params){if(key.substring(0,2)!='kw'&&(typeof(cto_params[key])=='string'))f+='&'+key+'='+encodeURIComponent(cto_params[key]);}if(cto_params['kw']!=undefined)c+='&kw='+encodeURIComponent(cto_params['kw']); }c+='&p1='+encodeURIComponent(f);c+='&cb='+Math.floor(Math.random()*99999999999);try{c+='&ref='+encodeURIComponent(document.referrer);}catch(e){}try{c+='&sc_r='+encodeURIComponent(screen.width+'x'+screen.height);}catch(e){}try{c+='&sc_d='+encodeURIComponent(screen.colorDepth);}catch(e){}b.Load(function(){a(c.substring(0,2000))})}}}();CRITEO.Load(document.location.protocol+'//dis.as.criteo.com/dis/dis.aspx?');
		</script>";
		return $scripts;
	}

	########### 상품주문 완료 order_end.php	 태그생성 ########### 
	########### parameter : 완료된 상품 인덱스 배열   ###########
	function get_order($arr_order) {
		$scripts="<script>addOnloadEvent(function(){ 
			".$this->commonScript." 
			document.getElementById('ifcr').src='../partner/criteoscript.php?mode=get_order&arr_order=".urlencode(serialize($arr_order))."';
			});</script>";
		$this->scripts = $scripts; 
	}

	function get_order_scripts($arr_order) {
		$scripts="
		<script type=\"text/javascript\"> var CRITEO=function(){var b={Load:function(d){var c=window.onload;window.onload=function(){d();if(c){c()}}}};function a(e){if(document.createElement){var c=document.createElement(\"IFRAME\");if(c){c.width=\"1px\";c.height=\"1px\";c.style.display=\"none\";c.src=e;var d=document.getElementById(\"cto_mg_div\");if(d!=null&&d.appendChild){d.appendChild(c)}}}}return{Load:function(c){document.write(\"<div id='cto_mg_div' style='display:none;'></div>\");b.Load(function(){a(c)})}}}(); ";
		$scripts.="CRITEO.Load(\"https:\"+\"//dis.as.criteo.com/dis/dis.aspx?p1=\"+escape(\"v=2&wi=".$this->criteo['wi_code2']."&t=".$arr_order[0]['ordno']."&s=1";
		for($i=0;$i<sizeof($arr_order);$i++) {
			$scripts.="&i".($i+1)."=".$arr_order[$i]['goodsno']."&p".($i+1)."=".$arr_order[$i]['price']."&q".($i+1)."=".$arr_order[$i]['ea'];	
		}
		$scripts.="\")+\"&t1=transaction&p=".$this->criteo['p_code']."&c=2&cb=\"+Math.floor(Math.random()*99999999999)); </script>";
		return $scripts;
	}

}
?>