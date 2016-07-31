<?php
class Acecounter
{
	var $acecounter;
	var $scripts;

	function Acecounter(){
		$l = dirname(__FILE__)."/../conf/config.acecounter.php";
		if(file_exists($l))@include $l;
		if($acecounter) $this->acecounter = $acecounter;
	}

	/* 서비스상태 */
	function open_state(){
		$startDate = date('Y-m-d',time());
		if($this->acecounter['use'] != 'Y') return false;
		if(!$this->acecounter['gcode']) return false;
		if($this->acecounter['start'] > $startDate) return false;
		return true;
	}

	function remove_cl($str){
		return eregi_replace("\t",'',$str)."\n";
	}

	function member_join($id){
		if(!$this->open_state()) return false;
		$scripts = "
		<script type='text/javascript'>
		var _jn = 'join';
		var _jid = '$id';
		</script>";
		$this->scripts = $this->remove_cl($scripts.$this->scripts);
	}

	function member_hack(){
		if(!$this->open_state()) return false;
		$scripts = "
		<script type='text/javascript'>
		var _jn = 'withdraw';
		</script>";
		$this->scripts = $this->remove_cl($scripts.$this->scripts);
	}

	function member_login($id){
		if(!$this->open_state()) return false;
		$db = & $GLOBALS['db'];
		$table = GD_MEMBER;
		$data = $db->fetch("select * from $table where m_id='$id' limit 1");

		$scripts = "
		<script type='text/javascript'>";
		if($data['birth_year']){
			$age = date('Y') - $data['birth_year'];
			$scripts .= "
			var _ag   = ".$age.";"; // 로그인사용자 나이
		}
		if($id) $scripts .= 'var _id   = "'.addslashes($id).'";'; // 로그인사용자 아이디
		if($data['marriyn']){
			$tmp = array('y'=>'married','n'=>'single');
			$scripts .= "
			var _mr   = '".$tmp[$data[marriyn]]."';"; // 로그인사용자 결혼여부 ('single' , 'married' )
		}

		if($data['sex']){
			$tmp = array('m'=>'man','w'=>'woman');
			$scripts .= "
			var _gd   = '".$tmp[$data[sex]]."';"; // 로그인사용자 성별 ('man' , 'woman')
		}
		$scripts .= "
		</script>";
		$this->scripts = $this->remove_cl($scripts.$this->scripts);
	}

	function goods_search($key){
		if(!$this->open_state()) return false;
		$scripts = "<script type='text/javascript'>var _skey=\"".addslashes($key)."\";</script>";
		$this->scripts = $this->remove_cl($scripts.$this->scripts);
	}

	function get_category($goodsno){
		$db = & $GLOBALS['db'];
		$table = GD_GOODS_LINK;
		$tmp = $db->fetch("select ".getCategoryLinkQuery('category', null, 'max')." from $table  where goodsno='$goodsno' and category is not null limit 1");
		return $tmp[category];
	}

	function goods_view($goodsno,$goodsnm,$price,$category=''){
		if(!$this->open_state()) return false;

		$scripts = $this->get_eCommerce();
		if(!$category)$category = $this->get_category($goodsno);
		$catnm = getCatename($category);
		$scripts .= '
		<script language=\'javascript\'>
		var _ct = _RP("'.addslashes($catnm).'");
		var _pd =_RP("'.addslashes(strip_tags($goodsnm)).'");
		var _amt = _RP("'.addslashes($price).'",1);
		_A_ct=Array("'.addslashes($catnm).'");
		_A_amt=Array("'.addslashes($price).'");
		_A_nl=Array("1");
		_A_pl=Array("'.addslashes($goodsno).'");
		_A_pn=Array("'.addslashes(strip_tags($goodsnm)).'");
		</script>';
		$this->scripts = $this->remove_cl($scripts.$this->scripts);
	}

	function goods_cart($item){
		if(!$this->open_state()) return false;
		$scripts = $this->get_eCommerce();
		$scripts .= "
		<script language='javascript'>";
		if($item) foreach($item as $k => $v){
			$price = ($v[price]+$v[addprice]) * $v[ea];
			$category = $this->get_category($v[goodsno]);
			$catnm = getCatename($category);
			$scripts .= '
			_A_amt['.$k.']="'.addslashes($price).'";
			_A_nl['.$k.']="'.addslashes($v[ea]).'";
			_A_pl['.$k.']="'.addslashes($v[goodsno]).'";
			_A_pn['.$k.']="'.addslashes(strip_tags($v[goodsnm])).'";
			_A_ct['.$k.']="'.addslashes($catnm).'";';
		}else{
			$scripts .= '
			AEC_D_A();';
		}
		$scripts .= "
		</script>";
		$this->scripts = $this->remove_cl($scripts.$this->scripts);
	}

	/**
	 * 장바구니 상품추가(goods_cart.php에서 실행)
	 *
	 * @author pr,
	 * @param array $item 장바구니상품목록
	 * @param array $goodsnos 상품번호
	 * @param array $eas 수량
	 * @return bool
	 *
	 */
	function goods_cart_add($item, $goodsnos, $eas)
	{
		if (!$this->open_state()) return false;
		$this->goods_cart($item);
		$scripts = '';
		$scripts = "<script type='text/javascript'>\n";
		foreach ($goodsnos as $k => $goodsno) {
			$scripts .= "AEC_F_D('".$goodsno."','i',".$eas[$k].");\n";
		}
		$scripts .= "</script>";
		$this->scripts = $this->remove_cl($this->scripts.$scripts);
		return true;
	}

	/**
	 * 장바구니 상품수량수정(goods_cart.php에서 실행)
	 *
	 * @author pr,
	 * @param array $item 장바구니상품목록
	 * @param array $idxs 장바구니 인덱스
	 * @param array $eas 수량
	 * @return bool
	 *
	 */
	function goods_cart_mod($item, $idxs, $eas)
	{
		if (!$this->open_state()) return false;
		$this->goods_cart($item);
		$scripts = '';
		$scripts .= "<script type='text/javascript'>\n";
		foreach ($idxs as $k => $v) {
			$scripts .= "if(_A_nl[".$v."] > ".$eas[$k]."){\n";
			$scripts .= "	AEC_F_D(_A_pl[".$v."],'o',_A_nl[".$v."]-".$eas[$k].");\n";
			$scripts .= "} else if(_A_nl[".$v."] < ".$eas[$k]."){\n";
			$scripts .= "	AEC_F_D(_A_pl[".$v."],'i',".$eas[$k]."-_A_nl[".$v."]);\n";
			$scripts .= "}\n";
		}
		$scripts .= "</script>";
		$this->scripts = $this->remove_cl($this->scripts.$scripts);
		return true;
	}

	/**
	 * 장바구니 상품개개삭제(goods_cart.php에서 실행)
	 *
	 * @author pr,
	 * @param array $item 장바구니상품목록
	 * @param array $idxs 상품번호
	 * @return bool
	 *
	 */
	function goods_cart_del($item, $idxs)
	{
		if (!$this->open_state()) return false;
		$this->goods_cart($item);
		$scripts = '';
		$scripts .= "<script type='text/javascript'>\n";
		foreach($idxs as $v){
			$scripts .= "AEC_F_D(_A_pl[".$v."],'o',_A_nl[".$v."]);\n";
		}
		$scripts .= "</script>";
		$this->scripts = $this->remove_cl($this->scripts.$scripts);
		return true;
	}

	/**
	 * 장바구니 비우기(goods_cart.php에서 실행)
	 *
	 * @author pr,
	 * @param array $item 장바구니상품목록
	 * @return bool
	 *
	 */
	function goods_cart_dels($item)
	{
		if (!$this->open_state()) return false;
		$this->goods_cart($item);
		$scripts = '';
		$scripts .= "<script type='text/javascript'>\n";
		$scripts .= "AEC_D_A();\n";
		$scripts .= "</script>";
		$this->scripts = $this->remove_cl($this->scripts.$scripts);
		return true;
	}

	/**
	 * Sendlog 준비(settle.php에서 호출)
	 *
	 * @author pr
	 * @return bool
	 *
	 */
	function readySendlog()
	{
		setcookie("aceSendlog",'Y',0,'/');
		return true;
	}

	/**
	 * 주문정보 별도 전송(order_end.php에서 실행)
	 *
	 * @author pr
	 * @param array $item 주문상품목록
	 * @param string $ordno 주문번호
	 * @return bool
	 *
	 */
	function actSendlog($item, $ordno)
	{
		if (isset($_COOKIE['aceSendlog']) === false) {
			return false;
		}
		$url = 'https://dgc1.acecounter.com/sendlog.amz';
		$params = array(
			'cuid' => $this->acecounter['gcode'],
			'authcd' => 'Z29kb21nwmyxmyzhbGw=',
			'ip' =>  $_SERVER['REMOTE_ADDR'],
			'orderno' => $ordno,
			'mode' => 'x',
			'll' => ''
		);
		if ($item) {
			$tmpGoods = array();
			foreach($item as $k=>$v){
				$price = $v[price] * $v[ea];
				$category = $this->get_category($v[goodsno]);
				$catnm = getCatename($category);
				$tmp = array();
				array_push($tmp, str_replace(array('@','^'), '', $catnm));
				array_push($tmp, str_replace(array('@','^'), '', strip_tags($v['goodsnm'])));
				array_push($tmp, $v['goodsno']);
				array_push($tmp, $v['ea']);
				array_push($tmp, $price);
				array_push($tmpGoods, implode("@", $tmp));
			}
			$params['ll'] = implode("^", $tmpGoods);
		}
		$result = $this->curlFunc($url, $params);
		$this->log('<actSendlog>'."\n".'URL : '.$url."\n".'POST_Params : '.getVars('', $params));
		setcookie("aceSendlog",'',time() - 3600,'/');
		return true;
	}

	/**
	 * curl 전송
	 *
	 * @author pr
	 * @param string $url 전송URL
	 * @param mixed $postdata POST정보 ('para1=val1&...' or array)
	 * @return mixed
	 *
	 */
	function curlFunc($url, $postdata='') {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 90);
		$ret_val = curl_exec($ch);
		curl_close($ch);
		return $ret_val;
	}

	function order_end($item, $ordno){
		if(!$this->open_state()) return false;

		$scripts = $this->get_eCommerce();
		$scripts .= "
		<script language='javascript'>";
		if($item) foreach($item as $k=>$v){
			$price = $v[price] * $v[ea];
			$tot += $price;
			$category = $this->get_category($v[goodsno]);
			$catnm = getCatename($category);
			$scripts .= '
			_A_amt['.$k.']="'.addslashes($price).'";
			_A_nl['.$k.']="'.addslashes($v[ea]).'";
			_A_pl['.$k.']="'.addslashes($v[goodsno]).'";
			_A_pn['.$k.']="'.addslashes(strip_tags($v[goodsnm])).'";
			_A_ct['.$k.']="'.addslashes($catnm).'";';
		}
		$scripts .= "
		var _order_code='".$ordno."';
		var _amt = '$tot' ;
		AEC_B_L();</script>";

		$this->scripts = $this->remove_cl($scripts.$this->scripts);

		// 주문정보 별도 전송
		$this->actSendlog($item, $ordno);
	}

	function get_eCommerce(){
		if(!$this->open_state()) return false;
		$scripts = "
		<script language='javascript'>
		var _JV=\"AMZ201211052\";
		var _UD='undefined';var _UN='unknown';
		function _IDV(a){return (typeof a!=_UD)?1:0}
		var _CRL='http://'+'dgc20.acecounter.com:8080/';
		var EL_GCD='".$this->acecounter['gcode']."';
		if( document.URL.substring(0,8) == 'https://' ){ _CRL = 'https://dgc20.acecounter.com/logecgather/' ;};
		//===========교체 부분 시작 201211051=========
		if(!_IDV(_A_i)) var _A_i = new Image() ;if(!_IDV(_A_i0)) var _A_i0 = new Image() ;if(!_IDV(_A_i1)) var _A_i1 = new Image() ;if(!_IDV(_A_i2)) var _A_i2 = new Image() ;if(!_IDV(_A_i3)) var _A_i3 = new Image() ;if(!_IDV(_A_i4)) var _A_i4 = new Image() ;
		if(!_IDV(_ll)) var _ll='';
		function _AGC(nm) {var _DC = document.cookie ; var cn = nm + \"=\"; var nl = cn.length; var cl = _DC.length; var i = 0; while ( i < cl ) { var j = i + nl; if ( _DC.substring( i, j ) == cn ){ var val = _DC.indexOf(\";\", j ); if ( val == -1 ) val = _DC.length; return unescape(_DC.substring(j, val)); }; i = _DC.indexOf(\" \", i ) + 1; if ( i == 0 ) break; } return ''; }
		function _ASC( nm, val, exp ){var expd = new Date(); if ( exp ){ expd.setTime( expd.getTime() + ( exp * 1000 )); document.cookie = nm+\"=\"+ escape(val) + \"; expires=\"+ expd.toGMTString() +\"; path=\"; }else{ document.cookie = nm + \"=\" + escape(val);};}
		function _RP(s,m){if(typeof s=='string'){if(m==1){return s.replace(/[#&^@,]/g,'');}else{return s.replace(/[#&^@]/g,'');}}else{return s;} };
		function _RPS(a,b,c){var d=a.indexOf(b),e=b.length>0?c.length:1; while(a&&d>=0){a=a.substring(0,d)+c+a.substring(d+b.length);d=a.indexOf(b,d+e);}return a};
		function AEC_F_D(pd,md,cnum){ var i = 0 , amt = 0 , num = 0 ; var cat = '' ,nm = '' ; num = cnum ; md=md.toLowerCase(); if( md == 'b' || md == 'i' || md == 'o' ){ for( i = 0 ; i < _A_pl.length ; i ++ ){ if( _A_pl[i] == pd ){ nm = _RP(_A_pn[i]); amt = ( parseInt(_RP(_A_amt[i],1)) / parseInt(_RP(_A_nl[i],1)) ) * num ; cat = _RP(_A_ct[i]); _A_cart = _CRL+'?cuid='+EL_GCD; _A_cart += '&md='+md+'&ll='+_RPS(escape(cat+'@'+nm+'@'+amt+'@'+num+'^&'),'+','%2B'); break;};};if(_A_cart.length > 0 ) _A_i.src = _A_cart;setTimeout(\"\",2000);};};
		function AEC_D_A(){ var i = 0,_AEC_str= ''; var ind = 0; for( i = 0 ; i < _A_pl.length ; i ++ ){ _AEC_str += _RP(_A_ct[i])+'@'+_RP(_A_pn[i])+'@'+_RP(_A_amt[i],1)+'@'+_RP(_A_nl[i],1)+'^'; if(  escape(_AEC_str).length > 800 ){ if(ind > 4) ind = 0; _AEC_str = _RPS(escape(_AEC_str),'+','%2B')+'&cmd=on' ; AEC_S_F(_AEC_str , 'o', ind) ; _AEC_str = '' ; ind++; }; }; if( _AEC_str.length > 0 ){ if(ind+1 > 4) ind = 0; AEC_S_F(_RPS(escape(_AEC_str),'+','%2B'), 'o', ind+1) ; }; };
		function AEC_B_A(){var i=0,_AEC_str='',_A_cart='';var ind = 0;_A_cart = _CRL+'?cuid='+EL_GCD+'&md=b';for( i = 0 ; i < _A_pl.length ; i ++ ){_AEC_str += ACE_REPL(_A_ct[i])+'@'+ACE_REPL(_A_pn[i])+'@'+ACE_REPL(_A_amt[i],1)+'@'+ACE_REPL(_A_nl[i],1)+'^';if(escape(_AEC_str).length > 800 ){if(ind > 4) ind = 0;_AEC_str = _RPS(escape(_AEC_str),'+','%2B')+'&cmd=on';AEC_S_F(_AEC_str,'b',ind); _AEC_str = '' ;ind++;};}; if( _AEC_str.length > 0 ){if(ind+1 > 4) ind = 0; AEC_S_F(_RPS(escape(_AEC_str),'+','%2B'),'b',ind+1);};};
		function AEC_U_V(pd,bnum){ var d_cnt = 0 ; var A_amt = 0 ; var A_md = 'n' ;var _AEC_str = '' ; for( j = 0 ; j < _A_pl.length; j ++ ){ if( _A_pl[j] == pd ){ d_cnt = 0; if( _A_nl[j] != bnum ){ d_cnt = bnum - parseInt(_RP(_A_nl[j],1)) ; A_amt = Math.round( parseInt(_RP(_A_amt[j],1)) / parseInt(_RP(_A_nl[j],1))); if( d_cnt > 0 ){ A_md = 'i' ; }else{ A_md = 'o' ;};_A_amt[j] = A_amt*Math.abs(d_cnt) ; _A_nl[j] = Math.abs(d_cnt);_AEC_str += _RP(_A_ct[j])+'@'+_RP(_A_pn[j])+'@'+_RP(_A_amt[j],1)+'@'+_RP(_A_nl[j],1)+'^';}}};if( _AEC_str.length > 0 ){ AEC_S_F(_RPS(escape(_AEC_str),'+','%2B') ,A_md, 0);};};
		function AEC_B_L(){try{var _AEC_order_code_cookie='';	if(document.cookie.indexOf('AECORDERCODE')>0){_AEC_order_code_cookie = _AGC('AECORDERCODE');};if(_order_code!=''){	if(_order_code==_AEC_order_code_cookie){return '';}else{_ASC(\"AECORDERCODE\",_order_code,86400 * 30 * 12);};}}catch(er){}; var i=0; _ll=''; for( i = 0 ; i < _A_pl.length ; i ++ ){ _ll += _RP(_A_ct[i])+'@'+_RP(_A_pn[i])+'@'+_RP(_A_amt[i],1)+'@'+_RP(_A_nl[i],1)+'@'+_RP(_A_pl[i],1)+'^';}; };
		function AEC_S_F(str,md,idx){ var i = 0,_A_cart = ''; var k = eval('_A_i'+idx); if(md == 'I' ) md = 'i' ; if(md == 'O' ) md = 'o' ; if(md == 'B' ) md = 'b' ; if( md == 'b' || md == 'i' || md == 'o'){ _A_cart = _CRL+'?cuid='+EL_GCD ; _A_cart += '&md='+md+'&ll='+(str)+'&'; k.src = _A_cart;window.setTimeout('',2000);};};
		//===========교체 부분 끝 201211051========
		if(!_IDV(_A_pl)) var _A_pl = Array(1) ;
		if(!_IDV(_A_nl)) var _A_nl = Array(1) ;
		if(!_IDV(_A_ct)) var _A_ct = Array(1) ;
		if(!_IDV(_A_pn)) var _A_pn = Array(1) ;
		if(!_IDV(_A_amt)) var _A_amt = Array(1) ;
		var _A_idx = 0 ;
		</script>";
		return $scripts;
	}

	function get_common_script(){
		if(!$this->open_state()) return false;
		$cfg = & $GLOBALS[cfg];
		$scripts = "
		<script type='text/javascript'>
		function ".$this->acecounter['gcode']."(){ gcode_act(); }
		if(typeof EL_GUL == 'undefined'){
		var EL_GUL = 'dgc20.acecounter.com';var EL_GPT='8080'; var _AIMG = new Image(); var _bn=navigator.appName; var _PR = location.protocol==\"https:\"?\"https://\"+EL_GUL:\"http://\"+EL_GUL+\":\"+EL_GPT;if( _bn.indexOf(\"Netscape\") > -1 || _bn==\"Mozilla\"){ setTimeout(\"_AIMG.src = _PR+'/?cookie';\",1); } else{ _AIMG.src = _PR+'/?cookie'; };
		var GCODE = '".$this->acecounter['gcode']."';
		document.writeln(\"<scr\"+\"ipt language='javascript' src='".$cfg['rootDir']."/lib/js/acecounter_V70.js'></scr\"+\"ipt>\");
		}
		</script>
		<noscript><img src='http://dgc20.acecounter.com:8080/?uid=".$this->acecounter['gcode']."&je=n&' border=0 width=0 height=0></noscript>";
		$this->scripts = $this->remove_cl($scripts);
	}

	/**
	 * 로그 남기기
	 *
	 * @author pr
	 * @param string $msg 로그메시지
	 * @return bool
	 *
	 */
	function log( $msg )
	{
		$msg = '['.date('Y-m-d_H:i:s:B').'] '.$msg."\n";
		$fPath = dirname(__FILE__) . '/../log/acecounter_' . date('D') . '.log';
		if (file_exists($fPath) === true && date('Ymd',filemtime($fPath)) != date('Ymd')) {
			@unlink($fPath);
		}
		error_log($msg, 3, $fPath);
		@chmod($fPath, 0707);
		return true;
	}


}
?>