<?
class Cart extends old_Cart
{
	var $db = null;
	var $isDirect = 0;
	var $item = array();
	var $mode = 'regular';
	var $ordering = true;

	var $keepPeriod=0;	//보관기간
	var $runoutDel=0;	//품절상품삭제여부
	var $redirectType='Direct';	//장바구니담고난후액션
	var $maxItemCnt=300;	//상품 최대보관개수
	var $act;
	var $ajaxMode=false;	//Ajax모드 True:False
	var $errorMsg;	//애러 메시지
	var $estimateUse='';	// 견적서사용여부
	var $estimateSeal='';	// 인감 이미지
	var $estimateMessage='';	// 견적서 메세지

	var $msg = array(
		'maxCount' => '장바구니 저장 가능상품수 초과! 장바구니의 상품을 먼저 주문하거나 정리 한 후 담아주세요'
	);

	var $is_sugi = false;

	function Cart($isDirect=0,$act=null)
	{
		global $orderitem_mode;

		if ($_GET['cart_type'] == 'todayshop') {
			$this->mode = 'todayshop';
		}

		if ($orderitem_mode == 'cart') $this->ordering = false;

		$this->db = $GLOBALS['db'];
		$this->isDirect = !$isDirect ? 0 : 1;
		$this->cartCfgSet();    //설정파일 세팅

		$this->act=$act;
		$this->getItems();

	}

	function _get_micro() {
		$m = explode(' ',microtime());
		return $m[1].$m[0];
	}

	function get_uid($reset = false) {

		global $sess;

		$uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : null;

		if ($uid == null || $reset == true) {

			$foo = false;

			do {

				$uid = md5( $this->_get_micro() . $_SERVER['REMOTE_ADDR'] );

				if ($this->db->fetch("SELECT uid FROM ".GD_CART." WHERE uid = '$uid'") == false) {

					$_SESSION['uid'] = $uid;
					$foo = true;

				}

			} while ($foo === false);

		}

		// uid가 sugi로 시작하면 수기주문
		if (preg_match('/^sugi__/', $uid)) $this->is_sugi = true;

		return $this->arrange_cart($uid,$sess['m_id']);

	}

	function arrange_cart($uid='',$m_id='') {

		static $arranged = null;

		if ($arranged == null && $this->is_sugi == false) {
			if ($m_id) {
				$this->chkKeepPeriod(); //보관기간 설정 체크
				$this->chkRunoutDel();  //품절상품 보관 체크
				// 기존 바로구매 상품 삭제
				if(substr($uid,0,4) != "sugi") {
					$query = "DELETE FROM gd_cart WHERE uid <> '$uid' AND m_id = '$m_id' AND is_direct = 1 AND is_buy = 0";
					$rs = $this->db->query($query);
				}
				// 회원 이면, uid 가 같거나, m_id 가 같은 장바구니 아이템 정보 업데이트
				list($cnt) = $this->db->fetch("SELECT count(*) FROM ".GD_CART." WHERE ((uid <> '$uid' AND m_id = '$m_id') OR (uid = '$uid' AND m_id = '')) AND is_buy = 0");
				if($cnt>$this->maxItemCnt) {
					$this->db->query("DELETE FROM ".GD_CART." WHERE   (uid = '$uid' AND m_id = '')  AND is_buy = 0" );
					echo "
						<script>
							var msg='장바구니 저장 가능 상품수 초과!장바구니의 상품을 먼저 주문하거나 정리 한 후 추가 담기가 가능합니다. 장바구니로 이동 하시겟습니까? ';
							if(confirm(msg)){
								location.href='../goods/goods_cart.php';
							}
						</script>
					";
				}

				if ($cnt > 0) {
					//-->
					## 기존에 있던 상품은 장바구니에 담지 않는다  2012-10-24 khs
					## m_id 틀리고 uid 같은 거는 .. 기존에 장바구니에 담겨 있는지 확인하고 , 회원장바구니에 담아준다
					$query = "SELECT uid, m_id, goodsno, optno, is_direct FROM ".GD_CART." WHERE uid = '$uid' AND m_id = '' AND is_buy = 0 AND is_direct = ".$this->isDirect;
					$rs = $this->db->query($query);

					while ($data = $this->db->fetch($rs,1)) {
						// 동일한 상품이 해당 고객의 장바구니에 담겨 있는지 확인한다.
						$cart_exist_check_query = $this->db->_query_print("SELECT count(*) FROM ".GD_CART."
						                           WHERE m_id =  [s] AND is_buy = 0 AND is_direct = [i]
						                           AND goodsno = [s] and optno = [s]",$m_id, $data['is_direct'], $data['goodsno'], $data['optno']);
						list($cart_exist_cnt) = $this->db->fetch($cart_exist_check_query);

						if ($cart_exist_cnt == 0) {
							$this->db->query("UPDATE ".GD_CART." SET m_id = '$m_id' WHERE uid='$uid' AND is_buy = 0 AND goodsno='".$data['goodsno']."' AND optno='".$data['optno']."' AND is_direct = ".$data['is_direct']);
						} else {
							$this->db->query("DELETE FROM ".GD_CART." WHERE uid='$uid' AND is_buy = 0 AND goodsno='".$data['goodsno']."' AND optno='".$data['optno']."' AND is_direct = ".$data['is_direct']);
						}
					}
					//<--
					//$this->db->query("UPDATE ".GD_CART." SET uid = '$uid', m_id = '$m_id' WHERE (m_id = '$m_id' OR uid='$uid') AND is_buy = 0");
					## uid 틀리고 m_id같은거는 일괄 업데이트 해준다.
					//관리자 > 주문 > 주문수기등록에서 주문서 작성 시 수기 주문 내역만 주문 처리 하기 위해 일괄업데이트 에서는 제외한다. 2013-08-07 kth.
					if(substr($uid,0,4) != "sugi") {
						$this->db->query("UPDATE ".GD_CART." SET uid = '$uid', m_id = '$m_id' WHERE m_id = '$m_id' AND (substr(uid,1,4) != 'sugi' and uid !='$uid') AND is_buy = 0 AND is_direct = ".$this->isDirect);
					}
				}

			}
			else {
				// 수기 주문이 아니고, 비회원일때, 같은 uid 를 갖는 회원 소유의 장바구니 아이템이 있을 경우 uid 를 교체
				list($cnt) = $this->db->fetch("SELECT count(*) FROM ".GD_CART." WHERE uid = '$uid' AND m_id <> '' AND is_buy = 0");
				if ($cnt > 0) {
					$uid = $this->get_uid(true);
				}
			}

			$arranged = true;
		}

		return $uid;
	}

	// 메서드 오버라이딩
	function getItems()
	{

		global $set, $sess, $cfg;
		$uid = $this->get_uid();

		$this->item = array();

		$today_query = "";

		if ($this->mode == 'todayshop') {
			$today_query = " AND TG.tgsno IS NOT NULL ";
		}
		else {
			$today_query = " AND TG.tgsno IS NULL ";
		}

		if ($this->ordering) {
			$ordering_query = " AND CT.ordering = 1";
		}
		else {
			$ordering_query = " ";
		}


		$query = "
			SELECT
				CT.optno,
				CT.addno,
				CT.ea,
				CT.seq,

				G.goodsno,
				G.goodsnm,
				G.img_s AS img,
				G.img_i,
				G.img_m,
				G.img_l,
				G.use_mobile_img,
				G.img_x,
				G.img_pc_x,
				G.delivery_type,
				G.goods_delivery,
				G.use_emoney,
				G.addoptnm,
				G.runout,
				G.usestock,
				G.exclude_member_reserve,
				G.exclude_member_discount,
				G.sales_unit,
				G.min_ea,
				G.max_ea,
				G.use_only_adult,

				G.todaygoods,

				O.sno AS go_option_sno,
				O.reserve,
				O.price,
				O.opt1,
				O.opt2,
				O.stock,

				TG.tgsno,TG.goodstype,TG.usememberdc
				,CT.c_addno_value


			FROM ".GD_CART." AS CT
			INNER JOIN ".GD_GOODS." AS G		ON CT.goodsno = G.goodsno
			LEFT JOIN ".GD_GOODS_OPTION." AS O	ON O.goodsno = CT.goodsno AND O.optno = CT.optno and go_is_deleted <> '1' and go_is_display = '1'
			LEFT JOIN ".GD_GOODS_ADD." AS A		ON A.goodsno = CT.goodsno AND A.sno = CT.addno AND CT.addno <> ''
			LEFT JOIN ".GD_TODAYSHOP_GOODS." AS TG ON CT.goodsno=TG.goodsno

			WHERE CT.uid = '".$uid."' AND CT.is_buy = 0 AND CT.is_direct = ".$this->isDirect." $ordering_query $today_query

			ORDER BY G.delivery_type ,CT.regdt
		";

		$rs = $this->db->query($query);

		while ($data = $this->db->fetch($rs,1)) {

			// 옵션 붙이기
			if ($data[opt1] || $data[opt2]) {
				$data['opt'][] = $data['opt1'];
				$data['opt'][] = $data['opt2'];
			}

			// 2015-07 qnibus 장바구니 모바일 전용이미지 오버라이드
			if (Clib_Application::isMobile()) {
				if ($data['use_mobile_img'] === '1') {
					$data['img'] = $data['img_x'];
				} else if ($data['use_mobile_img'] === '0') {
					if ($data['img_pc_x'] != 'img_s') {
						$imgArr = explode('|', $data[$data['img_pc_x']]);
					} else {
						$imgArr = explode('|', $data['img']);
					}
					$data['img'] = $imgArr[0];
				}
			}

			// 성인 상품 - 인증 전에는 19 이미지 보여줌 - 인증 후 상품 이미지 보여줌
			if($data['use_only_adult'] == '1' && !Clib_Application::session()->canAccessAdult()){
				$data['img'] = "../skin/" . $cfg['tplSkin'] . '/img/common/19.gif';
			}

			// 추가 옵션 가격 합산 및 붙이기
			if ($data[addno]) {

				$_addoptnm = array();
				$_addopttype = array();
				$_addoptreq = array();

				$__addoptnm = explode("|",$data[addoptnm]);
				for ($i=0,$m=sizeof($__addoptnm);$i<$m;$i++) {
					list($_addoptnm[], $_addoptreq[], $_addopttype[]) = explode("^", $__addoptnm[$i]);
				}

				$_addopt = explode(',', $data['addno']);
				$_addopt_value  = explode(G_STR_DIVISION,$data['c_addno_value']);

				$_rs = $this->db->query("SELECT sno, goodsno,step, opt, addprice as price, addno, stats, type, FIND_IN_SET(sno,'".$data['addno']."') as added FROM ".GD_GOODS_ADD." WHERE goodsno = '$data[goodsno]' order by type,step,sno");

				while ($add = $this->db->fetch($_rs,1)) {

					if (!$add['added']) continue;

					$data['addprice'] += $add['price'];

					$_name_offset = $add['step'] + ($add['type'] == 'I' ? (int) array_search('I', $_addopttype) : 0);
					$_value_offset = $add['added'] - 1;

					if ($add['type'] == 'I') {
						$add['optnm'] = $_addoptnm[$_name_offset];
						$add['opt'] = $_addopt_value[$_value_offset];
						$data['input_addopt'][] = $add;
					}
					else {
						$add['optnm'] = $_addoptnm[$_name_offset];
						$data['select_addopt'][] = $add;
					}

					$data['addopt'][] = $add;
				}
			}

			### 상품 적립금 계산
			if($set['emoney']['useyn'] != 'n'){
				if(!$data['use_emoney']){
					if($set['emoney']['chk_goods_emoney'] == 0){
						$data['reserve'] = getDcprice($data[price],($set['emoney']['goods_emoney']+0).'%');
					}else{
						$data['reserve'] = $set['emoney']['goods_emoney']+0;
					}
				}
			}else{
				$data['reserve'] = 0;
			}

			$this->item[] = $data;
		}

		return $this->item;

	}

	function buy() {

		$uid = $this->get_uid();
		$query = "UPDATE ".GD_CART." SET is_buy = 1 WHERE uid = '".$uid."' AND is_direct = ".$this->isDirect." AND ordering = 1";
		$this->db->query($query);
	}

	function reset()
	{
		$this->getItems();
	}

	function emptyCart()
	{
		$uid = $this->get_uid();
		$query = "DELETE FROM ".GD_CART." WHERE uid = '".$uid."' AND is_buy = 0";
		$this->db->query($query);
	}


	function addCart($goodsno,$opt,$addopt,$addopt_inputable,$ea,$reOrder_chk='')
	{
		global $sess;
		static $_delete_direct = null;

		if($ea < 1) {
			msg('구매수량 1 이상 입력해야 합니다.', -1);
			exit;
		}

		$uid = $this->get_uid();

		// 일반 옵션 삽입
		if ($opt){
			$opt = get_magic_quotes_gpc() ? array_map("stripslashes",$opt) : $opt;
			$opt = explode("|",implode("|",$opt));
			list($sno,$optno) = $this->db->fetch("SELECT sno, optno FROM ".GD_GOODS_OPTION." WHERE goodsno='".$goodsno."' and opt1='".mysql_real_escape_string($opt[0])."' and opt2='".mysql_real_escape_string($opt[1])."' and go_is_display = '1' and go_is_deleted <> '1'");
		}
		else {
			list($sno,$optno) = $this->db->fetch("SELECT sno, optno FROM ".GD_GOODS_OPTION." WHERE goodsno='".$goodsno."' and `link` = 1 and go_is_display = '1' and go_is_deleted <> '1'");
		}

		if (empty($optno)) {
			$optno = $sno;
			$this->db->query("UPDATE ".GD_GOODS_OPTION." SET optno = '".$optno."' WHERE sno = '".$sno."'");
		}

		$r_addopt = array();
		$r_addopt_value = array();

		if ($addopt){
			$addopt = array_filter((array)$addopt);
			foreach ($addopt as $v){
				if ($v == -1) $v = '';
				list ($tmp[sno],$tmp[optnm],$tmp[opt],$tmp[price]) = explode("^",$v);
				$r_addopt[] = $tmp[sno];
				$r_addopt_value[] = $tmp[opt];
				$addprice += $tmp[price];
			}
		}

		if ($addopt_inputable){
			foreach ($addopt_inputable as $v){
				list ($tmp[sno],$tmp[optnm],$tmp[opt],$tmp[price]) = explode("^",$v);
				$r_addopt[] = $tmp[sno];
				$r_addopt_value[] = $tmp[opt];
				$addprice += $tmp[price];
			}
		}


		$addno = implode(',', $r_addopt);
		$addno_value = implode(G_STR_DIVISION, $r_addopt_value);

		if (!$this->isDirect && $reOrder_chk == ''){
			for ($i=0;$i<count($this->item);$i++){ $data = $this->item[$i];
				if ($goodsno==$data[goodsno] && $opt==$data[opt] && $addno==$data[addopt]){
					return 0;
				}
				if ($goodsno==$data[goodsno] && $opt==$data[opt]){
					$tmp_ea += $data[ea];
				}
			}
		}

		if($tmp_ea)$new_ea = $tmp_ea + $ea;
		else $new_ea = $ea;

		### 재고체크
	    $result = $this->chkStock($goodsno,$opt[0],$opt[1],$new_ea,'add',$reOrder_chk);
		if($result == 0) return 0;

		if($this->errorMsg){
			return 0;
		}

		if ($this->isDirect && $_delete_direct === null) {
			$_delete_direct = true;
			$this->db->query("DELETE FROM ".GD_CART." WHERE uid = '".$uid."' AND is_direct = '".$this->isDirect."'");
		}

		// 데이터 삽입
		// 이미 동일한 상품(바로구매 여부, 옵션, 추가옵션이 같은)이 포함되어 있는지
		$query = "SELECT COUNT(uid) FROM ".GD_CART." WHERE uid = '".$uid."' AND goodsno = '".$goodsno."' AND optno = '".$optno."' AND addno='".$addno."' AND c_addno_value = '".$this->db->escape($addno_value)."' AND is_direct = ".$this->isDirect."  AND is_buy = 0";
		list($chk) = $this->db->fetch($query);

		if ((int)$chk > 0) {
			// 이미 같은 상품이 있는 경우.
			if($reOrder_chk === 'true') return 1;
			else return 0;
		}

		$query = "SELECT MAX(seq) FROM ".GD_CART." WHERE uid = '".$uid."' AND goodsno = '".$goodsno."'";
		list($seq) = $this->db->fetch($query);
		$seq = (int)$seq + 1;

		$query = "
		INSERT INTO ".GD_CART." SET
			uid = '".$uid."',
			m_id = '".$sess[m_id]."',
			goodsno = '".$goodsno."',
			optno = '".$optno."',
			addno  = '".$addno."',
			ea = '".$ea."',
			is_buy = 0,
			is_direct = '".$this->isDirect."',
			seq	= '".$seq."',
			c_addno_value = '".$this->db->escape($addno_value)."',
			regdt = NOW()
		";
		$this->db->query($query);

		$this->reset();
		return 1;
	}

	function _getEaRemainder($ea, $unit = 1)
	{
		if ((int)$unit < 1) $unit = 1;
		return $ea % $unit;
	}

	function modCart($ea)
	{
		$chk_ea = 0;
		foreach($ea as $val) {
			if($val == "" || $val < 1)	$chk_ea = 1;
		}

		if($chk_ea) {
			exit('
			<script>
			window.onload = function() {
				alert("구매수량 1 이상 입력해야 합니다.");
				location.replace("goods_cart.php?cart_type='.$_GET['cart_type'].'");
			}
			</script>
			');
		}

		$uid = $this->get_uid();

		foreach ($this->item as $k => $ici) {

			// 변경할 수량
			$ici = $this->item[$k];
			$ea_new = ($ea[$k]) ? $ea[$k] : $ici['sales_unit'];
			if ($remainder = $this->_getEaRemainder($ea_new, $ici['sales_unit']) > 0) {
				$ea_new = $ea_new - $remainder;
			}

			$tmp_ea[$ici['goodsno']][$ici['optno']] = $ea_new;

			### 잔여재고체크
			$this->chkStock($ici[goodsno],$ici[opt][0],$ici[opt][1],$tmp_ea[$ici['goodsno']][$ici['optno']]);

			$query = "UPDATE ".GD_CART." SET ea = $ea_new WHERE uid = '".$uid."' AND goodsno = '".$ici['goodsno']."' AND optno = '".$ici['optno']."' AND addno = '".$ici['addno']."' AND seq = '".$ici['seq']."'";
			$this->db->query($query);
		}

		$this->reset();
	}

	function modEA($idx, $ea)
	{
		$uid = $this->get_uid();

		$item = $this->item[$idx];

		if ($item) {
			$query = "UPDATE ".GD_CART." SET ea = $ea WHERE uid = '".$uid."' AND goodsno = '".$item['goodsno']."' AND optno = '".$item['optno']."' AND addno = '".$item['addno']."' AND seq = '".$item['seq']."'";
			$this->db->query($query);
		}
	}

	function chkStock($goodsno,$opt1='',$opt2='',$ea=1,$mode='',$reOrder_chk='')
	{
		if (!$ea) $ea = 1;

		// 쿼리 수정 2011-01-26 by 육승우
		$query = "
		select

			a.goodsno, a.goodsnm, a.usestock, a.runout, a.todaygoods, a.min_ea, a.max_ea,
			b.stock,
			tg.tgsno, tg.startdt, tg.enddt

		from ".GD_GOODS." as a
		left join ".GD_GOODS_OPTION." as b on a.goodsno=b.goodsno
		left join ".GD_TODAYSHOP_GOODS." AS tg ON a.goodsno=tg.goodsno
		where
			a.goodsno='$goodsno' and b.go_is_deleted <> '1' and b.go_is_display = '1' and ((a.use_option = '0' and b.link) or (a.use_option = '1' and b.opt1='".mysql_real_escape_string($opt1)."' and b.opt2='".mysql_real_escape_string($opt2)."'))
		";

		extract($GLOBALS[db]->fetch($query)); //list ($goodsnm,$usestock,$runout, $todaygoods,$min_ea,$max_ea,$stock, $tgsno) = $GLOBALS[db]->fetch($query);	//2011-01-26 by 육승우
		if(!$goodsnm){
			if ($reOrder_chk === 'true') return 0;
			else $this->msg("일치하는 상품이 없습니다.",-1);
		}
		$goodsnm = addslashes($goodsnm);

		### 기존 장바구니 상품에서 동일상품 구매 개수 체크
		if (!$this->isDirect || !$ea){
			for ($i=0;$i<count($this->item);$i++){ $data = $this->item[$i];
				if ($goodsno==$data[goodsno] && $opt1==$data[opt][0] && $opt2==$data[opt][1]) $tmp = $i;
			}
		}

		// 오류 발생시 리턴 url
		if ($todaygoods == 'y') {
			$goodsurl = '../todayshop/today_goods.php?tgsno='.$tgsno;
		}
		else if (isset($_GET['isMobile'])) {
			$goodsurl = $_SERVER['HTTP_REFERER'];
		}
		else {
			$goodsurl = '../goods/goods_view.php?goodsno='.$goodsno;
		}

		// 구매 가능 기간 체크 2011-01-26
		if ($todaygoods == 'y') {
			$_now = time();

			$_startdt = ($startdt) ? strtotime($startdt) : 0;	// 투데이샵 상품의 진행기간 미입력시 DB값은 null 이어야 함
			$_enddt = ($enddt) ? strtotime($enddt) : 0;

			if ($_startdt > 0 && $_startdt > $_now) {
				//echo '안시작해서 못삼';
				if ($reOrder_chk === 'true') return 0;
				else $this->msg("{$goodsnm} 상품은 {$startdt} 부터 구매 가능합니다.",$goodsurl);
			}

			if ($_enddt > 0 && $_enddt < $_now) {
				//echo '끝나서 못삼';
				if ($reOrder_chk === 'true') return 0;
				else $this->msg("{$goodsnm} 상품은 판매마감 되었습니다.",$goodsurl);
			}

		}	// if 2011-01-26

		### 최소,최대구매수량체크
		if($ea < $min_ea) {
			if($mode == 'add' || $this->isDirect){
				if ($reOrder_chk === 'true') return 0;
				else $this->msg("{$goodsnm} 상품의 최소구매수량은 {$min_ea}개 입니다.",$goodsurl);
			}else{
				if ($reOrder_chk === 'true') return 0; 
				else $this->msg("{$goodsnm} 상품의 최소구매수량은 {$min_ea}개 입니다.",'../goods/goods_cart.php');
			}
		}
		else if($max_ea > 0 && $ea > $max_ea) {
			if($mode == 'add' || $this->isDirect){
				if ($reOrder_chk === 'true') return 0;
				else $this->msg("{$goodsnm} 상품의 최대구매수량은 {$max_ea}개 입니다.",$goodsurl);
			}else{
				if ($reOrder_chk === 'true') return 0;
				else $this->msg("{$goodsnm} 상품의 최대구매수량은 {$max_ea}개 입니다.",'../goods/goods_cart.php');
			}
		}

		if( basename($_SERVER['PHP_SELF']) != "cartMove_proc.php" ){
			if ($runout){
				if ($reOrder_chk === 'true') return 0;
				else $this->msg("{$goodsnm}은 품절된 상품입니다",$goodsurl);
			}
			if ($usestock && $ea > $stock){
				if ($stock>0){
					$this->ritem[$tmp][3] = $stock;
					$this->reset();

					if($mode == 'add' || $this->isDirect){
						if ($reOrder_chk === 'true') return 0;
						else $this->msg("{$goodsnm} 상품의 잔여 재고는 {$stock}개입니다",$goodsurl);
					}else{
						if ($reOrder_chk === 'true') return 0;
						else $this->msg("{$goodsnm} 상품의 잔여 재고는 {$stock}개입니다",'../goods/goods_cart.php');
					}

				}else {
					if ($reOrder_chk === 'true') return 0;
					else $this->msg("{$goodsnm} 상품의 잔여 재고가 존재하지 않습니다",$goodsurl);
				}
			}
		}

		return 1;
	}

	function delCart($idxs)	// array or int
	{
		$uid = $this->get_uid();

		if (is_array($idxs) && !empty($idxs)) {
			foreach($idxs as $idx) {

				$item = $this->item[$idx];
				if ($item) {

					$query = "DELETE FROM ".GD_CART." WHERE uid = '".$uid."' AND goodsno = '".$item['goodsno']."' AND optno = '".$item['optno']."' AND addno = '".$item['addno']."' AND seq = '".$item['seq']."'";
					$this->db->query($query);
				}
			}
		}
		else {

			$item = $this->item[$idxs];

			if ($item) {

				$query = "DELETE FROM ".GD_CART." WHERE uid = '".$uid."' AND goodsno = '".$item['goodsno']."' AND optno = '".$item['optno']."' AND addno = '".$item['addno']."' AND seq = '".$item['seq']."'";
				$this->db->query($query);

			}
		}
		$this->reset();
	}

	function setOrder($idxs='all') {	// array or 'all'

		// 실질적인 데이터 추가가 아닌, 이미 담겨진 데이터들의 ordering 필드 업데이트

		$uid = $this->get_uid();

		$this->ordering = !$this->ordering;
		$this->reset();

		if ($idxs == 'all') {
			$this->db->query("UPDATE ".GD_CART." SET ordering = 1 WHERE uid = '".$uid."'");
		}
		else {

			$this->db->query("UPDATE ".GD_CART." SET ordering = 0 WHERE uid = '".$uid."'");

			if (is_array($idxs) && !empty($idxs)) foreach($idxs as $idx) {

				$item = $this->item[$idx];

				if ($item) {

					$query = "UPDATE ".GD_CART." SET ordering = 1 WHERE uid = '".$uid."' AND goodsno = '".$item['goodsno']."' AND optno = '".$item['optno']."' AND addno = '".$item['addno']."' AND seq = '".$item['seq']."'";
					$this->db->query($query);
				}

			}
		}
		$this->ordering = !$this->ordering;
		$this->reset();

	}

	function editOption($data)
	{
		// addCart 메서드와 거의 같음.

		global $sess;

		$item = $this->item[$data['idx']];

		if (empty($item)) return false;
		if ($data['ea'] < 1) return false;

		// 변수 할당
		$uid = $this->get_uid();

		$goodsno = $item['goodsno'];
		$opt = $data['opt'];
		$addopt = ($data['addopt']);
		$addopt_inputable = ($data['_addopt_inputable']);
		$ea = $data['ea'];

		// 일반 옵션 삽입
		if ($opt){
			$opt = get_magic_quotes_gpc() ? array_map("stripslashes",$opt) : $opt;
			$opt = explode("|",implode("|",$opt));
			list($sno,$optno) = $this->db->fetch("SELECT sno, optno FROM ".GD_GOODS_OPTION." WHERE goodsno='".$goodsno."' and opt1='".mysql_real_escape_string($opt[0])."' and opt2='".mysql_real_escape_string($opt[1])."' and go_is_deleted <> '1' and go_is_display = '1'");
		}
		else {
			list($sno,$optno) = $this->db->fetch("SELECT sno, optno FROM ".GD_GOODS_OPTION." WHERE goodsno='".$goodsno."' and `link` = 1 and go_is_deleted <> '1' and go_is_display = '1'");
		}

		if (empty($optno)) {
			$optno = $sno;
			$this->db->query("UPDATE ".GD_GOODS_OPTION." SET optno = '".$optno."' WHERE sno = '".$sno."'");
		}

		$r_addopt = array();
		$r_addopt_value = array();

		if ($addopt){
			foreach ($addopt as $v){
				if ($v == -1) $v = '';
				list ($tmp[sno],$tmp[optnm],$tmp[opt],$tmp[price]) = explode("^",$v);
				$r_addopt[] = $tmp[sno];
				$r_addopt_value[] = $tmp[opt];
				$addprice += $tmp[price];
			}
		}

		if ($addopt_inputable){
			foreach ($addopt_inputable as $v){
				list ($tmp[sno],$tmp[optnm],$tmp[opt],$tmp[price]) = explode("^",$v);
				$r_addopt[] = $tmp[sno];
				$r_addopt_value[] = $tmp[opt];
				$addprice += $tmp[price];
			}
		}

		$addno = implode(',', $r_addopt);
		$addno_value = implode(G_STR_DIVISION, $r_addopt_value);

		// 동일상품으로 변경되는 경우 수량을 합치고, 기존에 담긴 상품은 지운다.
		for ($i=0;$i<count($this->item);$i++){ $data = $this->item[$i];
			if ($goodsno==$data[goodsno] && $opt==array_notnull($data[opt]) && $addno==$data[addno]){
				if ($item['seq'] !== $data['seq']) {
					// 삭제;
					$this->db->query("DELETE FROM ".GD_CART." WHERE uid = '".$uid."' AND goodsno = '".$goodsno."' AND optno = '".$data['optno']."' AND addno='".$data['addno']."' AND seq = '".$data['seq']."'");
					$tmp_ea += $data[ea];
				}
			}
		}

		if($tmp_ea)$new_ea = $tmp_ea + $ea;
		else $new_ea = $ea;

		### 재고체크
		$this->chkStock($goodsno,$opt[0],$opt[1],$new_ea,'add');

		// 업데이트
		$query = "
		UPDATE ".GD_CART." SET
			optno = '".$optno."',
			addno  = '".$addno."',
			c_addno_value = '".$this->db->escape($addno_value)."',
			ea = '".$new_ea."',
			is_buy = 0,
			is_direct = '".$this->isDirect."'
		WHERE uid = '".$uid."' AND goodsno = '".$goodsno."' AND optno = '".$item['optno']."' AND addno = '".$item['addno']."' AND c_addno_value = '".$item['c_addno_value']."' AND seq = '".$item['seq']."'
		";
		if ($this->db->query($query)) return true;
		else return false;
	}

	/**
		2011-09-28 by x-ta-c
		결제가 기준 적립금 지급시 구매 상품의 적립금을 재 계산합니다.
	 */
	function resetReserveAmount($settleprice = 0) {

		global $set;

		$_bonus = 0;

		if($set['emoney']['chk_goods_emoney'] == '0' && $set['emoney']['emoney_standard'] == '1') {

			$_adjust_items = array();

			foreach ($this->item as $ik => $_item) {

				if (is_array($this->coupons)) { foreach ($this->coupons as $_coupon) {
					// 할인
					$_dc = $_coupon['sale'][$_item['goodsno']];
					if ($_dc > 0) {
						$_item['price'] = $_item['price'] - $_dc;
					}
				}}

				if ((int)$_item['use_emoney'] === 1) {	// 개별 설정 (즉, 재계산 필요 없음)
					$_bonus = $_bonus + (int)$_item['reserve'] * $_item['ea'];
					$settleprice = $settleprice - $_item['price'] * $_item['ea'];	// 상품가를 뺌.
				}
				else {
					$_adjust_items[$ik] = $_item;
				}
			}

			$_bonus = $_bonus + ($settleprice > 0 ? (int)getDcprice($settleprice,$set['emoney']['goods_emoney'].'%') : 0);

			// 개별 설정 상품을 제외한 적립금 재 계산
			if (sizeof($_adjust_items) > 0  && (int)$this->bonus != (int)$_bonus) {

				$_total_price = 0;
				foreach($_adjust_items as $_item) $_total_price = $_total_price + ($_item['price'] * $_item['ea']);

				$_total_adj_reserve = 0;
				$_reserve_gap = $this->bonus - $_bonus;
				foreach($_adjust_items as $ik => $_item) {

					$_adj_reserve = floor(($_reserve_gap - floor($_reserve_gap * (1 - ($_item['price'] * $_item['ea'] / $_total_price)))) / $_item['ea']);
					$this->item[$ik]['reserve'] = $this->item[$ik]['reserve'] - $_adj_reserve;
					$_total_adj_reserve = $_total_adj_reserve + ($_adj_reserve * $_item['ea']);
				}

				// 자투리 적립금은 총 적립금액에서 제외.
				$_bonus = $_bonus - ($_total_adj_reserve - $_reserve_gap);
			}

		}
		else {
			return;
		}

		$this->bonus = $_bonus;
	}

	function cartCfgSet() {

		require_once(dirname(__FILE__)."/../lib/qfile.class.php");
		$l = dirname(__FILE__)."/../conf/config.cart.php";
		if(file_exists($l) && (file_get_contents($l))!=''  ){
			require $l;
		}
		else{
			###기본 세팅값
			$arr= array(
					'keepPeriod'=>$this->keepPeriod,
					'runoutDel'=>$this->runoutDel,
					'redirectType'=>$this->redirectType,
					'estimateUse'=>$this->estimateUse,
					'estimateSeal'=>$this->estimateSeal,
					'estimateMessage'=>$this->estimateMessage
				);

			$qfile = new qfile();
			$qfile->open($l);
			$qfile->write("<? \n");
			$qfile->write("\$cartCfg = array( \n");
			foreach ($arr as $k=>$v){
				$qfile->write("'$k' => '$v', \n");
			}
			$qfile->write(") \n;");
			$qfile->write("?>");
			$qfile->close();
			@chmod($l,0707);
			require $l;
		}
		$this->keepPeriod=$cartCfg['keepPeriod'];
		$this->runoutDel=$cartCfg['runoutDel'];
		$this->redirectType=$cartCfg['redirectType'];
		$this->estimateUse=$cartCfg['estimateUse'];
		$this->estimateSeal=$cartCfg['estimateSeal'];
		$this->estimateMessage=$cartCfg['estimateMessage'];

	}

	function chkKeepPeriod() {	//void 보관기간 설정체크
		global $sess;
		if(!$this->keepPeriod)return;
		if(is_null($sess[m_id]))return;
		if(!$this->act[chkKeepPeriod])return;
		$where=" where m_id='".$sess[m_id]."' and  TIMESTAMPDIFF(DAY,regdt,NOW()) >=".$this->keepPeriod;
		list($cnt) = $this->db->fetch("select count(*) from ".GD_CART.$where);
		if($cnt>0){
			$this->db->query("delete from ".GD_CART.$where );
		}
	}

	function chkMaxCount() {	//Boolen 상품보관개수 설정체크
		global $sess;
		$itemCnt=count($this->item);
		if($itemCnt>=$this->maxItemCnt){
			return false;
		}
		return true;
	}

	function chkRunoutDel() {	//void 품절상품 보관설정체크
		global $sess,$actRunoutDel;
		if(is_null($sess[m_id]))return;	//회원체크
		if(!$this->runoutDel)return;	//품절상품보관설정>남겨둠일경우
		if(!$this->act[chkRunoutDel])return;	//액션
		$query = "
			SELECT
				G.goodsno,
				G.runout,
				G.usestock,
				O.stock
			FROM ".GD_CART." AS CT
			INNER JOIN ".GD_GOODS." AS G		ON CT.goodsno = G.goodsno
			LEFT JOIN ".GD_GOODS_OPTION." AS O	ON O.goodsno = CT.goodsno AND O.optno = CT.optno and go_is_deleted <> '1' and go_is_display = '1'
			WHERE CT.uid = '".$_SESSION['uid']."' AND CT.is_buy = 0 AND (CT.is_direct = ".$this->isDirect." and  G.runout=1 or (O.stock<1 and G.usestock='o' ))
		";

		$rs = $this->db->query($query);

		while ($data = $this->db->fetch($rs)) {
			$query="delete from ".GD_CART."	where m_id='".$sess[m_id]."' and  goodsno=".$data[goodsno];
			$this->db->query($query);
		}
	}

	function goRedirectType() {	//장바구니 담기 레이어 이동설정
		echo $this->redirectType;
	}

	function getErrorMsg() {
		return $this->errorMsg;
	}

	function msg() {
		$args = func_get_args();
		if($this->ajaxMode) {
			$this->errorMsg=$args[0];
			return $args[0];
		}
		else {
			if($_POST[preview]=='y'){
				$args[1]=-1;
				return call_user_func_array('msg', $args);
			}
			else{
				return call_user_func_array('msg', $args);
			}

		}
	}

	function setAjaxMode($isAjax) {	//Ajax모드 설정
		$this->ajaxMode = $isAjax;
	}

}
?>
