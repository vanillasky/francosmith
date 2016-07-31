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

	$data		= readurl('http://gongji.godo.co.kr/autodb/test4.php');
	$ar_data	= unserialize($data);
	$ar_querys	= array();

	$ar_tables	= array();
	$result		= $db->query('show tables');

	while($row = $db->fetch($result))
	{
		$sub_result	= $db->query('show create table '.$row[0]);
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
			#게시판부분
		}
		else
		{
			$ar_tables[$key]['fields']	= array_map('replace_emptydefault',$ar_tables[$key]['fields']);
			$value['fields']			= array_map('replace_emptydefault',$value['fields']);

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

	// 스마트 검색 Insert 체크
	$strSQL	= 'SELECT themenm FROM gd_goods_smart_search WHERE themenm = \'기본검색\' AND basic = \'y\' AND price = \'y\' LIMIT 1';
	list($smartS)	= $db->fetch($strSQL);

	$strSQL	= 'SELECT groupcd FROM gd_code WHERE groupcd=\'colorList\' LIMIT 1';
	list($smartC)	= $db->fetch($strSQL);

	$chk_smart	= false;
	if (empty($smartS) && empty($smartC)) {
		$chk_smart	= true;
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
	<td colspan="2">&nbsp; <b>쿼리선택</b></td>
</tr>
<?php
	// 쿼리 종류
	$arrQueryMode['create']		= '테이블 추가';
	$arrQueryMode['f_insert']	= '필드 추가';
	$arrQueryMode['f_modify']	= '필드 수정';
	$arrQueryMode['f_drop']		= '필드 삭제';
	$arrQueryMode['keys']		= '인덱스(키)';

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
				<input type="radio" name="<?php echo $qKey?>[<?php echo $key?>][yn]" value="y" checked />추가 &nbsp;
				<input type="radio" name="<?php echo $qKey?>[<?php echo $key?>][yn]" value="n" />추가안함
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

	// 금액 재계산 (prn_settleprice)
	if ($chk_prn_settleprice === true)
	{
?>
<tr>
	<td width="110" nowrap> prn_settleprice</td>
	<td width="100%">
		<table width="100%" border="0" cellspacing="0" cellpadding="3">
		<tr>
			<td>
				<input type="radio" name="prn_settleprice" value="y" checked />재계산 &nbsp;
				<input type="radio" name="prn_settleprice" value="n" />재계산 안함
			</td>
		</tr>
		</table>
	</td>
</tr>
<?php
	}

	// 스마트 검색 Insert
	if ($chk_smart === true)
	{
?>
<tr>
	<td width="110" nowrap>스마트 검색 insert</td>
	<td width="100%">
		<table width="100%" border="0" cellspacing="0" cellpadding="3">
		<tr>
			<td>
				<input type="radio" name="gd_smart" value="y" checked />스마트 검색 관련 필드 등록 &nbsp;
				<input type="radio" name="gd_smart" value="n" />등록안함
			</td>
		</tr>
		</table>
	</td>
</tr>
<?php
	}
?>
</table>
<br>
<center>
	<input type="submit" value="쿼리실행">
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

		// 주문서 업데이트 - 최종결제금액 UPDATE
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

			if ($tcnt == $icnt) { //모두 주문취소일 경우
				$csettleprice	= $settleprice1 - $data1['enuri'] -$data1['emoney'] + $data1['delivery'];
			} else {
				$csettleprice	= $settleprice2 - $data1['enuri'] -$data1['emoney'] + $data1['delivery'];
			}

			$db->query('UPDATE gd_order SET prn_settleprice = \''.$csettleprice.'\' WHERE ordno=\''.$data1['ordno'].'\'') or die(mysql_error());
		}

		$db->query('UPDATE gd_member SET sum_sale=\'0\'') or die(mysql_error()); //구매금액 초기화

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
		// 스마트 검색 기본 테마 등록
		$strSQL1	= 'INSERT INTO `gd_goods_smart_search` SET `themenm` = \'기본검색\', `basic` = \'y\', `price` = \'y\', `regdt` = NOW()';

		// 스마트 검색 팔레트 색 지정
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
	<td colspan="2" >&nbsp; <b>쿼리 결과</b></td>
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
		주문서 업데이트 성공<br>
		회원구매금액 업데이트 성공
	</td>
</td>
<?php
	}

	if($_POST['gd_smart'] == 'y' && $gd_smart_result) {
?>
<tr>
	<td width="100%">스마트 검색 업데이트 성공</td>
</td>
<?php
	}
?>
</table>
<center><a href="?mode=insert">처음으로</center>
</body>
</html>

<?php
}
?>