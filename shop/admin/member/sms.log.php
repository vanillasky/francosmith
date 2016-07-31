<?
header("location:./sms.sendList.php");
exit;
$location = "SMS설정 > SMS 발송내역";
include "../_header.php";
include "../../lib/page.class.php";

### 그룹명 가져오기
$query = "SELECT sms_grp FROM ".GD_SMS_ADDRESS." GROUP BY sms_grp ORDER BY sms_grp ASC";
$res = $db->query($query);
while ($data=$db->fetch($res)) $r_grp[] = $data['sms_grp'];

include "znd_sms.log.php";

?>
<style>
table.sms-log {word-break:break-all;}
table.sms-log tr {height:23px;}
table.sms-log tr td {color:#262626;font-family:Tahoma,Dotum;font-size:11px;}
table.sms-log tr td.status {color:#0070C0;}
table.sms-log tr.res td {color:#C00000}
table.sms-log tr.res td.status {}

</style>

<div class="title title_top"><font face="굴림" color="black"><b>SMS</b></font> 발송내역<span>전송된 SMS 발송내역을 관리합니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=18')"><img src="../img/btn_q.gif" align="absmiddle" hspace="2" /></a></div>

<table border="4" bordercolor="#dce1e1" style="border-collapse:collapse; margin-bottom:10px" width="700">
<tr><td style="padding:7 0 10 10">
<div style="padding-top:5"><b>※ SMS 발송내역 확인 안내</b></div>
<div style="padding-top:7px; color:#666666" class="g9">① 발송완료된 건수만 포인트차감되며, 발송실패된 건수는 하루에 한번 새벽 1시경에 정산됩니다.<br/>
&nbsp;&nbsp;&nbsp; (그러나, 새벽1시 이후 처음 sms 발송된 후 정확히 남은 건수가 보여지게 됩니다.)
</div>
<div style="padding-top:5px; color:#666666" class="g9">② <span style="color:#627dce">보다 정확한 SMS 발송내역 데이터는 고도몰에 로그인 하신 후, 마이고도에서 다운로드가 가능합니다.<br/>
&nbsp;&nbsp;&nbsp; 메뉴 : 고도몰 로그인 > 마이고도 > 나의 쇼핑몰 > [상세정보/관리] 클릭 > SMS 발송 내역에서 다운로드</span><br/>
&nbsp;&nbsp;&nbsp; <a href="http://www.godo.co.kr/mygodo/index.html" target="_blank"><font class=extext_l>[마이고도 바로가기 > ]</font></a>
</div>
</td></tr>
</table>

<form>
<input type="hidden" name="search" value="yes" />

<table class="tb">
<col class="cellC" /><col class="cellL" />
<tr>
	<td>발송상태</td>
	<td class="noline">
	<label><input type="radio" name="status" value="" <?=$_GET['status'] == '' ? 'checked' : ''?>>전체</label>
	<label><input type="radio" name="status" value="send" <?=$_GET['status'] == 'send' ? 'checked' : ''?>>발송완료</label>
	<label><input type="radio" name="status" value="res" <?=$_GET['status'] == 'res' ? 'checked' : ''?>>발송예약</label>
	</td>
</tr>
<tr>
	<td>발송기간</td>
	<td>
	<input type="text" name="regdt[]" value="<?=$_GET['regdt'][0]?>" size="8" maxlength="8" onkeydown="onlynumber();" class="cline" /> -
	<input type="text" name="regdt[]" value="<?=$_GET['regdt'][1]?>" size="8" maxlength="8" onkeydown="onlynumber();" class="cline" />
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align="absmiddle" /></a>
</tr>
<tr>
	<td>수신번호</td>
	<td><input type="text" name="tran_phone" value="<?=$_GET['tran_phone']?>" class="line"></td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div><p>

</form>

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="sms-log">
<tr><td class="rnd" colspan="14"></td></tr>
<tr class="rndbg">
	<th>번호</th>
	<th>전송시간/전송예약시간</th>
	<th>전송형태</th>
	<th>수신번호</th>
	<th>제목</th>
	<th>메세지</th>
	<th>사용포인트</th>
	<th>상태</th>
</tr>
<tr><td class="rnd" colspan="14"></td></tr>
<col width="35" align="center">
<col width="130" align="center">
<col width="80" align="center">
<col width="100" align="center">
<col width="200" align="center">
<col style="padding-left:10px">
<col width="80" align="center">
<col width="70" align="center">

<?
// 예약 발송인 경우, 발송 성공 여부를 알아올 수 없으므로, 현재가 지나면 그냥 발송으로;;
$now = date('Y-m-d H:i:s');
if ($loop){ foreach ($loop as $data){
$reserved = ($data['reservedt'] > $now) ? true : false ;?>

<tr class="<?=$reserved ? 'res' : 'send'?>">
	<td><?=$pg->idx--?></td>
	<td><?=$data['reservedt'] != '0000-00-00 00:00:00' ? $data['reservedt'] : $data['regdt']?></td>
	<td><?=$data['sms_type']?></td>
	<td><?=$data['to_tran']?></td>
	<td><?=$data['subject']?></td>
	<td><?=$data['msg']?></td>
	<td><?=number_format($data['cnt'])?>포인트</td>
	<td class="status"><?=$reserved ? '발송예약' : '발송완료'?></td>
</tr>
<tr><td colspan="14" class="rndline"></td></tr>
<? }} ?>
</table>

<div class="pageNavi" align="center"><font class="ver8"><?=$pg->page['navi']?></div>



<div id="MSG01">
<table cellpadding=1 cellspacing=0 border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />발송완료된 건수만 포인트차감됩니다.</td></tr>
<!--<tr><td><img src="../img/icon_list.gif" align="absmiddle" />충전한 <font color=0074BA>SMS 포인트는 환불되지 않습니다.</font></td></tr>-->
</table>
</div>
<script>cssRound('MSG01');</script>

<? include "../_footer.php"; ?>