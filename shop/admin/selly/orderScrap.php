<?
/*********************************************************
* 파일명     :  orderScrap.php
* 프로그램명 :  주문수집
* 작성자     :  dn
* 생성일     :  2012.05.12
**********************************************************/
$location = "셀리 > 주문수집";
include "../_header.php";
include "../../conf/config.pay.php";
include "../../lib/sAPI.class.php";

list($cust_seq) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_seq'");
list($cust_cd) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_cd'");

if(!$cust_seq || !$cust_seq) {
	msg("셀리를 신청하고 상점 인증 등록 후에 사용가능한 서비스입니다.");
	go("./setting.php");
}

$sAPI = new sAPI();

$code_arr = array();
$code_arr['grp_cd'] = 'MALL_CD';

$mall_cd = $sAPI->getcode($code_arr, 'hash');
unset($code_arr);

$scrap_data = $sAPI->getMallLoginId();

?>
<script type="text/javascript">
var popup_no = 0;

function scrapOrderPop() {

	var chk = document.getElementsByName("chk[]");
	var bool_chk = false;
	
	for (var i=0; i< chk.length; i++) {	
		if(chk[i].checked == true) {
			bool_chk = true;
		}
	}	

	if(bool_chk == false) {
		alert('주문수집할 아이디를 선택해 주세요');
		return;
	}
	
	popup_return('_blank.php', 'scrap_pop' + popup_no, 800, 700, '', '', 1);
	var frm = document.frmOrderScrap;
		
	frm.target = 'scrap_pop' + popup_no;
	frm.action = 'orderScrapPop.php';
	frm.submit();

	popup_no ++;
}
</script>

<div class="title title_top">마켓 주문 수집 <span>마켓에 링크되어 있는 상품의 주문을 일괄 수집할 수 있습니다.</span><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=selly&no=12')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>
<form name="frmOrderScrap" method="post" action="">
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=4></td></tr>
<tr class="rndbg">
	<th width="50" align="center">선택</th>
	<th width="30%" align="center">마켓</th>
	<th width="30%" align="center">로그인 ID</th>
	<th width="30%" align="center">최종 주문 수집일</th>
</tr>
<tr><td class="rnd" colspan="4"></td></tr>
<tr><td height=4 colspan=4></td></tr>
<?
if(!empty($scrap_data)) {
	foreach($scrap_data as $row_scrap) {
		if($row_scrap['mall_cd'] == 'mall0005') continue;
?>
<tr><td height=4 colspan=7></td></tr>
<tr height=25>
	<td width="50" align="center" class="noline">
		<input type="checkbox" name="chk[]" value="<?=$row_scrap['minfo_idx']?>" />
	</td>
	<td width="30%" align="center"><?=$mall_cd[$row_scrap['mall_cd']]?></td>
	<td width="30%" align="center"><?=$row_scrap['mall_login_id']?></td>
	<td width="30%" align="center"><?=$row_scrap['last_order_date']?></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=7 class=rndline></td></tr>
<?
	}
}
else { ?>
<tr><td height=4 colspan=7></td></tr>
<tr height=25>
	<td colspan=4 align="center">기본설정에서 마켓을 등록해 주세요</td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=7 class=rndline></td></tr>
<? } ?>
</table>
<div class="button">
	<input type="image" src="../img/btn_orderscrap.gif" alt="주문수집" onclick="javascript:scrapOrderPop();return false;" />
</div>
</div>
</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr><td>
<a href="../selly/mallList.php"><font color=white><u>[마켓관리]</u></font></a>에서 등록된 마켓의 주문을 수집하실 수 있습니다.<br/><br/><br/>

주문수집시 e나무에 등록된 주문이 아닌경우 신규주문(결제확인)만 수집되어 e나무에 등록됩니다.<br/>
e나무에 등록된 주문의 경우 취소/반품/교환요청 수집과 구매확정, 정산완료 수집이 가능합니다.<br/>
주문수집시 마켓에 없는 상태일 경우 해당 상태로 수집되지 않습니다.<br/>
수집된 주문은 <a href="../selly/marketOrderList.php"><font color=white><u>[마켓주문관리]</u></font></a>에서 상태를 처리하실 수 있습니다.<br/>
마켓의 정보가 실제와 다를 경우 <a href="../selly/mallList.php"><font color=white><u>[마켓관리]</u></font></a>에서 정보수정을 하셔야 주문수집이 됩니다.<br/>
마지막으로 주문수집한 일자를 최종 주문 수집일에서 확인하실 수 있습니다.
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>