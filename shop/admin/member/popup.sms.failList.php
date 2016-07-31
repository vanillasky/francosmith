<?php
include '../_header.popup.php';
include '../../lib/page.class.php';
include '../../lib/sms_sendlist.class.php';

if($_POST['smsFailSnoList']){
	$_POST['smsFailSnoList'] = str_replace("|", "','", $_POST['smsFailSnoList']);
	$db->query("UPDATE " . GD_SMS_FAILLIST . " SET popupyn = 'n'");
	$db->query("UPDATE " . GD_SMS_FAILLIST . " SET popupyn = 'y' WHERE sno IN ('".$_POST['smsFailSnoList']."'); ");
	$_REQUEST['smsFailSnoList'] = '';
	unset($_REQUEST['smsFailSnoList']);
}

$sms_sendlist = new sms_sendlist();
$smsErrorCode = $sms_sendlist->errorCodeList();

if(!$_GET['page'])		$_GET['page'] = 1;
if(!$_GET['page_num'])	$_GET['page_num'] = 10;

$selected['page_num'][$_GET['page_num']] = " selected='selected'";
$selected['failCode'][$_GET['failCode']] = " selected='selected'";

$where[] = "popupyn = 'y'";

//검색-수신번호 
if ($_GET['phoneNumber']) {
	$where[] = "phoneNumber like '%" . $_GET['phoneNumber'] . "%'";
}

//검색- 발송실패사유 
if ($_GET['failCode']) {
	$where[] = "failCode = '" . $_GET['failCode'] . "'";
}

$pg = new Page($_GET['page'], $_GET['page_num']);
$pg->field = " phoneNumber, failCode ";
$pg->setQuery( GD_SMS_FAILLIST, $where, "sno desc");
$pg->exec();
$result = $db->query($pg->query);

list ($total) = $db->fetch("SELECT COUNT(sno) FROM " . GD_SMS_FAILLIST . ' WHERE ' . implode(' and ', $where));
?>
<script language="JavaScript" type="text/JavaScript">
function formCheck(f)
{
	var phone = f.phoneNumber;
	var phoneValue = phone.value;
	
	if(phoneValue){
		if(phoneValue.length < 8){
			alert('수신번호는 8자 이상이어야 합니다.');
			phone.focus();
			return false;
		}
	}
	
	return true;
}
</script>
<style type="text/css">
.button_top							{ margin-top: 10px; text-align: center; }
.button_top input					{ border: 0px; }
.guideFont							{ color: #627dce; font-weight: bold; font-size: 11px; }
#failList-search					{ margin-top: 10px; }
#failList							{ word-break: break-all; }
#failList th						{ color: #333333; background: #f6f6f6; font: 9pt tahoma; text-align: center; }
#failList tr td						{ color:#262626; font-family:Tahoma,Dotum; font-size:11px; height: 25px; text-align: center;}
.failList-total						{ padding: 20px 0px 5px 0px; }
.failList-TextAlignL				{ text-align: left; }
.failList-TextAlignR				{ text-align: right; }
.imgLink							{ border: 0px; cursor: pointer; }
</style>

<div class="title title_top">
	<font face="굴림" color="black"><strong>SMS</strong></font> 발송실패번호 목록<span>SMS 발송대상중 발송실패 이력이 있는 번호를 조회합니다. 단, SMS 발송내역에서 발송결과를 확인한 번호만 조회됩니다.</span>
</div>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td class="failList-contents">
		<form name="failList_SearchForm" id="failList_SearchForm" method="get" onsubmit="return formCheck(this);">
		<table width="100%" id="failList-search" class="tb">
		<colgroup>
			<col class="cellC" />
			<col class="cellL" />
		</colgroup>
		<tr>
			<td>발송실패번호</td>
			<td><input type="text" class="line" name="phoneNumber" value="<?php echo $_GET['phoneNumber']; ?>" />&nbsp;<span class="guideFont">ex) 010-1111-1111</span></td>
		</tr>
		<tr>
			<td>발송실패사유</td>
			<td>
				<select name="failCode">
					<option value="" <?php echo $selected['failCode']['']; ?>> - 전체 - </option>
					<?php foreach($smsErrorCode as $code => $value){ ?>
					<option value="<?php echo $code; ?>" <?php echo $selected['failCode'][$code]; ?>><?php echo $value; ?></option>
					<? } ?>
				<select>
			</td>
		</tr>
		</table>
		<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>

		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="failList-total">
		<tr>
			<td class="failList-TextAlignL">총 <?php echo $total; ?>개, 검색 <?php echo $pg->recode['total']; ?>개 / <?php echo $pg->page['total']; ?> of <?php echo $pg->page['now']; ?> Pages</td>
			<td class="failList-TextAlignR">
				<select name="page_num" onchange="javascript:this.form.submit();">
					<option value="10" <?php echo $selected['page_num'][10]; ?>>10개 출력</option>
					<option value="20" <?php echo $selected['page_num'][20]; ?>>20개 출력</option>
					<option value="40" <?php echo $selected['page_num'][40]; ?>>40개 출력</option>
					<option value="60" <?php echo $selected['page_num'][60]; ?>>60개 출력</option>
					<option value="100" <?php echo $selected['page_num'][100]; ?>>100개 출력</option>
				</select>
			</td>
		</tr>
		</table>
		</form>

		<table width="100%" cellpadding="0" cellspacing="0" border="0" id="failList" class="tb">
		<colgroup>
			<col width="10%" />
			<col width="20%" />
			<col width="35%" />
			<col width="15%" />
		</colgroup>
		<tr>
			<th>번호</th>
			<th>발송실패번호</th>
			<th>발송실패사유</th>
			<th>발송내역 보기</th>
		</tr>
		<?php 
		while ($data = $db->fetch($result, 1)){ 
			$failMsg = ' - ';
			if($data['failCode'] > 0){
				$failMsg = $smsErrorCode[$data['failCode']];
			}
		?>
		<tr>
			<td><?php echo $pg->idx--; ?></td>
			<td><?php echo $data['phoneNumber']; ?></td>
			<td><?php echo $failMsg; ?></td>
			<td><img src="../img/btn_sms_sendinfo.gif" class="imgLink" onclick="javascript:popup('./popup.sms.sendView.php?sms_phoneNumber=<?php echo $data['phoneNumber']; ?>', '700', '500');" /></td>
		</tr>
		<?php } ?>
		</table>

		<div class="pageNavi" align="center"><font class="ver8"><?php echo $pg->page['navi']; ?></div>
	</td>
</tr>
</table>

<script>
table_design_load();
</script>