<?php
$location = "기본관리 > 마일리지(적립금) 설정";
include "../_header.php";

$commonInformation = Core::config('goods_common_information');
?>
<script type="text/javascript" src="../../lib/meditor/mini_editor.js"></script>
<h2 class="title">상품 공통정보/이용안내<span>상품정보제공고시 에 따른 상품 거래정보 내용 설정</span></h2>

<form name="admin-form" method="post" action="indb_adm_common_information.php">
<table class="admin-form-table">
<tr>
	<th>공급관련 정보</th>
	<td>
		<div class="field-wrapper IF_use_separate_supply_info_IS_1">
		<textarea style="width:100%;height:400px" name="separate_supply_info" type="editor"><?=$commonInformation['separate_supply_info']?></textarea>
		</div>
	</td>
</tr>
<tr>
	<th>청약철회 및 계약해제</th>
	<td>
		<div class="field-wrapper IF_use_separate_cancel_info_IS_1">
		<textarea style="width:100%;height:400px" name="separate_cancel_info" type="editor"><?=$commonInformation['separate_cancel_info']?></textarea>
		</div>
	</td>
</tr>
<tr>
	<th>교환/반품/보증 조건과 절차</th>
	<td>
		<div class="field-wrapper IF_use_separate_claim_info_IS_1">
		<textarea style="width:100%;height:400px" name="separate_claim_info" type="editor"><?=$commonInformation['separate_claim_info']?></textarea>
		</div>
	</td>
</tr>
<tr>
	<th>분쟁처리 사항</th>
	<td>
		<div class="field-wrapper IF_use_separate_trouble_info_IS_1">
		<textarea style="width:100%;height:400px" name="separate_trouble_info" type="editor"><?=$commonInformation['separate_trouble_info']?></textarea>
		</div>
	</td>
</tr>
<tr>
	<th>거래약관</th>
	<td>
		<div class="field-wrapper IF_use_separate_service_info_IS_1">
		<textarea style="width:100%;height:400px" name="separate_service_info" type="editor"><?=$commonInformation['separate_service_info']?></textarea>
		</div>
	</td>
</tr>
</table>

<div class="button">
	<input type="image" src="../img/btn_save.gif">
</div>

</form>

<script type="text/javascript">
mini_editor("../../lib/meditor/");
</script>

<? include "../_footer.php"; ?>
