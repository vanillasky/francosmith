<?

include "../_header.php";

### ����üũ
if ( empty($cfg['reviewWriteAuth']) && !isset($sess) ) msg($msg='ȸ�������Դϴ�.',$code='close'); // ȸ������ & �α�����

### �����Ҵ�
$mode		= $_GET[mode];
$sno		= $_GET[sno];

### ȸ�� �ڽ��� ������ ��ǰ ���
$query = "
SELECT
	DISTINCT TG.goodsnm, TG.goodsno
FROM ".GD_ORDER." AS O
INNER JOIN ".GD_ORDER_ITEM." AS OI
ON O.ordno = OI.ordno
INNER JOIN ".GD_TODAYSHOP_GOODS_MERGED." AS TG
ON OI.goodsno = TG.goodsno
WHERE O.m_no = '".$sess['m_no']."'
";
$rs = $db->query($query);
$goodslist = array();
while ($row = $db->fetch($rs,1)) {
	$goodslist[] = $row;
}


### ȸ������
if($mode != 'mod_review' && $sess['m_no']){
	list($data['name'],$data['nickname']) = $db-> fetch("select name,nickname from ".GD_MEMBER." where m_no='".$sess['m_no']."' limit 1");
	if($data['nickname'])$data['name'] = $data['nickname'];
} //end if

### ��ǰ ����
if ( $mode == 'mod_review'){
	$query = "select b.m_no, b.m_id, a.subject, a.contents, a.point, a.name, a.goodsno, a.attach from ".GD_TODAYSHOP_GOODS_REVIEW." a left join ".GD_MEMBER." b on a.m_no=b.m_no where a.sno='$sno'";
	$data = $db->fetch($query,1);

	$data['point'] = array( $data['point'] => 'checked' );

}
else {

	if ($mode == 'reply_review') {
		$query = "select goodsno from ".GD_TODAYSHOP_GOODS_REVIEW." where sno='$sno'";
		$_tmp = $db->fetch($query,1);
		$data[goodsno] = $_tmp[goodsno];
	}

	$data['m_id'] = $sess['m_id'];

}

### ���ø� ���
$tpl->print_('tpl');

?>