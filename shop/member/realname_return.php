<?

include "../lib/library.php";
include "../conf/config.php";

header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");

/* 실명인증 결과 메시지 정의 *************************************************/
$msg = array();

$msg[2] = "[실명 확인 오류]
입력하신 주민번호와 성명이 일치하지 않습니다.

◎ 에러메세지가 뜨는 경우
실명확인에 사용되는 정보는 재정경제부로부터 신용조회업을 허가 받은 서울신용평가정보(주)의 DB를 사용합니다.
이러한 신용정보는 해당 기관(업체)들이 고객과의 상거래(신용카드 발급 등) 발생시 취합되는 정보들인데,
정보를 입력하는 과정에서 주민번호 및 성명정보를 잘못 기입하는 경우가 있습니다.
(예) 라가명→나가명, 나불량→나불양, 홍길동→홍길도

기존가입자 및 신규회원 가입시, 본인이 실명임에도 불구하고 일치하지 않는다고 나타나는 경우는
대부분 이러한 이유이므로, 저희와 계약된 서울신용평가정보(주) SIREN24로 문의하시면 정보를 수정토록 하겠습니다.
* 확인자료:실명확인 신청서(본인의 신분증 사본 첨부-주민등록증, 운전면허증,여권,의료보험증,주민등록초본 중 택1)
* 복사는 흐리게하여 보내주시기 바랍니다.

보내주신 서류는 단순한 실명확인 외 다른 목적으로 사용되지 않사오니 안심하시기 바랍니다.
";


$msg[4] = "[실명 확인 오류]
주민등록번호 조합 오류입니다.
다시 한번 주민등록번호를 확인하신 후 입력하여 주시기 바랍니다.";


$msg[5] = "[실명 확인 오류]
시스템 오류입니다.
잠시 후 다시 한번 시도하여주시기 바랍니다.";


foreach( $msg as $k => $v ) $msg[$k] = str_replace( "\n", "\\n", $v );
/*---------------------------------------------------------------------------*/


if ( $_POST[result]==2 || $_POST[result]==3 || $_POST[result]==4 || $_POST[result]==5 ){ # 초기화
	echo "
	<script>
	parent.document.frmAgree['name']. value = '';
	parent.document.frmAgree['resno[]'][0]. value = '';
	parent.document.frmAgree['resno[]'][1]. value = '';
	</script>
	";
}

switch ( $_POST[result] ){
	case 1:
		echo "
		<script>
		parent.document.frmAgree.action = '';
		parent.document.frmAgree.target = '';
		parent.document.frmAgree.submit();
		</script>
		";
		break;

	case 2:
	case 3:
		msg($msg[2]);
		break;

	case 4:
		msg($msg[4]);
		break;

	case 5:
		msg($msg[5]);
		break;
}

?>