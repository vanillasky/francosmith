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
	echo '<script>alert(" ��ü�� �ù迬�� ���񽺸� �̿����Դϴ�.\n\n���� ���� ����� �Ұ����մϴ�.");</script>';

}
else {

	$query = "update ".GD_ORDER." set deliverycode='".$_POST['deliverycode']."',deliveryno='".$_POST['deliveryno']."' where ordno='".$_POST['ordno']."'";

	if ($db->query( $query )) {
		echo '<script>
		alert("�����ȣ �Է�.");
		parent.location.reload();
		</script>';
	} else {
		echo '<script>alert("�����ȣ ����.");</script>';
	}
}
?>

<script>
parent.closeLayer();
</script>