<?php
include "../lib.php";
@include './popup.newAreaDeliveryLib.func.php';

switch($_POST['type']){

	//엑셀등록
	case 'excel':
		setlocale(LC_ALL, "ko_KR.eucKR"); //fgetcsv 함수 사용시, lang 설정에 따른 오류 방지

		// CSV 파일 불러오기
		$csv = Core::helper('CSV', $_FILES['newAreaCsvFile'][tmp_name]);
		$header = $csv->getHeader();

		$rowInsertCnt = 0;
		$rowUpdateCnt = 0;
		$totalCnt	  = -1;
		$failCnt	  = 0;
		foreach ($csv as $newArea) {
				$totalCnt++;
				$checkType = 'insert';

				//주소지 등록 제한
				$limitMsg = newAreaLimitCheck();
				if($limitMsg){
					msg($limitMsg);
					popupReload();
					exit;
				}

				//배송비체크
				$limitPayMsg = newAreaPayLimitCheck($newArea[1]);
				if($limitPayMsg){
					msg($limitPayMsg);
					popupReload();
					exit;
				}
					
				if(!$newArea[0] || $newArea[1] == '') continue;
				
				//숫자형 체크
				newAreaOnlyNumCheck($newArea[1]);
				
				//입력될 배열 값 가공
				list($areaSido, $areaGugun, $areaEtc) = newAreaProcess($newArea[0]);
				
				//기존 row 존재 여부
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
		$msg = '등록 : ' . $rowInsertCnt . '건\n';
		$msg .= '수정 : ' . $rowUpdateCnt . '건\n';
		$msg .= '실패 : ' . $failCnt . '건\n\n';

		msg($msg . '총 ' . $totalCnt . '건 처리가 완료되었습니다.');
		echo "<script>parent.parent.newAreaLayerReload();parent.parent.newAreaLayerClose();</script>";
		exit;
	break;

	//마이그레이션
	case 'migration' :
		$errorMsg = '데이터 변환을 실패하였습니다.';
		if(!is_file('../../conf/config.pay.php')){
			msg($errorMsg, -1);
			exit;
		}
		include "../../conf/config.pay.php";

		$checkSido		= array('강원도'=>'강원', '경기도'=>'경기', '경상남도'=>'경남', '경상북도'=>'경북', '광주광역시'=>'광주', '대구광역시'=>'대구', '대전광역시'=>'대전', '부산광역시'=>'부산', '서울특별시'=>'서울', '세종특별자치시'=>'세종', '울산광역시'=>'울산', '인천광역시'=>'인천', '전라남도'=>'전남', '전라북도'=>'전북', '제주특별자치도'=>'제주', '충청남도'=>'충남', '충청북도'=>'충북');

		switch($_POST['migrationType']){
			//지역명으로 설정
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
						//주소지 등록 제한
						$limitMsg = newAreaLimitCheck();
						if($limitMsg) {
							msg($limitMsg);	
							echo "<script>parent.parent.location.reload();</script>";
							exit;
						}

						//기존 row 존재 여부
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

			//우편번호로 설정
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
								//주소지 등록 제한
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

								//기존 row 존재 여부
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

		msg('데이터변환을 성공하였습니다.');
		echo "<script>parent.parent.addNewAreaDelivery('normal');</script>";
		exit;
	break;

	//기본값 셋팅
	case 'setting':
		$db->query("TRUNCATE TABLE " . GD_AREA_DELIVERY . " ");

		setlocale(LC_ALL, "ko_KR.eucKR"); //fgetcsv 함수 사용시, lang 설정에 따른 오류 방지

		// CSV 파일 불러오기
		$csv = Core::helper('CSV', '../data/csv_newAreaDelivery.csv');
		$header = $csv->getHeader();

		foreach ($csv as $newArea) {
			if(!$newArea[0]) continue;

			//주소지 등록 제한
			$limitMsg = newAreaLimitCheck();
			if($limitMsg){ 
				msg($limitMsg, -1);
				exit;
			}

			//입력될 배열 값 가공
			list($areaSido, $areaGugun, $areaEtc) = newAreaProcess($newArea[0]);
			
			//기존 row 존재 여부
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

		msg('정상적으로 적용되었습니다.', './popup.newAreaDelivery.php');
	break;

	//삭제
	case 'delete':
		foreach($_POST['newAreaChk'] as $areaNo){
			$db->query(" DELETE FROM " . GD_AREA_DELIVERY . " WHERE areaNo = '$areaNo' ");
		}

		echo "
			<script>
			alert('정상적으로 삭제되었습니다.');
			parent.document.getElementById('newAreaIframe').src='./popup.newAreaDelivery.php?" . $_POST['returnUrl'] . "';
			</script>
		";
	break;

	//수정
	default: case 'modify':
		foreach($_POST['newAreaName'] as $key => $areaName){

			if($_POST['newAreaPay'][$key] == ""){ 
				msg("추가배송비를 입력하여 주세요.", -1);
				break;
			}

			//입력될 배열 값 가공
			list($areaSido, $areaGugun, $areaEtc) = newAreaProcess($areaName);
			
			if($_POST['newAreaNo'][$key]){
				$query = " UPDATE " . GD_AREA_DELIVERY . " SET areaPay = '" . trim($_POST['newAreaPay'][$key]) . "' WHERE areaNo = '".$_POST['newAreaNo'][$key]."' ";
			}
			if(!$db->query($query)){
				msg('정상적으로 처리하지 못하였습니다. \n다시한번 시도하여 주세요', -1);	
			}
		}
		
		msg('정상적으로 저장되었습니다.', './popup.newAreaDelivery.php');
	break;
}
exit;
?>