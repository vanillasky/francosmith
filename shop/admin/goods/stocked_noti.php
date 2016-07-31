<?
/**
	2011-05-17 by x-ta-c

*/

$location = "상품관리 > 상품 재입고 알림";
include "../_header.php";
include "../../lib/page.class.php";


// 변수 받기 및 기본값 설정
	$_GET['skey'] = isset($_GET['skey']) ? $_GET['skey'] : '';
	$_GET['sword'] = isset($_GET['sword']) ? $_GET['sword'] : '';
	$_GET['page_num'] = isset($_GET['page_num']) ? $_GET['page_num'] : 10;

// 쿼리 만들기
	$db_table = "
	".GD_GOODS_STOCKED_NOTI." gsn
	LEFT JOIN ".GD_GOODS." g ON g.goodsno = gsn.goodsno
	LEFT JOIN ".GD_GOODS_OPTION." go ON go.goodsno = gsn.goodsno AND go.opt1 = gsn.opt1 AND go.opt2 = gsn.opt2 and go_is_deleted <> '1'
	LEFT JOIN (
		SELECT
			sno,
			goodsno,
			optno,
			count(sno) c
		FROM
			".GD_GOODS_STOCKED_NOTI."
		WHERE
			sended = '1'
		GROUP BY goodsno, optno
	) sub ON sub.goodsno = go.goodsno AND sub.optno = go.optno";


// 조건절
	if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";

// 정렬
	$orderby = "g.goodsno desc";

// 그룹
	$groupby = " GROUP BY gsn.goodsno, gsn.opt1, gsn.opt2";

// 전체 상품수 (품절건만)
	$total = $db->affected($db->fetch("SELECT count(g.goodsno) from ".$db_table." ".$groupby ));

// 레코드 가져오기
	$pg = new Page($_GET[page],$_GET[page_num]);
	$pg->field = "g.goodsno, g.goodsnm, g.img_s, gsn.sno, gsn.optno, gsn.opt1, gsn.opt2, go.opt1 as opt1Ori, go.opt2 as opt2Ori, go.sno gosno, count(gsn.sno) as request_count, go.stock, COALESCE(sub.c, 0) as sended_count";
	$pg->recode[total] = $total;
	$pg->setQuery($db_table,$where,$orderby,$groupby);
	$pg->exec();
	$res = $db->query($pg->query);
?>
<script><!--

function iciSelect(obj, normalColor)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :normalColor;
}
function chkBox(chk) {
	var cnt = document.getElementsByName("chk[]").length;
	for(i = 0; i < cnt; i++){
		document.getElementsByName("chk[]")[i].checked = chk;
		document.getElementsByName("chk[]")[i].onclick();
	}
}

function chkDelete(idx) {
	var cnt = document.getElementsByName("chk[]").length;
	var chkcnt = 0;

	for(i = 0; i < cnt; i++) if(document.getElementsByName("chk[]")[i].checked) chkcnt++;

	if(chkcnt == 0) {
		alert("삭제할 게시글을 선택하세요");
		return;
	}

	if(!confirm("리스트 삭제시, 신청자 목록도 함께 삭제되며 복구가 불가능 합니다.\n삭제하시겠습니까?")) {
		return;
	}

	if(idx || idx == 0) $("chk" + idx).checked = true;
	document.fmList.mode.value="stockedNotiListDelete";
	document.fmList.action = "indb.php";
	document.fmList.method = "post";
	document.fmList.submit();
}
--></script>

<div class="title title_top">상품 재입고 알림<span>상품 재입고 알림 서비스를 신청한 고객들에게 SMS 메시지를 발송 및 설정할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=31')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<!-- 상품출력조건 : start -->
<form name=frmList onsubmit="return chkForm(this)">

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td><font class=small1>검색어</td>
	<td>
	<select name=skey>
	<? foreach ( array('goodsnm'=>'상품명','a.goodsno'=>'고유번호','goodscd'=>'상품코드','keyword'=>'유사검색어') as $k => $v) { ?>
		<option value="<?=$k?>" <?=($k == $_GET['skey']) ? 'selected' : ''?>><?=$v?></option>
	<? } ?>
	<? unset($k,$v) ?>
	</select>
	<input type=text name=sword class=lline value="<?=$_GET[sword]?>" class="line">
	</td>
</tr>
</table>

<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<table width=100% cellpadding=0 cellspacing=0>
<tr>
	<td class=pageInfo><font class=ver8>
	총 <b><?=$total?></b>개, 검색 <b><?=$pg->recode[total]?></b>개, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages
	</td>
	<td align=right>

	<table cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td style="padding-left:20px">
		<img src="../img/sname_output.gif" align=absmiddle>
		<select name=page_num onchange="this.form.submit()">
		<?
		$r_pagenum = array(10,20,40,60,100);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?=$v?>" <?=($v == $_GET['page_num']) ? 'selected' : ''?>><?=$v?>개 출력
		<? } ?>
		</select>
		</td>
	</tr>
	</table>

	</td>
</tr>
</table>
</form>
<!-- 상품출력조건 : end -->

<form name="fmList" method="post" action="./indb.php" target="_self">
<input type="hidden" name="mode" value="quickstock">



<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th width=60><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>전체선택</a></th>
	<th><font class=small1><b>번호</th>
	<th><font class=small1><b></th>
	<th><font class=small1><b>상품명</th>
	<th><font class=small1><b>옵션1</th>
	<th><font class=small1><b>옵션2</th>
	<th><font class=small1><b>재고</th>
	<th><font class=small1><b>신청자</th>
	<th><font class=small1><b>발송</th>
	<th><font class=small1><b>&nbsp;</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=35><col width=50><col width=50><col><col width=70 span=5><col width=60><col width=60><col width=35>
<?
while (is_resource($res) && $data=$db->fetch($res)){
	if($data['opt1'] != $data['opt1Ori'] || $data['opt2'] != $data['opt2Ori']){
		$bgcolor="#ffff99";
	}else{
		$bgcolor="#ffffff";
	}
?>
<tr style="background-color: <?=$bgcolor?>"><td height=4 colspan=12></td></tr>
<tr style="background-color: <?=$bgcolor?>">
	<td align=center class="noline"><input type=checkbox name=chk[] value="<?=$data['sno']?>" onclick="iciSelect(this, '<?=$bgcolor?>')"></td>
	<td align=center><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td><a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a></td>
	<td><a href="javascript:popup('popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',825,600)"><font class=small1 color=0074BA><?=$data[goodsnm]?></font></a></td>
	<td align="center"><?=$data['opt1']?></td>
	<td align="center"><?=$data['opt2']?></td>
	<td align="center"><?=$data['stock']?></td>
	<td align=center><font class=small color=555555><?=number_format($data[request_count])?>명</td>
	<td align=center><font class=small color=555555><?=number_format($data[sended_count])?>명</td>
	<td align=center>
	<a href="javascript:void(0);" onClick="popupLayer('./popup.stocked_noti.php?sno=<?=$data[sno]?>&goodsno=<?=$data[goodsno]?>&optno=<?=$data['optno']?>&opt1=<?=$data['opt1']?>&opt2=<?=$data['opt2']?>',800,800);"><img src="../img/btn_stocked_noti_list.gif"></a>
	</td>

</tr>
<tr style="background-color: <?=$bgcolor?>"><td height=4 colspan=12></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>
<div style="margin:10px 0"><font class=extext><span style="color:red">[※ 주의]</span> 노란색 리스트는 상품 옵션정보(옵션명)가 변경 및 삭제된 리스트 이며, 재고량이 다를 수 있습니다.<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;해당 상품의 옵션정보를 확인하신 후 재입고 알림 메세지를 전송해 주세요.</font></div>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
<td width=6% style="padding-left:12"><!--a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')"><img src="../img/btn_allchoice.gif"></a--></td>
<td width=88% align=center><div class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div></td>
<td width=6%></td>
</tr></table>

<table style="width:100%">
<tr>
	<td><a href="javascript:chkBox(true)"><img src="../img/btn_allselect_s.gif" border="0"/></a>
		<a href="javascript:chkBox(false)"><img src="../img/btn_alldeselect_s.gif" border="0"></a>
		<a href="javascript:chkDelete()"><img src="../img/btn_alldelet_s.gif" border="0"></a>
	</td>
</tr>
</table>
</form>





<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">검색을 통해 원하는 상품의 재입고 신청 현황을 확인 할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">재입고 신청 상품의 신청인원 및 신청자목록 버튼을 통해 신청자 리스트를 확인 할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품 재입고 알림 신청자 리스트 창에서 신청회원에게 SMS메시지를 발송할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">SMS 메시지 발송은 신청회원 전체와 선택한 회원으로 구분하여 발송할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">SMS 메시지는 상품 재입고 현황에 따라 신청자 리스트 창에서 메시지 설정이 가능합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품 재입고 알림 SMS 메시지는 [상품 재입고 알림 설정]메뉴에서 기본문구를 설정 할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />SMS 포인트가 충전되어 있어야 발송이 가능합니다. <a href="../member/sms.pay.php"><font color=white><u>[SMS 포인트 충전하기]</u></font></a> 에서 충전하세요</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>