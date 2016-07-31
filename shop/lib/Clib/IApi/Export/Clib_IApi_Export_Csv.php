<?php
/**
 * Clib_IApi_Export_Csv
 * @author extacy @ godosoft development team.
 */
class Clib_IApi_Export_Csv
{
	private $_fileHandler = null;

	private $_temporaryPath = '';

	private $_completePath = '';

	private $_header = array();

	private $_fileName = '';

	public function __construct()
	{
		$this->_temporaryPath = dirname(__FILE__) . '/../../../../data/download/';
		$this->_completePath = $this->_temporaryPath . 'complete/';
	}

	/**
	 *
	 * @param array $header
	 * @return
	 */
	public function setHeader($header)
	{
		if ( ! is_array($header)) {
			//
		}

		$this->_header = $header;

	}

	public function setFileName($name)
	{
		$this->_fileName = $name;

	}

	public function open()
	{
		// 열린게 있으면 닫고, 다시 연다.
		if ($this->_hasFileHandler()) {
			$this->_delFileHandler();
		}

		$this->_setFileHandler();

		// utf-8 csv 는 엑셀에서 깨지므로 bom 삽입.
		$this->_writeBom();
	}

	public function close()
	{
		// 열린게 있으면 닫고, 파일을 완료 디렉토리로 복사
		if ($this->_hasFileHandler()) {
			$this->_delFileHandler();

			// 파일 복사;
			$this->_copy();
		}
	}

	public function writeHeader()
	{
		@fputcsv($this->_getFileHandler(), array_keys($this->_header));
		@fputcsv($this->_getFileHandler(), array_values($this->_header));
	}

	private function _writeBom()
	{
		@fputs($this->_getFileHandler(), "\xEF\xBB\xBF");
	}

	public function write($row)
	{
		@fputcsv($this->_getFileHandler(), $this->_getRow($row));
	}

	private function _copy()
	{
		@rename($this->_temporaryPath . $this->_fileName, $this->_completePath . $this->_fileName . '.csv');
		@chmod($this->_completePath . $this->_fileName . '.csv', 0777);
	}

	private function _getRow($row)
	{
		// array_intersect_key 는 값이 없으면 키가 날아가므로 아래 처럼 구현
		$_row = array();

		foreach ($this->_header as $k) {
			$_row[$k] = $row[$k];
		}

		return $_row;
	}

	private function _hasFileHandler()
	{
		return ! is_null($this->_fileHandler);
	}

	private function _setFileHandler()
	{
		$path = $this->_temporaryPath . $this->_fileName;
		$this->_fileHandler = @fopen($path, 'w');
	}

	private function _getFileHandler()
	{
		return $this->_fileHandler;

	}

	private function _delFileHandler()
	{
		@fclose($this->_getFileHandler());
		$this->_fileHandler = null;
	}

}
