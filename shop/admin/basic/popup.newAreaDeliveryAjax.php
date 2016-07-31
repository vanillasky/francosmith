<?php
header("Content-Type:text/html; charset=utf-8");
include '../lib.php';

switch($_POST['mode']){
	case 'write' : 
			try {
				if(!is_file('./popup.newAreaDeliveryAPI.class.php')){
					throw new Exception('�ּ����߰��� �����Ͽ����ϴ�.');
				}
				include './popup.newAreaDeliveryLib.func.php';

				//����� ��� ����
				$limitMsg = newAreaLimitCheck();
				if($limitMsg) throw new Exception($limitMsg);
				if($_POST['newAreaPay'] == '') throw new Exception('�߰���ۺ� �Է��Ͽ� �ּ���.');
				if(!$_POST['newAreaSido']) throw new Exception('�õ��� �Է��Ͽ� �ּ���.');
				if(!$_POST['newAreaGugun'] && newAreaIconv($_POST['newAreaSido']) != '����Ư����ġ��') throw new Exception('������ �Է��Ͽ� �ּ���.');

				$limitPayMsg = newAreaPayLimitCheck($_POST['newAreaPay']);
				if($limitPayMsg){
					throw new Exception($limitPayMsg);
				}

				//���� row ���� ����
				$newAreaExist = newAreaExistCheck(newAreaIconv($_POST['newAreaSido']), newAreaIconv($_POST['newAreaGugun']), newAreaIconv($_POST['newAreaEtc']));
				if($newAreaExist == true) throw new Exception('�̹� ��ϵǾ� �ִ� �ּ��� �Դϴ�.');

				$query = "
					INSERT INTO " . GD_AREA_DELIVERY . " SET
						areaSido	= '" . trim(newAreaIconv($_POST['newAreaSido'])) . "',
						areaGugun	= '" . trim(newAreaIconv($_POST['newAreaGugun'])) . "',
						areaEtc		= '" . trim(newAreaIconv($_POST['newAreaEtc'])) . "',
						areaPay		= '" . trim($_POST['newAreaPay']) . "',
						areaRegdt	= now()
				";
				if(!$db->query($query)){ 
					throw new Exception('DB error');
				}
				$returnText = '���������� ����Ǿ����ϴ�.';
			} catch (Exception $e) {
				echo 'error-'.$e->getMessage(); exit;
			}
	break;

	case 'getAddressApi' :
		try {
			$data	= array();
			$result = array();
			
			if(!is_file('./popup.newAreaDeliveryAPI.class.php')){
				throw new Exception('����� �����Ͽ����ϴ�.');
			}
			include './popup.newAreaDeliveryAPI.class.php';
			$arrayData = Core::loader('newAreaDeliveryAPI')->getCurlData($_POST['listType'], $_POST['newAreaSido']);
			$dataTotalCnt = $arrayData['godojuso']['data']['total'];
			$data = $arrayData['godojuso']['data']['item'];
			$result = $arrayData['godojuso']['result'];
			if($result['code']!='000'){
				throw new Exception($result['msg']);
			}

			$returnText = '<select name="' . $_POST['listType'] . '" id="' . $_POST['listType'] . '"><option value="">�������ּ���</option>';
			switch($_POST['listType']){
				case 'newAreaSido' : 
					foreach ($data as $data) {
						$newAreaSidoValue = $data['sido_code'] . '|' . $data['sido_name'];
						$returnText .= '<option value="' . $newAreaSidoValue . '">' . $data['sido_name'] . '</option>';
					}
				break;

				case 'newAreaGugun' : 
					foreach ($data as $data) $returnText .= '<option value="' . $data['sigungu_name'] . '">' . $data['sigungu_name'] . '</option>';
				break;
			}
			$returnText .= '</select>';
		} catch (Exception $e) {
			msg($e->getMessage()); exit;
		}
	break;
}

echo $returnText;
exit;
?>