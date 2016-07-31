<?

### ������ (Noteurl_Link_PHP)
### ���ϰ�������� ó���մϴ�.

include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.dacom.php";

// PG���� ������ üũ �� ��ȿ�� üũ
if (forge_order_check($_POST['oid'],$_POST['amount']) === false) {
	msg('�ֹ� ������ ���� ������ ���� �ʽ��ϴ�. �ٽ� ���� �ٶ��ϴ�.','../../order_fail.php?ordno='.$_POST['oid'],'parent');
	exit();
}

// Ncash ���� ���� API
include "../../../lib/naverNcash.class.php";
$naverNcash = new naverNcash();
if($naverNcash->useyn=='Y')
{
	if($_POST['paytype']==='SC0040') $ncashResult = $naverNcash->payment_approval($_POST['oid'], false);
	else $ncashResult = $naverNcash->payment_approval($_POST['oid'], true);
	if($ncashResult===false)
	{
		msg('���̹� ���ϸ��� ��뿡 �����Ͽ����ϴ�.','../../order_fail.php?ordno='.$_POST['oid'],'parent');
		exit();
	}
}

foreach ( $_POST as $k => $v ) $_POST[$k] = trim( $v );
extract($_POST);


$ordno = $oid;


### �����α� ����
/******************************************************************************
//������
transaction					# �ŷ���ȣ
mid							# �������̵�
oid							# �ֹ���ȣ
amount						# �ݾ�
respcode					# �����ڵ� "0000" �Ǵ� "C000" �̸� ���� �ܴ̿� ����
respmsg						# ����޼���

//�ſ�ī��
authdate					# �������� (yyyyMMDDHHMMSS)
authnumber					# ���ι�ȣ
cardtype					# ī�� Ÿ��
cardname					# ī�� ����

//������ü
accountNum					# ���¹�ȣ
userName					# ���¼����� �̸�
bankcode					# �����ڵ�
pid							# ���¼����� �ֹε�Ϲ�ȣ
respDate					# �������� (yyyyMMDDHHMMSS


//�ڵ���
respDate					# �������� (yyyyMMDDHHMMSS)
email						# �޴��������� �Է��� �����ּ�(��������뺸)
telCo						# �̵���Ż� (1:SKT, 2:KTF, 3:LGT)
telNo1						# �޴�����ȣ1
telNo2						# �޴�����ȣ2
telNo3						# �޴�����ȣ3
*******************************************************************************/

### item check stock
include "../../../lib/cardCancel.class.php";
$cancel = new cardCancel();
if(!$cancel->chk_item_stock($ordno))$respcode="OUTOFSTOCK";

if( !strcmp($respcode,"0000") || !strcmp($respcode,"C000") || !strcmp($respcode,"S007") ){		// ���� ����

	### �������� (�ֹ�Ȯ�θ���/�Ա�Ȯ�θ���)
	$pre = $db->fetch("select * from ".GD_ORDER." where ordno='$ordno'");
	$step = $pre['step'];

	if ($step == 0 && $cfg["mailyn_0"] == "y"){
		$pre['str_settlekind'] = $r_settlekind[ $pre['settlekind'] ];
		if ($pre['settlekind'] == 'v') $pre['str_settlekind'] .= ' ('. $pre['vAccount'] .')';
		$pre['zipcode'] = ($pre['zonecode']) ? $pre['zonecode'] : $pre['zipcode'];

		$tCart = (object) $tCart;
		$query = "
		select a.*, b.img_s img from
			".GD_ORDER_ITEM." a
			left join ".GD_GOODS." b on a.goodsno=b.goodsno
		where
			a.ordno = '$ordno'
		";
		$res = $db->query($query);
		while ($item=$db->fetch($res)){
			if ($item['opt1']) $item['opt'] = array($item['opt1'], $item['opt2']);
			if ($item['addopt']){
				$tmp1 = explode("^", $item['addopt']);
				$item['addopt'] = array();
				foreach ($tmp1 as $v){
					$tmp2 = explode(":", $v);
					$item['addopt'][] = array('optnm' => $tmp2[0], 'opt' => $tmp2[1]);
				}
			}
			$tCart->item[] = $item;
			$goodsprice += $item['price'] * $item['ea'];
		}
		$tCart->goodsprice = $goodsprice;
		$tCart->delivery = $pre['delivery'];
		$tCart->totalprice = $goodsprice + $pre['delivery'];

		include_once "../../../lib/automail.class.php";
		$automail = new automail();
		$automail->_set(0,$pre['email'],$cfg);
		$automail->_assign($pre);
		$automail->_assign('cart',$tCart);
		$automail->_send();
	}
	else if ($step == 1 && $cfg["mailyn_1"] == "y"){
		sendMailCase($pre['email'],1,$pre);
	}

	### SMS���� (�ֹ�Ȯ��SMS/�Ա�Ȯ��SMS)
	if ($step == 1 || $step == 0){
		$GLOBALS['dataSms'] = $pre;
		sendSmsCase(($step == 0 ? 'order' : 'incash'), $pre['mobileOrder']);
	}

	go("../../order_end.php?ordno=$ordno&card_nm=$cardname","parent");

} else {							// ���� ����

	if ($respcode == "OUTOFSTOCK") {
		$cancel->cancel_db_proc($ordno);
	}
	else {
		$db->query("update ".GD_ORDER." set step2='54' where ordno='".$ordno."'");
		$db->query("update ".GD_ORDER_ITEM." set istep='54' where ordno='".$ordno."'");
	}

	// Ncash ���� ���� ��� API ȣ��
	if($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($ordno);

	go("../../order_fail.php?ordno=$ordno","parent");

}

?>