<?php

define('MCASH_CANCEL_LIB', true);

function McashCancel()
{
	global $gResultcd;

	global $gszErrMsg;


	if ( ($rtn = checkPacket()) != RTN_OK ) {

		switch ($rtn) {
			/* 거의 일어나지 않을 현상
			 - MCash 서버가 죽었을 경우 / Network 장애시  발생할 수 있음 */
			case ERR_CONNECT:
				$gResultcd = "0098";
				$gszErrMsg = "Mcash 통신장애 입니다.connect";
				break;
			case ERR_SEND:
				$gResultcd = "0098";
				$gszErrMsg = "Mcash 통신장애 입니다.send";
				break;
			case ERR_RECV:
				$gResultcd = "0098";
				$gszErrMsg = "Mcash 통신장애 입니다.recieve";
				break;
			case ERR_RECV2:
				$gResultcd = "0097";
				$gszErrMsg = "Mcash 결제정보 요청자료 오류 입니다.";
				break;

			default:
				$gResultcd = "0099";
				$gszErrMsg = "Mcash 결제 비정상 오류 입니다.".$rtn;
				break;
		}

		makeResult();
		return $gResultcd;
	}

	return $gResultcd;
}



function checkPacket()
{
	$retVal	= 0;
	$p_len	= 0;

	$SendData	= "";


	$p_len = make_CancelPacket(&$SendData);

	if ( $p_len != 84 ) {
		log_write( "len[".strlen($SendData)."]SendData[".$SendData."]\n" );
		return INVALID_CGI;
	}

	// open socket to filehandle 
	$fp = fsockopen( SERVER_NAME, SERVER_PORT, &$errno, &$errstr, 5 );
	
	if( !$fp ) { 
		return ERR_CONNECT;  // 인터넷 연결장애 
	} 


	log_write( "SendData[".$SendData."]" );
	if( ($retVal = send_recv($fp, $SendData, $p_len)) != RTN_OK ) {
		fclose( $fp );

		return (retVal);
	}
	fclose( $fp );

	return RTN_OK;
}



function send_recv($fp, $SendData, $nSendLen)
{
	global $gMrchid;
	global $gSvcid;
	global $gTradeid;
	global $gPrdtprice;
	global $gMobilid;

	global $gResultcd;

	
	$len = 0;
	$pos = 0;

	$tmp = "";
	$buff = "";


	fputs($fp, $SendData, $nSendLen);

	while( !feof( $fp ) ) { 
		$buff .= fgets($fp, 84);
	}

	// Packet Reject했을 경우
	log_write( "RecvData[".$buff."]\n" );
	if ( strlen($buff) != 84 ) {
		if( substr($buff, 0, 2) == "EE" ) {
			log_write( "RECV ERROR because SendData : Result=>len[".$len."]|buff[".$buff."]\n" );
			return ERR_RECV2;
		}
		log_write( "RECV ERROR Result=>len[".$len."]|buff[".$buff."]\n" );
		return ERR_RECV;
	}


	$pos = 0;
	$gMrchid = substr($buff, $pos, 8 );
	$pos += 8;

	$gSvcid = substr($buff, $pos, 12 );
	$pos += 12;

	$tmp = substr($buff, $pos, 40 );
	$gTradeid = rtrim($tmp);
	$pos += 40;

	$tmp = substr($buff, $pos, 10 );
	$gPrdtprice = rtrim($tmp);
	$pos += 10;

	$tmp = substr($buff, $pos, 10 );
	$gMobilid = rtrim($tmp);
	$pos += 10;

	$tmp = substr($buff, $pos, 4 );
	$gResultcd = rtrim($tmp);
	$pos += 4;

	makeResult();

	return ( RTN_OK );
}



/*===================================================================
 make SendData data
 결제 취소 요청을 위한 발송 SendData 생성
 ===================================================================*/
function make_CancelPacket(&$SendData)
{
	global $gMrchid;
	global $gSvcid;
	global $gTradeid;
	global $gPrdtprice;
	global $gMobilid;

	$pos = 0;
	$p_len = 0;


	$SendData = sprintf( "%-8s%-12s%-40s%-10d%-10s%-4s", $gMrchid, $gSvcid, $gTradeid, $gPrdtprice, $gMobilid, "");

//send:84
//recv:84

	$p_len = strlen($SendData);

	return $p_len;
}


function makeResult()
{
	global $gMrchid;
	global $gSvcid;
	global $gTradeid;
	global $gPrdtprice;
	global $gMobilid;
	global $gResultcd;
	global $gszErrMsg;

	log_write( "Result=>Mrchid[".$gMrchid."]|Svcid[".$gSvcid."]|Tradeid[".$gTradeid."]|Price[".$gPrdtprice."]|Mobilid[".$gMobilid."]|ResultCD[".$gResultcd."]|Msg[".$gszErrMsg."]" );
}


function log_write($logMsg)
{
  if( LOG_RUN == "YES" )
  {
    $logMsg = "[".getmypid()."]".date("H:i:s")." ==> ".$logMsg;
  	$file_name = "mcash_cancel";
  	$fp = "";
    if( $fp = @fopen(LOG_DIR."/".$file_name, "a+") )
    {
      @fwrite($fp,"$logMsg \n");
    }
    @fclose($fp);
  }
}


function log_dir()
{
  if( LOG_RUN == "YES" )
  {
    if(!@is_dir(LOG_DIR) )
    {
      if( @mkdir(LOG_DIR, 0777)){
		  @chmod(LOG_DIR, 0777);
		  return RTN_OK;
      }else{
        return RTN_ERR;
	  }
    }
    else
      return RTN_OK;
  }
  else
    return RTN_OK;
}

?>
