<?
	// 기본 설정 및 인클루드
	$location = "사입처 관리 > 사입 이력 조회";
	include "../_header.php";
	include "../../lib/page.class.php";
	@include "../../conf/config.purchase.php";
	if($purchaseSet['usePurchase'] != "Y") msg("[사입처 관리 사용 설정] > [상품 사입처 연동]을 설정 하세요.", -1);

	// 파라메터 정리
	$pchsno			= isset($_GET['pchsno'])		? $_GET['pchsno']		: "";				// 사입처 번호
	$minQuantity	= isset($_GET['minQuantity'])	? $_GET['minQuantity']	: "";				// 재고량 (이하)
	$cate			= isset($_GET['cate'])			? $_GET['cate']			: array();			// 카테고리
	$price			= isset($_GET['price'])			? $_GET['price']		: array();			// 매입가
	$skey			= isset($_GET['skey'])			? $_GET['skey']			: "";				// 검색 필드
	$sword			= isset($_GET['sword'])			? $_GET['sword']		: "";				// 검색 키워드
	$pchsdt			= isset($_GET['pchsdt'])		? $_GET['pchsdt']		: array();			// 사입일
	$page_num		= isset($_GET['page_num'])		? $_GET['page_num']		: 10;				// 한 페이지당 출력될 게시물 수
	$sort			= isset($_GET['sort'])			? $_GET['sort']			: "pchsdt DESC";	// 정렬 순서
	$page			= isset($_GET['page'])			? $_GET['page']			: 1;				// 현재 페이지

	// 총 레코드수
	if($pchsno) list($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_PURCHASE_GOODS." WHERE pchsno = '$pchsno'");
	else list($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_PURCHASE_GOODS."");

	// 변수할당
	$selected['page_num'][$page_num]	= "selected";
	$selected['sort'][$sort]			= "selected";

	// 쿼리 설정
		// 테이블
		$db_table = "".GD_PURCHASE_GOODS." AS PG
			LEFT JOIN gd_goods AS G
				ON PG.goodsno = G.goodsno
			LEFT JOIN ".GD_PURCHASE." AS P
				ON PG.pchsno = P.pchsno";

		// 검색
		$where[] = "G.goodsnm != ''";
		$where[] = "G.goodsnm IS NOT NULL";
		if($pchsno)			$where[] = "PG.pchsno = '$pchsno'"; // 사입처
		if($sword)			$where[] = "$skey LIKE '%$sword%'"; // 키워드
		if($minQuantity) { // 재고량 : 재고량이 있는 경우 테이블 추가
			$db_table .= " LEFT JOIN ".GD_GOODS_OPTION." AS O ON PG.goodsno = O.goodsno AND PG.opt1 = O.opt1 AND PG.opt2 = O.opt2 and go_is_deleted <> '1'";
			$where[] = "O.stock <= $minQuantity";
		}
		if(!empty($cate)) { // 카테고리 : 카테고리가 있는 경우 테이블 추가
			$category = array_notnull($cate);
			$category = $category[count($category) - 1];

			if($category) {
				$db_table .= " LEFT JOIN ".GD_GOODS_LINK." AS L ON PG.goodsno = L.goodsno";

				// 상품분류 연결방식 전환 여부에 따른 처리
				$whereArr	= getCategoryLinkQuery('L.category', $category, null, 'PG.pgno');
				$where[]	= $whereArr['where'];
				$groupby	= $whereArr['group'];
			}
		}
		if($price[0] && $price[1]) $where[] = "PG.p_price >= '".$price[0]."' AND PG.p_price <= '".$price[1]."'";
		if($pchsdt[0] && $pchsdt[1]) { // 입고일
			$where[] = "PG.pchsdt BETWEEN DATE_FORMAT(".$pchsdt[0].",'%Y-%m-%d 00:00:00') AND DATE_FORMAT(".$pchsdt[1].",'%Y-%m-%d 23:59:59')";
		}

		// 페이지 및 쿼리 생성
		$pg = new Page($page, $page_num);
		$pg->field = " PG.pgno, PG.pchsno, PG.goodsno, PG.opt1, PG.opt2, PG.pchsdt, PG.p_price, PG.p_stock, G.goodsno, G.goodsnm, G.img_s, P.comnm, PG.goodsnm AS p_goodsnm, PG.img_s AS p_img_s ".$addField; // 필드 설정
		$pg->setQuery($db_table, $where, $sort, $groupby);

		$pg->setTotal();
		$res = $db->query($pg->query);
		$pg->exec();

		// 인자값 정리
		$qstr = "pchsno=".$pchsno."&sort=".$sort."&page_num=".$page_num."&skey=".$skey."&sword=".$sword."&minQuantity=".$minQuantity."&page=".$page;
		if(count($price)) foreach($price as $k => $v) $qstr .= "&price[]=".$v;
		if(count($cate)) foreach($cate as $k => $v) $qstr .= "&cate[]=".$v;
		if(count($pchsdt)) foreach($pchsdt as $k => $v) $qstr .= "&pchsdt[]=".$v;


		// 사입처가 선택되어 있으면 사입처 정보 읽어오기
		$pchs = $db->fetch("SELECT * FROM ".GD_PURCHASE." WHERE pchsno = '$pchsno'");
?>

<script>
	// 사입처 선택시 사입처 정보 창 토글
	function pchsInfoToggle() {
		if($('pchsInfo').style.display == "none") {
			$('pchsInfo').style.display = '';
			$('btnCode').src = '../img/ico_arrow_up.gif';
			$('codeLink').title = '사입처 상세 정보 닫기';
		}
		else {
			$('pchsInfo').style.display = 'none';
			$('btnCode').src = '../img/ico_arrow_down.gif';
			$('codeLink').title = '사입처 상세 정보 보기';
		}
	}

	// 바뀐 값을 수정할 때 사용
	function chkChangeVal(targetObj, stateObjID) {
		if(targetObj.value != targetObj.oVal) $(stateObjID	).value = "1";
		else document.getElementById('stateObjID').value = "2";
	}

	// 저장전 폼 체크
	function chkForm() {
		var ar_pchsdt = document.getElementsByName('p_pchsdt[]');
		var ar_price = document.getElementsByName('p_price[]');
		var ar_stock = document.getElementsByName('p_stock[]');
		var ar_checkChange = document.getElementsByName('checkChange[]');

		for(i = 0; i < ar_pchsdt.length; i++) {
			if((ar_pchsdt[i].value != ar_pchsdt[i].oVal) || (ar_price[i].value != ar_price[i].oVal) || (ar_stock[i].value != ar_stock[i].oVal)) {
				ar_checkChange[i].value = "1";
			}
			else ar_checkChange[i].value = "0";

			if(!ar_pchsdt[i].value) {
				alert("입고일을 입력해주세요.");
				ar_pchsdt[i].focus();
				return false;
			}

			if(!ar_price[i].value) {
				alert("매입가를 입력해주세요.");
				ar_price[i].focus();
				return false;
			}

			if(!ar_stock[i].value) {
				alert("입고량을 입력해주세요.");
				ar_stock[i].focus();
				return false;
			}
		}

		return true;
	}

	function chkMoveURL() {
		var ar_pchsdt = document.getElementsByName('p_pchsdt[]');
		var ar_price = document.getElementsByName('p_price[]');
		var ar_stock = document.getElementsByName('p_stock[]');
		var ar_checkChange = document.getElementsByName('checkChange[]');
		var chkCnt = 0;

		for(i = 0; i < ar_pchsdt.length; i++) {
			if((ar_pchsdt[i].value != ar_pchsdt[i].oVal) || (ar_price[i].value != ar_price[i].oVal) || (ar_stock[i].value != ar_stock[i].oVal)) {
				chkCnt = chkCnt + 1;
			}
		}

		if(chkCnt > 0) {
			if(!confirm('수정을 중지하고 [입고 상품 등록] 페이지로 이동하시겠습니까?')) return false;
		}
		else return true;
	}
</script>

<!-- 타이틀 --><div class="title title_top">사입 이력 조회<span>등록한 사입 이력을 조회 합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=25')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>

<!-- 검색 폼 영역 S -->
<div><form method="get" action="<?=$_SERVER['PHP_SELF']?>">

	<!-- 검색 S -->
	<table class="tb">
	<col class="cellC" /><col style="padding-left:10px;<?=($pchsno) ? "width:250;" : ""?>" />
	<? if($pchsno) { ?><col class="cellC" /><col style="padding-left:10px;" /><? } ?>
	<tr>
		<td>사입처 검색</td>
		<td>
			<select name="pchsno" id="pchsno" onchange="location.href='./purchase_log.php?pchsno=' + this.value;">
				<option value="">사입처선택</option>
<?
	$sql_pchs = "SELECT * FROM ".GD_PURCHASE." ORDER BY comnm ASC";
	$rs_pchs = $db->query($sql_pchs);
	for($i = 0; $row_pchs = $db->fetch($rs_pchs); $i++) {
?>
				<option value="<?=$row_pchs['pchsno']?>"<?=($row_pchs['pchsno'] == $pchsno) ? "selected" : ""?>><?=$row_pchs['comnm']?></option>
<?
	}
?>
			</select>
			<a href="javascript:;" onclick="window.open('../goods/popup.purchase_find.php?ctrlType=url', 'purchaseSearchPop', 'width=640,height=450');"><img src="../img/purchase_find.gif" title="사입처 검색" align="absmiddle" /></a>
		</td>
<? if($pchsno) { ?>
		<td>업체코드</td>
		<td><? if($pchs['comcd'] != "0000") { ?><a href="javascript:;" id="codeLink" title="사입처 상세 정보 보기" onclick="pchsInfoToggle()"><?=$pchs['comcd']; ?> <img src="../img/ico_arrow_down.gif" id="btnCode" align="absmiddle" /></a><? } else { echo $pchs['comcd']; } ?></td>
<? } ?>
	</tr>
	<tr>
		<td>분류선택</td>
		<td colspan="3"><script>new categoryBox('cate[]',4,'<?=$category?>');</script></td>
	</tr>
	<tr>
		<td>검색어</td>
		<td colspan="3">
		<select name="skey">
<? foreach ( array('G.goodsnm'=>'상품명','G.goodsno'=>'고유번호','G.goodscd'=>'상품코드','G.keyword'=>'유사검색어') as $k => $v) { ?>
			<option value="<?=$k?>" <?=($k == $skey) ? 'selected' : ''?>><?=$v?></option>
<? } ?>
		</select>
		<input type=text name="sword" class="lline" value="<?=$sword?>" class="line">
		</td>
	</tr>
	<tr>
		<td>매입가</td>
		<td><font class="small" color="#444444">
			<input type=text name="price[]" value="<?=$price[0]?>" onkeydown="onlynumber()" size="15" class="rline"> 원 -
			<input type=text name="price[]" value="<?=$price[1]?>" onkeydown="onlynumber()" size="15" class="rline"> 원
		</td>
	</tr>
	<tr>
		<td>재고량</td>
		<td colspan="3">
		<input type="text" name="minQuantity" class="line" value="<?=$minQuantity?>" style="width:50px;"> 개 이하 (입력값이 없을시 전체 레코드를 조회합니다)
		</td>
	</tr>
	<tr>
		<td>입고일</td>
		<td colspan="3">
		<input type=text name="pchsdt[]" value="<?=$pchsdt[0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline"> -
		<input type=text name="pchsdt[]" value="<?=$pchsdt[1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline">
		<a href="javascript:setDate('pchsdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle"></a>
		<a href="javascript:setDate('pchsdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle"></a>
		<a href="javascript:setDate('pchsdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"></a>
		<a href="javascript:setDate('pchsdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle"></a>
		<a href="javascript:setDate('pchsdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"></a>
		<a href="javascript:setDate('pchsdt[]')"><img src="../img/sicon_all.gif" align="absmiddle"></a>
		</td>
	</tr>
	</table>
	<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>
	<!-- 검색 E -->

	<!-- 사입처 정보 S -->
<? if($pchsno) { ?>
	<table class="tb" id="pchsInfo" style="margin-top:5px; display:none;">
		<col class="cellC" /><col style="padding-left:10px; width:250;" />
		<col class="cellC" /><col style="padding-left:10px; width:170;" />
		<col class="cellC" /><col style="padding-left:10px;" />
		<tr>
			<td>대표자명</td>
			<td><?=$pchs['ceonm']?></td>
			<td>사업자번호</td>
			<td colspan="3"><?=str_replace("-", " - ", $pchs['comno'])?></td>
		</tr>
		<tr>
			<td>주소</td>
			<td colspan="5"><?=str_replace("-", " - ", $pchs['zipcode'])?> <?=$pchs['address']?> <?=$pchs['address_sub']?></td>
		</tr>
		<tr>
			<td>계좌번호</td>
			<td><?=$pchs['accountno']?></td>
			<td>은행명</td>
			<td><?=$pchs['banknm']?></td>
			<td>예금주</td>
			<td><?=$pchs['accountnm']?></td>
		</tr>
		<tr>
			<td>연락처1</td>
			<td><?=str_replace("-", " - ", $pchs['phone1'])?></td>
			<td>연락처2</td>
			<td colspan="3"><?=str_replace("-", " - ", $pchs['phone2'])?></td>
		</tr>
		<tr>
			<td>메모</td>
			<td colspan="5"><?=nl2br($pchs['memo'])?></td>
		</tr>
		<tr height="35">
			<td>등록일</td>
			<td colspan="5"><?=$pchs['regdt']?></td>
		</tr>
	</table>
<? } ?>
	<!-- 사입처 정보 E -->

	<!-- 목록 정보, 정렬/목록수 설정 S -->
	<table width="100%">
	<tr>
		<td class="pageInfo">
			총 <font class="ver8"><b><?=number_format($total)?></b>개, 검색 <b><?=number_format($pg->recode['total'])?></b>개, <b><?=number_format($pg->page['now'])?></b> of <?=number_format($pg->page['total'])?> Pages
		</td>
		<td align="right">
			<select name="sort" onchange="this.form.submit();">
<?
	$r_pagenum = array("입고일 ↑" => "PG.pchsdt ASC", "입고일 ↓" => "PG.pchsdt DESC", "상품명 ↑" => "G.goodsnm ASC", "상품명 ↓" => "G.goodsnm DESC", "입고일 ↑" => "PG.pchsdt ASC", "입고일 ↓" => "PG.pchsdt DESC", "매입가 ↑" => "PG.p_price ASC", "매입가 ↓" => "PG.p_price DESC", "입고량 ↑" => "PG.p_stock ASC", "입고량 ↓" => "PG.p_stock DESC");
	foreach ($r_pagenum as $k => $v){
?>
				<option value="<?=$v?>" <?=$selected['sort'][$v]?>><?=$k?></option>
<?
	}
?>
			</select>
			<select name="page_num" onchange="this.form.submit();">
<?
	$r_pagenum = array(10, 20, 40, 60, 100);
	foreach ($r_pagenum as $v){
?>
				<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>개 출력</option>
<?
	}
?>
			</select>
		</td>
	</tr>
	</table>
	<!-- 목록 정보, 정렬/목록수 설정 E -->
</form></div>
<!-- 검색 폼 영역 E -->

<!-- 목록 영역 S -->
<div><form name="pList" method="post" action="../goods/indb.purchase.php" onsubmit="return chkForm()">
<input type="hidden" name="mode" value="pchs_log_modify" />
<input type="hidden" name="qstr" value="<?=$qstr?>" />
<input type="hidden" name="page" value="<?=$page?>" />
<input type="hidden" name="query" value="<?=substr($pg->query,0,strpos($pg->query,"limit"))?>" />

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="10"></td></tr>
<tr class="rndbg">
	<th width="50">번호</th>
	<th width="120">사입처</th>
	<th width="50"></th>
	<th>상품명</th>
	<th>옵션1</th>
	<th>옵션2</th>
	<th width="70">입고일</th>
	<th width="90">매입가</th>
	<th width="60">입고량</th>
</tr>
<tr><td class="rnd" colspan="10"></td></tr>

<!-- 목록 S -->
<?
	while($data = $db->fetch($res)) {
		if(!$data['img_s']) $data['img_s'] = $data['p_img_s'];
		if(!$data['goodsnm']) $data['goodsnm'] = $data['p_goodsnm'];
?>
<tr height="50" align="center">
	<td><font class="ver81" color="#616161"><?=$pg->idx--?></font></td>
	<td><a href="purchase_info.php?mode=pchs_mod&pchsno=<?=$data['pchsno']?>"><font class="small" color="#616161"><?=$data['comnm']?></font></a></td>
	<td><a href="../../goods/goods_view.php?goodsno=<?=$data['goodsno']?>" target="_blank"><?=goodsimg($data['img_s'], 40, '', 1)?></a></td>
	<td align="left"><a href="../../goods/goods_view.php?goodsno=<?=$data['goodsno']?>" target="_blank"><?=$data['goodsnm']?></a></td>
	<td><?=$data['opt1']?></td>
	<td><?=$data['opt2']?></td>
	<td><input type="text" name="p_pchsdt[]" id="pchsdt_<?=$data['pgno']?>" size="8" class="line" oVal="<?=str_replace("-", "", $data['pchsdt'])?>" value="<?=str_replace("-", "", $data['pchsdt'])?>" onclick="calendar()" onkeydown="onlynumber()" /></td>
	<td><input type="text" name="p_price[]" size="8" class="line" onkeydown="onlynumber()" oVal="<?=$data['p_price']?>" value="<?=$data['p_price']?>" /> 원</td>
	<td><input type="text" name="p_stock[]" size="6" class="line" onkeydown="onlynumber()" oVal="<?=$data['p_stock']?>" value="<?=$data['p_stock']?>" /></td>
</tr>
<tr><td colspan="10" class="rndline"></td></tr>
<input type="hidden" name="pgno[]" value="<?=$data['pgno']?>" />
<input type="hidden" name="checkChange[]" value="0" />
<? } ?>
<!-- 목록 E -->

</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td height="35" align="center"><font class="ver8"><?=$pg->page['navi']?></font></td>
</tr>
<tr>
	<td height="35" align="center"><input type="image" src="../img/btn_editall.gif" align="absmiddle" style="border:0px;" title="사입 이력 일괄수정" /></td>
</tr>
</table>

<div style="height:20px"></div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr>
	<td>
		<b>[사입 이력 조회]</b>는 과거의 사입이력을 조회하거나 수정 하실 수 있습니다.<br />
		입고량을 수정하면 재고량에 해당 내용이 반영 됩니다.<br />
		현재 재고량이 “0” 이하가 되는 경우 (-)마이너스 입고량을 입력 하실 수 없습니다.
	</td>
</tr>
</table>
</div>
<script>cssRound('MSG01')</script>

</form></div>
<!-- 목록 영역 E -->

<script>window.onload = function(){ UNM.inner();};</script>
<? include "../_footer.php"; ?>
