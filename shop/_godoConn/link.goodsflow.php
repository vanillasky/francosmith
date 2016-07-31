<?
include("../lib/library.php");

if (get_magic_quotes_gpc()) stripslashes_all($_POST);

$godo = $config->load('godo');

if (md5($godo['sno']) != $_SERVER['HTTP_ENAMOO']) exit;

switch ($_SERVER['HTTP_ACTION']) {
	case 'GF_CONFIG':

		$MallID		= isset($_POST['MallID']) ? $_POST['MallID'] : '';
		$status		= isset($_POST['status']) ? $_POST['status'] : '';

		if (empty($MallID) || empty($status)) exit;

		$config->save('goodsflow',array('MallID'=>$MallID,'status'=>$status));
		exit('OK');
		break;

	case 'GF_TRACKING_RESULT' :
		$raw_post = file_get_contents("php://input");
		$datas = unserialize(base64_decode($raw_post));

		$failed = array();

		foreach ($datas as $UniqueCd => $data) {

			// ���� ������ ó�� ������ ����
			$data = array_pop($data);

			if (($gf = $db->fetch("SELECT * FROM ".GD_GOODSFLOW." WHERE UniqueCD = '".$UniqueCd."' AND status = 'print_invoice' ",1)) == false) {
				$failed[] = $UniqueCd;
				continue;
			}

			// �ֹ� ���� ������Ʈ�� ��ǰ��, �ֹ��� ������

			$query = "
				SELECT

					DISTINCT O.ordno

				FROM ".GD_GOODSFLOW." AS GF

				INNER JOIN ".GD_GOODSFLOW_ORDER_MAP." AS OD
				ON GF.sno = OD.goodsflow_sno

				INNER JOIN ".GD_ORDER." AS O
				ON OD.ordno = O.ordno

				WHERE GF.UniqueCd = '".$UniqueCd."'
				";

			$rs = $db->query($query);
			while ($row = $db->fetch($rs,1)) {

				// �ֹ��ܰ� ó��..
				switch ($data['DlvStatCode']) {
					case '30' :	// ��ȭ(=����� ����)
						$_step = 3;
						break;

					case '70' :	// ��޿Ϸ�
						$_step = 4;
						break;

					case 'ER' :	// ���� (������ ���� �̰����� ���޵��� �ʰ�, �߰輭������ �ɷ����ϴ�)
					default:
						continue;
						break;
				}

				### �����Ȳ�� ó��
				ctlStep($row['ordno'],$_step,'stock');
				setStock($row['ordno']);
				set_prn_settleprice($row['ordno']);

			}

		}

		if (sizeof($failed) > 0) {
			exit(base64_encode(serialize($failed)));
		}
		else {
			exit('OK');
		}
		break;

}
?>