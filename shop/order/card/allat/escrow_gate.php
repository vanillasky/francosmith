<? 

include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.$cfg[settlePg].php";
include "../../../conf/pg.escrow.php";

$ordno = $_GET[ordno];

if (!$escrow[id]) $escrow[id] = $pg[id];
$allat_settlekind	= array(
					'c'	=> 'CARD',
					'o'	=> 'ABANK',
					);


$query = "
select 
	deliverycomp,deliverycode,settlekind 
from 
	".GD_ORDER." a
	left join ".GD_LIST_DELIVERY." b on a.deliveryno = b.deliveryno
where
	a.ordno = '$ordno'
";
$data = $db->fetch($query);

?>

<script language=JavaScript src="https://tx.allatpay.com/common/allatpayX.js"></script>
<script language=javascript>
function ftn_escrowcheck(dfm) {
  var ret;
  ret = invisible_eschk(dfm);//Function ���ο��� submit�� �ϰ� �Ǿ�����.
  if( ret.substring(0,4)!="0000" && ret.substring(0,4)!="9999"){
    // ���� �ڵ� : 0001~9998 �� ������ ���ؼ� ������ ó���� ���ֽñ� �ٶ��ϴ�.
    alert(ret.substring(4,ret.length));     // Message ��������
  }
  if( ret.substring(0,4)=="9999" ){
    // ���� �ڵ� : 9999 �� ������ ���ؼ� ������ ó���� ���ֽñ� �ٶ��ϴ�.
    alert(ret.substring(8,ret.length));     // Message ��������
  }
}
</script>

<form name="fm"  method=POST action="./allat_escrowcheck.php"> 

<input type=hidden name=allat_enc_data value=""> 
<input type=hidden name=allat_opt_pin value="NOVIEW"> 
<input type=hidden name=allat_opt_mod value="WEB">
<input type=hidden name=allat_test_yn value="N">
<table class=tb cellpadding=4 cellspacing=0>
<col style="width:110px; text-align:center; background-color:#F6F6F6">
<col style="padding-left:10px">
<tr>
	<td>����ID</td>
	<td><?=$escrow[id]?></td>
</tr>
<tr>
	<td>�ֹ���ȣ</td>
	<td><?=$ordno?></td>
</tr>
<tr>
	<td>�ù��</td>
	<td><?=$data[deliverycomp]?></td>
</tr>
<tr>
	<td>������ȣ</td>
	<td><?=$data[deliverycode]?></td>
</tr>
<tr>
	<td>�������</td>
	<td><?=$allat_settlekind[$data[settlekind]]?></td>
</tr>
</table>

<input type=hidden name=allat_shop_id value="<?=$escrow[id]?>">
<input type=hidden name=allat_order_no value="<?=$ordno?>">
<input type=hidden name=allat_escrow_express_nm value="<?=$data[deliverycomp]?>">
<input type=hidden name=allat_escrow_send_no value="<?=$data[deliverycode]?>">
<input type=hidden name=allat_pay_type value="<?=$allat_settlekind[$data[settlekind]]?>">

</form>

<script>
ftn_escrowcheck(document.fm);
</script>