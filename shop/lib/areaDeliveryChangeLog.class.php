<?php
/**
 * 지역별 추가배송비 산출방식 변경이력 기록 클래스
 * @author pr
 */
class areaDeliveryChangeLog
{
	private $_godoConfig;
	private $_key = '4c17dc44fc3cd1aaed6395bd5c5b52f8';
	private $_mcryptKey = 'gudwnsdldhfmsWhr';

	public function __construct()
	{
		// 어트리뷰트 정의
		$this->_godoConfig = Core::loader('config')->load('godo');
	}

	/**
	 * 변경이력 전송
	 * @param string $areaDeliTypeAfter 산출방식 변경 후
	 * @param string $areaDeliTypeBefore 산출방식 변경 전
	 * @return string
	 */
	public function sendChangeLog($areaDeliTypeAfter, $areaDeliTypeBefore)
	{
		// 1) Godo sno 암호화
		$shopBasicSno = $this->setAesEncrypt($this->_godoConfig['sno']);

		// 2) 전송
		$curl_url = 'http://frequency.godo.co.kr/delivery/areaDeliChangeLogAPI.php';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $curl_url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
			'key'		=> $this->_key,
			'shopBasicSno'		=> $shopBasicSno,
			'areaDeliTypeAfter'	=> $areaDeliTypeAfter,
			'areaDeliTypeBefore'=> $areaDeliTypeBefore,
		)));
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	/**
	 * Data encode
	 * @param string $data data
	 * @return string
	 */
	private function setAesEncrypt($data)
	{
		$expected_length	= 16 * (floor(strlen($data) / 16) +1);
		$padding_length		= $expected_length - strlen($data);
		$data		= $data . str_repeat(chr($padding_length), $padding_length);
		$iv_size	= mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
		$iv			= mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$enc		= mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->_mcryptKey, $data, MCRYPT_MODE_ECB, $iv);

		return strtoupper(bin2hex($enc));
	}

	/**
	 * Data decode
	 * @param string $data data
	 * @return string
	 */
	private function aes_decrypt($data)
	{
		// bin2hex의 역 함수가 php는 없어서 새로 만듦
		if(!function_exists('hex2bin')){
			function hex2bin($h) {
				if (!is_string($h)) return null;
				$r='';
				for ($a=0; $a<strlen($h); $a+=2) { $r.=chr(hexdec($h{$a}.$h{($a+1)})); }
				return $r;
			}
		}

		$data = hex2bin($data);
		$iv_size	= mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
		$iv			= mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$dec		= mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->_mcryptKey, $data, MCRYPT_MODE_ECB,$iv);
		$last		= $dec[strlen($dec) - 1];
		$dec		= substr($dec, 0, strlen($dec) - ord($last));
		return $dec;
	}
}
?>