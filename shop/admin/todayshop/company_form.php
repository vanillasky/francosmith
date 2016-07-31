<?
//$hiddenLeft = 1;
$location = "투데이샵 > 공급업체등록";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";
$todayShop = &load_class('todayshop', 'todayshop');

if (!$todayShop->auth()) {
	msg(' 서비스 신청안내는 고도몰 고객센터로 문의해주시기 바랍니다.', -1);
}


// 변수 받기 및 기본값 설정
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$sno = isset($_GET['sno']) ? $_GET['sno'] : '';


// 업체정보 가져오기
$query = "SELECT * FROM ".GD_TODAYSHOP_COMPANY." WHERE cp_sno=".$sno;

// 데이터가 있다.
if ( $sno != '' && ($data = $db->fetch($query)) !== NULL) {	// 연산자 순위에 의해 sno 값 유무를 먼저 체크하게 됨.
	$mode = 'modify';
}
else {
	$mode = 'register';
}

?>

<!-- -->
<form name="frmCompany" method="post" action="./indb.company.php" target="_self" onSubmit="return chkForm(this);">
<input type="hidden" name="returnUrl" value="<?=$_SERVER['REQUEST_URI']?>">
<input type="hidden" name="mode" value="<?=$mode?>">
<input type="hidden" name="sno" value="<?=($mode == 'modify') ? $sno : ''?>">

<!-- 기본정보 -->
	<div class=title style="margin-top:0px">기본정보<span>*는 필수 입력 정보입니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=12')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
	<table class=tb>
	<colgroup><col class=cellC width="120"><col class=cellL width="50%"><col class=cellC width="120"><col class=cellL width="50%"></colgroup>
	<tr>
		<td nowrap>업체명*</td>
		<td <?=($mode != 'modify') ? 'colspan="3"' : '' ?>><input type="text" name="cp_name" style="width:200px" value="<?=$data['cp_name']?>" required label="업체명" class="line"></td>
		<? if ($mode == 'modify') { ?>
		<td nowrap>등록일</td>
		<td><?=$data['regdt']?></td>
		<? }?>
	</tr>
	<tr>
		<td nowrap>대표자명</td>
		<td colspan=3><input type="text" name="cp_ceo" style="width:200px" value="<?=$data['cp_ceo']?>" label="대표자명" class="line"></td>
	</tr>
	<tr>
		<td nowrap>업체형태</td>
		<td><input type="text" name="cp_type" style="width:200px" value="<?=$data['cp_type']?>" label="업체형태" class="line"></td>
		<td nowrap>사업자번호</td>
		<td><input type="text" name="cp_bizno" style="width:150px" value="<?=$data['cp_bizno']?>" class="line"></td>
	</tr>
	<tr>
		<? $_arPhone_prefix = array('02','051','053','032','062','042','052','031','033','043','041','063','061','054','055','064','070','080'); ?>
		<td nowrap>전화번호*</td>
		<td>
			<input type="text" name="cp_phone" style="width:150px" value="<?=$data['cp_phone']?>" required label="전화번호" class="line">
		</td>

		<td nowrap>팩스번호</td>
		<td><input type="text" name="cp_fax" style="width:150px" value="<?=$data['cp_fax']?>" label="팩스번호" class="line"></td>
	</tr>
	<tr>
		<td nowrap>주소</td>
		<td colspan="3">
		<? $_post = explode("-",$data['cp_address_post']) ?>
		<input type="text" name="zipcode[]" style="width:35px" value="<?=array_shift($_post)?>" class="line" label="우편번호">
		-
		<input type="text" name="zipcode[]" style="width:35px" value="<?=array_shift($_post)?>" class="line" label="우편번호">

		<a href="javascript:popup('../proc/popup_zipcode.php?form=opener.document.frmCompany',400,500)"><img src="../img/btn_zipcode.gif" align=absmiddle></a>

		<input type="text" name="address" style="width:100%" value="<?=$data['cp_address']?>" label="주소" class="line">
		</td>
	</tr>
	<tr>
		<td nowrap>홈페이지</td>
		<td colspan="3"><input type="text" name="cp_www" style="width:100%" value="<?=$data['cp_www']?>" label="홈페이지" class="line"></td>
	</tr>
	</table>

<!-- 담당자 정보 -->
	<div class=title style="margin-top:0px">담당자 정보</div>

	<table class=tb>
	<colgroup><col class=cellC width="120"><col class=cellL width="50%"><col class=cellC width="120"><col class=cellL width="50%"></colgroup>
	<tr>
		<td nowrap>담당자명*</td>
		<td colspan="3"><input type="text" name="cp_man" style="width:200px" value="<?=$data['cp_man']?>" required label="담당자명" class="line"></td>
	</tr>
	<tr>
		<td nowrap>전화번호*</td>
		<td><input type="text" name="cp_man_phone" style="width:200px" value="<?=$data['cp_man_phone']?>" required label="전화번호" class="line"></td>
		<td nowrap>휴대폰*</td>
		<td><input type="text" name="cp_man_mobile" style="width:200px" value="<?=$data['cp_man_mobile']?>" required class="line"></td>
	</tr>
	<tr>
		<td nowrap>이메일*</td>
		<td colspan="3"><input type="text" name="cp_man_email" style="width:380px" value="<?=$data['cp_man_email']?>" required label="이메일" class="line"></td>
	</tr>
	</table>

<!-- 정산정보 -->
	<div class=title style="margin-top:0px">정산정보</div>
	<table class=tb>
	<colgroup><col class=cellC width="120"><col class=cellL width=""></colgroup>
	<tr>
		<td nowrap>수수료</td>
		<td><input type="text" name="cp_calc_rate" style="width:30px" value="<?=$data['cp_calc_rate']?>" label="수수료" class="line"> %</td>
	</tr>
	<tr>
		<td nowrap>정산일</td>
		<td><input type="text" name="cp_calc_day" style="width:30px" value="<?=$data['cp_calc_day']?>" label="정산일" class="line"></td>
	</tr>
	<tr>
		<td nowrap>은행명*</td>
		<td><input type="text" name="cp_calc_account_bank" style="width:200px" value="<?=$data['cp_calc_account_bank']?>" required label="은행명" class="line"></td>
	</tr>
	<tr>
		<td nowrap>계좌번호*</td>
		<td><input type="text" name="cp_calc_account_no" style="width:200px" value="<?=$data['cp_calc_account_no']?>" required label="계좌번호" class="line"></td>
	</tr>
	<tr>
		<td nowrap>예금주*</td>
		<td><input type="text" name="cp_calc_account_owner" style="width:200px" value="<?=$data['cp_calc_account_owner']?>" required label="예금주" class="line"></td>
	</tr>
	</table>

	<div style="border-bottom:3px #efefef solid;padding-top:30px"></div>

	<div class=button>
		<input type=image src="../img/btn_<?=$mode?>.gif">
		<?=$btn_list?>
		<?if($_GET['tgsno']){?>&nbsp;<a href="../../todayshop/today_goods.php?tgsno=<?=$_GET['tgsno']?>" target="_blank"><img src="../img/btn_goods_view.gif"></a><?}?>
	</div>
</form>
<!-- -->

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>
	<div>상품을 공급하는 업체에 대한 정보를 등록하고 관리합니다.</div>
	<div>업체의 기본정보와 상품 주문내역관련 업무를 담당할 담당자 정보를 입력합니다.</div>
	<div>정산정보는 정산시 필요한 정보를 확인 할 수 있도록 등록하는 정보이며, 업체별 정산기능은 지원하지 않습니다.</div>
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>