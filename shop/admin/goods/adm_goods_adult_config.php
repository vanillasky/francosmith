<?
$location = "상품 부가기능 관리 > 성인인증 상품 인증대상 설정";
include "../_header.php";

$config = Core::loader('config')->load('goods_adult_auth');
?>
<h2 class="title">성인인증 상품 인증대상 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=45');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

<form method="post" class="admin-form" name="form" action="indb_adm_goods_adult_config.php">
<table class="admin-form-table">
<tr>
	<th>인증대상 설정</th>
	<td>
		<label><input type="radio" name="allow_guest_auth" value="1" <?=$config['allow_guest_auth'] != '0' ? 'checked ' : ''?>/> 전체(비회원+회원)</label>
		<label><input type="radio" name="allow_guest_auth" value="0" <?=$config['allow_guest_auth'] == '0' ? 'checked ' : ''?>/> 회원전용(비회원제외)</label>

		<dl class="help">
			<dt>전체로 설정시</dt>
			<dd>비회원의 경우 성인인증상품에 접근시 마다 인증절차를 필요로 하기 때문에 서비스비용이 추가로 부과 됩니다. 이점 유의해 주세요.</dd>
			<dt>회원전용 으로 설정시</dt>
			<dd>쇼핑몰에 가입된 회원에 한하여 성인인증 절차가 진행됩니다. 비회원 일 경우 회원가입 완료시 성인인증을 할 수 있습니다.</dd>
			<dd>성인인증 상품 설정은 각 상품별 [ 상품등록/수정하기 > 상품추가관리설정 > 성인인증 ] 에서 하실 수 있습니다.</dd>
			<dd>성인인증 기능은 별도의 인증 서비스 신청완료 후 이용 가능합니다. <a href="../member/realname_info.php" target="_blank"><img src="../img/buttons/btn_confirmation.gif" /></a></dd>
		</dl>
	</td>
</tr>
</table>

<div class="button-container">
	<input type="image" src="../img/btn_save.gif" />
</div>
</form>

<? include "../_footer.php"; ?>
