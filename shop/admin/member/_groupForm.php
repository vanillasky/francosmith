<?
### ȸ���� ����Ÿ ��������
if($adminAuth){
	$where[] = "level >= 80";
	@include "../../conf/groupAuth.php";
	$rAuthMsg = array(
		'basic'		=>'���θ��⺻����',
		'design'	=>'�����ΰ���',
		'goods'		=>'��ǰ����',
		'order'		=>'�ֹ�����',
		'member'	=>'ȸ������',
		'dormant'	=>'�޸�ȸ������',
		'board'		=>'�Խ��ǰ���',
		'event'		=>'���θ��',
		'marketing'	=>'�����ð���',
		'log'		=>'������',
		'todayshop' =>'�����̼� ����',
		'mobileShop'	=>'����ϼ�����',
		'selly' =>'����',
		'shople' =>'����',
		'hiebay' =>'����! eBay',
		'etc' =>'�����',
	);
}else{
	$where[] = "level < 80";
}

$query = "select level,count(*) from ".GD_MEMBER." where 1 and ".implode('and',$where)." AND ".MEMBER_DEFAULT_WHERE." group by level order by level";
$res = $db->query($query);
while ($data=$db->fetch($res)) $cnt[$data['level']] = $data[1];

$query = "select * from ".GD_MEMBER_GRP."  where 1 and ".implode('and',$where)." order by level";
$res = $db->query($query);
?>

<?if($adminAuth){?><div style="padding:10 0 5 5;color:#fe5400;"><font color="000000"><b>1. ���� �Ʒ����� �������� �׷�� ������ ���մϴ�.</b></font></div><?}?>

<table width="100%" cellpadding="0" cellspacing="0" border=0>
<tr><td class="rnd" colspan="10"></td></tr>
<tr class="rndbg" style="padding-top:2">
	<th><font class="small1"><b>��ȣ</th>
	<th><font class="small1"><b>�׷��</th>
	<th><font class="small1"><b>�׷췹��</th>
	<th><font class="small1"><b>��� �򰡱���</b></th>
	<?if($adminAuth){?>
	<th><font class="small1"><b>����</th>
	<?}else{?>
	<th>ȸ����</th>
	<th>����������</th>
	<th>�߰�������</th>
	<th>��ۺ񹫷�����</th>
	<?}?>
	<th><font class="small1"><b>���Ѽ���</th>
	<th><font class="small1"><b>����</th>
</tr>
<tr><td class="rnd" colspan="10"></td></tr>
<col align="center" span="10">
<?
	while ($data=$db->fetch($res)){
		if($data['free_deliveryfee'] == "Y"){
			$free_deliveryfee	= "������";
		}else{
			$free_deliveryfee	= "��ۺ���å";
		}
?>
<tr><td height="4" colspan="10"></td></tr>
<tr>
	<td width="50"><font class="ver8" color="444444"><?=++$idx?></td>
	<td width="120"><?=$data['grpnm']?><?if($data['level'] == 100)echo "&nbsp;<font class=\"small1\" color=\"777777\">(��ü)</font>";?></td>
	<td width="80"><font class="ver8" color="444444"><?=$data['level']?></td>
	<td width="250">
	<?
		$rule_data = $db->fetch("SELECT * FROM ".GD_MEMBER_GRP_RULESET." WHERE sno = '".$data['sno']."'");

		$pc = "";
		$mobile = "";
		$pc_amt = $pc_amt1 = $pc_amt2 = "";
		$mobile_amt = $mobile_amt1 = $mobile_amt2 = "";
		if($grp_ruleset['apprSystem'] == "figure") {

			//�� ��ü
			if($rule_data[by_number_buy_limit] || $rule_data[by_number_buy_max]) {
				$pc_amt = "���űݾ� : ";
				if($rule_data[by_number_buy_limit]) $pc_amt1 = $rule_data[by_number_buy_limit]." �� �̻�";
				if($rule_data[by_number_buy_max]) $pc_amt2 = $rule_data[by_number_buy_max]." �� �̸�";
				if($pc_amt1 && $pc_amt2) {
					$pc = $pc_amt.$pc_amt1." ~ ".$pc_amt2;
				} else {
					$pc = $pc_amt.$pc_amt1.$pc_amt2;
				}
			}
			if($rule_data[by_number_order_require])	{
				if($pc) $pc .= "<br />����Ƚ�� : ".$rule_data[by_number_order_require]."ȸ �̻�";
			}
			if($rule_data[by_number_review_require])	{
				if($pc) $pc .= "<br />�����ı� : ".$rule_data[by_number_review_require]."ȸ �̻�";
			}

			//����ϼ� �߰�����
			if($rule_data[mobile_by_number_buy_limit] || $rule_data[mobile_by_number_buy_max]) {
				$mobile_amt = "���űݾ� : ";
				if($rule_data[mobile_by_number_buy_limit]) $mobile_amt1 = $rule_data[mobile_by_number_buy_limit]." �� �̻�";
				if($rule_data[mobile_by_number_buy_max]) $mobile_amt2 = $rule_data[mobile_by_number_buy_max]." �� �̸�";
				if($mobile_amt1 && $mobile_amt2) {
					$mobile = $mobile_amt.$mobile_amt1." ~ ".$mobile_amt2;
				} else {
					$mobile = $mobile_amt.$mobile_amt1.$mobile_amt2;
				}
			}
			if($rule_data[mobile_by_number_order_require])	{
				if($mobile) $mobile .= "<br />����Ƚ�� : ".$rule_data[mobile_by_number_order_require]."ȸ �̻�";
			}
			if($rule_data[mobile_by_number_review_require])	{
				if($mobile) $mobile .= "<br />�����ı� : ".$rule_data[mobile_by_number_review_require]."ȸ �̻�";
			}

			if($pc) $pc = "�� ��ü<br />".$pc;
			if($mobile) {
				$mobile = "����ϼ� �߰�����<br />".$mobile;
				$pc .= "<br />".$mobile;
			}
		} else if($grp_ruleset['apprSystem'] == "point") {
			if($rule_data[by_score_limit] || $rule_data[by_score_max]) {
				$pc_amt = "�������� : ";
				if($rule_data[by_score_limit]) $pc_amt1 = $rule_data[by_score_limit]." �� �̻�";
				if($rule_data[by_score_max]) $pc_amt2 = $rule_data[by_score_max]." �� �̸�";
				if($pc_amt1 && $pc_amt2) {
					$pc = $pc_amt.$pc_amt1." ~ ".$pc_amt2;
				} else {
					$pc = $pc_amt.$pc_amt1.$pc_amt2;
				}
			}
		}
		echo $pc;
	?>
	</td>
	<?if(!$adminAuth){?>
	<td><font class="ver8" color="444444"><?=number_format($cnt[$data['level']])?></td>
	<td><font class="ver8" color="444444"><?=$data['dc']?>%</td>
	<td><font class="ver8" color="444444"><?=$data['add_emoney']?>%</td>
	<td><font class="ver8" color="444444"><?=$free_deliveryfee?></td>
	<?}else{?>
	<td><?if($data['level']==100)echo "<div style='float:left'><div style='text-overflow:ellipsis;overflow:hidden;width:95px;height:20;padding-top:5' nowrap><font class=\"small1\" color=\"777777\">���θ��⺻����</b></font></div></div>";?><?if($rAuth[$data['level']])foreach($rAuth[$data['level']] as $v)echo "<div style='float:left'><div style='text-overflow:ellipsis;overflow:hidden;width:95px;height:20;padding-top:5' nowrap><font class=\"small1\" color=\"777777\">".$rAuthMsg[$v]."</b></font></div></div>"; else echo "-";?></td>
	<?}?>
	<td width="60"><a href="javascript:popup('../member/popup.group.php?mode=modGrp&sno=<?=$data['sno']?>&adminAuth=<?=$adminAuth?>',1000,700)"><img src="../img/i_edit.gif" border="0" /></a></td>
	<td width="50"><? if ($data['level']!=1 && $data['level']!=100){ ?><a href="../member/indb.php?mode=delGrp&sno=<?=$data['sno']?>&level=<?=$data['level']?>&adminAuth=<?=$adminAuth?>" target="ifrmHidden" onclick="return confirm('������ <?=$data['grpnm']?> �׷��� �����Ͻðڽ��ϱ�?')"><img src="../img/i_del.gif" border="0" /></a></td><? } ?>
</tr>
<tr><td height="4" colspan="10"></td></tr>
<tr><td colspan="10" class="rndline"></td></tr>
<? } ?>
</table>

<div style="padding-top:10px"></div>
<?
$query = "select count(*) from ".GD_MEMBER." where level not in (select level from ".GD_MEMBER_GRP.") AND " . MEMBER_DEFAULT_WHERE;
list($notGroupCount) = $db->fetch($query);
if ($notGroupCount>0){
	echo "�� <b>".$notGroupCount."</b>���� �׷���� ȸ���� �ֽ��ϴ�. �׷��� �������ּ���. <a href='batch.php?func=level' class='extext'>[ȸ���׷� �ϰ�����]</a></b>";
}
?>
<div align="center">
<?if($adminAuth){?>
<a href="javascript:popupLayer('../member/popup.group.php?adminAuth=<?=$adminAuth?>');"><img src="../img/btn_add_admingroup.gif" border="0" vspace="5" hspace="40" /></a></div>
<?}else{?>
<a href="javascript:popupLayer('../member/popup.group.php?adminAuth=<?=$adminAuth?>');"><img src="../img/btn_addgroup.gif" border="0" vspace="5" hspace="40" /></a></div>
<?}?>


<div style="padding-top:15px"></div>