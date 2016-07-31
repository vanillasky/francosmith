<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

if (!$_POST['returnUrl']) $_POST['returnUrl'] = $_SERVER['HTTP_REFERER'];

function chk_member($sess,$mode,$key,$level=''){
	global $db;

	list($olevel) = $db->fetch("select level from ".GD_MEMBER." where $mode='$key' and m_id != 'godomall'");
	if(!$level || $level != $olevel){
		if($key == $sess[$mode]){
			msg("자신의 그룹이나 승인 여부를 변경,계정삭제를 하실 수 없습니다.");
			return false;
		}
		if($olevel == 100){
			$cnt = $db->fetch("select count(*) from ".GD_MEMBER." where level = 100 and m_id != 'godomall'");
			if($cnt < 2){
				msg("전체관리자 그룹이나 승인 여부를 변경,계정삭제를 하실 수 없습니다.");
				return false;
			}
		}
	}
	return true;
}

switch ($mode){

	case "manual_evaluate":
		flush();
		$member_grp = Core::loader('member_grp');
		$grp_ruleset = $member_grp->ruleset;

		if ($grp_ruleset['automaticFl'] == 'y') {
			msg("수동 평가를 이용할 수 없습니다.");
			echo '<script>parent.closeLayer();</script>';
			exit;
		}

		if ($member_grp->prevent == true ) {
			msg("수동 평가를 이용할 수 없습니다.");
			echo '<script>parent.closeLayer();</script>';
			exit;
		}

		ob_flush();
		printf("%2000s"," ");

		echo '처리중입니다. 기다려 주세요.';
		flush();
		$member_grp->execUpdate(true);

		msg("수동 평가가 완료되었습니다.");
		echo '<script>parent.closeLayer();</script>';
		exit;
		break;

	case "ruleset":

		unset($_POST['x'],$_POST['y']);

		$qfile->open("../../conf/config.member_group.php");
		$qfile->write("<? \n");
		$qfile->write("\$member_grp_ruleset = array( \n");
		foreach ($_POST as $k => $v) {
			if ($k == 'returnUrl' || $k == 'mode') continue;

			if (is_bool($v)) {
				$qfile->write("'$k' => ".($v ? 'true' : 'false').",\n" );
			}
			else {
				$qfile->write("'$k' => '".(get_magic_quotes_gpc() ? $v : addslashes($v))."',\n" );
			}
		}
		$qfile->write("); \n");
		$qfile->write("?>");
		$qfile->close();

		msg('저장되었습니다.');
		echo "<script>parent.location.reload();</script>";
		exit;
		break;

	case "sms_auto":

		include "../../conf/config.php";

		$cfg['smsRecall']	= $_POST['smsRecall'];
		$cfg['smsAdmin']	= @implode("-",$_POST['smsAdmin']);
		$cfg['smsPass']		= $_POST['smsPass'];

		# 추가 관리자 설정
		$i = 0;
		foreach($_POST['smsAddAdmin1'] AS $sKey => $sVal){
			$smsAddAdmin[]	= $_POST['smsAddAdmin1'][$i] . "-" . $_POST['smsAddAdmin2'][$i] . "-" . $_POST['smsAddAdmin3'][$i];
			$i++;
		}

		$cfg['smsAddAdmin']	= @implode("|",$smsAddAdmin);
		$cfg['smsAutoSendType'] = $_POST['smsAutoSendType'];

		$cfg = array_map("stripslashes",$cfg);
		$cfg = array_map("addslashes",$cfg);

		$qfile->open("../../conf/config.php");
		$qfile->write("<? \n");
		$qfile->write("\$cfg = array( \n");
		foreach ($cfg as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();
		foreach ($_POST['auto'] as $k=>$v){
			$qfile->open("../../conf/sms/$k.php");
			$qfile->write("<? \n");
			$qfile->write("\$sms_auto = array( \n");
			foreach ($v as $k2=>$v2){
				$k2	= str_replace( array("'", "\\"),"",$k2);
				$qfile->write("$k2 => \"$v2\", \n");
			}
			$qfile->write("); \n");
			$qfile->write("?>");
			$qfile->close();
		}
		break;

	case "sms_sample_reg":

		$query = "
		insert into ".GD_SMS_SAMPLE." set
			category	= '$_POST[category]',
			subject		= '$_POST[subject]',
			msg			= '$_POST[msg]',
			sort		= -unix_timestamp()
		";
		$db->query($query);
		popupReload();
		break;

	case "sms_sample_del":

		$query = "DELETE FROM ".GD_SMS_SAMPLE." where sno = '$_POST[sno]'";
		$db->query($query);
		popupReload();
		break;

	case "sms_sample_mod":

		$query = "
		update ".GD_SMS_SAMPLE." set
			category	= '$_POST[category]',
			subject		= '$_POST[subject]',
			msg			= '$_POST[msg]'
		where sno = '$_POST[sno]'
		";
		$db->query($query);
		popupReload();
		break;

	case "sms_address_add":

		# 변수 설정
		if($_POST['grp_chk'] == "Def"){
			$sms_grp	= $_POST['sms_grp'];
		}else{
			$sms_grp	= $_POST['sms_grp_new'];
		}
		$sms_mobile	= @implode("-",$_POST['sms_mobile']);

		# 생성인 경우
		if(!$_POST['sno']){
			$query = "INSERT INTO ".GD_SMS_ADDRESS." (sms_grp,sms_name,sms_mobile,sms_etc,sex,regdt) VALUES (
				'".$sms_grp."',
				'".$_POST['sms_name']."',
				'".$sms_mobile."',
				'".$_POST['sms_etc']."',
				'".$_POST['sex']."',
				now()
				)";
		# 수정인 경우
		}else{
			$query = "UPDATE ".GD_SMS_ADDRESS." SET
				sms_grp		= '".$sms_grp."',
				sms_name	= '".$_POST['sms_name']."',
				sms_mobile	= '".$sms_mobile."',
				sms_etc		= '".$_POST['sms_etc']."',
				sex			= '".$_POST['sex']."',
				moddt		= now()
				WHERE sno = '".$_POST['sno']."'";
		}
		$db->query($query);
		popupReload();
		break;

	case "sms_address_add_by_excel":	// 엑셀 일괄 등록

		if($_POST['grp_chk'] == "Def"){
			$sms_grp	= $_POST['sms_grp'];
		}else{
			$sms_grp	= $_POST['sms_grp_new'];
		}

		$file = $_FILES['xls_file'];

		if ($file['error'] == 0 && $file['size'] > 0) {

			setlocale(LC_CTYPE, 'ko_KR.eucKR');
			header( 'Content-type: application/vnd.ms-excel' );
			header( 'Content-Disposition: attachment; filename=['. strftime( '%y년%m월%d일' ) .'] SMS 주소록 등록 결과.xls' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate, post-check=0,pre-check=0' );
			header( 'Pragma: public' );
			header( 'Content-Description: PHP4 Generated Data' );

			echo '
				<html>
				<head>
				<title>list</title>
				<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
				<style>.xl31{mso-number-format:"0_\)\;\\\(0\\\)";}</style>
				</head>
				<body>
				<table border="1">
				';

			$fp = fopen( $file['tmp_name'], 'r' );

			$arRow = array();

			while (($data = fgetcsv($fp, 1000, ",")) !== FALSE) {	//
				$arRow[] = $data;
			}

			// 필드
			$fields = array_combine($arRow[1], array_keys($arRow[1]) ) ;

			// 쿼리
			$_tmp = array_keys($fields);

			foreach($_tmp as $k => $v) {
				if (! in_array($v, array('sms_name','sms_mobile','sms_etc','sex'))) {
					unset($_tmp[$k], $fields[$v]);
				}
			}

			$_query = "
				INSERT INTO ".GD_SMS_ADDRESS."
					(sms_grp, ".implode(',',$_tmp).",regdt)
				VALUES
					([s], ".implode(',', array_fill(0, sizeof($_tmp), '[s]')).",NOW())
			";

			$line = 1;

			for ($i=3, $max=sizeof($arRow);$i<$max;$i++) {

				// 이름 필수
				if (empty($arRow[$i][$fields['sms_name']])) {

					echo '<tr><td>line ' . $line++ . ': </td>';
					echo '<td>이름</td><td>-</td>';
					echo '<td>처리결과</td><td>이름은 필수값 입니다.</td>';
					echo '</tr>';

					continue;

				}

				// 휴대폰 번호 체크
				if (! preg_match('/^([0]{1}[0-9]{1,2})-?([1-9]{1}[0-9]{2,3})-?([0-9]{4})$/',trim($arRow[$i][$fields['sms_mobile']]))) {

					echo '<tr><td>line ' . $line++ . ': </td>';
					echo '<td>이름</td><td>' . $arRow[$i][$fields['sms_name']] . '</td>';
					echo '<td>처리결과</td><td>핸드폰번호 형식 오류</td>';
					echo '</tr>';

					continue;

				}

				// 성별 필수
				if (empty($arRow[$i][$fields['sex']])) {

					echo '<tr><td>line ' . $line++ . ': </td>';
					echo '<td>이름</td><td>' . $arRow[$i][$fields['sms_name']] . '</td>';
					echo '<td>처리결과</td><td>성별은 필수값 입니다.</td>';
					echo '</tr>';

					continue;

				}

				echo '<tr><td>line ' . $line++ . ': </td>';
				echo '<td>이름</td><td>' . $arRow[$i][$fields['sms_name']] . '</td>';

				// 저장
				array_unshift($arRow[$i], $sms_grp);
				array_unshift($arRow[$i], $_query);

				$query = call_user_func_array(array($db, '_query_print') , $arRow[$i]);

				echo '<td>처리결과</td><td>'.($db->query($query) ? '저장 성공' : '저장 실패').'</td>';
				echo '</tr>';
			}
			echo '
				</table>
				</body>
				</html>
				';

			exit;
		}
		break;

	case "sms_address_del":

		foreach ($_POST['chk'] as $v){

			# 주소록 삭제
			$db->query("DELETE FROM ".GD_SMS_ADDRESS." WHERE sno='".$v."'");
		}
		break;

	case "sms_address_allDel":
			# 주소록 전체삭제
			$db->query("DELETE FROM " . GD_SMS_ADDRESS);
		break;

	case "send_sms":

		include "../../conf/config.php";
		include_once('../../lib/sms_sendlist.class.php');
		$sms_sendlist = new sms_sendlist();

		if($_POST['includeFail'] == 'N'){
			$failListArr = $sms_sendlist->getSmsFailListNumber($_POST['smsFailSnoList']);
		}

		switch ($_POST[type]){
			case "1":				// 개별 발송
				$div = explode("\r\n",$_POST[phone]);
				$div = array_notnull(array_unique($div));
				if($_POST['includeFail'] == 'N') $div = array_diff($div, $failListArr);
				sort($div);
				$to_tran = implode(",",$div);	//$to_tran= $r_smsType[$_POST['type']];
				$total = count($div);
				break;
			case "2":				// 회원 검색 대상
				$to_tran= $r_smsType[$_POST['type']];
				$query = get_magic_quotes_gpc() ? stripslashes($_POST['query']) : $_POST['query'];
				if($_POST['includeFail'] == 'N' || $_POST['receiveRefuseType'] == 'N'){
					$query = preg_replace('/(order)(\s|\w|\D|\d)+/i', '', $query);
					if($_POST['includeFail'] == 'N') {
						$whereType = (preg_match('/where/i', $query)) ? ' AND ' : ' WHERE ';
						$query .= $whereType . "mobile NOT IN ('" . implode("','", $failListArr) . "')";
					}
					// 수신거부 제외 N - 제외, Y or '' - 포함
					if($_POST['receiveRefuseType'] == 'N'){
						$whereType = (preg_match('/where/i', $query)) ? ' AND ' : ' WHERE ';
						$query .= $whereType . "sms = 'y'";
					}

				}
				$res	= $db->query($query);
				$total	= $db->count_($res);
				break;
			case "3":				// 회원 선택 대상
				$to_tran= $r_smsType[$_POST['type']];
				$where	= "m_no IN (".implode(",",$_POST['chk']).") AND mobile <> '' ";
				if($_POST['includeFail'] == 'N') $where .= " and mobile NOT IN ('".implode("','", $failListArr)."') ";
				if($_POST['receiveRefuseType'] == 'N') $where .= " AND sms = 'y' "; // 수신거부 제외 N - 제외, Y or '' - 포함
				$query	= "SELECT * FROM ".GD_MEMBER." WHERE ".$where;
				$res	= $db->query($query);
				$total	= $db->count_($res);
				break;
			case "4":				// 주소록 검색 대상
				$to_tran= $r_smsType[$_POST['type']];
				$query = get_magic_quotes_gpc() ? stripslashes($_POST['query']) : $_POST['query'];
				if($_POST['includeFail'] == 'N'){
					$query = preg_replace('/(order)(\s|\w|\D|\d)+/i', '', $query);
					if(preg_match('/where/i', $query)){
						$whereType = ' and';
					}
					else {
						$whereType = ' WHERE ';
					}

					$query .= $whereType . " sms_mobile NOT IN ('" . implode("','", $failListArr) . "')";
				}
				$res	= $db->query($query);
				$total	= $db->count_($res);
				break;
			case "5":				// 주소록 선택 대상
				$to_tran= $r_smsType[$_POST['type']];
				$where	= "sno IN (".implode(",",$_POST['chk']).")";
				if($_POST['includeFail'] == 'N') $where .= " and sms_mobile NOT IN ('".implode("','", $failListArr)."') ";
				$query	= "SELECT * FROM ".GD_SMS_ADDRESS." WHERE ".$where;
				$res	= $db->query($query);
				$total	= $db->count_($res);
				break;

			// 일반 주소록 전체, 회원 주소록 전체
			case "6":
				$to_tran= $r_smsType[$_POST['type']];
				$where[] = "sms='y'";
				$where[] = "mobile!=''";
				$where[] = MEMBER_DEFAULT_WHERE;
				if($_POST['includeFail'] == 'N'){
					$where[] = "mobile NOT IN ('" . implode("','", $failListArr) . "')";
				}
				if ($where) $where = "where ".implode(" and ",$where);
				$query	= "select mobile, name, m_no from ".GD_MEMBER." $where";
				$res	= $db->query($query);
				$total	= $db->count_($res);
				break;
			case "7":
				$to_tran= $r_smsType[$_POST['type']];
				if($_POST['includeFail'] == 'N'){
					$where = " WHERE sms_mobile NOT IN ('" . implode("','", $failListArr) . "')";
				}
				$query	= "SELECT sms_mobile, sms_name FROM ".GD_SMS_ADDRESS . $where;
				$res	= $db->query($query);
				$total	= $db->count_($res);
				break;

			case "8": //SMS 발송결과 재전송 - 선택
				$to_tran= $r_smsType[$_POST['type']];
				$where	= "sms_no IN (" . implode(",", $_POST['chk']) . ")";
				if($_POST['includeFail'] == 'N') $where .= " and sms_phoneNumber NOT IN ('".implode("','", $failListArr)."') ";
				$query	= "SELECT * FROM " . GD_SMS_SENDLIST . " WHERE " . $where;
				$res	= $db->query($query);
				$total	= $db->count_($res);
				break;
			case "9": //SMS 발송결과 재전송 - 검색
				$to_tran= $r_smsType[$_POST['type']];
				$query = get_magic_quotes_gpc() ? stripslashes($_POST['query']) : $_POST['query'];
				if($_POST['includeFail'] == 'N'){
					$query = preg_replace('/(order)(\s|\w|\D|\d)+/i', '', $query);
					if(preg_match('/where/i', $query)){
						$whereType = ' and';
					}
					else {
						$whereType = ' WHERE ';
					}

					$query .= $whereType . " sms_phoneNumber NOT IN ('" . implode("','", $failListArr) . "')";
				}
				$res	= $db->query($query);
				$total	= $db->count_($res);
				break;
		}

		if($total < 1){
			msg('발송될 내역이 없습니다.');
			exit;
		}

		// SMS 와 LMS 구분
		// SMS
		if($_POST['sms_type'] == 'sms'){
			$_POST['msg'] = $_POST['sms_msg'];

			$_POST['msg'] = str_replace("\r","",$_POST['msg']);
			$msg = parseCode($_POST['msg']);

			$total = $total * 1;

			if ($total>getSmsPoint()){
				msg("SMS 발송예정인 ".number_format($total)."건이 잔여콜수인 ".number_format(getSmsPoint())."건보다 많습니다");
				exit;
			}

			### SMS 발송
			include "znd_sms.php";

			$smspt = number_format($sms->smsPt);

			$msg = "SMS 발송요청 건수 : ".number_format(array_sum($num))."건 \\n ------------------- \\n 발송요청 : ".number_format($num[success])." / 발송요청실패 : ".number_format($num[fail]);

		// LMS
		} else if($_POST['sms_type'] == 'lms'){
			$_POST['subject'] = $_POST['lms_subject'];
			$_POST['msg'] = $_POST['lms_msg'];

			# LMS 내용 처리
			$_POST['subject'] = preg_replace ("/[\{\}\[\]\/?.,;:|\)*~`!^\-_+<>@\#$%&\\\=\(\'\"]/i", "",$_POST['subject']); //특수문자 제거
			$_POST['subject'] = str_replace("\r","",$_POST['subject']);
			$subject = parseCode($_POST['subject']);
			$_POST['msg'] = str_replace("\r","",$_POST['msg']);
			$msg = parseCode($_POST['msg']);

			// LMS 는 SMS 의 3배
			$total = $total * 3;

			if ($total>getSmsPoint()){
				msg("LMS 발송예정인 ".number_format($total)."건이 잔여콜수인 ".number_format(getSmsPoint())."건보다 많습니다");
				exit;
			}

			### LMS 발송
			include "znd_lms.php";

			$smspt = number_format($lms->smsPt);

			$msg = "LMS 발송요청 건수 : ".number_format(array_sum($num))."건 \\n ------------------- \\n 발송요청 : ".number_format($num[success])." / 발송요청실패 : ".number_format($num[fail]);
		}

		msg($msg);
		echo "<script>parent.document.getElementById('span_sms').innerHTML = '".$smspt."'; parent.document.getElementById('sms_bar').style.width = 0;</script>";

		exit;
		break;

	case "addGrp":

		if(count($_POST['category']) > 17){
			msg('제한카테고리갯수는 17개 까지 가능합니다.');
			exit;
		}
		if(count($_POST['e_refer']) > 18){
			msg('제한상품갯수는 18개 까지 가능합니다.');
			exit;
		}

		$dc['dc_std_amt'] = $_POST['dc_std_amt_'.$_POST['dc_type']];
		$dc['dc'] = $_POST['dc_'.$_POST['dc_type']];

		$add_emoney['add_emoney_std_amt'] = $_POST['add_emoney_std_amt_'.$_POST['add_emoney_type']];
		$add_emoney['add_emoney'] = $_POST['add_emoney_'.$_POST['add_emoney_type']];

		$free_deliveryfee['free_deliveryfee_std_amt'] = $_POST['free_deliveryfee_std_amt_'.$_POST['free_deliveryfee_type']];

		if(!$_POST['dc']) $_POST['dc'] = 0;
		if(!$_POST['add_emoney']) $_POST['add_emoney'] = 0;
		if(!$_POST['free_deliveryfee']) $_POST['free_deliveryfee'] = "N";
		if($_POST['e_refer']) $e_refer = @ implode(',',$_POST['e_refer']);
		if($_POST['category']) $excate = @ implode(',',$_POST['category']);

		list ($cnt) = $db->fetch("select count(*) from ".GD_MEMBER_GRP." where level = '".$_POST['level']."'");
		if ($cnt) msg("동일 레벨의 그룹이 존재합니다",-1);

		// 등급 아이콘
		$icon_path = '../../data/member/icon/';
		$group_icon = '';

		if ($_FILES['group_icon']['error'] == 0 && $_FILES['group_icon']['size'] > 0) {

			$file = $_FILES['group_icon'];

			$_ext = array_pop(explode('.',$file['name']));

			if (strpos('gif jpg jpeg png',$_ext) !== false) {
				$group_icon_path = $icon_path.$file['name'];
				if (@move_uploaded_file($file['tmp_name'], $group_icon_path)) {
					chmod($group_icon_path,0707);
				}

				$group_icon = array_pop(explode('/',$group_icon_path));
			}

		}
		elseif($_POST['group_icon_preset']) {

			$tmp = array_pop(explode('/',$_POST['group_icon_preset']));

			$group_icon_path = $icon_path.$tmp;

			if (@copy($_POST['group_icon_preset'],$group_icon_path)) {
				chmod($group_icon_path,0707);
			}

			$group_icon = array_pop(explode('/',$group_icon_path));

		}

		$query = "
		insert into ".GD_MEMBER_GRP." set
			grpnm				= '".$_POST['grpnm']."',
			grpnm_icon			= '".$group_icon."',
			grpnm_disp_type		= '".$_POST['grpnm_disp_type']."',
			level				= '".$_POST['level']."',
			dc_type					= '".$_POST['dc_type']."',
			dc_std_amt				= '".$dc['dc_std_amt']."',
			dc						= '".$dc['dc']."',
			add_emoney_type			= '".$_POST['add_emoney_type']."',
			add_emoney_std_amt		= '".$add_emoney['add_emoney_std_amt']."',
			add_emoney				= '".$add_emoney['add_emoney']."',
			free_deliveryfee		= '".$_POST['free_deliveryfee_type']."',
			free_deliveryfee_std_amt= '".$free_deliveryfee['free_deliveryfee_std_amt']."',
			excate	= '".$excate."',
			excep	= '".$e_refer."',
			regdt				= now()
		";
		$db->query($query);
		$sno = $db->lastID();

		// 평가기준
		$query = "
			INSERT INTO ".GD_MEMBER_GRP_RULESET." SET
				sno							= '".$sno."',
				type						= '".$_POST[type]."',
				by_score_limit				= '".$_POST[by_score_limit]."',
				by_score_max				= '".$_POST[by_score_max]."',
				by_number_buy_limit			= '".$_POST[by_number_buy_limit]."',
				by_number_buy_max			= '".$_POST[by_number_buy_max]."',
				by_number_review_require	= '".$_POST[by_number_review_require]."',
				by_number_order_require		= '".$_POST[by_number_order_require]."',
				mobile_by_number_buy_limit			= '".$_POST[mobile_by_number_buy_limit]."',
				mobile_by_number_buy_max			= '".$_POST[mobile_by_number_buy_max]."',
				mobile_by_number_review_require	= '".$_POST[mobile_by_number_review_require]."',
				mobile_by_number_order_require		= '".$_POST[mobile_by_number_order_require]."'
			";

		$db->query($query);

		if($_POST[adminAuth]){
			$level = $_POST[level];
			@include "../../conf/groupAuth.php";
			if(is_array($_POST[menu])) $rAuth[$level] = array_unique($_POST[menu]);
			else $rAuth[$level] = array();

			$rAuthStatistics[$level] = $_POST['statistics'];

			$qfile->open("../../conf/groupAuth.php");
			$qfile->write("<? \n");
			foreach ($rAuth as $k=>$v){
				$qfile->write("\$rAuth[$k] = array( \n");
				foreach($v as $k1=>$v1) $qfile->write("'$k1' => '$v1', \n");
				$qfile->write("); \n");
			}

			foreach ($rAuthStatistics as $k=>$v) {
				$qfile->write("\$rAuthStatistics[$k] = '$v';\n");
			}

			$qfile->write("?>");
			$qfile->close();
			chmod('../../conf/groupAuth.php',0707);
		}

		echo "<script>parent.location.reload();</script>";
		exit;

		break;

	case "modGrp":

		if(count($_POST['category']) > 17){
			msg('제한카테고리갯수는 17개 까지 가능합니다.');
			exit;
		}
		if(count($_POST['e_refer']) > 18){
			msg('제한상품갯수는 18개 까지 가능합니다.');
			exit;
		}

		$dc['dc_std_amt'] = $_POST['dc_std_amt_'.$_POST['dc_type']];
		$dc['dc'] = $_POST['dc_'.$_POST['dc_type']];

		$add_emoney['add_emoney_std_amt'] = $_POST['add_emoney_std_amt_'.$_POST['add_emoney_type']];
		$add_emoney['add_emoney'] = $_POST['add_emoney_'.$_POST['add_emoney_type']];

		$free_deliveryfee['free_deliveryfee_std_amt'] = $_POST['free_deliveryfee_std_amt_'.$_POST['free_deliveryfee_type']];

		if(!$_POST['dc']) $_POST['dc'] = 0;
		if(!$_POST['add_emoney']) $_POST['add_emoney'] = 0;
		if(!$_POST['free_deliveryfee']) $_POST['free_deliveryfee'] = "N";
		if($_POST['e_refer']) $e_refer = @ implode(',',$_POST['e_refer']);
		if($_POST['category']) $excate = @ implode(',',$_POST['category']);

		// 등급 아이콘
		$icon_path = '../../data/member/icon/';
		$group_icon = '';

		if ($_FILES['group_icon']['error'] == 0 && $_FILES['group_icon']['size'] > 0) {

			$file = $_FILES['group_icon'];

			$_ext = array_pop(explode('.',$file['name']));

			if (strpos('gif jpg jpeg png',$_ext) !== false) {
				$group_icon_path = $icon_path.$file['name'];
				if (@move_uploaded_file($file['tmp_name'], $group_icon_path)) {
					chmod($group_icon_path,0707);
				}

				$group_icon = array_pop(explode('/',$group_icon_path));
			}

		}
		elseif($_POST['group_icon_preset']) {

			$tmp = array_pop(explode('/',$_POST['group_icon_preset']));

			$group_icon_path = $icon_path.$tmp;

			if (@copy($_POST['group_icon_preset'],$group_icon_path)) {
				chmod($group_icon_path,0707);
			}

			$group_icon = array_pop(explode('/',$group_icon_path));

		}

		$query = "select level from ".GD_MEMBER_GRP." where sno = '".$_POST['sno']."'";
		list($old_level) = $db->fetch($query);

		$query = "
		update ".GD_MEMBER_GRP." set
			grpnm				= '".$_POST['grpnm']."',
			grpnm_icon			= ".($group_icon == '' ? 'grpnm_icon' : "'".$group_icon."'").",
			grpnm_disp_type		= '".$_POST['grpnm_disp_type']."',
			level				= '".$_POST['level']."',
			dc_type					= '".$_POST['dc_type']."',
			dc_std_amt				= '".$dc['dc_std_amt']."',
			dc						= '".$dc['dc']."',
			add_emoney_type			= '".$_POST['add_emoney_type']."',
			add_emoney_std_amt		= '".$add_emoney['add_emoney_std_amt']."',
			add_emoney				= '".$add_emoney['add_emoney']."',
			free_deliveryfee		= '".$_POST['free_deliveryfee_type']."',
			free_deliveryfee_std_amt= '".$free_deliveryfee['free_deliveryfee_std_amt']."',
			excate	= '".$excate."',
			excep	= '".$e_refer."',
			moddt				= now()
		where sno = '$_POST[sno]'
		";

		$db->query($query);
		if($old_level != $_POST['level'] && mysql_affected_rows()>0 ){	//그룹래밸 변경이 있고 그룹테이블이 수정된경우
			$query ="update ".GD_MEMBER." set level=".$_POST['level']." where level='".$old_level."' ";
			$db->query($query);
		}

		// 평가기준
		$query = "
			type						= '".$_POST[type]."',
			by_score_limit				= '".$_POST[by_score_limit]."',
			by_score_max				= '".$_POST[by_score_max]."',
			by_number_buy_limit			= '".$_POST[by_number_buy_limit]."',
			by_number_buy_max			= '".$_POST[by_number_buy_max]."',
			by_number_review_require	= '".$_POST[by_number_review_require]."',
			by_number_order_require		= '".$_POST[by_number_order_require]."',
			mobile_by_number_buy_limit			= '".$_POST[mobile_by_number_buy_limit]."',
			mobile_by_number_buy_max			= '".$_POST[mobile_by_number_buy_max]."',
			mobile_by_number_review_require	= '".$_POST[mobile_by_number_review_require]."',
			mobile_by_number_order_require		= '".$_POST[mobile_by_number_order_require]."'
		";
		if (($rule = $db->fetch("SELECT * FROM ".GD_MEMBER_GRP_RULESET." WHERE sno = $_POST[sno]",1)) !== false) {
			$query = "
			UPDATE ".GD_MEMBER_GRP_RULESET." SET
				$query
			WHERE sno = $_POST[sno]
			";
		}
		else {
			$query = "
			INSERT INTO ".GD_MEMBER_GRP_RULESET." SET
				sno = $_POST[sno],
				$query
			";
		}

		$db->query($query);

		if($_POST[adminAuth]){
			list($level) = $db->fetch("select level from ".GD_MEMBER_GRP." where sno='$_POST[sno]'");
			@include "../../conf/groupAuth.php";
			if(is_array($_POST[menu])) $rAuth[$level] = array_unique($_POST[menu]);
			else $rAuth[$level] = array();

			$rAuthStatistics[$level] = $_POST['statistics'];

			$qfile->open("../../conf/groupAuth.php");
			$qfile->write("<? \n");
			foreach ($rAuth as $k=>$v){
				$qfile->write("\$rAuth[$k] = array( \n");
				foreach($v as $k1=>$v1) $qfile->write("'$k1' => '$v1', \n");
				$qfile->write("); \n");
			}

			foreach ($rAuthStatistics as $k=>$v) {
				$qfile->write("\$rAuthStatistics[$k] = '$v';\n");
			}

			$qfile->write("?>");
			$qfile->close();
			@chmod('../../conf/groupAuth.php',0707);
		}
		msg('저장되었습니다.');
		echo "<script>parent.location.reload();</script>";
		exit;

		break;

	case "delGrp":

		list ($cnt) = $db->fetch("select count(*) from ".GD_MEMBER." where level = '$_GET[level]'");
		if ($cnt) msg("그룹에 회원이 존재합니다");
		else {
			$db->query("delete from ".GD_MEMBER_GRP." where sno = '$_GET[sno]'");
			$db->query("delete from ".GD_MEMBER_GRP_RULESET." where sno = '$_GET[sno]'");
			echo "<script>parent.location.reload();</script>";
		}

		exit;
		break;

	case "fieldset":
	case "realname":

		include "../../conf/fieldset.php";
		@include "../../conf/mobile_fieldset.php";

		@include '../../conf/naverCheckout.cfg.php';
		if(isset($checkoutCfg['ncMemberYn']) && $checkoutCfg['ncMemberYn'] == 'y') {
			if($mode == 'fieldset' && ($_POST['useField']['resno'] != 'on' || $_POST['reqField']['resno'] != 'on')) {
				$_POST['useField']['resno'] = 'on';
				$_POST['reqField']['resno'] = 'on';
				msg('네이버 체크아웃 부가서비스를 이용중일 경우 주민등록번호는 필수사항이어야 합니다.');
			}

			if($mode == 'fieldset' && ($_POST['status'] != '1')) {
				$_POST['status'] = '1';
				msg('네이버 체크아웃 부가서비스를 이용중일 경우 회원인증절차를 사용하실 수 없습니다.');
			}

			if($mode == 'realname' && ($_POST['realname']['useyn'] != 'y' && $realname['useyn'] == 'y' && $ipin['useyn'] == 'y')) {
				$_POST['realname']['useyn'] = 'y';
				msg('네이버 체크아웃 부가서비스를 이용중일 경우 실명확인 수단으로 아이핀만 사용할 수 없어 실명확인을 해제할 수 없습니다.');
			}
		}

		if ($mode == 'fieldset'){
			$realname = @array_map("stripslashes",$realname);
			$realname = @array_map("addslashes",$realname);
			$_POST[realname] = $realname;
		}
		else {
			$joinset = @array_map("stripslashes",$joinset);
			$joinset = @array_map("addslashes",$joinset);
			$_POST = array_merge($_POST, $joinset);
			$_POST = array_merge($_POST, $checked);

			// 모바일샵 가입항목
			$checked_mobile = array(
				'useMobileField' => (array)$checked_mobile['useField'],
				'reqMobileField' => (array)$checked_mobile['reqField'],
			);
			$_POST = array_merge($_POST, $checked_mobile);
		}
		$_POST['ipin'] = $ipin;

		// PC용 설정
		$qfile->open("../../conf/fieldset.php");
		$qfile->write("<?\n");
		$qfile->write("\$joinset = array();\n");
		$qfile->write("\$joinset[status] = '" . $_POST[status] . "';\n");
		$qfile->write("\$joinset[rejoin] = '" . $_POST[rejoin] . "';\n");
		$qfile->write("\$joinset[unableid] = '" . $_POST[unableid] . "';\n");
		$qfile->write("\$joinset[emoney] = '" . $_POST[emoney] . "';\n");
		$qfile->write("\$joinset[grp] = '" . $_POST[grp] . "';\n");
		$qfile->write("\$joinset[recomm_emoney] = '" . $_POST[recomm_emoney] . "';\n");
		$qfile->write("\$joinset[recomm_add_emoney] = '" . $_POST[recomm_add_emoney] . "';\n");
		$qfile->write("\$joinset[under14status] = '" . $_POST[under14status] . "';\n");
		$qfile->write("\$joinset[ex1] = '" . $_POST[ex1] . "';\n");
		$qfile->write("\$joinset[ex2] = '" . $_POST[ex2] . "';\n");
		$qfile->write("\$joinset[ex3] = '" . $_POST[ex3] . "';\n");
		$qfile->write("\$joinset[ex4] = '" . $_POST[ex4] . "';\n");
		$qfile->write("\$joinset[ex5] = '" . $_POST[ex5] . "';\n");
		$qfile->write("\$joinset[ex6] = '" . $_POST[ex6] . "';\n");
		$qfile->write("\n");

		$qfile->write("\$realname = array();\n");
		while (list($key,$value)=@each($_POST[realname])) $qfile->write("\$realname[{$key}] = '{$value}';\n");
		$qfile->write("\n");

		$qfile->write("\$ipin = array();\n");
		while (list($key,$value)=@each($_POST[ipin])) $qfile->write("\$ipin[{$key}] = '$value';\n");
		$qfile->write("\n");

		$qfile->write("\$checked[useField] = array(\n");
		while (list($key,$value)=@each($_POST[useField])) $qfile->write("'$key'	=> 'checked',\n");
		$qfile->write(");\n");
		$qfile->write("\$checked[reqField] = array(\n");
		while (list($key,$value)=@each($_POST[reqField])) $qfile->write("'$key'	=> 'checked',\n");
		$qfile->write(");\n");
		$qfile->write("?>");
		$qfile->close();

		// 모바일샵용 설정
		$qfile->open("../../conf/mobile_fieldset.php");
		$qfile->write("<?\n");
		$qfile->write("\$checked_mobile[useField] = array(\n");
		while (list($key,$value)=@each($_POST['useMobileField'])) $qfile->write("'$key'	=> 'checked',\n");
		$qfile->write(");\n");
		$qfile->write("\$checked_mobile[reqField] = array(\n");
		while (list($key,$value)=@each($_POST['reqMobileField'])) $qfile->write("'$key'	=> 'checked',\n");
		$qfile->write(");\n");
		$qfile->write("?>");
		$qfile->close();
		break;
	case "ipin":

		include "../../conf/fieldset.php";

		@include '../../conf/naverCheckout.cfg.php';
		if(isset($checkoutCfg['ncMemberYn']) && $checkoutCfg['ncMemberYn'] == 'y') {
			if(($_POST['ipin']['useyn'] == 'y' || $_POST['ipin']['nice_useyn'] == 'y') && ($ipin['useyn'] != 'y' || $ipin['nice_useyn'] != 'y') && $realname['useyn'] != 'y') {
				$_POST['ipin']['useyn'] = 'n';
				$_POST['ipin']['nice_useyn'] = 'n';
				msg('네이버 체크아웃 부가서비스를 이용중일 경우 실명확인 수단으로 아이핀만 사용할 수 없습니다.');
			}
		}

		$joinset = @array_map("stripslashes",$joinset);
		$joinset = @array_map("addslashes",$joinset);
		$_POST = array_merge($_POST, $joinset);
		$_POST = array_merge($_POST, $checked);
		$_POST['realname'] = $realname;

		$ipin = @array_map("stripslashes", @array_map("addslashes",$ipin));
		if($ipin['useyn'] == 'y' && $_POST['ipin']['nice_useyn'] == 'y') $ipin['useyn'] = 'n';
		if($ipin['nice_useyn'] == 'y' && $_POST['ipin']['useyn'] == 'y') $ipin['nice_useyn'] = 'n';
		if(is_array($ipin)) $_POST['ipin'] = array_merge($ipin, $_POST['ipin']);

		$qfile->open("../../conf/fieldset.php");
		$qfile->write("<?\n");
		$qfile->write("\$joinset = array();\n");
		$qfile->write("\$joinset[status] = '" . $_POST[status] . "';\n");
		$qfile->write("\$joinset[rejoin] = '" . $_POST[rejoin] . "';\n");
		$qfile->write("\$joinset[unableid] = '" . $_POST[unableid] . "';\n");
		$qfile->write("\$joinset[emoney] = '" . $_POST[emoney] . "';\n");
		$qfile->write("\$joinset[grp] = '" . $_POST[grp] . "';\n");
		$qfile->write("\$joinset[recomm_emoney] = '" . $_POST[recomm_emoney] . "';\n");
		$qfile->write("\$joinset[recomm_add_emoney] = '" . $_POST[recomm_add_emoney] . "';\n");
		$qfile->write("\$joinset[under14status] = '" . $_POST[under14status] . "';\n");
		$qfile->write("\$joinset[ex1] = '" . $_POST[ex1] . "';\n");
		$qfile->write("\$joinset[ex2] = '" . $_POST[ex2] . "';\n");
		$qfile->write("\$joinset[ex3] = '" . $_POST[ex3] . "';\n");
		$qfile->write("\$joinset[ex4] = '" . $_POST[ex4] . "';\n");
		$qfile->write("\$joinset[ex5] = '" . $_POST[ex5] . "';\n");
		$qfile->write("\$joinset[ex6] = '" . $_POST[ex6] . "';\n");
		$qfile->write("\n");

		$qfile->write("\$realname = array();\n");
		while (list($key,$value)=@each($_POST[realname])) $qfile->write("\$realname[{$key}] = '{$value}';\n");
		$qfile->write("\n");

		$qfile->write("\$ipin = array();\n");
		while (list($key,$value)=@each($_POST[ipin])) $qfile->write("\$ipin[{$key}] = '$value';\n");
		$qfile->write("\n");

		$qfile->write("\$checked[useField] = array(\n");
		while (list($key,$value)=@each($_POST[useField])) $qfile->write("'$key'	=> 'checked',\n");
		$qfile->write(");\n");
		$qfile->write("\$checked[reqField] = array(\n");
		while (list($key,$value)=@each($_POST[reqField])) $qfile->write("'$key'	=> 'checked',\n");
		$qfile->write(");\n");
		$qfile->write("?>");
		$qfile->close();

		msg("설정이 저장되었습니다.");

		break;
	case "delete":
		@include "../../conf/naverCheckout.cfg.php";

		foreach ($_POST[chk] as $v){

			if(!chk_member($sess,'m_no',$v)) go($_POST[returnUrl]);

			### 네이버 체크아웃(회원연동)
			if($checkoutCfg['useYn']=='y'):
				$res = naverCheckoutHack($v);
				if ($res['result'] === false) {
					msg('네이버체크아웃 회원 철회가 실패되어 탈퇴할 수 없습니다.'.($res['error'] ? '\n('.$res['error'].')' : ''),-1);
				}
			endif;

			// 탈퇴로그 저장
			list( $m_no, $m_id, $name ) = $db->fetch("select m_no, m_id, name from ".GD_MEMBER." where m_no='$v'");
			$db->query("insert into ".GD_LOG_HACK." ( m_id, name, actor, ip, regdt ) values ( '$m_id', '$name', '0', '" . $_SERVER['REMOTE_ADDR'] . "', now() )" );

			$db->query("delete from ".GD_MEMBER." WHERE m_no='$v'");
			$db->query("delete from ".GD_LOG_EMONEY." WHERE m_no='$v'");
			$db->query('DELETE FROM gd_sns_member WHERE m_no='.$m_no);
		}

		break;

	case "modify":

		extract($_POST);

		if(!chk_member($sess,'m_id',$m_id,$level)) go($_POST[returnUrl]);

		$birth		= sprintf("%02d",$birth[0]).sprintf("%02d",$birth[1]);
		$zipcode	= implode("-",$zipcode);
		$phone		= implode("-",$phone);
		$mobile		= implode("-",$mobile);
		$fax		= implode("-",$fax);
		$marridate	= sprintf("%04d",$marridate[0]).sprintf("%02d",$marridate[1]).sprintf("%02d",$marridate[2]);
		$busino = preg_replace("/[^0-9-]+/","",$busino);
		$calendar  = ($calendar) ? $calendar : 's';
		$marriyn  = ($marriyn) ? $marriyn : 'n';

		if ( is_array( $interest ) ){
			foreach ( $interest as $k => $v ) $interest[$k] = pow( 2, $v );
			$interest = @array_sum($interest);
		}

		$mod_query = "";
		if ( $mod_pass == 'Y' ) $mod_query .= "password = password('$password'),";
		if ( $mod_sex == 'Y' ) {
			$sex = ($sex) ? $sex : 'm';
			$mod_query .= "sex = '$sex',";
		}

		### 닉네임 중복여부 체크
		list ($chk) = $db->fetch("select nickname from ".GD_MEMBER." where nickname='".$_POST['nickname']."' and m_id != '".$_POST['m_id']."'");
		if ($chk) msg("이미 등록된 닉네임입니다",-1);

		// 수신동의설정 안내메일
		$sendAcceptAgreeMail = false;
		$originalMailling = $oroginalSms = '';
		list($originalMailling, $oroginalSms) = $db->fetch("SELECT mailling, sms FROM ".GD_MEMBER." WHERE  m_id = '".$_POST['m_id']."' ");
		if($mailling != $originalMailling || $sms != $oroginalSms){
			$sendAcceptAgreeMail = true;
		}

		$query = "
		update ".GD_MEMBER." set $mod_query
			status		= '$status',
			name		= '$name',
			nickname	= '$nickname',
			level		= '$level',
			birth_year	= '$birth_year',
			birth		= '$birth',
			calendar	= '$calendar',
			email		= '$email',
			mailling	= '$mailling',
			zipcode		= '$zipcode',
			zonecode	= '$zonecode',
			address		= '$address',
			road_address= '$road_address',
			address_sub	= '$address_sub',
			phone		= '$phone',
			mobile		= '$mobile',
			fax			= '$fax',
			sms			= '$sms',
			company		= '$company',
			service		= '$service',
			busino		= '$busino',
			item		= '$item',
			job			= '$job',
			marriyn		= '$marriyn',
			marridate	= '$marridate',
			interest	= '$interest',
			memo		= '$memo',
			ex1			= '$ex1',
			ex2			= '$ex2',
			ex3			= '$ex3',
			ex4			= '$ex4',
			ex5			= '$ex5',
			ex6			= '$ex6',
			recommid	= '$recommid'
		where m_id = '$m_id'
		";
		$db->query($query);

		// 투데이샵 관심분류
		// 구독 신청 정보가 있는가?
		if (($subscribe = $db->fetch("SELECT sno FROM ".GD_TODAYSHOP_SUBSCRIBE." WHERE m_id = '".$m_id."'",1)) != false) {
			// update..
			$query = "
			UPDATE ".GD_TODAYSHOP_SUBSCRIBE." SET
				category = '".$_POST['interest_category']."'
			WHERE m_id = '".$m_id."'
			";
		}
		else {
			// insert..
			$query = "
			INSERT INTO ".GD_TODAYSHOP_SUBSCRIBE." SET
				m_id = '".$m_id."',
				category = '".$_POST['interest_category']."'
			";
		}
		$db->query($query);

		// CRM 팝업에서 회원정보 수정&일반 회원정보 수정시 return url 분기처리
		if ($_POST['crmyn'] == 'Y') $retUrl = "Crm_info.php";
		else $retUrl = "info.php";

		//보안서버사용
		if ($cfg['ssl'] == "1") {
			if($cfg['ssl_type'] == "free") { //무료보안서버사용
				$sslcheck = $sitelink->link('admin/member/'.$retUrl ,"regular");
			} else {//유료보안서버사용
				$sslcheck = $sitelink->link('admin/member/'.$retUrl.'?' ,"regular");
			}
			$_POST[returnUrl] = $sslcheck.'m_id=' . $m_id . '&returnUrl=' . urlencode( $_POST[returnUrl] );
		} else {
			$_POST[returnUrl] = './'.$retUrl.'?m_id=' . $m_id . '&returnUrl=' . urlencode( $_POST[returnUrl] );
		}

		//수신동의설정 안내메일
		if($sendAcceptAgreeMail === true && function_exists('sendAcceptAgreeMail')){
			sendAcceptAgreeMail($email, $mailling, $sms);
		}

		//debug($_POST[returnUrl]); exit;

		//$_POST[returnUrl] = './info.php?m_id=' . $m_id . '&returnUrl=' . urlencode( $_POST[returnUrl] );

		break;

	case "emoney_add":

		if ( $_POST[memo] == 'direct' ) $_POST[memo] = $_POST[direct_memo];

		$query = "
		insert into ".GD_LOG_EMONEY." set
			m_no	= '$_POST[m_no]',
			emoney	= '$_POST[emoney]',
			memo	= '$_POST[memo]',
			regdt	= now()
		";
		$db->query($query);

		$query = "update ".GD_MEMBER." set emoney = emoney + $_POST[emoney] where m_no='" . $_POST[m_no] . "'";
		$db->query($query);

		break;

	case "emoney_delete":

		list( $m_no, $emoney ) = $db->fetch("select m_no, emoney from ".GD_LOG_EMONEY." where sno='" . $_GET['sno'] . "'" );

		$query = "delete from ".GD_LOG_EMONEY." where sno='" . $_GET['sno'] . "'";
		$db->query($query);

		$query = "update ".GD_MEMBER." set emoney = emoney - $emoney where m_no='" . $m_no . "'";
		$db->query($query);

		break;

	case "batch_emoney":
	case "batch_level":
	case "batch_status":

		if ($mode == "batch_emoney" && $_POST[memo] == 'direct') $_POST[memo] = $_POST[direct_memo];
		$query = stripslashes($_POST[query]);
		if ($_POST[type]=="select") $query = "select * from ".GD_MEMBER." where m_no in (".implode(",",$_POST[chk]).")";

		if ($query){
			$s = strpos($query,"from");
			$e = strpos($query,"order by");
			if (!$e) $e = strlen($query);
			$res = $db->query("select m_no ".substr($query,$s,$e-$s));
			while ($data=$db->fetch($res)){

				if($mode != "batch_emoney"){
					if(!chk_member($sess,'m_no',$data[m_no])) go($_POST[returnUrl]);
				}

				if ($mode == "batch_emoney"){
					$query = "
					insert into ".GD_LOG_EMONEY." set
						m_no	= '$data[m_no]',
						emoney	= '$_POST[emoney]',
						memo	= '$_POST[memo]',
						regdt	= now()
					";
					$db->query($query);

					$query = "update ".GD_MEMBER." set emoney = emoney + $_POST[emoney] where m_no='" . $data[m_no] . "'";
					$db->query($query);
				}
				else if ($mode == "batch_level"){
					$query = "update ".GD_MEMBER." set level = $_POST[level] where m_no='" . $data[m_no] . "'";
					$db->query($query);
				}
				else if ($mode == "batch_status"){
					$query = "update ".GD_MEMBER." set status = $_POST[status] where m_no='" . $data[m_no] . "'";
					$db->query($query);
				}
			}
		}

		$_POST[returnUrl] = preg_replace("/func=[^&]*/", "func=" . str_replace("batch_", "", $mode), $_POST[returnUrl]);

		break;

	case "batch_sms":

		include "../../conf/config.php";
		include_once('../../lib/sms_sendlist.class.php');
		$sms_sendlist = new sms_sendlist();

		if($_POST['includeFail'] == 'N'){
			$failListArr = $sms_sendlist->getSmsFailListNumber($_POST['smsFailSnoList']);
		}

		// SMS 와 LMS 구분
		// SMS
		if($_POST['sms_type'] == 'sms'){
			$_POST['msg'] = $_POST['sms_msg'];

			$_POST['msg'] = str_replace("\r","",$_POST['msg']);
			$msg = parseCode($_POST['msg']);

			$query = stripslashes($_POST[query]);
			if ($_POST[type]=="select") {
				if($_POST['includeFail'] == 'N') $where = " and mobile NOT IN ('".implode("','", $failListArr)."') ";
				if($_POST['receiveRefuseType'] == 'N') $where .= " AND sms = 'y' "; // 수신거부 제외 N - 제외, Y or '' - 포함

				$query = "select * from ".GD_MEMBER." where m_no in (".implode(",",$_POST[chk]).")" . $where;
			}
			else {
				$query = preg_replace('/(order)(\s|\w|\D|\d)+/i', '', $query);
				if(preg_match('/where/i', $query)){
					$whereType = ' and ';
				}
				else {
					$whereType = ' WHERE ';
				}
				$query = $query . $whereType ." mobile != '' ";

				if($_POST['includeFail'] == 'N') $query .= " and mobile NOT IN ('" . implode("','", $failListArr) . "')";
				if($_POST['receiveRefuseType'] == 'N') $query .= " AND sms = 'y' "; // 수신거부 제외 N - 제외, Y or '' - 포함
			}

			$res = $db->query($query);
			$total = $db->count_($res);

			if($total < 1){
				msg('발송될 내역이 없습니다.');
				exit;
			}

			$total = $total * 1;

			if ($total>getSmsPoint()){
				msg("SMS 발송예정인 ".number_format($total)."건이 잔여콜수인 ".number_format(getSmsPoint())."건보다 많습니다");
				exit;
			}

			### SMS 발송
			include "znd_sms.php";

			$smspt = number_format($sms->smsPt);

			$msg = "SMS 발송요청 건수 : ".number_format(array_sum($num))."건 \\n ------------------- \\n 발송요청 : ".number_format($num[success])." / 발송요청실패 : ".number_format($num[fail]);

		// LMS
		} else if($_POST['sms_type'] == 'lms'){
			$_POST['subject'] = $_POST['lms_subject'];
			$_POST['msg'] = $_POST['lms_msg'];

			# LMS 내용 처리
			$_POST['subject'] = preg_replace ("/[\{\}\[\]\/?.,;:|\)*~`!^\-_+<>@\#$%&\\\=\(\'\"]/i", "",$_POST['subject']); //특수문자 제거
			$_POST['subject'] = str_replace("\r","",$_POST['subject']);
			$subject = parseCode($_POST['subject']);
			$_POST['msg'] = str_replace("\r","",$_POST['msg']);
			$msg = parseCode($_POST['msg']);

			$query = stripslashes($_POST[query]);
			if ($_POST[type]=="select") {
				if($_POST['includeFail'] == 'N') $where = " and mobile NOT IN ('".implode("','", $failListArr)."') ";
				$query = "select * from ".GD_MEMBER." where m_no in (".implode(",",$_POST[chk]).")" . $where;
			}
			else {
				$query = preg_replace('/(order)(\s|\w|\D|\d)+/i', '', $query);
				if(preg_match('/where/i', $query)){
					$whereType = ' and ';
				}
				else {
					$whereType = ' WHERE ';
				}
				$query .= $whereType ." mobile != '' ";

				if($_POST['includeFail'] == 'N') $query .= " and mobile NOT IN ('" . implode("','", $failListArr) . "')";
			}

			$res = $db->query($query);
			$total = $db->count_($res);

			if($total < 1){
				msg('발송될 내역이 없습니다.');
				exit;
			}

			// LMS 는 SMS 의 3배
			$total = $total * 3;

			if ($total>getSmsPoint()){
				msg("LMS 발송예정인 ".number_format($total)."건이 잔여콜수인 ".number_format(getSmsPoint())."건보다 많습니다");
				exit;
			}

			### LMS 발송
			include "znd_lms.php";

			$smspt = number_format($lms->smsPt);

			$msg = "LMS 발송요청 건수 : ".number_format(array_sum($num))."건 \\n ------------------- \\n 발송요청 : ".number_format($num[success])." / 발송요청실패 : ".number_format($num[fail]);
		}

		msg($msg);
		echo "<script>parent.document.getElementById('span_sms').innerHTML = '".$smspt."'; parent.document.getElementById('sms_bar').style.width = 0;</script>";

		exit;
		break;

	case "adminModify" :

		foreach($_POST[level] as $k => $v){
			$db->query("update ".GD_MEMBER." set level='".$v."' where m_no='$k'");
		}
		break;

	case "amailsetting" :
		@include "../../conf/amail.set.php";

		$set = (array)$set['amail'];
		$set = @array_map("stripslashes",$set);
		$set = @array_map("addslashes",$set);

		$_POST[user_tel] = @implode('-',$_POST[tel]);
		$_POST[user_cell] = @implode('-',$_POST[cell]);

		$tmp = array('user_id','user_cell','user_tel','user_email','user_name');
		foreach($_POST as $k => $v){
			if(in_array($k,$tmp)){
				$arr[$k] = $v;
			}
		}

		$arr = @array_merge($set,$arr);
		$qfile->open("../../conf/amail.set.php");
		$qfile->write("<?\n");
		$qfile->write("\$set['amail'] = array(\n");
		foreach($arr as $k => $v){
			$qfile->write("'".$k."' => '".$v."',\n");
		}
		$qfile->write(");\n");
		$qfile->write("?>");
		$qfile->close();
		@chmod("../../conf/amail.set.php",0707);

		break;

	case "setEmailAgree":
		unset($_POST['x'],$_POST['y']);
		@include "../../conf/mail.cfg.php";

		$mailCfg = (array)$mailCfg;
		$mailCfg = @array_map("stripslashes",$mailCfg);
		$mailCfg = @array_map("addslashes",$mailCfg);

		$mailCfg['agreeFlag'] = $_POST['set']['agreeFlag'];
		$mailCfg['agreeMsg'] = $_POST['set']['agreeMsg'];

		$qfile->open("../../conf/mail.cfg.php");
		$qfile->write("<? \n");
		$qfile->write("\$mailCfg = array( \n");
		foreach ($mailCfg as $k => $v) {
			$qfile->write("'".$k."' => '".$v."',\n");
		}
		$qfile->write("); \n");
		$qfile->write("?>");
		$qfile->close();
		@chmod("../../conf/mail.cfg.php",0707);

		msg('입력하신 정보가 저장되었습니다.');
		exit;
		break;

}

go($_POST[returnUrl]);

?>