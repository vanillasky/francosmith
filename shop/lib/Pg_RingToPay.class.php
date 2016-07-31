<?

include "../lib/json.class.php";


class Pg_RingToPay
{
	var $rpay_yn;
	var $godo;

	function Pg_RingToPay() 
	{
		/**************************************
		rpay_yn = Y (링투페이 사용)
		rpay_yn = N (링투페이 사용하지 않음)
		***************************************/
		$this->rpay_yn = 'N';
	
		//godo솔루션정보
		$config = Core::loader('config');
		$this->godo = $config->load('godo');
	}
	
	//링투페이 설정값을 읽어온다.
	function ringtopayConfig()
	{
		@include "../conf/pg.settlebank.ringtopay.php";
		
		// 값이 없다면 기본값을 준다.
		if(!is_array($rpay)) {
			$rpay['state'] = 'N';
			$rpay['date'] =  date( 'YmdHis', strtotime($rpay['date'])+1800);
		}

		return $rpay;
	}
	
	function RtoPConfigRead(){

		//$json class 로드한다.
		$json = new Services_JSON();

		//저장 링투페이 설정 을 읽어온다.
		$rpay = $this->ringtopayConfig();

		//설정 시간에 30분을 더해 설정값의 사간을 알아온다.
		$old_date = date( 'YmdHis', strtotime($rpay['date'])+1800);
		
		//저장시간이 30분이 지났다면 새로읽어온다.
		if(date("YmdHis") >= $old_date ) {

			//godo 서버로부터 새설정을 읽어온다.
			$new_rpay =  get_object_vars($json->decode(stripslashes($this->url_Reader())));

			//쇼핑몰 고유번호와 가져온 설정값의 고유번호가 같은지 확인한다.
			if($new_rpay['basic_sno'] == $this->godo['sno']) {

				//새로운 설정을 저장한다.
				$ret['state'] = $new_rpay['rpay_yn'];
				$ret['date'] = date('YmdHis');
				$this->save("../conf/pg.settlebank.ringtopay.php", "rpay", $ret);

				//새로운 설정값을 읽어 온다.
				$rpay = $this->ringtopayConfig();

			}
		}

		$this->rpay_yn = $rpay['state'];
		
	}
	
	function getRpay_yn()
	{	
		return $this->rpay_yn;
	}

	//godo사이트로부터 ringtopay 사용여부 정보를 읽어온다.
	//해당URl에서 html값을 읽어온다.
	function url_Reader()
	{
		$url = "http://sapi.godo.co.kr/pg/policy/get_ring2pay_policy.php";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		//정보 조회
		$ret = curl_exec($ch);
		
		//에러 처리
		if( curl_error($ch) || $ret == false){
			$ret = false;
		}
		
		//curl 세션닫기
		curl_close($ch);
		
		return $ret;
	} 
	
	//파일저장 
	function save($configFile, $configName, $cfgContent)
	{	
		$qfile = new qfile();
		$qfile->open($configFile);
		$qfile->write("<? \n");
		
		foreach ($cfgContent as $k=>$v){
			//1차배열
			if (!is_array($v)) {
				$qfile->write("\$".$configName."['".$k."'] = '".$v."'; \n");

			//2차배열
			} else {
				foreach ($v as $k2=>$v2){
					$qfile->write("\$".$configName."['".$k."']['".$k2."'] = '".$v2."'; \n");
				}
			}
		}
		
		$qfile->write("?>");
		$qfile->close();

		//파일 권한설정
		chmod($configFile,0707);
	}

}
?>