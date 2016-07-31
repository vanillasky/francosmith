<?php
//이 페이지는 수정하지 마십시요. 수정시 html태그나 자바스크립트가 들어가는 경우 동작을 보장할 수 없습니다

//hash데이타값이 맞는 지 확인 하는 루틴은 세틀뱅크에서 받은 데이타가 맞는지 확인하는 것이므로 꼭 사용하셔야 합니다
//정상적인 결제 건임에도 불구하고 노티 페이지(card_return)의 오류나 네트웍 문제 등으로 인한 hash 값의 오류가 발생할 수도 있습니다.
//그러므로 hash 오류건에 대해서는 오류 발생시 원인을 파악하여 즉시 수정 및 대처해 주셔야 합니다.
//그리고 정상적으로 data를 처리한 경우에도 세틀뱅크에서 응답을 받지 못한 경우는 결제결과가 중복해서 나갈 수 있으므로 관련한 처리도 고려되어야 합니다. 
//(PTrno 가 PAuthDt의 일자(8자리)에 대해 unique 하니 PTrno로 체크 해주세요) 

	// 회원사 callback function page
	include "../../../lib/library.php";
	include "../../../conf/config.php";
	include "../../../conf/pg.settlebank.php";
	include "./callback.php";

	//세틀뱅크 noti server에서 받은 value
	$P_STATUS;	  // 거래상태 : 0021(성공), 0031(실패), 0051(입금대기중)
	$P_TR_NO;     // 거래번호
	$P_AUTH_DT;   // 승인시간
	$P_AUTH_NO;   // 승인번호
	$P_TYPE;      // 거래종류 (CARD, BANK)
	$P_MID;       // 회원사아이디
	$P_OID;       // 주문번호
	$P_FN_CD1;    // 금융사코드1 (은행코드, 카드코드)
	$P_FN_CD2;    // 금융사코드2 (은행코드, 카드코드)
	$P_FN_NM;     // 금융사명 (은행명, 카드사명)
	$P_UNAME;     // 주문자명
	$P_AMT;       // 거래금액
	$P_NOTI;      // 주문정보
	$P_RMESG1;    // 메시지1
	$P_RMESG2;    // 메시지2
	$P_HASH;      // NOTI HASH 코드값
	
	$resp = false;

	$P_STATUS = get_param(PStateCd);
	$P_TR_NO = get_param(PTrno);
	$P_AUTH_DT = get_param(PAuthDt);
	$P_AUTH_NO = get_param(PAuthNo);
	$P_TYPE = get_param(PType);
	$P_MID = get_param(PMid);
	$P_OID = get_param(POid);
	$P_FN_CD1 = get_param(PFnCd1);
	$P_FN_CD2 = get_param(PFnCd2);
	$P_FN_NM = get_param(PFnNm);
	$P_UNAME = get_param(PUname);
	$P_AMT = get_param(PAmt);
	$P_NOTI = get_param(PNoti);
	$P_RMESG1 = get_param(PRmesg1);
	$P_RMESG2 = get_param(PRmesg2);
	$P_HASH = get_param(PHash);

	/* mid가 mid_test인 경우에 사용합니다
	   해당 회원사 id당 하나의 auth_key가 발급됩니다
	   발급받으신 auth_key를 설정하셔야 합니다 */
	$PG_AUTH_KEY = $pg['key'];    

	$md5_hash = md5($P_STATUS.$P_TR_NO.$P_AUTH_DT.$P_TYPE.$P_MID.$P_OID.$P_AMT.$PG_AUTH_KEY); 

	$value = array("P_STATUS"  => $P_STATUS,
                   "P_TR_NO"   => $P_TR_NO,  
                   "P_AUTH_DT" => $P_AUTH_DT,      
                   "P_TYPE"    => $P_TYPE,     
                   "P_MID"     => $P_MID,  
                   "P_OID"     => $P_OID,  
                   "P_FN_CD1"  => $P_FN_CD1,
                   "P_FN_CD2"  => $P_FN_CD2,
                   "P_FN_NM"   => $P_FN_NM,  
                   "P_UNAME"   => $P_UNAME,  
                   "P_AMT"     => $P_AMT,  
                   "P_NOTI"    => $P_NOTI,  
                   "P_RMESG1"  => $P_RMESG1,  
                   "P_RMESG2"  => $P_RMESG2,
                   "P_AUTH_NO" => $P_AUTH_NO,
                   "P_HASH"    => $P_HASH,
                   "HashData"  => $md5_hash );

	//관련 db처리는 callback.asp의 noti_success(),noti_failure(),noti_hash_err()에서 관련 루틴을 추가하시면 됩니다
	//각 함수 호출시 값은 배열로 전달되도록 되어 있으므로 처리시 주의하시기 바랍니다.
	//위의 각 함수에는 현재 결제에 관한 log남기게 됩니다. 회원사서버에서 남기실 절대경로로 맞게 수정하여 주세요

	if ($md5_hash == $P_HASH) {
		
		if(forge_order_check($P_OID, $P_AMT) === false){
			$resp = false;
		}else if($P_TYPE != 'VBANK' && $P_STATUS == "0021"){
			$resp = noti_success($value);
		}else if($P_TYPE == 'VBANK' && $P_STATUS == "0021"){
			$resp = noti_vbanksuccess($value);
		}else if($P_TYPE == 'VBANK' && $P_STATUS == "0051"){
			$resp = noti_waiting_pay($value);
		}else if($P_STATUS == "0031"){
			$resp = noti_failure($value);
		}else{
			$resp = false;
		}

	}
	else {
			$resp = noti_hash_err($value);
	}
   
        //세틀뱅크로 전송되어야 하는 값이므로 삭제하지 마세요.
   	if($resp === true){
   		echo "OK";
   	}else if($resp === false){
   		echo "CANC";
   	}
?>
