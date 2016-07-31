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
 * GODO_helper_csv
 *
 * 사용 예제 :
 *
 * <code>
 *  $csv = Core::helper('CSV', $파일경로);
 *  $header = $csv->getHeader();	// 필드명 => 필드설명 의 배열을 리턴
 *
 *  foreach($csv as $row) {
 *  	// debug($row);
 *  }
 * </code>
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Helper
 */
final class GODO_helper_csv extends GODO_helper Implements Iterator {

    /**
     * 파일 포인터
     * @var resource
     */
	private $fp = null;

    /**
     * 파일 포인터내 본문 위치
     * @var integer
     */
	private $sp = 0;

    /**
     * fgetcsv 를 이용하여 한번에 읽어 들일 길이
     * @var integer
     */
	private $length = 135000;

    /**
     * CSV 파일의 헤더 (이나무 기준 상단 2줄에 해당됨)
     * @var array
     */
	private $header = array();

    /**
     * 현재 처리중인 본문 위치
     * @var integer
     */
	private $position = 0;

    /**
     * CSV 파일을 읽어 들이고, iterate 할 준비를 함
     * @param string $path
     * @return void
     */
	public function __construct( $path ) {

		if ( is_readable( $path ) ) {

			$this->fp = @fopen( $path, 'r' );

			$_vals = fgetcsv( $this->fp, $this->length );
			$_keys = fgetcsv( $this->fp, $this->length );

			$this->sp = ftell( $this->fp );

			$this->header = array_combine( array_map('trim', $_keys), array_map('trim', $_vals) );
			unset( $_vals, $_keys );

		}
		else {
			Core::raiseError( '불러올 CSV 파일이 없습니다.' );
		}

	}

    /**
     * 파일 포인터를 닫음
     * @return void
     */
	public function __destruct() {

		if ( $this->fp !== null)
			fclose( $this->fp );
	}

    /**
     * CSV 파일 헤더를 리턴
     * @return array
     */
	public function getHeader() {

		return $this->header;
	}

    // 아래 구현된 메서드는 꼼수 이므로, 실제 iterator 와는 조금 다릅니다
	public function rewind() {

		$this->position = 0;
		fseek( $this->fp, $this->sp );
	}

	public function current() {

		$this->position++;
		return fgetcsv( $this->fp, $this->length );
	}

	public function key() {

		return $this->position;
	}

	public function valid() {

		return feof( $this->fp ) ? false : true;
	}

	public function next() {

		//++$this->position;
	}

}
?>
