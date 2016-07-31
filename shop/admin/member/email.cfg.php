<?

$mode = $_GET[mode];

$loc	= array(
		0	=> "주문확인메일",
		1	=> "입금확인메일",
		3	=> "배송/발송메일",
		4	=> "배송완료메일",
		10	=> "회원가입메일",
		11	=> "아이디/비밀번호찾기메일",
		12	=> "회원탈퇴메일",
		13	=> "비밀번호찾기 인증메일",
		14	=> "비밀번호변경 안내메일",
		20	=> "1:1문의답변메일",
		30	=> "수신거부완료 안내메일",
		31	=> "수신동의설정 안내메일",
		40	=> "휴면 전환 사전 안내 메일",
		);
$default_info="고객에게 자동발송되는 메일을 수정하고 관리합니다";
$info	= array(
		0	=> $default_info,
		1	=> $default_info,
		3	=> "고객에게 자동발송되는 메일을 수정하고 관리합니다",
		4	=> $default_info,
		10	=> $default_info,
		11	=> $default_info,
		12	=> $default_info,
		13	=> $default_info,
		14	=> $default_info,
		20	=> $default_info,
		30	=> $default_info,
		31	=> $default_info,
		40	=> $default_info,
		);

$location = "자동메일설정 > $loc[$mode]";
include "../_header.php";
include "../../conf/config.php";

$useyn = $cfg["mailyn_$mode"];
if (!$useyn) $useyn = "n";
$checked[useyn][$useyn] = "checked";

$body = file_get_contents("../../conf/email/tpl_{$mode}.php");	//내용
@require_once "../../conf/email/subject_{$mode}.php";	//제목

$skin_type_checked[$cfg[skin_type]]="checked";
$skin_type_checked[$cfg[skin_type]]="checked";
?>

<form method=post action="../proc/indb.email.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$mode?>">

<div class="title title_top"><?=$loc[$mode]?><span><?=$info[$mode]?> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=7')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table width=100% class=tb>
<col class=cellC><col class=cellL>
<?
if(! in_array($_GET[mode],array('11','20','13'))){
?>
<tr height=25>
	<td>자동발송여부</td>
	<td class=noline>
	<input type=radio name=cfg[mailyn_<?=$mode?>] value="y" <?=$checked[useyn][y]?>>자동으로 보냄
	<input type=radio name=cfg[mailyn_<?=$mode?>] value="n" <?=$checked[useyn][n]?>>보내지않음
	</td>
</tr>
<?}?>
<?php
if(in_array($_GET['mode'],$r_sendDateCode['mail'])) {
	// 기본값 처리
	if (empty($cfg['mailSendDate_'.$mode])) {
		$cfg['mailSendDate_'.$mode]	= $r_sendDateDefault['mail'];
	}
?>
<tr height=25>
	<td>발송대상</td>
	<td class=noline>
		최근
		<select name="cfg[mailSendDate_<?php echo $mode;?>]">
			<?php foreach ($r_sendDatePeriod['mail'] as $dayVal) {?>
			<option value="<?php echo $dayVal;?>" <?php if ($cfg['mailSendDate_'.$mode] == $dayVal) echo 'selected="selected"';?>><?php echo $dayVal;?>일</option>
			<?php }?>
		</select>
		주문건만
	</td>
</tr>
<?php
}
?>
<tr height=25>
	<td>발송자Email</td>
	<td><?if($cfg[adminEmail])echo $cfg[adminEmail]; else echo "없음";?>&nbsp;<a href="javascript:emailModify()"><img src="../img/i_edit.gif" align="absmiddle" /></a>
	<div style="margin-top:5px"><span class=small><font class=extext>※[기본정보설정>관리자 Email] 에 등록된 Email로 발송되며, 입력 누락시 메일이 발송되지 않습니다.</font></span></div>
	</td>
</tr>
<tr height=25>
	<td>메일제목</td>
	<td><input type=text name="subject" value="<?=$headers[Subject]?>" style="width:100%" required class="line"></td>
</tr>
<?if($mode==3) {?>
<tr height=25>
	<td>메일 기본양식</td>
	<td>
	<a href="javascript:skin_sel('1')"><img src="../img/btn_type_a.gif" alt="타입1 초기화" /></a>&nbsp;&nbsp;<a href="javascript:skin_sel('2')"><img src="../img/btn_type_b.gif" alt="타입2 초기화" /></a>
	</td>
</tr>
<?}?>
<tr>
	<td>내용</td>
	<td style="padding:5px">
	<textarea name=body type=editor style="width:100%;height:830px"><?=htmlspecialchars($body)?></textarea>
	<script src="../../lib/meditor/mini_editor.js"></script>
	<script>mini_editor("../../lib/meditor/")</script>
	</td>
</tr>
<?if($mode==3) {?>
<tr>
	<td>치환코드</td>
	<td style="padding:5px;line-height:20px">
		구매상품정보 내역 : {orderInfo} <a href="javascript:clipboard('{orderInfo}')">[복사]</a><br/>
		결제정보 내역 : {settleInfo} <a href="javascript:clipboard('{settleInfo}')">[복사]</a><br/>
		배송정보 내역 : {deliveryInfo} <a href="javascript:clipboard('{deliveryInfo}')">[복사]</a><br/>
		<br/>
		<span style="font:11px 돋움;color:#627dce">메일 내용 수정시 치환코드를 복사하여 사용하세요.</span>
	</td>
</tr>
<?}?>
</table>

	<div class='button' style="width:100%;position:relative" >

			<input type='image' src="../img/btn_modify.gif" />&nbsp;<a href="javascript:history.back()"><img src="../img/btn_cancel.gif" /></a>
			<?if($mode==3){?>
				&nbsp;&nbsp;
				<a href="javascript:rollbackList()" onMouseOver="javascript:rollbackList()"><img src="../img/btn_source_restore.gif" alt="소스복구" /></a>
				<div id="rollbackList" style="position:absolute;left:59%;top:0px;"></div>
			<?}?>

	</div>
</form>

<?php if($mode == 10 || $mode == 30 || $mode == 31){ ?>
<table cellpadding="0" cellspacing="2" width="100%" border="0" style="margin: 0px 0px 30px 0px; border: 3px #dce1e1 solid; padding: 5px;">
<tr>
	<td style="color: #627dce; font-weight: bold;">※ 광고성 정보 수신동의에 대한 정보통신망법 준수 사항 안내</td>
</tr>
<tr>
	<td style="padding-top: 15px; font-weight: bold;">정보통신망법의 기준에 따라 아래 자동메일 발송 시 광고성 정보 수신동의 내용이 함께 발송됩니다.</td>
</tr>
<tr>
	<td><a href="http://www.law.go.kr/lsInfoP.do?lsiSeq=164340&ancYd=20141128&ancNo=25789&efYd=20150818&nwJoYnInfo=N&efGubun=Y&chrClsCd=010202#0000" target="_blank"><span style="text-decoration: underline; color: blue; font-weight: bold;">[정보통신망 이용촉진 및 정보보호 등에 관한 법률 시행령 바로가기]</span></a></td>
</tr>
<tr>
	<td style="padding-top: 15px;">
		<div style="margin-left: 5px;">1. 회원가입메일</div>
		<div style="margin-left: 10px;">* 광고성 정보 수신동의 상태 내용도 함께 발송</div>
		<div style="margin-left: 13px;">※ 이메일 및 휴대폰 정보를 수집하지 않을 경우에는 수신거부 상태로 저장 및 안내됩니다.</div>
	</td>
</tr>
<tr>
	<td style="padding-top: 15px;">
		<div style="margin-left: 5px;">2. 수신동의설정 안내메일</div>
		<div style="margin-left: 10px;">* 정보수신동의 설정 변경 내용을 발송</div>
		<div style="margin-left: 10px;">* 회원정보에서 광고성 정보, 이벤트 SMS/메일 수신동의 여부 수정시 발송</div>
	</td>
</tr>
<tr>
	<td style="color: #627dce; padding-top: 15px;">
		※ 2015년 7월 30일 이후부터 운영중인 쇼핑몰의 경우 ‘회원가입메일’과 ‘수신거부완료 안내메일’에 관련 내용이 추가되어야 합니다.<br />
		<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=7')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>을 참고하시어 관련내용을 메일에 삽입하시길 바랍니다.
	</td>
</tr>
</table>
<?php } ?>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">메일 하단에 있는 로고는 <a href="../design/design_banner.php" target=_blank><font color=white><b>[로고/배너관리]</b></font></a> 에서 메일로고를 등록하시면 됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">메일 내용에 쓰이는 이미지들은 <a href="design/design_webftp.php" target=_blank><font color=white><b>[webFTP이미지관리 > data > editor]</b></font></a> 에서 관리하세요.</td></tr>
<?if($mode==3) {?>
<tr><td ><img src="../img/icon_list.gif" align="absmiddle">메일 디자인 내용을 수정 후 저장 시 이전의 html소스(이미지제외)만을 백업합니다.</td></tr>
<tr><td style="padding-left:10px">-백업파일은 최근순으로 3개까지만 저장되며 기타 사유로 백업이 되지 않을 경우를 대비하여
디자인 수정전에 html보기를 하셔서 직접 소스를 백업 받아두시고 작업하시기 바랍니다.
</td></tr>
<?}?>
</table>
</div>
<script>cssRound('MSG01')</script>
<script type="text/javascript">
<!--
	function clipboard(str){
		window.clipboardData.setData('Text',str);
		alert("클립보드에 복사되었습니다.");
	}

	function rollbackList(){
		ifrmHidden.location.href="email.cfg.proc.php?mail_mode=<?=$mode?>&mode=rollbackView";
	}

	function sel_file(filename){
		ifrmHidden.location.href="email.cfg.proc.php?mail_mode=<?=$mode?>&mode=rollbackForm&filename="+filename;
	}

	function rollbackList_remove(){
		document.getElementById("rollbackList").innerHTML='';
	}
	function skin_sel(skinType){
		if (confirm('선택하신 기본양식으로 초기화하시겠습니까?\n\n기존 디자인이 삭제되고 초기화 되므로 \n필요 시 HTML을 별도로 저장하신 후 초기화 하시기 바랍니다.'))
		{
			ifrmHidden.location.href="email.cfg.proc.php?skinType="+skinType+"&mail_mode=<?=$mode?>&mode=skin_select";
		}
	}
	function emailModify(){
		if(confirm('기본정보설정 페이지로 이동합니다. \n이동 시 작성중인 정보가 저장되지 않습니다.\n\n이동하시겠습니까?'))
		{
			location.href='../basic/default.php';
		}

	}
//-->
</script>
<? include "../_footer.php"; ?>