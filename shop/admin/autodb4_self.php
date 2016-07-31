<?php
//set_time_limit(0);
ini_set('max_execution_time', 60);
include '../lib/library.php';

function array_diff_assoc_ignorecase($array1 , $array2)
{
	$ar2_keys	= array();
	foreach($array2 as $key => $value)
	{
		$ar2_keys[strtolower($key)]	= $value;
	}

	foreach($array1 as $key => $value)
	{
		if(str_replace('varchar','char',$ar2_keys[strtolower($key)]) == str_replace('varchar','char',$value))
		{
			unset($array1[$key]);
		}
	}

	return $array1;
}

function replace_emptydefault($value)
{
	$value	= trim(str_replace('default \'\'','',$value));
	$value	= trim(str_replace('default \'0\'','',$value));
	$value	= trim(str_replace('default \'0000-00-00\'','',$value));
	$value	= trim(str_replace('default \'0000-00-00 00:00:00\'','',$value));

	return $value;
}


if($_GET['mode']=='insert'){

	$data		= readurl('http://newdata.godo.co.kr/enamoodb.php?db_name=s4self');
	$ar_data	= unserialize($data);
	$ar_querys	= array();

	$ar_tables	= array();
	$result		= $db->query('show tables');

	while($row = $db->fetch($result))
	{
		$sub_result	= $db->query('show create table ' . $row[0]);
		$sub_result	= $db->fetch($sub_result);

		$ar_tables[$row[0]]['create']	= $sub_result[1];
	}

	foreach($ar_tables as $key => $value)
	{
		preg_match_all('/^\s+`([_a-zA-Z0-9-]+)` (.+)\n/m',$value['create'],$matches);
		$length	= count($matches[0]);
		for($i=0; $i < $length; $i++)
		{
			$fieldname	= strtolower($matches[1][$i]);
			$ar_tables[$key]['fields'][$fieldname]		= preg_replace('/,?\n?$/','',$matches[2][$i]);
		}

		preg_match_all('/^\s+KEY `([_a-zA-Z0-9-]+)` (.+)\n/m',$value['create'],$matches);
		$length	= count($matches[0]);
		for($i=0; $i < $length; $i++)
		{
			$ar_tables[$key]['keys'][$matches[1][$i]]	= preg_replace('/,?\n?$/','',$matches[2][$i]);
		}
	}

	$chk_prn_settleprice	= false;
	foreach($ar_data as $key => $value)
	{
		if(!$ar_tables[$key] && !preg_match('/^gd_bd_.*$/',$key))
		{
			$ar_querys['create'][] = $value['create'];
		}
		else if(preg_match('/^gd_bd_.*$/',$key))
		{
			#�Խ��Ǻκ�
		}
		else
		{

			/* //default �� �񱳾��� �ҽ� ���ʿ� �ּ� ó��
			$ar_tables[$key]['fields']	= array_map('replace_emptydefault',$ar_tables[$key]['fields']);
			$value['fields']			= array_map('replace_emptydefault',$value['fields']);
			*/
			if($result = array_diff_assoc_ignorecase($value['fields'],$ar_tables[$key]['fields']))
			{	
				foreach($result as $fields => $f_value)
				{
					
					if($ar_tables[$key]['fields'][$fields])
					{
						$ar_querys['f_modify'][] = 'ALTER TABLE `'.$key.'` MODIFY `'.$fields.'` '.$value['fields'][$fields];
					}
					else if(!$ar_tables[$key]['fields'][strtolower($fields)])
					{
						$ar_querys['f_insert'][] = 'ALTER TABLE `'.$key.'` ADD `'.$fields.'` '.$value['fields'][$fields];

						if($key=='gd_order' && $fields=='prn_settleprice')
						{
							$chk_prn_settleprice	= true;
						}
					}
				}
			}
			if(!is_array($value['keys'])) $value['keys'] = array();
			if(!is_array($ar_tables[$key]['keys'])) $ar_tables[$key]['keys'] = array();
			if($result = array_diff_assoc($value['keys'],$ar_tables[$key]['keys']))
			{
				foreach($result as $key_name => $key_value)
				{
					if($ar_tables[$key]['keys'][$key_name])
					{
						$ar_querys['keys'][] = 'ALTER TABLE `'.$key.'` DROP key '.$key_name;
					}
					$ar_querys['keys'][] = 'ALTER TABLE `'.$key.'` ADD key '.$key_name.' '.$value['keys'][$key_name];
				}
			}
		}
	}

	// ����Ʈ �˻� Insert üũ
	$strSQL	= 'SELECT themenm FROM gd_goods_smart_search WHERE themenm = \'�⺻�˻�\' AND basic = \'y\' AND price = \'y\' LIMIT 1';
	list($smartS)	= $db->fetch($strSQL);

	$strSQL	= 'SELECT groupcd FROM gd_code WHERE groupcd=\'colorList\' LIMIT 1';
	list($smartC)	= $db->fetch($strSQL);

	$chk_smart	= false;
	if (empty($smartS) || empty($smartC)) {
		$chk_smart	= true;
	}
	
	/*	--------------------------------------------
	*	- member m_no Auto_increment üũ
	*	--------------------------------------------
	*/
	$arrayMnoAutoIncrement = 1;
	$mnoAutoIncrementResult = $db->query("SHOW TABLE STATUS WHERE name = 'gd_member'");
	$mnoAutoIncrementRow = $db->fetch($mnoAutoIncrementResult);
	$arrayMnoAutoIncrement = $mnoAutoIncrementRow['Auto_increment'];

	/*	--------------------------------------------
	*	- titleStyle ���� üũ
	*	--------------------------------------------
	*/
	$boardIdQuery = "Select id from gd_board order by sno;";
	$boardIdResult = $db->query($boardIdQuery);
	
	$arrayTitleStyle = array();
	while ($boardIdRow = $db->fetch($boardIdResult)) {
		$titleStyleYn = true;
		$titleStyleCheckQuery = "Select titleStyle From gd_bd_" . $boardIdRow['id'] . " limit 1";

		$db->query($titleStyleCheckQuery) or $titleStyleYn = false;
		if (!$titleStyleYn) {
			$arrayTitleStyle[$boardIdRow['id']] = 1;
		}
	}

	/*	--------------------------------------------
	*	- goods_review, member_qna notice == '' row üũ
	*	--------------------------------------------
	*/
	$goodsReviewQuery	= "Select count(sno) cnt From gd_goods_review Where notice = ''";
	$goodsReviewResult	= $db->query($goodsReviewQuery);
	$goodsReviewNoticeCheck = false;
	$goodsReviewNoticeCheckRow = $db->fetch($goodsReviewResult, 1);
	if ($goodsReviewNoticeCheckRow['cnt']) {
		$goodsReviewNoticeCheck = true;
	}

	$memberQnaQuery	= "Select count(sno) cnt From gd_member_qna Where notice = ''";
	$memberQnaResult	= $db->query($memberQnaQuery);
	$memberQnaNoticeCheck = false;
	$memberQnaNoticeCheckRow = $db->fetch($memberQnaResult, 1);
	if ($memberQnaNoticeCheckRow['cnt']) {
		$memberQnaNoticeCheck = true;
	}

//debug($ar_querys);
?>
<html>
<head>
<title>DB Sync</title>
</head>
<style>
td {font-size:10pt}
textarea {border:1px solid #999999}
.box {border:1px solid #999999}
</style>
<body>

<form method="post" action="?mode=exec">
<table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse" bordercolor="#999999">
<tr>
	<td colspan="2">&nbsp; <b>��������</b></td>
</tr>
<?php
	// ���� ����
	$arrQueryMode['create']		= '���̺� �߰�';
	$arrQueryMode['f_insert']	= '�ʵ� �߰�';
	$arrQueryMode['f_modify']	= '�ʵ� ����';
	$arrQueryMode['f_drop']		= '�ʵ� ����';
	$arrQueryMode['keys']		= '�ε���(Ű)';

	foreach($arrQueryMode as $qKey => $qVal)
	{
		if(count($ar_querys[$qKey]))
		{
?>
<tr>
	<td width="110" nowrap valign="top"><?php echo $qVal?></td>
	<td width="100%">
		<table width="100%" border="0" cellspacing="0" cellpadding="3">
<?php
		foreach($ar_querys[$qKey] as $key => $value) {
?>
		<tr>
			<td width="150" nowrap valign="top">
				<input type="radio" name="<?php echo $qKey?>[<?php echo $key?>][yn]" value="y" checked />�߰� &nbsp;
				<input type="radio" name="<?php echo $qKey?>[<?php echo $key?>][yn]" value="n" />�߰�����
			</td>
			<td width="100%">
<?php
			if($qKey == 'create') {
?>
				<textarea style="width:100%;height:200px;font-size:9pt" name="<?php echo $qKey?>[<?php echo $key?>][sql]"><?php echo htmlspecialchars($value)?></textarea>
<?php
			} else {
?>
				<input type="text" style="width:100%;font-size:9pt" name="<?php echo $qKey?>[<?php echo  $key?>][sql]" value="<?php echo htmlspecialchars($value)?>" class="box">
<?php
			}
?>
			</td>
		</tr>
<?php
		}
?>
		</table>
	</td>
</tr>
<?php
		}

	}

	// �ݾ� ���� (prn_settleprice)
	if ($chk_prn_settleprice === true)
	{
?>
<tr>
	<td width="110" nowrap> prn_settleprice</td>
	<td width="100%">
		<table width="100%" border="0" cellspacing="0" cellpadding="3">
		<tr>
			<td>
				<input type="radio" name="prn_settleprice" value="y" checked />���� &nbsp;
				<input type="radio" name="prn_settleprice" value="n" />���� ����
			</td>
		</tr>
		</table>
	</td>
</tr>
<?php
	}

	// ����Ʈ �˻� Insert
	if ($chk_smart === true)
	{
?>
<tr>
	<td width="110" nowrap>����Ʈ �˻� insert</td>
	<td width="100%">
		<table width="100%" border="0" cellspacing="0" cellpadding="3">
		<tr>
			<td>
				<input type="radio" name="gd_smart" value="y" checked />����Ʈ �˻� ���� �ʵ� ��� &nbsp;
				<input type="radio" name="gd_smart" value="n" />��Ͼ���
			</td>
		</tr>
		</table>
	</td>
</tr>
<?php
	}
?>
<tr>
	<td width="110" nowrap>ȸ�� m_no Auto_increment ������Ʈ</td>
	<td width="100%">
		<table width="100%" border="0" cellspacing="0" cellpadding="3">
		<tr>
			<td>
				<input type="radio" name="gd_member_ai_up" value="y" checked />ȸ�� m_no Auto_increment ������Ʈ
				/ ���� Auto_increment : <?=$arrayMnoAutoIncrement?> => <input type="text" name="gd_member_ai_change" value="<?=$arrayMnoAutoIncrement + 200?>" />
				&nbsp;
				<input type="radio" name="gd_member_ai_up" value="n" />������Ʈ ����
			</td>
		</tr>
		</table>
	</td>
</tr>
<?php
	if (!empty($arrayTitleStyle)) {
?>
<tr>
	<td width="110" nowrap>titleStyle �ʵ� ���� �Խ��� ���̺�</td>
	<td width="100%">
		<table width="100%" border="0" cellspacing="0" cellpadding="3">
<?php
		foreach ($arrayTitleStyle as $boardId => $value) {
?>
		<tr>
			<td>
				<input type="checkbox" name="titleStyleId[]" value="<?=$boardId?>" checked /><?=$boardId?> �Խ��� titleStyle Update &nbsp;
			</td>
		</tr>
<?php
		}
?>
		</table>
	</td>
</tr>
<?php
	}

	if ($goodsReviewNoticeCheck) {
?>
<tr>
	<td width="110" nowrap>��ǰ �ı� notice ���鰪 0���� ������Ʈ</td>
	<td width="100%">
		<table width="100%" border="0" cellspacing="0" cellpadding="3">
		<tr>
			<td>
				<input type="radio" name="gd_goods_review_notice_update" value="y" checked />���� ������Ʈ
				&nbsp;
				<input type="radio" name="gd_goods_review_notice_update" value="n" />������Ʈ ����
			</td>
		</tr>
		</table>
	</td>
</tr>
<?php
	}
	if ($memberQnaNoticeCheck) {
?>
<tr>
	<td width="110" nowrap>ȸ�� 1:1���� ���鰪 0���� ������Ʈ</td>
	<td width="100%">
		<table width="100%" border="0" cellspacing="0" cellpadding="3">
		<tr>
			<td>
				<input type="radio" name="gd_member_qna_notice_update" value="y" checked />���� ������Ʈ
				&nbsp;
				<input type="radio" name="gd_member_qna_notice_update" value="n" />������Ʈ ����
			</td>
		</tr>
		</table>
	</td>
</tr>
<?php
	}
?>
		
<tr>
	<td width="110" nowrap>��ǰ ���� ������Ʈ</td>
	<td width="100%">
		<table width="100%" border="0" cellspacing="0" cellpadding="3">
		<tr>
			<td>
				<input type="radio" name="gd_goods_stock" value="y" checked />��ǰ ���� ������Ʈ&nbsp;
				<input type="radio" name="gd_goods_stock" value="n" />������Ʈ ����
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td width="110" nowrap>��ǰ ��ü ���̺� ���� �κ� �� �ɼ� ��� ���� ������Ʈ</td>
	<td width="100%">
		<table width="100%" border="0" cellspacing="0" cellpadding="3">
		<tr>
			<td>
				<input type="radio" name="gd_option_upyn" value="y" checked />��ǰ ��ü ���̺� ���� �κ� �� �ɼ� ��� ���� ������Ʈ&nbsp;
				<input type="radio" name="gd_option_upyn" value="n" />������Ʈ ����
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td width="110" nowrap>��ǰ �� ���� �̹��� ��� ������Ʈ</td>
	<td width="100%">
		<table width="100%" border="0" cellspacing="0" cellpadding="3">
		<tr>
			<td>
				<input type="radio" name="gd_goods_longdesc" value="y" checked />��ǰ �� ���� �̹��� ��� ������Ʈ&nbsp;
				<input type="radio" name="gd_goods_longdesc" value="n" />������Ʈ ����&nbsp;
				<input type="text" name="gd_goods_before1" value="http://www." />&nbsp;www ���� ������&nbsp;
				<input type="text" name="gd_goods_before2" value="http://" />&nbsp;������ ������&nbsp;
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
<br>
<center>
	<input type="submit" value="��������">
</center>
</form>

</body>
</html>

<?php
} else if($_GET['mode'] == 'exec') {

	$ar_querys = array();
	foreach($_POST as $q_type => $value)
	{
		if(is_array($value))
		{
			foreach($value as $key => $sub_value)
			{
				if($sub_value['yn']=='y')
				{
					$ar_querys[]	= array('sql' => stripslashes($sub_value['sql']), 'done' => false);
				}
			}
		}
	}

	foreach($ar_querys as $key => $value)
	{
		if($db->query($value['sql']))
		{
			$ar_querys[$key]['done']	= true;
		} else {
			$ar_querys[$key]['done']	= false;
		}
	}

	if($_POST['prn_settleprice'] == 'y')
	{
		$prn_settleprice_result	= false;

		// �ֹ��� ������Ʈ - ���������ݾ� UPDATE
		$sql	= 'SELECT * FROM gd_order';
		$r1		= $db->query($sql);
		$i		= 0;
		while ($data1 = $db -> fetch($r1)) {
			$sql	= 'SELECT * FROM gd_order_item WHERE ordno=\''.$data1[ordno].'\'';
			$r2		= $db->query($sql);

			$settleprice1 = 0; $settleprice2=0; $csettleprice=0;
			$tcnt = 0; $icnt=0;

			while ($data2 = $db->fetch($r2)) {
				$tcnt++;
				if ($data2['istep'] > 40) {
					$icnt++;
					$settleprice1 += ($data2['price']-$data2['memberdc']-$data2['coupon']) * $data2['ea'];
				} else {
					$settleprice2 += ($data2['price']-$data2['memberdc']-$data2['coupon']) * $data2['ea'];
				}
			}

			if ($tcnt == $icnt) { //��� �ֹ������ ���
				$csettleprice	= $settleprice1 - $data1['enuri'] -$data1['emoney'] + $data1['delivery'];
			} else {
				$csettleprice	= $settleprice2 - $data1['enuri'] -$data1['emoney'] + $data1['delivery'];
			}

			$db->query('UPDATE gd_order SET prn_settleprice = \''.$csettleprice.'\' WHERE ordno=\''.$data1['ordno'].'\'') or die(mysql_error());
		}

		$db->query('UPDATE gd_member SET sum_sale=\'0\'') or die(mysql_error()); //���űݾ� �ʱ�ȭ

		$sql	= 'SELECT m_no,sum(prn_settleprice) as msum,count(ordno) as cnt FROM gd_order WHERE m_no!=\'0\' AND step=\'4\'AND (step2 is null or step2=\'0\') GROUP BY m_no';
		$r1		= $db->query($sql);
		while ($data1 = $db->fetch($r1)) {
			$db->query('UPDATE gd_member SET sum_sale=\''.$data1['msum'].'\', cnt_sale=\''.$data1['cnt'].'\' WHERE m_no=\''.$data1['m_no'].'\'') or die(mysql_error());
		}
		/*
		update gd_member as a , (SELECT m_no,sum(prn_settleprice) as prn_settleprice,count(ordno) as cnt FROM gd_order WHERE m_no!='0' AND step='4' AND (step2 is null or step2='0') GROUP BY m_no) as b set a.sum_sale=b.prn_settleprice where  a.m_no=b.m_no
		*/

		$prn_settleprice_result = true;
	}

	$gd_smart_result	= false;
	if($_POST['gd_smart'] == 'y')
	{
		// ����Ʈ �˻� �⺻ �׸� ���
		$strSQL1	= 'INSERT INTO `gd_goods_smart_search` SET `themenm` = \'�⺻�˻�\', `basic` = \'y\', `price` = \'y\', `regdt` = NOW()';

		// ����Ʈ �˻� �ȷ�Ʈ �� ����
		$strSQL2	= 'INSERT INTO `gd_code` (`groupcd`, `itemcd`, `itemnm`, `sort`) VALUES
			(\'colorList\', \'1\', \'8E562E\', 0),
			(\'colorList\', \'2\', \'E91818\', 1),
			(\'colorList\', \'3\', \'F4AA24\', 2),
			(\'colorList\', \'4\', \'F4D324\', 3),
			(\'colorList\', \'5\', \'F2F325\', 4),
			(\'colorList\', \'6\', \'A4DC0C\', 5),
			(\'colorList\', \'7\', \'37B300\', 6),
			(\'colorList\', \'8\', \'6F822E\', 7),
			(\'colorList\', \'9\', \'97D0E8\', 8),
			(\'colorList\', \'10\', \'3030F8\', 9),
			(\'colorList\', \'11\', \'1E2C89\', 10),
			(\'colorList\', \'12\', \'8417C2\', 11),
			(\'colorList\', \'13\', \'B120A5\', 12),
			(\'colorList\', \'14\', \'FDC4DA\', 13),
			(\'colorList\', \'15\', \'FFFFFF\', 14),
			(\'colorList\', \'16\', \'C5C5C6\', 15),
			(\'colorList\', \'17\', \'8C8C8C\', 16),
			(\'colorList\', \'18\', \'191919\', 17)';

		if($db->query($strSQL1))
		{
			$gd_smart_result	= true;
		} else {
			$gd_smart_result	= false;
		}

		if($db->query($strSQL2))
		{
			$gd_smart_result	= true;
		} else {
			$gd_smart_result	= false;
		}
	}

	// gd_member_ai_up 
	// member m_no Auto_increment ������Ʈ �κ�
	$memberAIResult = true;
	if ($_POST['gd_member_ai_up'] == 'y') {
		$memberAIQuery = "ALTER TABLE `gd_member` AUTO_INCREMENT=" . $_POST['gd_member_ai_change'];
		if (!$db->query($memberAIQuery)) {
			$memberAIResult = false;
		}
	}

	// titleStyle ������Ʈ �κ�
	$arrayErrorTitleStyle	= array();
	$arrayTrueTitleStyle	= array();
	{
		$titleStyleId = $_POST['titleStyleId'];
		
		
		if (!empty($titleStyleId)) {
			foreach ($titleStyleId as $boardId) {
				$titleStyleUpdateQuery = "alter table gd_bd_" . $boardId . " add titleStyle varchar(50) default NULL;";
				
				if (!$db->query($titleStyleUpdateQuery)) {
					$arrayErrorTitleStyle[$boardId] = $titleStyleUpdateQuery;
				}
				else {
					$arrayTrueTitleStyle[$boardId] = $titleStyleUpdateQuery;
				}
			}
		}
	}
	
	$goodsReviewNoticeUpdateTF = false;
	if ($_POST['gd_goods_review_notice_update'] == 'y') {
		$goodsReviewNoticeUpdateQuery = "Update gd_goods_review Set notice = 0 Where notice =''";
		if ($db->query($goodsReviewNoticeUpdateQuery)) {
			$goodsReviewNoticeUpdateTF = true;
		}
	}
	
	$memberQnaNoticeUpdateTF = false;
	if ($_POST['gd_member_qna_notice_update'] == 'y') {
		$memberQnaNoticeUpdateQuery = "Update gd_member_qna Set notice = 0 Where notice =''";
		if ($db->query($memberQnaNoticeUpdateQuery)) {
			$memberQnaNoticeUpdateTF = true;
		}
	}

	// ��ǰ ���� ������Ʈ �κ�
	if ($_POST['gd_goods_stock'] == 'y') {
		$goodsStockUpdateTF = true;

		$goodsStockUpdateQuery = "UPDATE `gd_goods` a SET 
									a.totstock = (
										SELECT sum( b.stock )  
										FROM gd_goods_option b  
										WHERE a.goodsno = b.goodsno  
										GROUP BY b.goodsno
									)";
		if (!$db->query($goodsStockUpdateQuery)) {
			$goodsStockUpdateTF = false;
		}
	}

	// ��ǰ ��ü ���̺� ���� �� �ɼ� ��� ���� ������Ʈ
	if ($_POST['gd_option_upyn'] == 'y') {
		$arrayErrorOptionUpdate = array();
		$arrayTrueOptionUpdate = array();

		$goodsPriceUpdateQuery = "update gd_goods a set 
									goods_price = (select price from gd_goods_option b where b.goodsno = a.goodsno and link = 1 and go_is_deleted = '0'),
									goods_supply = (select supply from gd_goods_option b where b.goodsno = a.goodsno and link = 1 and go_is_deleted = '0'),
									goods_consumer = (select consumer from gd_goods_option b where b.goodsno = a.goodsno and link = 1 and go_is_deleted = '0'),
									goods_reserve = (select reserve from gd_goods_option b where b.goodsno = a.goodsno and link = 1 and go_is_deleted = '0');
								";
		if (!$db->query($goodsPriceUpdateQuery)) {
			$arrayErrorOptionUpdate['goodsPrice'] = $goodsPriceUpdateQuery;
		} 
		else {
			$arrayTrueOptionUpdate['goodsPrice'] = $goodsPriceUpdateQuery;
		}

		$goodsOptionUseUpdateQuery = "update gd_goods a set use_option = if((select count(sno) from gd_goods_option b where b.goodsno = a.goodsno and opt1 != '') >= 1, '1', '0');";
		if (!$db->query($goodsOptionUseUpdateQuery)) {
			$arrayErrorOptionUpdate['goodsOptionUse'] = $goodsOptionUseUpdateQuery;
		} 
		else {
			$arrayTrueOptionUpdate['goodsOptionUse'] = $goodsOptionUseUpdateQuery;
		}
	}

	// ��ǰ �󼼳��� �̹��� ��� ������Ʈ

	if ($_POST['gd_goods_longdesc'] == 'y') {
		$arrayErrorLongDescUpdate = array();
		$arrayTrueLongDescUpdate = array();

		$goodsLongDescUpdateQuery = "update gd_goods set longdesc = replace(longdesc, '" . $_POST['gd_goods_before1'] . "', '');";
		if (!$db->query($goodsLongDescUpdateQuery)) {
			$arrayErrorLongDescUpdate['gd_goods_before1'] = $goodsLongDescUpdateQuery;
		} 
		else {
			$arrayTrueLongDescUpdate['gd_goods_before1'] = $goodsLongDescUpdateQuery;
		}

		$goodsLongDescUpdateQuery = "update gd_goods set longdesc = replace(longdesc, '" . $_POST['gd_goods_before2'] . "', '');";

		if (!$db->query($goodsLongDescUpdateQuery)) {
			$arrayErrorLongDescUpdate['gd_goods_before2'] = $goodsLongDescUpdateQuery;
		} 
		else {
			$arrayTrueLongDescUpdate['gd_goods_before2'] = $goodsLongDescUpdateQuery;
		}
	}
?>

<html>
<head>
<title>DB Sync Result</title>
</head>
<style>
td {font-size:10pt}
textarea {border:1px solid #999999}
.box {border:1px solid #999999}
</style>
<body>

<table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse" bordercolor="#999999">
<tr>
	<td colspan="2" >&nbsp; <b>���� ���</b></td>
</tr>
<?php
	if(count($ar_querys)) {
		foreach($ar_querys as $key => $value) {
?>
<tr>
	<td width="100%">
		<?php if($value['done']) {?><font color="#0000CC"><?php } else {?><font color="#CC0000"><?php } ?>
		<pre><?php echo htmlspecialchars($value['sql'])?></pre>
		</font>
	</td>
</tr>
<?php
		}
		flush();
	}

	if($_POST['prn_settleprice'] == 'y' && $prn_settleprice_result) {
?>
<tr>
	<td width="100%">
		�ֹ��� ������Ʈ ����<br>
		ȸ�����űݾ� ������Ʈ ����
	</td>
</tr>
<?php
	}

	if($_POST['gd_smart'] == 'y' && $gd_smart_result) {
?>
<tr>
	<td width="100%">����Ʈ �˻� ������Ʈ ����</td>
</tr>
<?php
	}

	if (!empty($arrayErrorTitleStyle)) {
		foreach ($arrayErrorTitleStyle as $boardId => $titleStyleQuery) {
?>
<tr>
	<td width="100%">
		<font color="#CC0000">gd_bd_<?=$boardId?> titleStyle ������Ʈ ����<br>
		<?=$titleStyleQuery?></font>
	</td>
</tr>
<?php
		}
	}
?>
<?php
	if (!empty($arrayTrueTitleStyle)) {
		foreach ($arrayTrueTitleStyle as $boardId => $titleStyleQuery) {
?>
<tr>
	<td width="100%">
		<font color="#0000CC">gd_bd_<?=$boardId?> titleStyle ������Ʈ ����<br>
		<?=$titleStyleQuery?></font>
	</td>
</tr>
<?php
		}
	}

if ($_POST['gd_goods_review_notice_update'] == 'y') {
	if ($goodsReviewNoticeUpdateTF) {
?>
	<tr>
		<td width="100%">
			<font color="#0000CC">��ǰ �ı� notice ������Ʈ ����<br>
			<?=$goodsReviewNoticeUpdateQuery?></font>
		</td>
	</tr>
<?php
	}
	else {
?>
	<tr>
		<td width="100%">
			<font color="#CC0000">��ǰ �ı� notice ������Ʈ ����<br>
			<?=$goodsReviewNoticeUpdateQuery?></font>
			
		</td>
	</tr>
<?php
	}
}

if ($_POST['gd_member_qna_notice_update'] == 'y') {
	if ($memberQnaNoticeUpdateTF) {
?>
	<tr>
		<td width="100%">
			<font color="#0000CC">1:1���� notice ������Ʈ ����<br>
			<?=$memberQnaNoticeUpdateQuery?></font>
		</td>
	</tr>
<?php
	}
	else {
?>
	<tr>
		<td width="100%">
			<font color="#CC0000">1:1���� notice ������Ʈ ����<br>
			<?=$memberQnaNoticeUpdateQuery?></font>
		</td>
	</tr>
<?php
	}
}

	if ($_POST['gd_member_ai_up'] == 'y') {
?>
	<tr>
		<td width="100%">
			<?php
				if ($memberAIResult) {
			?>
			<font color="#0000CC">gd_member Auto_increment ������Ʈ ����<br>
			<?=$memberAIQuery?></font>
			<?php
				}
				else {
			?>
			<font color="#CC0000">gd_member Auto_increment ������Ʈ ����<br>
			<?=$memberAIQuery?></font>
			<?php
				}
			?>
		</td>
	</tr>
<?php
	}
?>
<?php
	if ($_POST['gd_goods_stock'] == 'y') {
?>
<tr>
	<td width="100%">
		<?=($goodsStockUpdateTF) ? '<font color="#0000CC">gd_goods �� ���� ������Ʈ ����<br>' . $goodsStockUpdateQuery . '</font>' : '<font color="#CC0000">gd_goods �� ���� ������Ʈ ����<br>' . $goodsStockUpdateQuery . '</font>'?>
	</td>
</tr>
<?php
	}
?>
<?php
	if ($_POST['gd_option_upyn'] == 'y' && !empty($arrayErrorOptionUpdate)) {
		foreach ($arrayErrorOptionUpdate as $optionKey => $optionQuery) {
?>
<tr>
	<td width="100%">
		<font color="#CC0000"><?=$optionKey?> ������Ʈ ����<br>
		<?=$optionQuery?></font>
	</td>
</tr>
<?php
		}
	}
?>
<?php
	if ($_POST['gd_option_upyn'] == 'y' && !empty($arrayTrueOptionUpdate)) {
		foreach ($arrayTrueOptionUpdate as $optionKey => $optionQuery) {
?>
<tr>
	<td width="100%">
		<font color="#0000CC"><?=$optionKey?> ������Ʈ ����<br>
		<?=$optionQuery?></font>
	</td>
</tr>
<?php
		}
	}
?>
<?php
	if ($_POST['gd_goods_longdesc'] == 'y' && !empty($arrayErrorLongDescUpdate)) {
		foreach ($arrayErrorLongDescUpdate as $longDescKey => $longDescQuery) {
?>
<tr>
	<td width="100%">
		<font color="#CC0000"><?=$longDescKey?> ������Ʈ ����<br>
		<?=$longDescQuery?></font>
	</td>
</tr>
<?php
		}
	}
?>
<?php
	if ($_POST['gd_goods_longdesc'] == 'y' && !empty($arrayTrueLongDescUpdate)) {
		foreach ($arrayTrueLongDescUpdate as $longDescKey => $longDescQuery) {
?>
<tr>
	<td width="100%">
		<font color="#0000CC"><?=$longDescKey?> ������Ʈ ����<br>
		<?=$longDescQuery?></font>
	</td>
</tr>
<?php
		}
	}
?>
</table>
<center><a href="?mode=insert">ó������</center>
</body>
</html>

<?php
}
/**
* Date = ���� �۾���(2015.11.04)
* ETC = autodb �������� ���� ���μ��� ����
* Developer = �ѿ���
*/
?>