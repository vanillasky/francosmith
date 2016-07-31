<?php
include "../lib.php";
@include './popup.newAreaDeliveryLib.func.php';

switch($_POST['type']){

	//�������
	case 'excel':
		setlocale(LC_ALL, "ko_KR.eucKR"); //fgetcsv �Լ� ����, lang ������ ���� ���� ����

		// CSV ���� �ҷ�����
		$csv = Core::helper('CSV', $_FILES['newAreaCsvFile'][tmp_name]);
		$header = $csv->getHeader();

		$rowInsertCnt = 0;
		$rowUpdateCnt = 0;
		$totalCnt	  = -1;
		$failCnt	  = 0;
		foreach ($csv as $newArea) {
				$totalCnt++;
				$checkType = 'insert';

				//�ּ��� ��� ����
				$limitMsg = newAreaLimitCheck();
				if($limitMsg){
					msg($limitMsg);
					popupReload();
					exit;
				}

				//��ۺ�üũ
				$limitPayMsg = newAreaPayLimitCheck($newArea[1]);
				if($limitPayMsg){
					msg($limitPayMsg);
					popupReload();
					exit;
				}
					
				if(!$newArea[0] || $newArea[1] == '') continue;
				
				//������ üũ
				newAreaOnlyNumCheck($newArea[1]);
				
				//�Էµ� �迭 �� ����
				list($areaSido, $areaGugun, $areaEtc) = newAreaProcess($newArea[0]);
				
				//���� row ���� ����
				$newAreaExistNo = newAreaExistCheck($areaSido, $areaGugun, $areaEtc);
			
				if($newAreaExistNo){
					$checkType = 'update';
					$query = " UPDATE " . GD_AREA_DELIVERY . " SET areaPay = '" . trim($newArea[1]) . "' WHERE areaNo = '" . $newAreaExistNo . "' ";
				} else {
					$query = "
						INSERT INTO " . GD_AREA_DELIVERY . " SET
							areaSido	= '" . trim($areaSido) . "',
							areaGugun	= '" . trim($areaGugun) . "',
							areaEtc		= '" . trim($areaEtc) . "',
							areaPay		= '" . trim($newArea[1]) . "',
							areaRegdt	= now()
					";
				}

				if($db->query($query)){
					if($checkType=='insert') $rowInsertCnt++;
					else $rowUpdateCnt++;
				}
		}
		$failCnt = $totalCnt - ($rowInsertCnt + $rowUpdateCnt);
		$msg = '��� : ' . $rowInsertCnt . '��\n';
		$msg .= '���� : ' . $rowUpdateCnt . '��\n';
		$msg .= '���� : ' . $failCnt . '��\n\n';

		msg($msg . '�� ' . $totalCnt . '�� ó���� �Ϸ�Ǿ����ϴ�.');
		echo "<script>parent.parent.newAreaLayerReload();parent.parent.newAreaLayerClose();</script>";
		exit;
	break;

	//���̱׷��̼�
	case 'migration' :
		$errorMsg = '������ ��ȯ�� �����Ͽ����ϴ�.';
		if(!is_file('../../conf/config.pay.php')){
			msg($errorMsg, -1);
			exit;
		}
		include "../../conf/config.pay.php";

		$checkSido		= array('������'=>'����', '��⵵'=>'���', '��󳲵�'=>'�泲', '���ϵ�'=>'���', '���ֱ�����'=>'����', '�뱸������'=>'�뱸', '����������'=>'����', '�λ걤����'=>'�λ�', '����Ư����'=>'����', '����Ư����ġ��'=>'����', '��걤����'=>'���', '��õ������'=>'��õ', '���󳲵�'=>'����', '����ϵ�'=>'����', '����Ư����ġ��'=>'����', '��û����'=>'�泲', '��û�ϵ�'=>'���');

		switch($_POST['migrationType']){
			//���������� ����
			case 'area' :
				if(!is_file('../../conf/area.delivery.php') || !function_exists('funcExplode')){
					msg($errorMsg, -1);
					exit;
				}

				include "../../conf/area.delivery.php";
				$_areaDelivery['area'] = array_map("funcExplode", explode("|", $r_area[deliveryArea]));
				$_areaDelivery['pay']  = explode("|", $set['delivery']['overAdd']);

				foreach($_areaDelivery['area'] as $key => $keyArray){
					foreach($keyArray as $value){
						//�ּ��� ��� ����
						$limitMsg = newAreaLimitCheck();
						if($limitMsg) {
							msg($limitMsg);	
							echo "<script>parent.parent.location.reload();</script>";
							exit;
						}

						//���� row ���� ����
						$newAreaExistNo = newAreaExistCheck($value['sido'], $value['gugun'], $value['etc']);
						if($newAreaExistNo) continue;

						$db->query("
							INSERT INTO " . GD_AREA_DELIVERY . " SET
								areaSido	= '" . trim($value['sido']) . "',
								areaGugun	= '" . trim($value['gugun']) . "',
								areaEtc		= '" . trim($value['etc']) . "',
								areaPay		= '" . trim($_areaDelivery['pay'][$key]) . "',
								areaRegdt	= now()						
						");
					}
				}
			break;

			//�����ȣ�� ����
			case 'zipcode' :
				if($set['delivery']['areaZip1']){
					$arr1		= explode('|',trim($set['delivery']['areaZip1']));
					$arr2		= explode('|',trim($set['delivery']['areaZip2']));
					$zipcodePay = explode('|',trim($set['delivery']['overAddZip']));

					foreach($arr1 as $key => $value){
						$zipcodeKeyword1 = substr($value,0,3) . '-' . substr($value,3,3);
						$zipcodeKeyword2 = substr($arr2[$key],0,3) . '-' . substr($arr2[$key],3,3);
						$address = Core::loader('Zipcode')->getBetween( array(
							'keyword' => $zipcodeKeyword1 . ' ' . $zipcodeKeyword2, 
							'where' => 'zipcode',
							'page_size' => 60000
						) );

						while($row = $address->fetch()){
							if($row['no']){
								//�ּ��� ��� ����
								$limitMsg = newAreaLimitCheck();
								if($limitMsg) {
									msg($limitMsg);	
									echo "<script>parent.parent.location.reload();</script>";
									exit;
								}

								if($row['sido']){
									list($areaSido) = array_keys(preg_grep('/' . $row['sido'] . '/', $checkSido));
								}

								$row['dong'] = $row['dong'] . ' ' . $row['bunji'];

								//���� row ���� ����
								$newAreaExistNo = newAreaExistCheck($areaSido, $row['gugun'], $row['dong']);
								if($newAreaExistNo) continue;

								$db->query("
									INSERT INTO " . GD_AREA_DELIVERY . " SET
										areaSido	= '" . trim($areaSido) . "',
										areaGugun	= '" . trim($row['gugun']) . "',
										areaEtc		= '" . trim($row['dong']) . "',
										areaPay		= '" . trim($zipcodePay[$key]) . "',
										areaRegdt	= now()						
								");
							}
						}
					}
				}

			break;
		}

		msg('�����ͺ�ȯ�� �����Ͽ����ϴ�.');
		echo "<script>parent.parent.addNewAreaDelivery('normal');</script>";
		exit;
	break;

	//�⺻�� ����
	case 'setting':
		$db->query("TRUNCATE TABLE " . GD_AREA_DELIVERY . " ");

		setlocale(LC_ALL, "ko_KR.eucKR"); //fgetcsv �Լ� ����, lang ������ ���� ���� ����

		// CSV ���� �ҷ�����
		$csv = Core::helper('CSV', '../data/csv_newAreaDelivery.csv');
		$header = $csv->getHeader();

		foreach ($csv as $newArea) {
			if(!$newArea[0]) continue;

			//�ּ��� ��� ����
			$limitMsg = newAreaLimitCheck();
			if($limitMsg){ 
				msg($limitMsg, -1);
				exit;
			}

			//�Էµ� �迭 �� ����
			list($areaSido, $areaGugun, $areaEtc) = newAreaProcess($newArea[0]);
			
			//���� row ���� ����
			$newAreaExistNo = newAreaExistCheck($areaSido, $areaGugun, $areaEtc);

			if(!$newAreaExistNo){
				$query = "
					INSERT INTO " . GD_AREA_DELIVERY . " SET
						areaSido	= '" . trim($areaSido) . "',
						areaGugun	= '" . trim($areaGugun) . "',
						areaEtc		= '" . trim($areaEtc) . "',
						areaRegdt	= now()
				";

				$db->query($query);
			}
		}

		msg('���������� ����Ǿ����ϴ�.', './popup.newAreaDelivery.php');
	break;

	//����
	case 'delete':
		foreach($_POST['newAreaChk'] as $areaNo){
			$db->query(" DELETE FROM " . GD_AREA_DELIVERY . " WHERE areaNo = '$areaNo' ");
		}

		echo "
			<script>
			alert('���������� �����Ǿ����ϴ�.');
			parent.document.getElementById('newAreaIframe').src='./popup.newAreaDelivery.php?" . $_POST['returnUrl'] . "';
			</script>
		";
	break;

	//����
	default: case 'modify':
		foreach($_POST['newAreaName'] as $key => $areaName){

			if($_POST['newAreaPay'][$key] == ""){ 
				msg("�߰���ۺ� �Է��Ͽ� �ּ���.", -1);
				break;
			}

			//�Էµ� �迭 �� ����
			list($areaSido, $areaGugun, $areaEtc) = newAreaProcess($areaName);
			
			if($_POST['newAreaNo'][$key]){
				$query = " UPDATE " . GD_AREA_DELIVERY . " SET areaPay = '" . trim($_POST['newAreaPay'][$key]) . "' WHERE areaNo = '".$_POST['newAreaNo'][$key]."' ";
			}
			if(!$db->query($query)){
				msg('���������� ó������ ���Ͽ����ϴ�. \n�ٽ��ѹ� �õ��Ͽ� �ּ���', -1);	
			}
		}
		
		msg('���������� ����Ǿ����ϴ�.', './popup.newAreaDelivery.php');
	break;
}
exit;
?>