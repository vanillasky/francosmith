<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Helper
 */

/**
 * GODO_helper_string
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Helper
 */
final class GODO_helper_string extends GODO_helper {


	/**
	 * 따옴표 처리한 문자열을 풉니다
	 * @param mixed $var
	 * @return void
	 */
	public function stripslashes(&$var) {

		if (is_array($var)) {
			foreach($var as $k=>$v) {
				if(is_array($var[$k])) $this->stripslashes($var[$k]);
				else $var[$k]=stripslashes($var[$k]);
			}
		}
		else {
			$var = stripslashes($var);
		}

		return $var;

	}

	/**
	 * 유니코드로 인코딩된 한글을 본래대로 수정 (&#46736; -> ㄸㅛㅁ 나누는게 아니고 합친거임. 깨져서;;)
	 * @param string $str
	 * @return string
	 */
	public function toHan($str) {

		$_fr = array(
			'~&#([0-9]+);~e',
			'~&#([0-9]+)~e',
		);
		$_to = array(
			'sprintf("&#x%X;",\\1)',
			'sprintf("&#x%X;",\\1)',
		);
		$str = preg_replace( $_fr, $_to, $str, -1, $_cnt );

		if ($_cnt > 0) {

			$charset = strtoupper( Core::config('global', 'charset') );

			if ($charset == 'EUC-KR')
				$charset = 'CP949';	// 확장 한글때문에 cp949

			if ($charset != 'UTF-8')
				$str = iconv($charset,'UTF-8',$str);

			$str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');

			if ($charset != 'UTF-8')
				$str = iconv('UTF-8', $charset, $str);

		}

		return $str;

	}

	/**
	 * 대문자를 소문자로 변경
	 * @param string $str
	 * @return
	 */
	public function strtolower($str) {

		$A = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$a = 'abcdefghijklmnopqrstuvwxyz';

		return strtr($str, $A, $a);

	}


	/**
	 * 한글 자소 분리 (아래 링크에서 분리 공식 참고)
	 * @see http://dream.ahboom.net/entry/한글-유니코드-자소-분리-방법
	 * @param string $str
	 * @return string
	 */
	public function splitKorean($str) {

		$_chars = array(
			'cho'  => array('ㄱ', 'ㄲ', 'ㄴ', 'ㄷ', 'ㄸ', 'ㄹ', 'ㅁ', 'ㅂ', 'ㅃ', 'ㅅ', 'ㅆ', 'ㅇ', 'ㅈ', 'ㅉ', 'ㅊ', 'ㅋ', 'ㅌ', 'ㅍ', 'ㅎ'),
			'jung' => array('ㅏ', 'ㅐ', 'ㅑ', 'ㅒ', 'ㅓ', 'ㅔ', 'ㅕ', 'ㅖ', 'ㅗ', 'ㅘ', 'ㅙ', 'ㅚ', 'ㅛ', 'ㅜ', 'ㅝ', 'ㅞ', 'ㅟ', 'ㅠ', 'ㅡ', 'ㅢ', 'ㅣ'),
			'jong' => array('', 'ㄱ', 'ㄲ', 'ㄳ', 'ㄴ', 'ㄵ', 'ㄶ', 'ㄷ', 'ㄹ', 'ㄺ', 'ㄻ', 'ㄼ', 'ㄽ', 'ㄾ', 'ㄿ', 'ㅀ', 'ㅁ', 'ㅂ', 'ㅄ', 'ㅅ', 'ㅆ', 'ㅇ', 'ㅈ', 'ㅊ', 'ㅋ', 'ㅌ', 'ㅍ', 'ㅎ')
		);

		$charset = strtoupper( Core::config('global', 'charset') );

		if ($charset == 'EUC-KR')
			$charset = 'CP949';

		// 16비트 유니코드로 변환
		$str = iconv($charset, "UTF-16LE", $str);

		$words = array();

		for ($i=0,$m=strlen($str);$i<$m;$i+=2) {	// 2 byte 이므로.

			$a = ord($str[$i+1]);
			$b = ord($str[$i]);

			$unicode = sprintf('0x%02X%02X', $a, $b);

			if ($unicode >= 0xAC00 && $unicode <= 0xD7A3) {	// 'ㄱㅏ' 부터 'ㅎㅣㅎ' 까지)

				// 분리
				$temp = $unicode - 0xAC00;

				$jong = $temp % 28;
				$jung = (($temp - $jong) / 28) % 21;
				$cho  = ((($temp - $jong) / 28) - $jung) / 21;

				$words[] = $_chars['cho'][$cho].$_chars['jung'][$jung].$_chars['jong'][$jong];

			}
			else {
				$words[] = iconv("UTF-16LE", $charset, $str[$i].($str[$i + 1] ? $str[$i + 1] : ''));
			}

		}

		unset($_chars, $a, $b, $unicode);

		return implode('',$words);

	}

}
?>
