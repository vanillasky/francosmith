<?php
include "../lib.php";
$godotax = Core::loader('godotax');

$arTaxsno = (array)$_POST['chk'];

if(count($arTaxsno)==0) {
	exit;
}
$successCount=0;
foreach($arTaxsno as $eachSno) {
	if($godotax->sendGodoetax($eachSno)) {
		$successCount++;
	}
}

?>
<script>
alert("<?=count($arTaxsno)?>�� �� <?=$successCount?>���� �����߽��ϴ�");
parent.location.href=parent.location.href;
window.open('<?=$godotax->getLinkList()?>','_blank','toolbar=1,  status=1, menubar=1 , scrollbars=1, resizable=1');
</script>
