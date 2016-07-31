<?
include "../lib.php";
include "../../conf/config.php";

$ordno = $_POST[ordno];

/*
Array
(
    [ordno] => 1294981820963
    [deliveryno] => 100
    [deliverycode] => 123456-67867
)
*/

if($_POST['deliveryno']=='100') {
	echo '<script>alert(" 우체국 택배연동 서비스를 이용중입니다.\n\n개별 송장 등록이 불가능합니다.");</script>';

}
else {

	$query = "update ".GD_ORDER." set deliverycode='".$_POST['deliverycode']."',deliveryno='".$_POST['deliveryno']."' where ordno='".$_POST['ordno']."'";

	if ($db->query( $query )) {
		echo '<script>
		alert("송장번호 입력.");
		parent.location.reload();
		</script>';
	} else {
		echo '<script>alert("송장번호 성공.");</script>';
	}
}
?>

<script>
parent.closeLayer();
</script>