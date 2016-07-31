<?
	$location = "사입처관리 > 사입처 리스트";
	include "../_header.php";
	@include "../../conf/config.purchase.php";
	if($purchaseSet['usePurchase'] != "Y") msg("[사입처 관리 사용 설정] > [상품 사입처 연동]을 설정 하세요.", -1);

	$mode		= ($_REQUEST['mode'])		? $_REQUEST['mode']			: "pchs_reg";
	$returnUrl	= ($_REQUEST['returnUrl'])	? $_REQUEST['returnUrl']	: $_SERVER['PHP_SELF'];

	if($mode == "pchs_mod" && !$_GET['pchsno']) $mode = "pchs_reg";
	if($mode == "pchs_mod" && $_GET['pchsno']) {
		$data = $db->fetch("SELECT * FROM ".GD_PURCHASE." WHERE pchsno = '".$_GET['pchsno']."'");
		$comno = explode("-", $data['comno']);
		$zipcode = explode("-", $data['zipcode']);
		$phone1 = explode("-", $data['phone1']);
		$phone2 = explode("-", $data['phone2']);

		if($data['comcd'] == "0000") msg("미등록은 정보를 수정하실 수 없습니다.", -1);
	}

	$qstr = "skey=".$_GET['skey']."&sword=".$_GET['sword']."&sort=".$_GET['sort']."&page_num=".$_GET['page_num']."&page=".$_GET['page'];
	$listUrl = "purchase_list.php?".$qstr;
?>

<script language="javascript">
function chkFormPurchase(f) {
	if(!f.comnm.value) {
		alert("사입업체명을 입력해 주세요.");
		f.comnm.focus();
		return false;
	}

	return true;
}
</script>

<div><form name="frmPurchase" method="post" action="indb.purchase.php" onsubmit="return chkFormPurchase(this);">
<input type="hidden" name="mode" value="<?=$mode?>" />
<input type="hidden" name="pchsno" value="<?=$_GET['pchsno']?>" />
<input type="hidden" name="returnUrl" value="<?=$returnUrl?>" />
<input type="hidden" name="qstr" value="<?=$qstr?>" />

<div class="title title_top">사입처 <?=($mode == "pchs_reg") ? "등록" : "수정"?><span>사입처 정보를 등록하고 수정 합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=29')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>

<table class="tb">
<col class="cellC" /><col style="padding-left:10px; width:250;" />
<col class="cellC" /><col style="padding-left:10px; width:170;" />
<col class="cellC" /><col style="padding-left:10px;" />
<tr>
	<td>업체명</td>
	<td><input type="text" name="comnm" value="<?=$data['comnm']?>" required label="업체명" class="line" /></td>
	<td>업체코드</td>
	<td colspan="3"><?=($mode == "pchs_reg") ? "등록시 자동생성 됩니다." : $data['comcd']?></td>
</tr>
<tr>
	<td>대표자명</td>
	<td><input type="text" name="ceonm" value="<?=$data['ceonm']?>" required label="대표자명" class="line" /></td>
	<td>사업자번호</td>
	<td colspan="3">
		<input type="text" name="comno[]" value="<?=$comno[0]?>" size="3" maxlength="3" label="사업자번호" class="line" /> -
		<input type="text" name="comno[]" value="<?=$comno[1]?>" size="2" maxlength="2" label="사업자번호" class="line" /> -
		<input type="text" name="comno[]" value="<?=$comno[2]?>" size="5" maxlength="5" label="사업자번호" class="line" />
	</td>
</tr>
<tr>
	<td>주소</td>
	<td colspan="5">

		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<input type="text" name="zipcode[]" id="zipcode0" size="3" readonly value="<?=$zipcode[0]?>" class="line" /> -
				<input type="text" name="zipcode[]" id="zipcode1" size="3" readonly value="<?=$zipcode[1]?>" class="line" />
				<a href="javascript:popup('../../proc/popup_address.php',500,432)"><img src="../img/btn_zipcode.gif" align="absmiddle" /></a>
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" name="address" id="address" value="<?=$data['address']?>" readonly size="50" label="주소" class="line" />
				<input type="text" name="address_sub" id="address_sub" value="<?=$data['address_sub']?>" size="30" onkeyup="SameAddressSub(this)" oninput="SameAddressSub(this)" label="상세주소" class="line" /><br/>
				<input type="hidden" name="road_address" id="road_address" value="<?=$data['road_address']?>">
				<div style="padding:5px 5px 0 5px;font:12px dotum;color:#999;float:left;" id="div_road_address"><?=$data['road_address']?></div>
				<div style="padding:5px 0 0 1px;font:12px dotum;color:#999;" id="div_road_address_sub"><? if ($data['road_address']) { echo $data['address_sub']; } ?></div>
			</td>
		</tr>
		</table>

	</td>
</tr>
<tr>
	<td>계좌번호</td>
	<td><input type="text" name="accountno" value="<?=$data['accountno']?>" size="30" label="계좌번호" class="line" /></td>
	<td>은행명</td>
	<td><input type="text" name="banknm" value="<?=$data['banknm']?>" required label="은행명" class="line" /></td>
	<td>예금주</td>
	<td><input type="text" name="accountnm" value="<?=$data['accountnm']?>" required label="예금주" class="line" /></td>
</tr>
<tr>
	<td>휴대전화</td>
	<td>
		<input type="text" name="phone1[]" value="<?=$phone1[0]?>" size="4" maxlength="4" label="연락처1" class="line" /> -
		<input type="text" name="phone1[]" value="<?=$phone1[1]?>" size="4" maxlength="4" label="연락처1" class="line" /> -
		<input type="text" name="phone1[]" value="<?=$phone1[2]?>" size="4" maxlength="4" label="연락처1" class="line" />
	</td>
	<td>유선전화</td>
	<td colspan="3">
		<input type="text" name="phone2[]" value="<?=$phone2[0]?>" size="4" maxlength="4" label="연락처2" class="line" /> -
		<input type="text" name="phone2[]" value="<?=$phone2[1]?>" size="4" maxlength="4" label="연락처2" class="line" /> -
		<input type="text" name="phone2[]" value="<?=$phone2[2]?>" size="4" maxlength="4" label="연락처2" class="line" />
	</td>
</tr>
<tr>
	<td>메모</td>
	<td colspan="5"><textarea name="memo" style="width:100%;height:80px" class="tline"><?=$data['memo']?></textarea></td>
</tr>
<? if($data['regdt']) { ?>
<tr height="35">
	<td>등록일</td>
	<td colspan="5"><font class="ver8"><?=$data['regdt']?></font></td>
</tr>
<? } ?>
</table>

<div class="button">
<input type="image" src="../img/btn_<?=($mode == "pchs_reg") ? "regist" : "modify"?>.gif" />
<a href='<?=$listUrl?>'><img src="../img/btn_list.gif" /></a>
</div>
</form></div>

<? include "../_footer.php"; ?>