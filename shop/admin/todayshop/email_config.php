<?
/*
	mode 값으 종류는 sms_config.php 의 arTitle 배열 변수의 키값을 사용함
*/

$mode = $_GET[mode];

$location = "투데이샵 > $loc[$mode]";
include "../_header.php";

$todayShop = &load_class('todayshop', 'todayshop');

if (!$todayShop->auth()) {
	msg(' 서비스 신청안내는 고도몰 고객센터로 문의해주시기 바랍니다.', -1);
}

$tsCfg = $todayShop->cfg;

$arTitle['orderc'] = array('title'=>'쿠폰상품 주문완료시 자동발송', 'desc'=>'(결제 완료시 발송되는 메시지입니다.)');
$arTitle['salec'] = array('title'=>'쿠폰상품 판매성공시 자동발송', 'desc'=>'(판매가 결정되면 발송됩니다.)');
//$arTitle['giftc'] = array('title'=>'쿠폰판매 성공시 자동발송(선물하기)', 'desc'=>'(선물을 받는 사람에게 발송됩니다.)');
$arTitle['orderg'] = array('title'=>'실물상품 주문완료시 자동발송', 'desc'=>'(결제 완료시 발송되는 메시지입니다.)');
$arTitle['deliveryg'] = array('title'=>'실물상품 배송시 자동발송', 'desc'=>'(판매가 결정되고 상태가 배송중으로 바뀔 때 발송되는 메세지입니다.)');
$arTitle['cancel'] = array('title'=>'판매실패시 자동발송', 'desc'=>'(목표구매량에 도달하지 못한 경우 구매 취소 메시지 입니다.)');

// 분문 가져오기
$mail_body = '';

if (preg_match('/\.php$/',$tsCfg['mailMsg_'.$mode]) && is_file('../../conf/email/'.$tsCfg['mailMsg_'.$mode])) {
	ob_start();
		include( '../../conf/email/'.$tsCfg['mailMsg_'.$mode] );
		$mail_body = ob_get_contents();
	ob_end_clean();
}
?>

<form method=post action="./indb.email_config.php" onsubmit="return chkForm(this)">

<div class="title title_top"><?=$arTitle[$mode]['title']?><span><?=$arTitle[$mode]['desc']?> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=13')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table width=100% class=tb>
<col class=cellC><col class=cellL>
<tr height=25>
	<td>자동발송여부</td>
	<td class=noline>
	<label><input type=radio name=mailUse_<?=$mode?> value="y" <?=($tsCfg['mailUse_'.$mode] == 'y') ? 'checked' : ''?>>자동으로 보냄</label>
	<label><input type=radio name=mailUse_<?=$mode?> value="n" <?=($tsCfg['mailUse_'.$mode] != 'y') ? 'checked' : ''?>>보내지않음</label>
	</td>
</tr>

<tr height=25>
	<td>메일제목</td>
	<td><input type=text name="mailSbj_<?=$mode?>" value="<?=$tsCfg['mailSbj_'.$mode]?>" style="width:100%" required class="line"></td>
</tr>
<tr>
	<td>내용</td>
	<td style="padding:5px">
	<textarea name=mailMsg_<?=$mode?> type=editor style="width:100%;height:500px"><?=htmlspecialchars($mail_body)?></textarea>
	<script src="../../lib/meditor/mini_editor.js"></script>
	<script>mini_editor("../../lib/meditor/")</script>
	</td>
</tr>
</table>

<div class=button>
<input type=image src="../img/btn_modify.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">메일 하단에 있는 로고는 <a href="../todayshop/codi.banner.php" target=_blank><font color=white><b>[로고/배너관리]</b></font></a> 에서 메일로고를 등록하시면 됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">메일 내용에 쓰이는 이미지들은 <a href="../design/design_webftp.php" target=_blank><font color=white><b>[webFTP이미지관리 > data > editor]</b></font></a> 에서 관리하세요.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>