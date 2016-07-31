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

//�˻�-���Ź�ȣ 
if ($_GET['phoneNumber']) {
	$where[] = "phoneNumber like '%" . $_GET['phoneNumber'] . "%'";
}

//�˻�- �߼۽��л��� 
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
			alert('���Ź�ȣ�� 8�� �̻��̾�� �մϴ�.');
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
	<font face="����" color="black"><strong>SMS</strong></font> �߼۽��й�ȣ ���<span>SMS �߼۴���� �߼۽��� �̷��� �ִ� ��ȣ�� ��ȸ�մϴ�. ��, SMS �߼۳������� �߼۰���� Ȯ���� ��ȣ�� ��ȸ�˴ϴ�.</span>
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
			<td>�߼۽��й�ȣ</td>
			<td><input type="text" class="line" name="phoneNumber" value="<?php echo $_GET['phoneNumber']; ?>" />&nbsp;<span class="guideFont">ex) 010-1111-1111</span></td>
		</tr>
		<tr>
			<td>�߼۽��л���</td>
			<td>
				<select name="failCode">
					<option value="" <?php echo $selected['failCode']['']; ?>> - ��ü - </option>
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
			<td class="failList-TextAlignL">�� <?php echo $total; ?>��, �˻� <?php echo $pg->recode['total']; ?>�� / <?php echo $pg->page['total']; ?> of <?php echo $pg->page['now']; ?> Pages</td>
			<td class="failList-TextAlignR">
				<select name="page_num" onchange="javascript:this.form.submit();">
					<option value="10" <?php echo $selected['page_num'][10]; ?>>10�� ���</option>
					<option value="20" <?php echo $selected['page_num'][20]; ?>>20�� ���</option>
					<option value="40" <?php echo $selected['page_num'][40]; ?>>40�� ���</option>
					<option value="60" <?php echo $selected['page_num'][60]; ?>>60�� ���</option>
					<option value="100" <?php echo $selected['page_num'][100]; ?>>100�� ���</option>
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
			<th>��ȣ</th>
			<th>�߼۽��й�ȣ</th>
			<th>�߼۽��л���</th>
			<th>�߼۳��� ����</th>
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