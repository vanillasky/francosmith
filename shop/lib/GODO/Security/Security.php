<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Security
 */

/**
 * Security
 * @see http://ha.ckers.org/xss.html
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Security
 */
class Security {

	/**
	 * 운영체제
	 * @var string
	 */
	private $PHP_OS;

	/**
	 * 검사 대상
	 * @var array
	 */
	private $_target;

	/**
	 * 검사 결과
	 * @var array
	 */
	private $_result;

	/**
	 * 토큰 설정
	 * @var array
	 */
	private $token_cfg= array();

	/**
	 * 실행 가능한 파일 확장자 (binary 파일 제외)
	 * @var array
	 */
	private $_excutableFileExtension = array('asa', 'asax', 'asp', 'aspx', 'cgi', 'htm', 'html', 'inc', 'js', 'jsp', 'php', 'php3', 'phtm', 'pl', 'pm', 'py', 'sh');

	/**
	 * Construct
	 * 검사 대상 및 OS 설정
	 * @return void
	 */
	public function __construct() {

		// 서버의 운영체제
		if ( strpos( strtoupper( PHP_OS ), 'WIN' ) === 0)
			$this->_PHP_OS = 'WIN';
		else
			$this->_PHP_OS = 'LINUX';

		// GET, POST, COOKIE, FILES 에 대해서 실행
		$this->_target['GET'] = true;
		$this->_target['POST'] = true;
		$this->_target['COOKIE'] = true;
		$this->_target['FILES'] = true;

		// 토큰 설정
		$this->token_cfg = array(
			'use' => session_id() == '' ? false : true,
			'pipe' => '|',
			'name' => '_gd_security_tk_',
		);

	}

	/**
	 * 검사
	 * @return void
	 */
	public function detect() {

        // temporary not use.
        return ;

		$this->detectThreat();

	}

	/**
	 * 감지 결과를 출력
	 * @return void
	 */
	public function result() {

		if ( empty( $this->_result ))
			return;

		ob_start();

		echo '
		<style>
		.gd-security-result-table {border-collapse:collapse;}
		.gd-security-result-table th,
		.gd-security-result-table td {font-family:Bitstream Vera Sans Mono,Dotum;font-size:11px;padding:5px;}
		.gd-security-result-table tr.threat {background:#FABCB0}
		.gd-security-result-table tr.safe {background:#C6F6C4}
		</style>
		<table width="100%" border="1" bordercolor="#E6E6E6" class="gd-security-result-table">
		<tr>
			<th>type</th>
			<th>input (html special chars)</th>
			<th>threat</th>
		</tr>';

		foreach ( $this->_result as $log ) {
			echo '
			<tr class="' . ( $log['type'] != 'none' ? 'threat' : 'safe' ) . '">
				<td>' . $log['type'] . '</td>
				<td>' . htmlspecialchars( $log['value'] ) . '</td>
				<td>' . htmlspecialchars( $log['founded'] ) . '</td>
			</tr>';
		}

		echo '</table>';

		$_html = ob_get_contents();
		ob_end_clean();

		return $_html;

	}

	/**
	 * 현재 페이지가 프로세스 페이지 인지 체크
	 * 솔루션내 프로세스 페이지는 indb 를 포함하는 것으로 간주
	 * @return boolean
	 */
	private function isWritePage() {

		$_php = basename( $_SERVER['PHP_SELF'] );
		$_php = explode('.', $_php);
		array_pop($_php);
		$_php = implode('.',$_php);

		return
		  (strpos($_php,'indb') !== false)
		? true
		: false;
	}

	/**
	 * 검사가 필요없는 페이지 인지 체크
	 * @param string $path [optional]
	 * @return boolean
	 */
	private function isWhiteList( $path = '' ) {

		if ( $path == '' )
			$path = $_SERVER['PHP_SELF'];

		$whitelist = array(
			/*/shop*/ '/admin/proc/indb.php',
			/*/shop*/ '/_godoConn/.+',
			/*/shop*/ '/_spt_service/.+',
			/*/shop*/ '/proc/.+',
			/*/shop*/ '/partner/.+',
			/*/shop*/ '/engine/.+',
			/*/shop*/ '/order/ipay_order_indb.php',
			/*/shop*/ '/order/card/.+',					// pg 결제 처리
			/*/shop*/ '/todayshop/card/.+',				// pg 결제 처리
		);

		foreach ( $whitelist as $item ) {
			$pattern = '/^([a-zA-Z0-9_\/]+)?' . str_replace( array(	'\\', '/' ), '\/', $item ) . '$/';
			if ( preg_match( $pattern, $path ))
				return true;
		}

		return false;
	}

	/**
	 * CSRF (cross site request forgery) 를 감지할 토큰 생성
	 * @return void
	 */
	private function setToken() {

		$_SESSION[ $this->token_cfg['name'] ] = $this->getClientHash();

	}

    /**
     * 클라이언트의 고유한(?) 값을 생성하여 리턴
     * @return string
     */
	private function getClientHash() {

		$tmp = array();
		$tmp[] = session_id();
		$tmp[] = $_SERVER['HTTP_USER_AGENT'];

		return md5( implode( $this->token_cfg['pipe'], $tmp ) );

	}

	/**
	 * 프로세스 페이지로 직접 호출인지 체크
	 * @return void
	 */
	private function checkToken() {

		if (!$_SESSION[ $this->token_cfg['name'] ] || ($_SESSION[ $this->token_cfg['name'] ] != $this->getClientHash())) {
			$msg = sprintf( '%s request is not valid.', $_SERVER['PHP_SELF'] );
			Core::raiseError($msg);
		}

	}

	/**
	 * 검사 대상에 위험 요소가 있는지 체크
	 * @return void
	 */
	private function detectThreat() {

		if ($this->token_cfg['use']) {

			if ( $this->isWritePage() ) {

				if (!$this->isWhiteList())
					 $this->checkToken();
			}
			else {
				$this->setToken();
			}

		}

		foreach ( $this->_target as $_target=>$status ) {

			if ( $status === false)
				continue;

			@eval( '$V = &$_' . strtoupper( $_target ) . ';' );

			if ( $_target === 'FILES')
				$this->scanFile( $V );
			else
				$this->scanString( $V );

		}

	}

	/**
	 * 위험요소 체크
	 * @param string $value
	 * @param string $patterns
	 * @return mixed 위험 요소가 있을시 해당 array, 없을시 false
	 */
	private function testThreat( $value, $patterns ) {

		$value = $this->getTestString( $value );

		foreach ( $patterns as $regexp)
			if ( preg_match( $regexp, $value, $matches ))
				return $matches;

		return false;

	}

	/**
	 * 파일의 위험요소 체크
	 * @param string $var
	 * @return void
	 */
	private function scanFile( $var ) {

		if ( is_array( $var ) ) {

			foreach ( $var as $val ) {

				$is_threat = false;

				if ( $val['error'] === 0) {
					if ( $matches = $this->isExcutableFile( $val['name'] )) $is_threat = true;
				}

				$this->throwThreat( 'file', $val['name'], $matches, $is_threat );

			}

		}

	}

	/**
	 * 문자열의 위험요소 체크
	 * @param string $var
	 * @return void
	 */
	private function scanString( $var ) {

		if ( is_array( $var ) ) {

			foreach ( $var as $val ) {

				if ( empty( $val ))
					continue;
				elseif ( is_array( $val ) ) {
					$this->scanString( $val );
					continue;
				}

				$is_threat = false;

				foreach ( $this->getThreatPatterns() as $type=>$patterns ) {

					if ( ( $matches = $this->testThreat( $val, $patterns ) ) !== false ) {

						switch ( $type ) {
							case 'img' :
								if ( $this->isExcutableFile( $matches[1] ) ) {
									$is_threat = true;
									break ( 2 );
								}
								break;

							case 'path' :
								if ( $this->isSystemPath( $matches[0] ) ) {
									$is_threat = true;
									break ( 2 );
								}
								break;

							default :
								$is_threat = true;
								break ( 2 );
						}

					}

				}

				$this->throwThreat( $type, $val, $matches, $is_threat );

			}

		}

	}

	/**
	 * 경로가 시스템 경로인지 체크
	 * @param string $path
	 * @return boolean
	 */
	private function isSystemPath( $path ) {

		if ( $this->_PHP_OS == 'WIN')
			$endslash_pattern = '/\\$/';
		else
			$endslash_pattern = '/\/$/';

		$doc_root = isset( $_SERVER['DOCUMENT_ROOT'] ) ? $_SERVER['DOCUMENT_ROOT'] : $_SERVER['HOME'];
		$doc_root .= preg_match( $endslash_pattern, $doc_root ) ? DIRECTORY_SEPARATOR : '';

		$_path = $doc_root . dirname( $_SERVER['PHP_SELF'] ) . DIRECTORY_SEPARATOR . $path;

		$_path = realpath( $_path );

		return ( strlen( $_path ) < strlen( $doc_root ) ) ? true : false;

	}


	/**
	 * 파일 확장자를 이용하여 실행형 파일인지 체크
	 * @param string $filepath
	 * @return mixed
	 */
	private function isExcutableFile( $filepath ) {

		$_path = parse_url( $filepath );

		if ( preg_match( '/\.(' . implode( '|', $this->_excutableFileExtension ) . ')$/i', $_path['path'], $matches ) ) {
			return $matches;
		}
		/*else if (preg_match('/\.('.implode('|', $this->_excutableFileExtension).')/i', $_path['path'], $matches)) {
		 return $matches;
		 }*/
		else {
			return false;
		}

	}

	/**
	 * 위험요소를 체크할 문자열을 리턴
	 * @param string $str
	 * @return string
	 */
	private function getTestString( $str ) {

		$str = G_CONST_MAGIC_QUOTES ? stripslashes( $str ) : $str;

		// url decode
		$str = urldecode( $str );
		$str = rawurldecode( $str );

		// &xxxx 치환
		$str = strtr( $str, array_flip( get_html_translation_table( HTML_SPECIALCHARS /* HTML_SPECIALCHARS or HTML_ENTITIES*/ ) ) );

		// CR, LF, &#xnn, &#nn, /* */ 치환
		$_source_pattern = array(
			'#\/\*.*\*\/#Ui',
			'/\x00/',
			'/\x0a/',
			'~&#x([0-9a-f]+);~ei',
			'~&#x([0-9a-f]+)~ei',
			'~&#([0-9]+);~e',
			'~&#([0-9]+)~e'
		);
		$_target_pattern = array(
			'',
			'',
			'',
			'chr(hexdec("\\1"))',
			'chr(hexdec("\\1"))',
			'chr("\\1")',
			'chr("\\1")'
		);
		$str = preg_replace( $_source_pattern, $_target_pattern, $str );

		return $str;

	}

	/**
	 * 위험요소를 감지할 정규 표현식을 리턴
	 * @return array 위험요소를 감지할 정규 표현식
	 */
	private function getThreatPatterns() {

		static $patterns = array();

		if ( empty( $patterns ) ) {

			$patterns['tag'] = array(
				'/<iframe[[:space:]]*/i',
				'/<meta[[:space:]]*/i'
			);

			$patterns['xss'] = array(
				'/expression[[:space:]]*/',
				'/window\.open[[:space:]]*\(/',
				'/location.href[[:space:]]*=/',
				'/xss\:*\(.+\)/',
				'/document\.cookie/',
				'/document\.location/',
				'/document\.write/',
				'/<script/i',
				'/%3script/',
				'/\x3script/i',
				'/javascript:/i',
				'/(on)([a-z]+)[[:space:]]*=/i'
			);

			$patterns['img'] = array(
				'@<img.+src="(.+)".*>@Ui'
			);

			$patterns['path'] = array(
				'/(\.\.\/)+/',
				'/(\.\.\\\)+/'
			);

		}

		return $patterns;

	}

	/**
	 * 위험 요소 발견시 처리
	 * @param string $type
	 * @param string $value
	 * @param array $matches
	 * @param boolean $is_threat
	 * @return void
	 */
	private function throwThreat( $type, $value, $matches, $is_threat ) {

		if ( !$is_threat)
			return;

		// logging.
		$tmp = array(
			'type'=>$is_threat ? $type : 'none',
			'value'=>$value,
			'founded'=>$is_threat ? $matches[0] : '',
			'threat'=>$is_threat ? 'true' : 'false'
		);

		$this->_result[] = $tmp;

		// action.
		// what to do ?
		/*$msg = "사용할 수 없는 문자열이 포함되어 있습니다.\\n검출 문자열 : ".addslashes($matches[0]);
		 msg($msg, -1);*/

	}

}
?>
