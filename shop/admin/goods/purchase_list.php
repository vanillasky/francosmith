<?
	$location = "사입처 관리 > 사입처 리스트";
	include "../_header.php";
	include "../../lib/page.class.php";
	@include "../../conf/config.purchase.php";
	if($purchaseSet['usePurchase'] != "Y") msg("[사입처 관리 사용 설정] > [상품 사입처 연동]을 설정 하세요.", -1);

	list($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_PURCHASE); # 총 레코드수

	if( !$_GET['page_num'] ) $_GET['page_num'] = 10;
	$orderby = ($_GET['sort']) ? $_GET['sort'] : "regdt desc"; # 정렬 쿼리

	### 변수할당
	$selected['skey'][$_GET['skey']]			= "selected";
	$selected['page_num'][$_GET['page_num']]	= "selected";
	$selected['sort'][$orderby]					= "selected";

	### 목록
	$db_table = GD_PURCHASE;

	if( $_GET['sword'] ) {
		if($_GET['skey'] == "all") {
			$where[] = "CONCAT(comnm, ceonm, phone1, phone2) LIKE '%".$_GET['sword']."%'";
		}
		else if($_GET['skey'] == "phone") {
			$where[] = "CONCAT(phone1, phone2) LIKE '%".$_GET['sword']."%'";
		}
		else {
			$where[] = $_GET['skey']." LIKE '%".$_GET['sword']."%'";
		}
	}

	$pg = new Page($_GET['page'],$_GET['page_num']);
	$pg->setQuery($db_table,$where,"ordgrade DESC, ".$orderby);
	$pg->exec();
	$res = $db->query($pg->query);

	$qstr = "skey=".$_GET['skey']."&sword=".$_GET['sword']."&sort=".$_GET['sort']."&page_num=".$_GET['page_num'];
?>
<script>
	function iciSelect( obj ) {
		var row = obj.parentNode.parentNode;
		row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
	}

	function delPurchase( f ) {
		if( !isChked( document.getElementsByName( 'chk[]' ) ) ) return;
		if( !confirm( '정말로 하시겠습니까?' ) ) return;
		f.target = "_self";
		f.mode.value = "pchs_del";
		f.action = "indb.purchase.php";
		f.submit();
	}
</script>

<div><form>
<div class="title title_top">사입처 리스트<span>등록하신 사입처 리스트를 조회하고 수정하실 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=28')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>
<table class="tb">
<col class="cellC" /><col class="cellL" />
<tr>
	<td>검색어</td>
	<td>
		<select name="skey">
			<option value="comnm" <?=$selected['skey']['comnm']?>> 사입처명 </option>
			<option value="ceonm" <?=$selected['skey']['ceonm']?>> 대표자 </option>
			<option value="phone" <?=$selected['skey']['phone']?>> 연락처 </option>
		</select>
		<input type="text" name="sword" value="<?=$_GET['sword']?>" class="line" />
	</td>
</tr>
</table>

<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>

<table width="100%">
<tr>
	<td class="pageInfo">
		총 <font class="ver8"><b><?=number_format($total)?></b>개, 검색 <b><?=number_format($pg->recode['total'])?></b>개, <b><?=number_format($pg->page['now'])?></b> of <?=number_format($pg->page['total'])?> Pages
	</td>
	<td align="right">
		<select name="sort" onchange="this.form.submit();">
			<option value="regdt desc" <?=$selected['sort']['regdt desc']?>>등록일 정렬↑</option>
			<option value="regdt asc" <?=$selected['sort']['regdt asc']?>>등록일 정렬↓</option>
			<option value="comnm desc" <?=$selected['sort']['comnm desc']?>>사입체명 정렬↑</option>
			<option value="comnm asc" <?=$selected['sort']['comnm asc']?>>사입체명 정렬↓</option>
			<option value="ceonm desc" <?=$selected['sort']['ceonm desc']?>>대표자명 정렬↑</option>
			<option value="ceonm asc" <?=$selected['sort']['ceonm asc']?>>대표자명 정렬↓</option>
		</select>&nbsp;
		<select name="page_num" onchange="this.form.submit();">
<?
	$r_pagenum = array( 10, 20, 40, 60, 100 );
	foreach( $r_pagenum as $v ) {
?>
			<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>개 출력</option>
<? } ?>
		</select>
	</td>
</tr>
</table>
</form></div>

<div><form name="pList" method="post">
<input type="hidden" name="mode" />
<input type="hidden" name="qstr" value="<?=$qstr?>" />
<input type="hidden" name="query" value="<?=substr( $pg->query, 0, strpos( $pg->query, "limit" ) )?>" />

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="9"></td></tr>
<tr class="rndbg">
	<th>번호</th>
	<th>사입처</th>
	<th>대표자</th>
	<th>연락처</th>
	<th>최근 등록일</th>
	<th>등록 상품</th>
	<th width="80">사입 등록</th>
	<th width="80">사입 이력</th>
</tr>
<tr><td class="rnd" colspan="9"></td></tr>
<?
	while($data=$db->fetch($res)) {
		$last_login = (substr($data['last_login'], 0, 10) != date("Y-m-d")) ? substr($data['last_login'], 0, 10) : "<font color=#7070B8>".substr($data['last_login'], 11)."</font>";

		if($data['comcd'] == "0000") { // 미등록
			list($data['count']) = $db->fetch("SELECT COUNT(G.goodsno) FROM gd_goods AS G LEFT JOIN ".GD_PURCHASE_GOODS." AS PG ON G.goodsno = PG.goodsno WHERE PG.pchsno IS NULL OR PG.pchsno = '".$data['pchsno']."'");
?>
<tr height="40" align="center">
	<td><font class="ver81" color="#616161"><?=$pg->idx--?></font></td>
	<td><font class="small" color="#616161"><?=$data['comnm']?></font></td>
	<td>-</td>
	<td>-</td>
	<td>-</td>
	<td><font class="ver81" color="#616161"><?=$data['count']?></font></td>
	<td><a href="../goods/purchase_goods.php?pchsno=<?=$data['pchsno']?>"><img src="../img/i_add.gif" title="사입 등록하기" /></a></td>
	<td><a href="../goods/purchase_log.php?pchsno=<?=$data['pchsno']?>"><img src="../img/btn_viewbbs.gif" title="'<?=$data['comnm']?>' 사입 이력 보기" /></a></td>
</tr>
<tr><td colspan="9" class="rndline"></td></tr>
<?
		}
		else { // 일반 사입처
			$data['count'] = $db->count_($db->query("SELECT sno FROM gd_goods_option WHERE pchsno = '".$data['pchsno']."' and go_is_deleted <> '1' GROUP BY goodsno, opt1, opt2"));
?>
<tr height="40" align="center">
	<td><font class="ver81" color="#616161"><?=$pg->idx--?></font></td>
	<td><a href="purchase_info.php?mode=pchs_mod&pchsno=<?=$data['pchsno']?>&<?=$qstr?>&page=<?=$_GET['page']?>"><font class="small" color="#616161"><?=$data['comnm']?></font></a></td>
	<td><font class="small" color="#616161"><?=$data['ceonm']?></font></td>
	<td><font class="ver81" color="#616161"><?=$data['phone1']?></font></td>
	<td><font class="ver81" color="#616161"><?=substr($data['regdt'], 0, 10)?></font></td>
	<td><font class="ver81" color="#616161"><?=number_format($data['count'])?></font></td>
	<td><a href="../goods/purchase_goods.php?pchsno=<?=$data['pchsno']?>"><img src="../img/i_add.gif" title="'<?=$data['comnm']?>' 사입 등록하기" /></a></td>
	<td><a href="../goods/purchase_log.php?pchsno=<?=$data['pchsno']?>"><img src="../img/btn_viewbbs.gif" title="'<?=$data['comnm']?>' 사입 이력 보기" /></a></td>
</tr>
<tr><td colspan="9" class="rndline"></td></tr>
<?
		}
	}
?>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td height="35" align="center"><font class="ver8"><?=$pg->page['navi']?></font></td>
</tr>
</table>

<div style="height:20px"></div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr>
	<td>한 번 등록한 사입처는 상품 정보와 연동 되기 때문에 삭제 하실 수 없습니다.</td>
</tr>
</table>
</div>
<script>cssRound('MSG01')</script>

</form></div>
<script>window.onload = function() { UNM.inner(); }</script>
<? include "../_footer.php"; ?>