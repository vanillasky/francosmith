<?

$location = "SMS���� > SMS ������";
include "../_header.php";

### �з��� ���� üũ
$query = "select category,count(*) cnt from ".GD_SMS_SAMPLE." group by category";
$res = $db->query($query);
while ($data=$db->fetch($res)) $cnt[$data[category]] = $data[cnt];

### ȸ�� �׷캰 �ο��� üũ
$query = "select level,count(*) cnt from ".GD_MEMBER." where sms='y' and mobile!='' group by level";
$res = $db->query($query);
while ($data=$db->fetch($res)) $cnt_grp[$data[level]] = $data[cnt];

?>

<script>
function insChr(str)
{
	var fm = document.forms[0];
	fm.msg.value = fm.msg.value + str;
	chkLength(fm.msg);
}
function chkLength(obj){
	str = obj.value;
	document.getElementsByName('vLength')[0].value = chkByte(str);
	if (chkByte(str)>80){
		alert("80byte������ �Է��� �����մϴ�");
		obj.value = strCut(str,80);
	}
}
</script>

<div class="title title_top"><font  face=���� color=black><b>SMS</b></font> ������<span>SMS���ڸ޼����� �̿��Ͽ� ������ ������Ű����</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=8')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form method=post action="indb.php" target=ifrmHidden onsubmit="return chkForm(this)">
<input type=hidden name=mode value="send_sms">

<!--<div style="padding-top:0px"></div>-->
<!--<img src="../img/title_smssend.gif" border=0 hspace=10>-->

<table>
<tr>
	<td valign=top>

	<table>
	<tr>
		<td>

		<table width=146 cellpadding=0 cellspacing=0 border=0>
		<tr><td><img src="../img/sms_top.gif"></td></tr>
		<tr>
			<td background="../img/sms_bg.gif" align=center height="81"><textarea name=msg style="font:9pt ����ü;overflow:hidden;border:0;background-color:transparent;width:98px;height:74px;" onkeydown="chkLength(this)" onkeyup="chkLength(this)" onchange="chkLength(this)" required msgR="�޼����� �Է����ּ���"></textarea></td>
		</tr>
		<tr><td height=31 background="../img/sms_bottom.gif" align=center><font class=ver8 color=262626><input name=vLength type=text style="width:20px;text-align:right;border:0;font-size:8pt;font-style:verdana;" value=0>/80 Bytes</td></tr>
		</table>

		</td>
	</tr>
	<tr>
		<td>

		<table>
		<tr>
			<td><font class=small1 color=262626>�����»��<td>
			<td><input type=text name=callback value="<?=str_replace("-","",$cfg[smsRecall])?>" size=12></td>
		</tr>
		<tr>
			<td><font class=small1 color=262626>�����Ǽ�<td>
			<td><span id=span_sms style="font-weight:bold"><font class=ver9 color=0074BA><b><?=number_format($godo[sms])?></b></span><font color=262626>��</td>
		</tr>
		</table>

		</td>
	</tr>
	<tr>
		<td align=center>
		<input type="image" src="../img/btn_smssend.gif" class=null>
		</td>
	</tr>
	</table>

	</td>
	<td valign=top>
    <div style="padding-top:13px"></div>

	<script>

	var isOpenSearch = 0;
	function vSearch()
	{
		openLayer('srch_member');
		if (!isOpenSearch){
			ifrmSearch.location.href = "popup.srch_member.php?ifrmScroll=1";
		}
		isOpenSearch++;
	}
	</script>

	<table>
	<tr>
		<td>
		<input type=radio name=type value=1 class=null checked><font color=262626>���� �߼��ϱ� &nbsp;<a href="javascript:vSearch()" onfocus=blur()><img src="../img/btn_member_search.gif" border=0 align=absmiddle></a>

		<!-- ȸ���˻� ���������� -->
		<div id=srch_member style="position:absolute;border:1 solid #cccccc;display:none">
		<iframe id=ifrmSearch frameborder=0 style="width:380;height:500"></iframe>
		</div>

		<br><table>
		<tr>
			<td style="padding-left:5px" valign=top><div style="padding-top:5px"></div><font class=small1 color=262626>�޴»��</td>
			<td valign=top><textarea name=phone style="overflow:visible;width:126px"></textarea> <font class=small1 color=444444> ����Ű�� ������ ��ȣ�߰�</td>
			<td>
			<!--<a href="javascript:popup('popup.srch_member.php',380,420)"><img src="../img/btn_member_search.gif" border=0 align=absmiddle></a>-->
			</td>
		</tr>
        <!--<tr>
        <td colspan=5 style="padding-left:50px" valign=top><font class=small1 color=444444> ����Ű�� ������ ��ȣ�߰�</td>
        </tr>-->
		</table>

		</td>
	</tr>

	<tr><td height=5></td></tr>

	<tr>
		<td>
		<input type=radio name=type value=2 class=null><font color=262626>�׷캰 �߼��ϱ�
		<select name=level>
		<? foreach( member_grp() as $v ){ ?>
		<option value="<?=$v[level]?>"><?=$v[grpnm]?> (<?=number_format($cnt_grp[$v[level]])?>��)
		<? } ?>
		</select>
		</td>
	</tr>

	<tr><td height=5></td></tr>

	<tr>
		<td>
		<input type=radio name=type value=3 class=null><font color=262626>��üȸ������ �߼��ϱ� (<?=number_format(@array_sum($cnt_grp))?>��)
		</td>
	</tr>
	</table>
    <div style="padding-left:10px">
	<div style="background:#D7D7D7;border:0 solid #C5C5C5;width:205px;height:10px;font:0">
	<div id=sms_bar style="width:0;height:10px;font:0;background:#ff0000"></div>
	</div>
	</div>

	<div style="padding:3px 7px">
	<table>
	<tr>
		<td><a href="javascript:openLayer('special')" onfocus=blur()><img src="../img/btn_smstext.gif" border=0 align=absmiddle></a>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_tip>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font color=0074BA>�����߼۽�</font> �޴� ��� �Է¶��� ��ȣ�� �ְ� <font color=0074BA>EnterŰ�� ������ ��ȭ��ȣ�� �߰�</font>�� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font color=0074BA>��üȸ������ �߼��ϱ�</font>�� �̿��ϸ� �ٷξƷ� <font color=0074BA>ȸ���ٸ� ���� �߼���Ȳ</font>�� �������ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>


		</td>
	</tr>
	<tr><td>

	<table id=special style="position:absolute;border:1 solid #cccccc;background:#f7f7f7;padding:5px;display:none">
	<tr>
		<? for ($i=0;$i<count($r_sms_chr);$i++){ ?>
		<td style="border:1 solid #dddddd;width:20px;height:20px;background:#ffffff" align=center onClick="insChr(this.innerHTML)" class=hand onmouseover=this.style.background='#FFC0FF' onmouseout=this.style.background=''>
		<?=$r_sms_chr[$i]?>
		</td>
		<? if ($i%15==14){ ?></tr><tr><? } ?>
		<? } ?>
	</tr>
	</table>

	</td></tr>
	</table>
	</div>

	</td>
</tr>
</table><p>

&nbsp;&nbsp;&nbsp;<font color=262626><b><font size=1 face=helvetica>��</font> ���ڸ޼�������</b></font> &nbsp;<span class=small><font class=small color=444444>�޼����� Ŭ���ϸ� �޼���â�� �ٷ� �Է��� �˴ϴ�</font></span>&nbsp;&nbsp;
<a href="javascript:popupLayer('sms.sample_reg.php?mode=sms_sample_reg')"><img src="../img/btn_smsadd.gif" border=0 align=absmiddle></a>

<div style="height:5;font:0"></div>
<table border=1 bordercolor=#dddddd style="border-collapse:collapse">
<col align=center span=10>
<tr>
	<td width=100><a href="sms.sample_list.php?ifrmScroll=1" target=ifrmSms><font class=small1 color=161616>��ü����</font></a></td>
	<? $idx=1; foreach($r_sms_category as $v){ ?>
	<td width=100 height=25><a href="sms.sample_list.php?ifrmScroll=1&category=<?=$v?>" target=ifrmSms><font class=small color=161616><?=$v?></a> (<font color=0074BA><b><?=number_format($cnt[$v])?></b></font>)</td>
	<? if (++$idx%7==0){ ?></tr><tr><? } ?>
	<? } ?>
</tr>
</table>

<iframe id=ifrmSms name=ifrmSms src="sms.sample_list.php?ifrmScroll=1" style="width:100%;height:100px" frameborder=0></iframe>

</form>

<? include "../_footer.php"; ?>