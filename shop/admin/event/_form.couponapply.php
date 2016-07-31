<?
if($_GET[sno]){
	$query = "select * from ".GD_COUPON_APPLY." where sno='$_GET[sno]'";
	$data2 = $db->fetch($query);
}

if(!$data2[membertype])$data2[membertype] = 0;
if(!$data2[member_grp_sno])$data2[member_grp_sno] = 0;

$checked[membertype][$data2[membertype]] = "checked";
$selected[member_grp_sno][$data2[member_grp_sno]] = "selected";

$allTotal = 0;
$result = $db->query("SELECT count(*) FROM ".GD_MEMBER);
list($allTotal) = $db->fetch($result);
?>
<script>
function chkLength(obj){
	var obj2 = document.getElementsByName('vLength')[0];
	var str = obj.value;
	obj2.value = chkByte(str);
	if (chkByte(str)>90) {
		obj2.style.color = "#FF0000";
//		SMS.chkLength(obj);
	}
	else {
		obj2.style.color = "";
	}
}
</script>
<div style='height:10'></div>
<div style="padding:3 0 5 8"><img src="../img/ico_arrow_down.gif" align=absmiddle> <font color=0074BA><b>이 쿠폰을 제공할 회원선택</b></font> <font class=extext>(쿠폰을 지급할 회원을 추가하려면 아래에서 회원을 선택하세요)</font></div>
<form action="indb.coupon.php" method=post onSubmit="return checkform1(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=couponcd value="<?=$_GET[couponcd]?>">
<input type=hidden name=sno value="<?=$_GET[sno]?>">

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td valign=top>
		<table class=tb>
		<col class=cellC style='padding:5,0,5,5'><col class=cellL style='padding:5,0,5,5'>
		<tr>
			<td align=left><input type=radio name=membertype value=0 <?=$checked[membertype][0]?> onclick='calSmsCnt();' class=null>전체회원발급</td>
			<td>전체회원(현재 총 <font color=EA0095><b><?=number_format($allTotal)?>명</b></font>)에게 쿠폰을 발급합니다.</td>
		</tr>
		<tr>
			<td align=left><input type=radio name=membertype value=1 <?=$checked[membertype][1]?> onclick='calSmsCnt();' class=null>그룹별발급</td>
			<td>
				<select name=member_grp_sno onchange='calSmsCnt();'>
					<option>해당그룹을선택하세요.</option>
					<?
					foreach($r_mgrp as $v){
					?>
						<option value='<?=$v[sno]?>' <?=$selected[member_grp_sno][$v[sno]]?>><?=$v[grpnm]?>(<?=number_format($v[cnt])?>명)</option>
					<?}?>
				</select>
			</td>
		</tr>
		<tr>
			<td height=170  align=left valign=top><input type=radio name=membertype value=2 onclick='calSmsCnt();' <?=$checked[membertype][2]?> class=null>회원개별발급</td>
			<td valign=top>
				<div style="padding-top:4"><a href="javascript:popup('popup.member.php',800,600);"><img src="../img/arrow_blue.gif" align=absmiddle><font color=0074BA><b>[회원검색하기]</b></a></div>
				<div class="box">
					<table width=300 id=m_ids>
			<?
			if($_GET[sno]){
			$i = 0;
			$query = "select b.m_id,b.name,a.m_no from ".GD_COUPON_APPLY."member a left join ".GD_MEMBER." b on a.m_no = b.m_no where applysno='$_GET[sno]'";
			$res3 = $db->query($query);
			while($data3 = $db->fetch($res3)){
				$i++;
			?>
			<tr>
				<td id=currPosition><?=$data3[name]?>(<?=$data3[m_id]?>)</td>
				<td>
				<input type=text name=m_ids[] value="<?=$data3[m_no]?>" style="display:none">
				<input type=hidden name=sort[] value="<?=-$i?>" class="sortBox right" maxlength=10 <?=$hidden[sort]?>>
				</td>
				<td>
				<a href="javascript:void(0)" onClick="del_options(this.parentNode.parentNode)"><img src="../img/i_del.gif" border=0 align=absmiddle></a>
				</td>
			</tr>
			<? }} ?>
					<col><col width=50 style="padding-right:10"><col width=52 align=right>
					</table>
				</div>
			</td>
		</tr>
		</table>
	</td>
	<td width=180 align=center>
		<table border=0>
		<tr>
			<td>
			<div align=center><input type=checkbox name='smsyn' value='1' onclick='calSmsCnt();' class=null>SMS 동시 발송</div>
			<table width=146 cellpadding=0 cellspacing=0 border=0>
			<tr><td><img src="../img/sms_top.gif"></td></tr>
			<tr>
				<td background="../img/sms_bg.gif" align=center height="81"><textarea name=msg cols=16 rows=5 style="font:9pt 굴림체;overflow:hidden;border:0;background-color:transparent;" onkeydown="chkLength(this)" onkeyup="chkLength(this)" onchange="chkLength(this)" msgR="메세지를 입력해주세요"></textarea></td>
			</tr>
			<tr><td height=31 background="../img/sms_bottom.gif" align=center><font class=ver8 color=262626><input name=vLength type=text style="width:20px;text-align:right;border:0;font-size:8pt;font-style:verdana;" value=0>/90 Bytes</td></tr>
			</table>

			</td>
		</tr>
		<tr>
			<td>

			<table>
			<tr>
				<td><font class=small1 color=262626>발신번호<td>
				<td>
					<input type=text name=callback size=12 readonly="readonly"><br>
					<a onclick="popup_return('../member/popup.callNumber.php?target=callback','callNumber',450,250,0,0,'yes')" class="hand"><img src="../img/call_number_btn.gif" align="absmiddle"></a>
				</td>
			</tr>
			<tr>
				<td><font class=small1 color=262626>발송건수<td>
				<td><span id=span_sms_send style="font-weight:bold"><?=number_format($total)?></span>건</td>
			</tr>
			<tr>
				<td><font class=small1 color=262626>남은건수<td>
				<td><span id=span_sms style="font-weight:bold"><?=number_format(getSmsPoint())?></span>건</td>
			</tr>
			<tr>
				<td colspan=4 height=28><img src="../img/arrow_blue.gif" align=absmiddle><a href="/shop/admin/member/sms.pay.php" target=_new><font class=small1 color=0074BA><u>SMS포인트 충전하기</u></a><td>
			</tr>
			</table>

			</td>
		</tr>
		</table>
	</td>
</tr>


	</td>


</tr>
</table>

<div class=button align=center>
<?if($_GET[mode] == 'applyAdd'){?>
<input type=image src="../img/btn_register.gif">
<?}?>
<?if($_GET[mode] == 'applyMod'){?>
<input type=image src="../img/btn_modify.gif">
<?}?>
<a href="javascript:history.back();"><img src="../img/btn_cancel.gif"></a>

</div>
</form>