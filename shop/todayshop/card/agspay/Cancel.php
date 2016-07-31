<?php
/**********************************************************************************************
*
* 파일명 : AGS_cancel_ing.php
* 작성일자 : 2009/04/01
*
* 올더게이트 플러그인에서 리턴된 데이타를 받아서 소켓취소요청을 합니다.
*
* Copyright AEGIS ENTERPRISE.Co.,Ltd. All rights reserved.
*
**********************************************************************************************/

	/****************************************************************************
	*
	* [1] 라이브러리(AGSLib.php)를 인클루드 합니다.
	*
	****************************************************************************/
	require (dirname(__FILE__)."/lib/AGSLib.php");

	/****************************************************************************
	*
	* [2]. agspay4.0 클래스의 인스턴스를 생성합니다.
	*
	****************************************************************************/
	$agspay = new agspay40;


	/****************************************************************************
	*
	* [3] AGS_pay.html 로 부터 넘겨받을 데이타
	*
	****************************************************************************/

	/*공통사용*/
	//$agspay->SetValue("AgsPayHome","C:/htdocs/agspay");				//올더게이트 결제설치 디렉토리 (상점에 맞게 수정)
	$agspay->SetValue("AgsPayHome",$_SERVER['DOCUMENT_ROOT'].$data['rootDir'].'/log/agspay');			//올더게이트 결제설치 디렉토리 (상점에 맞게 수정)
	$agspay->SetValue("log","true");									//true : 로그기록, false : 로그기록안함.
	$agspay->SetValue("logLevel","INFO");								//로그레벨 : DEBUG, INFO, WARN, ERROR, FATAL (해당 레벨이상의 로그만 기록됨)
	$agspay->SetValue("Type", "Cancel");								//고정값(수정불가)
	$agspay->SetValue("RecvLen", 7);									//수신 데이터(길이) 체크 에러시 6 또는 7 설정.

	$agspay->SetValue("StoreId",trim($data["StoreId"]));				//상점아이디
	$agspay->SetValue("AuthTy",'card');									//결제형태
	$agspay->SetValue("SubTy",trim($data["SubTy"]));					//서브결제형태
	$agspay->SetValue("rApprNo",trim($data["rApprNo"]));				//승인번호
	$agspay->SetValue("rApprTm",trim($data["rApprTm"]));				//승인일자
	$agspay->SetValue("rDealNo",trim($data["rDealNo"]));				//거래번호
	if (empty($data["cancelPrice"]) === false) {
		$agspay->SetValue("cancelPrice",trim($data["cancelPrice"]));	//취소금액
	}

	/****************************************************************************
	*
	* [4] 올더게이트 결제서버로 결제를 요청합니다.
	*
	****************************************************************************/
	echo ($agspay->startPay());

	/****************************************************************************
	*
	* [5] 취소요청결과에 따른 상점DB 저장 및 기타 필요한 처리작업을 수행하는 부분입니다.
	*
	* 신용카드결제 취소결과가 정상적으로 수신되었으므로 DB 작업을 할 경우
	* 결과페이지로 데이터를 전송하기 전 이부분에서 하면된다.
	*
	* 여기서 DB 작업을 해 주세요.
	* 취소성공여부 : $agspay->GetResult("rCancelSuccYn") (성공:y 실패:n)
	* 취소결과메시지 : $agspay->GetResult("rCancelResMsg")
	*
	****************************************************************************/

	if($agspay->GetResult("rCancelSuccYn") == "y")
	{
		$settlelog .= '올더게이트 카드 취소 결과'."\n";
		$settlelog .= "결과코드: ".$agspay->GetResult("rCancelSuccYn")."\n";
		$settlelog .= "결과메세지: ".$agspay->GetResult("rCancelResMsg");
		$cardCancelResult = true;
	}
	else
	{
		// 결제실패에 따른 상점처리부분
		$settlelog = "\n----------------------------------------\n";
		$settlelog .= "올더게이트 카드 취소 결과\n";
		$settlelog .= "$ordno (".date('Y:m:d H:i:s').")\n";
		$settlelog .= "결과코드: ".$agspay->GetResult("rCancelSuccYn")."\n";
		$settlelog .= "결과메세지: ".$agspay->GetResult("rCancelResMsg")."\n";
		$settlelog .= "----------------------------------------\n";
		$cardCancelResult = false;
	}
?>