<?
	### ���ݰ�꼭 ����
	$query = "select * from ".GD_TAX." where step>0 and ordno='$ordno' order by sno desc";
	$tax_data = $db->fetch($query);

	if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
		$tax_data = validation::xssCleanArray($tax_data, array(
			validation::DEFAULT_KEY	=> 'text'
		));
	}

	$tax_data[busino] = substr($tax_data[busino], 0, 3) . '-' . substr($tax_data[busino], 3, 2) . '-' . substr($tax_data[busino], 5);
	$tax_data[issuedate] = explode("-", $tax_data[issuedate]);

	### ����
	include_once dirname(__FILE__) . "/../../conf/config.pay.php";
	$sealpath = '/shop/data/skin/' . $cfg['tplSkin'] . '/img/common/' . $set[tax][seal];

	$classids = array( "cssblue", "cssred" ); //-- ���޹޴��ڿ�, �����ڿ�
	$headuser = array( "cssblue"=>"���޹޴��ں�����", "cssred"=>"�����ں�����" );
	switch ($_GET[taxarea]){
		case 'blue':
			$classids = array( "cssblue" ); //-- ���޹޴��ڿ�
			break;
		case 'red':
			$classids = array( "cssred" ); //-- �����ڿ�
			break;
	}

	if ($tax_data[step] == 3) $classids = array();

	//����������
	if (!$cfg['road_address']) {
		$address = $cfg['address'];
	} else {
		$address = $cfg['road_address'];
	}
?>
<style type = "text/css">
td, th { font-family: ����; font-size: 9pt; color: 333333;}

#cssblue { width: 604px; border: solid 2px #364F9E;  }
#cssblue strong { font:18pt ����; color:#364F9E; font-weight:bold; }
#cssblue table { border-collapse: collapse; }
#cssblue td, #cssblue table { border-color: #364F9E; border-style: solid; border-width: 0; }

#cssblue #head { border-width: 1px 1px 0 1px; }
#cssblue #box td { border-width: 1px; padding-top: 3px; }

#cssred { width: 604px; border: solid 2px #FF4633;  }
#cssred strong { font:18pt ����; color:#FF4633; font-weight:bold; }
#cssred table { border-collapse: collapse; }
#cssred td, #cssred table { border-color: #FF4633; border-style: solid; border-width: 0; }

#cssred #head { border-width: 1px 1px 0 1px; }
#cssred #box td { border-width: 1px; padding-top: 3px; }
</style>
<div <? if( array_search('cssblue', $classids) !== false ){ ?>id="taxtable" taxsno="<?=$tax_data[sno]?>"<? } ?>>
<center>

<? if ($tax_data[step] == 3){ ?>
<DIV class="notprint" style="margin:0 40 20 40;">
<div class=small style="background-color:#eeeeee; padding: 15px 10px; text-align:center; line-height: 20px;">
<font color=#0074BA><strong><?=$ordno?></strong></font> �ֹ��� <font color=EA0095>���ڼ��ݰ�꼭 ������ ��û�� �����Դϴ�.</font><br>
���ڼ��ݰ�꼭�� �����û�� �ֹ����� �Ϲݼ��ݰ�꼭�� ����� �� �����ϴ�.
</div>
</div>
<? } ?>

<? foreach($classids as $cloop=>$classid){ ?>
<? if ( $cloop != 0 ){ ?>
<hr style="border:1px dashed #d9d9d9; width:500;">
<? } ?>
<div id="<?=$classid?>">
<table id="head" width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="50%" align="right"><strong>�� �� �� �� ��</strong></td>
		<td width="50%" style="padding-left:6px">(<?=$headuser[$classid]?>)</td>
	</tr>
	</table>
	</td>
	<td width="60" style="border-right-width: 3px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td height="28" align="center">å&nbsp;��&nbsp;ȣ</td>
	</tr>
	<tr>
		<td height="24" align="center">�Ϸù�ȣ</td>
	</tr>
	</table>
	</td>
	<td width="150">
	<table width="100%" border="0" cellspacing="0" cellpadding="4">
	<tr height="26">
		<td width="50%" align="right" style="border-right-width: 1px; border-bottom-width: 1px;"> ��</td>
		<td width="50%" align="right" style="border-bottom-width: 1px;"> ȣ</td>
	</tr>
	<tr height="26">
		<td align="center" style="border-right-width: 1px;">&nbsp;</td>
		<td align="center">&nbsp;</td>
	</tr>
	</table>
	</td>
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<col width=3%><col width=47%><col width=3%>
<tr>
	<!-- ������ -->
	<td align="center" style="border-width: 3px 1px 0px 1px; padding-left: 2px; line-height: 36px;">��<br>��<br>��</td>
	<td valign="top" height="100%" style="border-width: 3px 1px 0 0; background: url(<?=$sealpath?>) no-repeat; background-position: 207px 0px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<col width=53><col width=100><col width=26>
	<tr height="38" align="center">
		<td style="border-width: 0 1px 1px 2px;">��Ϲ�ȣ</td>
		<td colspan="3" style="border-width: 0 2px 1px 0; padding-left:6px;"><?=$cfg[compSerial]?></td>
	</tr>
	<tr height="38" align="center">
		<td style="border-width: 0 1px 3px 2px;">��&nbsp;&nbsp;&nbsp;&nbsp;ȣ<br>(���θ�) </td>
		<td style="padding:0 6px; border-bottom-width:3px;"><?=$cfg[compName]?></td>
		<td style="border-width: 0 1px 3px 1px;">����</td>
		<td style="border-width: 0 2px 3px 0; padding-right:10px;"><?=$cfg[ceoName]?></td>
	</tr>
	<tr height="38" align="center">
		<td style="border-width: 0 1px 1px 0px;">�� �� ��<br>�� �� �� </td>
		<td colspan="3" style="padding: 0 6px; border-bottom-width: 1px;" align=left><?=$address?></td>
	</tr>
	<tr height="38" align="center">
		<td style="border-right-width: 1px;">��&nbsp;&nbsp;&nbsp;&nbsp;��</td>
		<td style="padding:0 6px;"><?=$cfg[service]?></td>
		<td style="border-width: 0 1px; padding-left:4px">��<br>�� </td>
		<td style="padding: 0 6px;"><?=$cfg[item]?></td>
	</tr>
	</table>
	</td>
	<!-- ���޹޴��� -->
	<td align="center" style="border-top-width: 3px; border-right-width: 1px; line-height: 22px; padding-left: 2px">��<br>��<br>��<br>��<br>��</td>
	<td valign="top" height="100%" style="border-top-width: 3px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<col width=53><col width=100><col width=26>
	<tr height="38" align="center">
		<td style="border-width: 0 1px 1px 2px;">��Ϲ�ȣ</td>
		<td colspan="3" style="border-bottom-width: 1px; padding-left:6px;"><?=$tax_data[busino]?></td>
	</tr>
	<tr height="38" align="center">
		<td style="border-width: 0 1px 3px 2px;">��&nbsp;&nbsp;&nbsp;&nbsp;ȣ<br>(���θ�) </td>
		<td style="padding:0 6px; border-bottom-width:3px;"><?=$tax_data[company]?></td>
		<td style="border-width: 0 1px 3px 1px;">����</td>
		<td style="border-bottom-width: 3px; padding-right:10px;"><?=$tax_data[name]?></td>
	</tr>
	<tr height="38" align="center">
		<td style="border-width: 0 1px 1px 0px;">�� �� ��<br>�� �� �� </td>
		<td colspan="3" style="padding: 0 6px; border-bottom-width: 1px;" align=left><?=$tax_data[address]?></td>
	</tr>
	<tr height="38" align="center">
		<td style="border-right-width: 1px;">��&nbsp;&nbsp;&nbsp;&nbsp;��</td>
		<td style="padding:0 6px;"><?=$tax_data[service]?></td>
		<td style="border-width: 0 1px; padding-left:4px">��<br>�� </td>
		<td style="padding: 0 6px;"><?=$tax_data[item]?></td>
	</tr>
	</table>
	</td>
</tr>
</table>

<table id="box" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-top-width:3px; border-bottom-width:2px;">
<col width="34"><colgroup span="2" width="20"></colgroup><col width="34"><colgroup span="11"></colgroup><col width="1"><colgroup span="10"></colgroup><col width="64">
<tr align="center">
	<td colspan="3">��&nbsp;&nbsp;&nbsp;��</td>
	<td colspan="12">��&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;��&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;��&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;��</td>
	<td><img src="" width="1" height="1"></td>
	<td colspan="10" style="border-right-width:3px;">��&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;��</td>
	<td>��&nbsp;&nbsp;&nbsp;&nbsp;��</td>
</tr>
<tr align="center">
	<td>��</td>
	<td>��</td>
	<td>��</td>
	<td style="letter-spacing:-2">������</td>
	<td>��</td>
	<td>��</td>
	<td>��</td>
	<td>õ</td>
	<td>��</td>
	<td>��</td>
	<td>��</td>
	<td>õ</td>
	<td>��</td>
	<td>��</td>
	<td>��</td>
	<td><img src="" width="1" height="1"></td>
	<td>��</td>
	<td>��</td>
	<td>õ</td>
	<td>��</td>
	<td>��</td>
	<td>��</td>
	<td>õ</td>
	<td>��</td>
	<td>��</td>
	<td>��</td>
	<td style="border-left-width:3px;">&nbsp;</td>
</tr>
<tr align="center" height="34">
	<td><?=$tax_data[issuedate][0]?></td>
	<td><?=$tax_data[issuedate][1]?></td>
	<td><?=$tax_data[issuedate][2]?></td>
	<td>&nbsp;<?=11-strlen($tax_data[supply])?></td>
	<?for($ixx=(strlen($tax_data[supply]) - 11);$ixx<strlen($tax_data[supply]);++$ixx){?><td><?=$tax_data[supply][$ixx]?>&nbsp;</td><?}?>
	<td><img src="" width="1" height="1"></td>
	<?for($ixx=(strlen($tax_data[surtax]) - 10);$ixx<strlen($tax_data[surtax]);++$ixx){?><td><?=$tax_data[surtax][$ixx]?>&nbsp;</td><?}?>
	<td style="border-left-width:3px;"><?=substr($ordno,0,7)?><br><?=substr($ordno,7)?></td>
</tr>
</table>

<!-- �ֹ�list -->
<table id="box" width="100%" border="0" cellspacing="0" cellpadding="0">
<colgroup span="2" width="3%"></colgroup><col><colgroup span="2" width="9%"></colgroup><col width="12%"><col width="19%"><col width="13%"><col width="8%">
<tr height="24" align="center">
	<td>��</td>
	<td>��</td>
	<td>ǰ&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;��</td>
	<td>��&nbsp;&nbsp;��</td>
	<td>��&nbsp;&nbsp;��</td>
	<td>��&nbsp;&nbsp;��</td>
	<td>��&nbsp;&nbsp;��&nbsp;&nbsp;��&nbsp;&nbsp;��</td>
	<td>��&nbsp;&nbsp;��</td>
	<td>��&nbsp;&nbsp;��</td>
</tr>
<tr height="25" align="center">
	<td><?=$tax_data[issuedate][1]?></td>
	<td><?=$tax_data[issuedate][2]?></td>
	<td style="padding: 0 6px; word-break:break-all"><?=$tax_data[goodsnm]?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td align="right" style="padding-right:6px">&nbsp;</td>
	<td align="right" style="padding-right:6px"><?=number_format($tax_data[supply])?>��</td>
	<td align="right" style="padding-right:6px"><?=number_format($tax_data[surtax])?>��</td>
	<td>&nbsp;</td>
</tr>
<? for($jj = 1; $jj < 4; $jj++){ ?>
<tr height="25">
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<? } ?>
</table>

<table id="box" width="100%" border="0" cellspacing="0" cellpadding="0">
<col width="100"><colgroup span="4" width="88"></colgroup>
<tr align="center">
	<td style="border-top-width: 0;">�� �� �� ��</td>
	<td style="border-top-width: 0;">��&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;��</td>
	<td style="border-top-width: 0;">��&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ǥ</td>
	<td style="border-top-width: 0;">��&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;��</td>
	<td style="border-top-width: 0;">�ܻ�̼���</td>
	<td rowspan="3" style="border-top-width: 0;">�� �ݾ��� ���� ��</td>
</tr>
<tr height="25" align="center">
	<td><?=number_format($tax_data[price])?>��</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</table>
</div>
<? } ?>

</center>
</div>