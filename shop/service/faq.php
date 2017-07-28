<?

include "../_header.php";
include "../lib/page.class.php";

//�����±׻�����
if (class_exists('validation')) {
	$validation = new validation();
	if (method_exists($validation, 'check_digit')) {
		if ($_GET['ssno'] != null && $validation->check_digit($_GET['ssno']) === false) {
			$_GET['ssno'] = '';
		}
		if ($_GET['sitemcd'] != null && $validation->check_digit($_GET['sitemcd']) === false) {
			$_GET['sitemcd'] = '';
		}
	}
	if (method_exists($validation, 'xssCleanArray')) {
		$_GET = $validation->xssCleanArray($_GET, array(
				Validation::DEFAULT_KEY => 'text',
				'faq_sword' => 'html',
				'sword' => 'html'
		));
	}
}

$codeitem = codeitem('faq');

# �����ڵ� ����
$summary_search = array();
$summary_search[] = "/__shopname__/is";			# ���θ��̸�
$summary_search[] = "/__shopdomain__/is";		# ���θ��ּ�
$summary_search[] = "/__shopcpaddr__/is";		# ������ּ�
$summary_search[] = "/__shopcoprnum__/is";		# ����ڵ�Ϲ�ȣ
$summary_search[] = "/__shopcpmallceo__/is";	# ���θ� ��ǥ
$summary_search[] = "/__shopcpmanager__/is";	# ��������������
$summary_search[] = "/__shoptel__/is";			# ���θ� ��ȭ
$summary_search[] = "/__shopfax__/is";			# ���θ� �ѽ�
$summary_search[] = "/__shopmail__/is";			# ���θ� �̸���

$summary_replace = array();
$summary_replace[] = $cfg["shopName"];			# ���θ��̸�
$summary_replace[] = $cfg["shopUrl"];			# ���θ��ּ�
$summary_replace[] = $cfg["address"];			# ������ּ�
$summary_replace[] = $cfg["compSerial"];		# ����ڵ�Ϲ�ȣ
$summary_replace[] = $cfg["ceoName"];			# ���θ� ��ǥ
$summary_replace[] = $cfg["adminName"];			# ��������������
$summary_replace[] = $cfg["compPhone"];			# ���θ� ��ȭ
$summary_replace[] = $cfg["compFax"];			# ���θ� �ѽ�
$summary_replace[] = $cfg["adminEmail"];		# ���θ� �̸���

$sword_flag = isset( $_GET['sword'] ) || isset( $_GET['faq_sword'] );
if ( isset( $_GET[ssno] ) == false && isset( $_GET[sitemcd] ) == false && $sword_flag == false ){
	$_GET['sitemcd'] = array_shift( array_keys( $codeitem ) ); // �з� �⺻��
}


### FAQ
$pg = new Page($_GET[page],1000);
$pg->field = "sno, itemcd, question, descant, answer";
$db_table = "".GD_FAQ."";

if ($_GET[ssno]){
	$where[] = "sno='$_GET[ssno]'";
}

if ($_GET[sitemcd]){
	$where[] = "itemcd='$_GET[sitemcd]'";
}

if ($_GET[sword]){
	$where[] = "concat(question, descant, answer) like '%$_GET[sword]%'";
}
else if ($_GET[faq_sword]){
	$where[] = "concat(question, descant, answer) like '%$_GET[faq_sword]%'";
}

$pg->setQuery($db_table,$where,$sort='sort');
$pg->exec();

$res = $db->query($pg->query);
while ($data=$db->fetch($res)){

	$data['idx'] = $pg->idx--;

	$data['itemcd'] = $codeitem[ $data['itemcd'] ];

	if ( blocktag_exists( $data[descant] ) == false ){
		$data[descant] = nl2br($data[descant]);
	}

	$data[descant] = preg_replace( $summary_search, $summary_replace, $data[descant] );

	if ( blocktag_exists( $data[answer] ) == false ){
		$data[answer] = nl2br($data[answer]);
	}

	$data[answer] = preg_replace( $summary_search, $summary_replace, $data[answer] );

	$loop[] = $data;
}

$tpl->assign( 'pg', $pg );

### ���ø� ���
$tpl->print_('tpl');

?>