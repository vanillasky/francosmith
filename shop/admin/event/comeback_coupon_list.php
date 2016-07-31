<?
$location = "컴백 쿠폰/SMS > 컴백 쿠폰/SMS 관리";
include "../_header.php";

$db_table = GD_COMEBACK_COUPON;
$_GET[page_num] = $_GET[page_num] ? $_GET[page_num] : 20;

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg -> field = "*";

$pg->cntQuery = "SELECT COUNT(*) FROM ".$db_table;

$pg->setQuery($db_table,'',"sno DESC");
$pg->exec();

$res = $db->query($pg->query);
?>
<style>
#alt_msg {border: solid 4px #dce1e1; border-collapse: collapse; margin-bottom: 20px; padding: 10px 0 10px 10px;}
#alt_msg img {float: left; padding: 15px 15px;}
</style>
<script type="text/javascript">
function comeback_coupon_copy(sno) {
	if (confirm("컴백구폰/SMS가 동일한 조건으로 새로 등록됩니다.\n이미 발급된 쿠폰은 같은 회원에게 재발급할 수 없고, 쿠폰사용일이 지난 쿠폰도 사용할 수 없으므로 새로운 운영자 쿠폰을 선택/수정 후 발급하시기 바랍니다.")) {
		location.href = "./comeback_coupon_indb.php?mode=copy&sno="+sno;
	}
}

function comeback_coupon_delete(sendyn, sno) {
	var confirm_text = "삭제하시겠습니까?";
	if (sendyn == 'y') confirm_text = "삭제시 쿠폰 발급 내역, SMS 발송결과를 확인할 수 없습니다. "+confirm_text;

	if (confirm(confirm_text)) {
		location.href = "./comeback_coupon_indb.php?mode=delete&sno="+sno;
	}
}
</script>

<div class="title title_top">컴백 쿠폰/SMS &nbsp; &nbsp; 
    <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=25')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
</div>
<div id="alt_msg">
	<img src="../img/btn__icon.gif"/>
	<ul>
		<li>주문 이력이 있는 고객 중 한동안 주문을 하지 않았던 고객에게 컴백 쿠폰을 발행하고</li>
		<li>SMS를 발송해 재 방문을 유도하세요!</li>
		<li>쿠폰만 발급하거나 SMS만 발송 할 수도 있습니다.</li>
	</ul>
</div>

<div class="title title_top">상점 SMS 정보 입력<span>상점 SMS 정보를 입력하세요. </span>
    <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=25')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
</div>
<table class="tb">
    <col class="cellC">
    <col class="cellL">
    <tr>
        <td>회신전화번호</td>
        <td><?=$cfg['smsRecall']?>
            <p class="extext">*<strong>SMS 자동발송/설정 메뉴</strong>에서 발신번호를 설정해 주세요.
                <a href="../member/sms.auto.php">[바로가기]</a></p>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="padding:7px 0px 10px 10px">
            <table style="width: 700px;">
                <tr>
                    <td>
                        <? $sms = Core::loader('Sms'); ?>
                        잔여 SMS 포인트 :
                        <span style="font-weight:bold;color:#627DCE;"><?=number_format($sms->smsPt)?></span> 건
                    </td>
                    <td>
                        <div style="padding-top:7px; color:#666666" class="g9">SMS 포인트가 없는 경우 SMS가 발송되지 않습니다.</div>
                        <div style="padding-top:5px; color:#666666" class="g9">SMS포인트를 충전하여 발송하시길 바랍니다.</div>
                    </td>
                    <td>
                        <a href="../member/sms.pay.php"><img src="../img/btn_point_pay.gif"/></a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div style="padding-top:15px"></div>

<div class="title title_top">컴백 쿠폰 / SMS 리스트<span>쿠폰 발급/SMS 발송 조건을 만들고 관리합니다.</span>
    <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=25')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
</div>

<div class="right" style="margin-bottom:5px;">
	<img src="../img/sname_output.gif" align="absmiddle" />
	<select name=page_num onchange="location.href=location.pathname+'?page_num='+this.value;">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=($v == Clib_Application::request()->get('page_num')) ? 'selected' : ''?>><?=$v?>개 출력
	<? } ?>
	</select>
	<a href="./comeback_coupon_form.php"><img src="../img/btn_comeback_new.gif" align="absmiddle" /></a>
</div>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<col><col width="200"><col width="100"><col width="100"><col width="250"><col width="100"><col width="150">
<tr class=rndbg>
	<th><font class=small1>이름</th>
	<th><font class=small1>종류</th>
	<th><font class=small1>상태</th>
	<th><font class=small1>발송대상</th>
	<th><font class=small1>발송내역 조회</th>
	<th><font class=small1>발급/발송</th>
	<th><font class=small1>복사/수정/삭제</th>
</tr>
<tr><td class=rnd colspan=11></td></tr>
<? while ($data = $db->fetch($res)) {?>
<tr height=35>
	<td style="padding-left:10px;"><a href="./comeback_coupon_form.php?sno=<?=$data['sno']?>"><?=$data['title']?></a></td>
	<td align="center">
		<?if ($data['couponyn'] == 'y') { echo '<img src="../img/img_01.gif" align="absmiddle" /> 쿠폰';}?>
		<?if ($data['smsyn'] == 'y') { echo '<img src="../img/img_02.gif" align="absmiddle" /> SMS';}?>
	</td>
	<td align="center"><?if ($data['sendyn'] == 'y') { echo "완료";} else { echo "대기";}?></td>
	<td align="center"><?if ($data['sendyn'] == 'n') { echo '<a onclick="popup(\'popup.comeback_coupon_user.php?sno='.$data['sno'].'\',550,850)" class="hand"><img src="../img/btn_comeback_see.gif" align="absmiddle" /></a>';}?></td>
	<td align="center">
		<?if ($data['sendyn'] == 'y' && $data['couponyn'] == 'y') { echo '<a onclick="popup(\'popup.coupon_user.php?couponcd='.$data['couponcd'].'&applysno='.$data['applysno'].'\',650,850)" class="hand"><img src="../img/btn_comeback_coupon.gif" align="absmiddle" /></a>';}?>
		<?if ($data['sendyn'] == 'y' && $data['smsyn'] == 'y') { echo '<a onclick="popup(\'../member/popup.sms.sendList.php?sms_logNo='.$data['sms_logNo'].'\',800,750)" class="hand"><img src="../img/btn_comeback_sms.gif" align="absmiddle" /></a>';}?>
	</td>
	<td align="center"><?if ($data['sendyn'] == 'n') { echo '<a href="./comeback_coupon_indb.php?sno='.$data['sno'].'&mode=send"><img src="../img/btn_comeback_send.gif" align="absmiddle" /></a>';}?></td>
	<td align="center">
		<a onclick="comeback_coupon_copy('<?=$data['sno']?>')" class="hand"><img src="../img/btn_comeback_copy.gif" align="absmiddle" /></a>
		<?if ($data['sendyn'] == 'n') {?><a href="./comeback_coupon_form.php?sno=<?=$data['sno']?>"><img src="../img/buttons/btn_modify_small.gif" align="absmiddle" /></a><?}?>
		<a onclick="comeback_coupon_delete('<?=$data['sendyn']?>','<?=$data['sno']?>')" class="hand"><img src="../img/i_del.gif" align="absmiddle" /></a>
	</td>
</tr>
<tr><td colspan=11 class=rndline></td></tr>
<? } ?>
</table>
<div class="pageNavi" align=center><font class=ver8><?=$pg->page[navi]?></div>

<? include "../_footer.php"; ?>