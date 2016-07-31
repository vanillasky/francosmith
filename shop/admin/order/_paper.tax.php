<?
	### 세금계산서 정보
	$query = "select * from ".GD_TAX." where step>0 and ordno='$ordno' order by sno desc";
	$tax_data = $db->fetch($query);

	if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
		$tax_data = validation::xssCleanArray($tax_data, array(
			validation::DEFAULT_KEY	=> 'text'
		));
	}

	$tax_data[busino] = substr($tax_data[busino], 0, 3) . '-' . substr($tax_data[busino], 3, 2) . '-' . substr($tax_data[busino], 5);
	$tax_data[issuedate] = explode("-", $tax_data[issuedate]);

	### 직인
	include_once dirname(__FILE__) . "/../../conf/config.pay.php";
	$sealpath = '/shop/data/skin/' . $cfg['tplSkin'] . '/img/common/' . $set[tax][seal];

	$classids = array( "cssblue", "cssred" ); //-- 공급받는자용, 공급자용
	$headuser = array( "cssblue"=>"공급받는자보관용", "cssred"=>"공급자보관용" );
	switch ($_GET[taxarea]){
		case 'blue':
			$classids = array( "cssblue" ); //-- 공급받는자용
			break;
		case 'red':
			$classids = array( "cssred" ); //-- 공급자용
			break;
	}

	if ($tax_data[step] == 3) $classids = array();

	//사업장소재지
	if (!$cfg['road_address']) {
		$address = $cfg['address'];
	} else {
		$address = $cfg['road_address'];
	}
?>
<style type = "text/css">
td, th { font-family: 돋움; font-size: 9pt; color: 333333;}

#cssblue { width: 604px; border: solid 2px #364F9E;  }
#cssblue strong { font:18pt 굴림; color:#364F9E; font-weight:bold; }
#cssblue table { border-collapse: collapse; }
#cssblue td, #cssblue table { border-color: #364F9E; border-style: solid; border-width: 0; }

#cssblue #head { border-width: 1px 1px 0 1px; }
#cssblue #box td { border-width: 1px; padding-top: 3px; }

#cssred { width: 604px; border: solid 2px #FF4633;  }
#cssred strong { font:18pt 굴림; color:#FF4633; font-weight:bold; }
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
<font color=#0074BA><strong><?=$ordno?></strong></font> 주문은 <font color=EA0095>전자세금계산서 발행을 요청한 상태입니다.</font><br>
전자세금계산서로 발행요청한 주문건은 일반세금계산서로 출력할 수 없습니다.
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
		<td width="50%" align="right"><strong>세 금 계 산 서</strong></td>
		<td width="50%" style="padding-left:6px">(<?=$headuser[$classid]?>)</td>
	</tr>
	</table>
	</td>
	<td width="60" style="border-right-width: 3px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td height="28" align="center">책&nbsp;번&nbsp;호</td>
	</tr>
	<tr>
		<td height="24" align="center">일련번호</td>
	</tr>
	</table>
	</td>
	<td width="150">
	<table width="100%" border="0" cellspacing="0" cellpadding="4">
	<tr height="26">
		<td width="50%" align="right" style="border-right-width: 1px; border-bottom-width: 1px;"> 권</td>
		<td width="50%" align="right" style="border-bottom-width: 1px;"> 호</td>
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
	<!-- 공급자 -->
	<td align="center" style="border-width: 3px 1px 0px 1px; padding-left: 2px; line-height: 36px;">공<br>급<br>자</td>
	<td valign="top" height="100%" style="border-width: 3px 1px 0 0; background: url(<?=$sealpath?>) no-repeat; background-position: 207px 0px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<col width=53><col width=100><col width=26>
	<tr height="38" align="center">
		<td style="border-width: 0 1px 1px 2px;">등록번호</td>
		<td colspan="3" style="border-width: 0 2px 1px 0; padding-left:6px;"><?=$cfg[compSerial]?></td>
	</tr>
	<tr height="38" align="center">
		<td style="border-width: 0 1px 3px 2px;">상&nbsp;&nbsp;&nbsp;&nbsp;호<br>(법인명) </td>
		<td style="padding:0 6px; border-bottom-width:3px;"><?=$cfg[compName]?></td>
		<td style="border-width: 0 1px 3px 1px;">성명</td>
		<td style="border-width: 0 2px 3px 0; padding-right:10px;"><?=$cfg[ceoName]?></td>
	</tr>
	<tr height="38" align="center">
		<td style="border-width: 0 1px 1px 0px;">사 업 장<br>소 재 지 </td>
		<td colspan="3" style="padding: 0 6px; border-bottom-width: 1px;" align=left><?=$address?></td>
	</tr>
	<tr height="38" align="center">
		<td style="border-right-width: 1px;">업&nbsp;&nbsp;&nbsp;&nbsp;태</td>
		<td style="padding:0 6px;"><?=$cfg[service]?></td>
		<td style="border-width: 0 1px; padding-left:4px">종<br>목 </td>
		<td style="padding: 0 6px;"><?=$cfg[item]?></td>
	</tr>
	</table>
	</td>
	<!-- 공급받는자 -->
	<td align="center" style="border-top-width: 3px; border-right-width: 1px; line-height: 22px; padding-left: 2px">공<br>급<br>받<br>는<br>자</td>
	<td valign="top" height="100%" style="border-top-width: 3px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<col width=53><col width=100><col width=26>
	<tr height="38" align="center">
		<td style="border-width: 0 1px 1px 2px;">등록번호</td>
		<td colspan="3" style="border-bottom-width: 1px; padding-left:6px;"><?=$tax_data[busino]?></td>
	</tr>
	<tr height="38" align="center">
		<td style="border-width: 0 1px 3px 2px;">상&nbsp;&nbsp;&nbsp;&nbsp;호<br>(법인명) </td>
		<td style="padding:0 6px; border-bottom-width:3px;"><?=$tax_data[company]?></td>
		<td style="border-width: 0 1px 3px 1px;">성명</td>
		<td style="border-bottom-width: 3px; padding-right:10px;"><?=$tax_data[name]?></td>
	</tr>
	<tr height="38" align="center">
		<td style="border-width: 0 1px 1px 0px;">사 업 장<br>소 재 지 </td>
		<td colspan="3" style="padding: 0 6px; border-bottom-width: 1px;" align=left><?=$tax_data[address]?></td>
	</tr>
	<tr height="38" align="center">
		<td style="border-right-width: 1px;">업&nbsp;&nbsp;&nbsp;&nbsp;태</td>
		<td style="padding:0 6px;"><?=$tax_data[service]?></td>
		<td style="border-width: 0 1px; padding-left:4px">종<br>목 </td>
		<td style="padding: 0 6px;"><?=$tax_data[item]?></td>
	</tr>
	</table>
	</td>
</tr>
</table>

<table id="box" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-top-width:3px; border-bottom-width:2px;">
<col width="34"><colgroup span="2" width="20"></colgroup><col width="34"><colgroup span="11"></colgroup><col width="1"><colgroup span="10"></colgroup><col width="64">
<tr align="center">
	<td colspan="3">작&nbsp;&nbsp;&nbsp;성</td>
	<td colspan="12">공&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;급&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;가&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;액</td>
	<td><img src="" width="1" height="1"></td>
	<td colspan="10" style="border-right-width:3px;">세&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;액</td>
	<td>비&nbsp;&nbsp;&nbsp;&nbsp;고</td>
</tr>
<tr align="center">
	<td>년</td>
	<td>월</td>
	<td>일</td>
	<td style="letter-spacing:-2">공란수</td>
	<td>백</td>
	<td>십</td>
	<td>억</td>
	<td>천</td>
	<td>백</td>
	<td>십</td>
	<td>만</td>
	<td>천</td>
	<td>백</td>
	<td>십</td>
	<td>일</td>
	<td><img src="" width="1" height="1"></td>
	<td>십</td>
	<td>억</td>
	<td>천</td>
	<td>백</td>
	<td>십</td>
	<td>만</td>
	<td>천</td>
	<td>백</td>
	<td>십</td>
	<td>일</td>
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

<!-- 주문list -->
<table id="box" width="100%" border="0" cellspacing="0" cellpadding="0">
<colgroup span="2" width="3%"></colgroup><col><colgroup span="2" width="9%"></colgroup><col width="12%"><col width="19%"><col width="13%"><col width="8%">
<tr height="24" align="center">
	<td>월</td>
	<td>일</td>
	<td>품&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;목</td>
	<td>규&nbsp;&nbsp;격</td>
	<td>수&nbsp;&nbsp;량</td>
	<td>단&nbsp;&nbsp;가</td>
	<td>공&nbsp;&nbsp;급&nbsp;&nbsp;가&nbsp;&nbsp;액</td>
	<td>세&nbsp;&nbsp;액</td>
	<td>비&nbsp;&nbsp;고</td>
</tr>
<tr height="25" align="center">
	<td><?=$tax_data[issuedate][1]?></td>
	<td><?=$tax_data[issuedate][2]?></td>
	<td style="padding: 0 6px; word-break:break-all"><?=$tax_data[goodsnm]?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td align="right" style="padding-right:6px">&nbsp;</td>
	<td align="right" style="padding-right:6px"><?=number_format($tax_data[supply])?>원</td>
	<td align="right" style="padding-right:6px"><?=number_format($tax_data[surtax])?>원</td>
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
	<td style="border-top-width: 0;">합 계 금 액</td>
	<td style="border-top-width: 0;">현&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;금</td>
	<td style="border-top-width: 0;">수&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;표</td>
	<td style="border-top-width: 0;">어&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;음</td>
	<td style="border-top-width: 0;">외상미수금</td>
	<td rowspan="3" style="border-top-width: 0;">위 금액을 영수 함</td>
</tr>
<tr height="25" align="center">
	<td><?=number_format($tax_data[price])?>원</td>
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