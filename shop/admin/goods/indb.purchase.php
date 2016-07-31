<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
require_once("../../lib/upload.lib.php");
require_once("../../lib/load.class.php");

$qfile = new qfile();
$upload = new upload_file;

$mode	= ($_POST['mode'])	? trim($_POST['mode'])	: $_GET['mode'];
$qstr	= ($_POST['qstr'])	? trim($_POST['qstr'])	: "";
$page	= ($_POST['page'])	? trim($_POST['page'])	: 1;
$query	= ($_POST['query'])	? trim($_POST['query'])	: "";

switch ($mode) {

	// 사입처 관리 사용 설정
	case "pchs_set" :
		if($_POST['usePurchase'] == "Y") {
			if(!$cfg['compName']) $cfg['compName'] = $_SERVER['SERVER_NAME'];
			if(!$cfg['shopName']) $cfg['shopName'] = $_SERVER['SERVER_NAME'];
			list($unregCnt) = $db->fetch("SELECT COUNT(comcd) FROM ".GD_PURCHASE." WHERE comcd = '0000'");
			if(!$unregCnt) $db->query("INSERT INTO ".GD_PURCHASE." SET comnm = '미등록', comcd = '0000', regdt = NOW(), ordgrade = '50'");
			if(!$unregCnt) $db->query("INSERT INTO ".GD_PURCHASE." SET comnm = '".$cfg['compName']."', comcd = '0001', ceonm = '".$cfg['ceoName']."', comno = '".$cfg['compSerial']."', phone1 = '".$cfg['compPhone']."', memo = '[".$cfg['shopName']."] 운영 회사', regdt = NOW()");
		}

		$qfile->open("../../conf/config.purchase.php");
		$qfile->write("<?\n");
		$qfile->write("\$purchaseSet = array(\n");
		$qfile->write("'usePurchase' => '".$_POST['usePurchase']."',\n");
		$qfile->write("'soldoutAlarm' => '".$_POST['soldoutAlarm']."',\n");
		$qfile->write("'popYn' => '".(($_POST['popYn'] == "1") ? "1" : "0")."',\n");
		$qfile->write("'popStock' => '".$_POST['popStock']."',\n");
		$qfile->write("'smsYn' => '".(($_POST['smsYn'] == "1") ? "1" : "0")."',\n");
		$qfile->write("'smsStock' => '".$_POST['smsStock']."',\n");
		$qfile->write("'cp1' => '".$_POST['cp1']."',\n");
		$qfile->write("'cp2' => '".$_POST['cp2']."',\n");
		$qfile->write("'cp3' => '".$_POST['cp3']."',\n");
		$qfile->write(");\n");
		$qfile->write("?>");
		$qfile->close();
		msg("설정이 저장되었습니다.");
		break;

	// 사입처 등록
	case "pchs_reg" :
		$comnm			= ($_POST['comnm'])			? trim($_POST['comnm'])				: "";
		$ceonm			= ($_POST['ceonm'])			? trim($_POST['ceonm'])				: "";
		$comno			= ($_POST['comno'])			? implode("-",$_POST['comno'])		: "";
		$zipcode		= ($_POST['zipcode'])		? implode("-",$_POST['zipcode'])	: "";
		$address		= ($_POST['address'])		? trim($_POST['address'])			: "";
		$road_address	= ($_POST['road_address'])	? trim($_POST['road_address'])		: "";
		$address_sub	= ($_POST['address_sub'])	? trim($_POST['address_sub'])		: "";
		$accountno		= ($_POST['accountno'])		? trim($_POST['accountno'])			: "";
		$banknm			= ($_POST['banknm'])		? trim($_POST['banknm'])			: "";
		$accountnm		= ($_POST['accountnm'])		? trim($_POST['accountnm'])			: "";
		$phone1			= ($_POST['phone1'])		? implode("-",$_POST['phone1'])		: "";
		$phone2			= ($_POST['phone2'])		? implode("-",$_POST['phone2'])		: "";
		$memo			= ($_POST['memo'])			? trim($_POST['memo'])				: "";

		list($lastCompanyCode) = $db->fetch("SELECT comcd FROM ".GD_PURCHASE." ORDER BY comcd DESC LIMIT 1");
		$comcd = ($lastCompanyCode * 1) + 1;

		$sql = "INSERT INTO ".GD_PURCHASE." SET
			comnm		= '$comnm',
			comcd		= '$comcd',
			ceonm		= '$ceonm',
			comno		= '$comno',
			zipcode		= '$zipcode',
			address		= '$address',
			road_address= '$road_address',
			address_sub	= '$address_sub',
			accountno	= '$accountno',
			banknm		= '$banknm',
			accountnm	= '$accountnm',
			phone1		= '$phone1',
			phone2		= '$phone2',
			memo		= '$memo',
			regdt		= NOW()";
		$db->query($sql);
		$_POST['returnUrl'] .= "?mode=pchs_mod&pchsno=".$db->lastID()."&".$qstr;
		msg("사입처가 등록되었습니다.");
		break;

	// 사입처 수정
	case "pchs_mod" :
		$pchsno			= ($_POST['pchsno'])		? trim($_POST['pchsno'])			: "";
		$comnm			= ($_POST['comnm'])			? trim($_POST['comnm'])				: "";
		$ceonm			= ($_POST['ceonm'])			? trim($_POST['ceonm'])				: "";
		$comno			= ($_POST['comno'])			? implode("-",$_POST['comno'])		: "";
		$zipcode		= ($_POST['zipcode'])		? implode("-",$_POST['zipcode'])	: "";
		$address		= ($_POST['address'])		? trim($_POST['address'])			: "";
		$road_address	= ($_POST['road_address'])	? trim($_POST['road_address'])		: "";
		$address_sub	= ($_POST['address_sub'])	? trim($_POST['address_sub'])		: "";
		$accountno		= ($_POST['accountno'])		? trim($_POST['accountno'])			: "";
		$banknm			= ($_POST['banknm'])		? trim($_POST['banknm'])			: "";
		$accountnm		= ($_POST['accountnm'])		? trim($_POST['accountnm'])			: "";
		$phone1			= ($_POST['phone1'])		? implode("-",$_POST['phone1'])		: "";
		$phone2			= ($_POST['phone2'])		? implode("-",$_POST['phone2'])		: "";
		$memo			= ($_POST['memo'])			? trim($_POST['memo'])				: "";

		$sql = "UPDATE ".GD_PURCHASE." SET
			comnm		= '$comnm',
			ceonm		= '$ceonm',
			comno		= '$comno',
			zipcode		= '$zipcode',
			address		= '$address',
			road_address= '$road_address',
			address_sub	= '$address_sub',
			accountno	= '$accountno',
			banknm		= '$banknm',
			accountnm	= '$accountnm',
			phone1		= '$phone1',
			phone2		= '$phone2',
			memo		= '$memo'
			WHERE pchsno = '$pchsno'
		";
		$db->query($sql);
		$_POST['returnUrl'] .= "?mode=pchs_mod&pchsno=".$pchsno."&".$qstr;
		msg("해당 사입처가 수정되었습니다.");
		break;

	// 사입처 삭제
	case "pchs_del" :
		$chk			= $_POST['chk'];

		for($i = 0, $imax = count($chk); $i < $imax; $i++) {
			$db->query("DELETE FROM ".GD_PURCHASE." WHERE pchsno = '".$chk[$i]."'");
		}

		$_POST['returnUrl'] = $_SERVER['HTTP_REFERER']."?".$qstr;
		msg("해당 사입처가 삭제되었습니다.");
		break;

	// 사입처 이력등록
	case "pchs_manager" :

		$pchsno		= isset($_POST['pchsno'])	? $_POST['pchsno']		: '';
		$sno		= isset($_POST['sno'])		? $_POST['sno']			: '';
		$pgno		= isset($_POST['pgno'])		? $_POST['pgno']		: '';
		$pchsdt		= isset($_POST['pchsdt'])	? $_POST['pchsdt']		: '';
		$p_price	= isset($_POST['p_price'])	? $_POST['p_price']		: '';
		$p_stock	= isset($_POST['p_stock'])	? $_POST['p_stock']		: '';
		$page		= isset($_POST['page'])		? $_POST['page']		: '';
		$modifyCount = 0;

		if (is_array($pchsno)) {
			foreach($pchsno as $k => $v) {
				if($v && $p_price[$k] && $p_stock[$k] && $pchsno[$k] && $pchsdt[$k]) {
					$data[$k] = $db->fetch("SELECT * FROM ".GD_GOODS_OPTION." WHERE sno = '".$sno[$k]."'");
					$txt_pchsdt[$k] = substr($pchsdt[$k], 0, 4)."-".substr($pchsdt[$k], 4, 2)."-".substr($pchsdt[$k], 6, 2);
					list($data[$k]['goodsnm'], $data[$k]['img_s']) = $db->fetch("SELECT goodsnm, img_s FROM ".GD_GOODS." WHERE goodsno = '".$data[$k]['goodsno']."'");

					$sql[$k] = "INSERT INTO ".GD_PURCHASE_GOODS." SET goodsno = '".$data[$k]['goodsno']."', goodsnm = '".$data[$k]['goodsnm']."', img_s = '".$data[$k]['img_s']."', opt1 = '".$data[$k]['opt1']."', opt2 = '".$data[$k]['opt2']."', pchsno = '".$pchsno[$k]."', p_stock = '".$p_stock[$k]."', p_price = '".$p_price[$k]."', pchsdt = '".$txt_pchsdt[$k]."'";

					$db->query($sql[$k]);
					$db->query("UPDATE ".GD_GOODS_OPTION." SET stock = stock + ".$p_stock[$k].", pchsno = '".$pchsno[$k]."' WHERE sno = '".$sno[$k]."'");
					$db->query("UPDATE ".GD_GOODS." SET totstock = totstock + ".$p_stock[$k]." WHERE goodsno = '".$data[$k]['goodsno']."'");
					$db->query("DELETE FROM ".GD_PURCHASE_SMSLOG." WHERE goodsno = '".$data[$k]['goodsno']."' AND opt1 = '".$data[$k]['opt1']."' AND opt2 = '".$data[$k]['opt2']."'");

					$modifyCount++; // 수정 개수 추가
				}
			}
		}

		$arReferer = explode("?", $_SERVER['HTTP_REFERER']);
		$_POST['returnUrl'] = $arReferer[0]."?".$qstr."&page=".$page;
		if($modifyCount) msg("총 {$modifyCount}개의 사입처 이력이 등록되었습니다.");
		else msg("등록된 사입처 이력이 없습니다.");
		break;

	// 사입처 이력 수정
	case "pchs_log_modify" :
		$p_pchsdt		= isset($_POST['p_pchsdt'])		? $_POST['p_pchsdt']		: array();
		$p_price		= isset($_POST['p_price'])		? $_POST['p_price']			: array();
		$p_stock		= isset($_POST['p_stock'])		? $_POST['p_stock']			: array();
		$pgno			= isset($_POST['pgno'])			? $_POST['pgno']			: array();
		$checkChange	= isset($_POST['checkChange'])	? $_POST['checkChange']		: array();
		$modifyCount	= 0;
		$resultMsg		= "";

		for($i = 0, $imax = count($pgno); $i < $imax; $i++) {
			// 수정이 된 내역을 감지 - 입력된 것을 일일이 모두 DB에서 읽어 검사하는 부하를 줄이기 위함
			if($checkChange[$i] == 1) {

				// 필수 값 모두 검사 ( 입고일, 매입가, 입고량, 이력 구분 값 )
				if($p_pchsdt[$i] && $p_price[$i] && $p_stock[$i] && $pgno[$i]) {

					// 현재 상품의 재고, 이력의 재고
					list($tmpStock, $tmpLogStock) = $db->fetch("SELECT O.stock, PG.p_stock FROM gd_goods_option AS O INNER JOIN ".GD_PURCHASE_GOODS." AS PG ON O.goodsno = PG.goodsno AND O.opt1 = PG.opt1 AND O.opt2 = PG.opt2 WHERE PG.pgno = '".$pgno[$i]."' and go_is_deleted <> '1'");
					$stockTerm = $tmpLogStock - $p_stock[$i]; // 줄이는 재고량의 차이 ( 재고량이 늘어나면 마이너스 단위로 그대로 저장 )

					// 입고량을 기존보다 적게 입력했을 경우 - 수정 실패
					if($tmpLogStock > $p_stock[$i]) {
						$resultStock = $tmpStock - $stockTerm; // 최종 상품의 재고량

						// 재고가 마이너스가 되는 현상이 일어나면 수정 하지 않음
						if($resultStock < 0) {
							list($tmpGoodsno, $tmpOpt1, $tmpOpt2, $tmpGoodsnm, $tmpOptnm, $tmpPchsdt, $tmpPrice, $tmpStock) = $db->fetch("SELECT O.goodsno, O.opt1, O.opt2, G.goodsnm, G.optnm, PG.pchsdt, PG.p_price, PG.p_stock FROM gd_goods_option AS O LEFT JOIN ".GD_PURCHASE_GOODS." AS PG ON O.goodsno = PG.goodsno AND O.opt1 = PG.opt1 AND O.opt2 = PG.opt2 LEFT JOIN gd_goods AS G ON O.goodsno = G.goodsno WHERE PG.pgno = '".$pgno[$i]."' and go_is_deleted <> '1'");

							// 옵션 유무 검사
							if($tmpOptnm) {
								$tmpOptList = explode("|", $tmpOptnm);
								if($tmpOpt1) $tmpOpt = $tmpOptList[0]." : ".$tmpOpt1;
								if($tmpOpt2) {
									if($tmpOpt) $tmpOpt .= " / ";
									$tmpOpt .= $tmpOptList[1]." : ".$tmpOpt2;
								}
							}

							// 결과 메세지 리스트에 추가
							$resultMsg[] = $tmpGoodsnm.(($tmpOpt) ? " ".$tmpOpt : "")."\\n입고일 : ".$tmpPchsdt." / 매입가 : ".$tmpPrice." / 입고량 : ".$tmpStock." - 이력 수정을 실패 했습니다.";

							continue; // 수정 건너띄기
						}
					}

					// 수정 성공
					$pchsdt = substr($p_pchsdt[$i], 0, 4)."-".substr($p_pchsdt[$i], 4, 2)."-".substr($p_pchsdt[$i], 6, 2);
					$db->query("UPDATE ".GD_PURCHASE_GOODS." SET pchsdt = '".$pchsdt."', p_price = '".$p_price[$i]."', p_stock = '".$p_stock[$i]."' WHERE pgno = '".$pgno[$i]."'"); // 이력 수정
					$db->query("UPDATE gd_goods_option SET stock = stock - $stockTerm WHERE goodsno = '$tmpGoodsno' AND opt1 = '$tmpOpt1' AND opt2 = '$tmpOpt2'");
					$db->query("UPDATE gd_goods SET totstock = totstock - $stockTerm WHERE goodsno = '$tmpGoodsno'");

					$modifyCount++; // 수정 개수 추가

				}

			}
		}

		if($modifyCount > 0) $resultMsg[] = $modifyCount."개의 이력이 성공적으로 수정되었습니다."; // 수정됐으면 결과 메세지 리스트에 추가
		else $resultMsg[] = "수정된 내역이 없습니다.";

		if($imax = count($resultMsg)) {
			for($i = 0; $i < $imax; $i++) {
				if($msg) $msg.= "\\n\\n";
				$msg .= $resultMsg[$i];
			}

			msg($msg);
		}

		break;
}

if (!$_POST['returnUrl']) $_POST['returnUrl'] = $_SERVER['HTTP_REFERER'];
go($_POST['returnUrl']);

?>