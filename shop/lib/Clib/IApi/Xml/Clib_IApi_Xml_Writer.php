<?php
/**
 * Simon Willison, 16th April 2003
 * Based on Lars Marius Garshol's Python XMLWriter class
 * See http://www.xml.com/pub/a/2003/04/09/py-xml.html
 *
 * Revision History
 * Author		Date		Description
 * ---------	---------	---------
 * dn			2013.01.30	Clib repack 및 encoding 지정 추가
 *
 */
class Clib_IApi_Xml_Writer
{
	var $xml;
	var $indent;
	var $stack = array();

	private $_encoding = '';

	public function __construct($encoding)
	{
		if ($encoding) {
			$this->_encoding = $encoding;
		}
		else {
			$this->_encoding = 'utf-8';
		}

		$this->indent = $indent;
		$this->xml = '<?xml version="1.0" encoding="' . $this->_encoding . '"?>' . "\n";
	}

	private function _indent()
	{
		for ($i = 0, $j = count($this->stack); $i < $j; $i++) {
			$this->xml .= $this->indent;
		}
	}

	public function push($element, $attributes = array())
	{
		$this->_indent();
		$this->xml .= '<' . $element;
		foreach ($attributes as $key => $value) {
			$this->xml .= ' ' . $key . '="' . htmlentities($value) . '"';
		}
		$this->xml .= ">\n";
		$this->stack[] = $element;
	}

	public function element($element, $content, $attributes = array())
	{
		$this->_indent();
		$this->xml .= '<' . $element;
		foreach ($attributes as $key => $value) {
			$this->xml .= ' ' . $key . '="' . htmlentities($value) . '"';
		}
		if (ctype_alnum($content) === false)
			$content = "<![CDATA[{$content}]]>";
		$this->xml .= '>' . $content . '</' . $element . '>' . "\n";
	}

	public function emptyelement($element, $attributes = array())
	{
		$this->_indent();
		$this->xml .= '<' . $element;
		foreach ($attributes as $key => $value) {
			$this->xml .= ' ' . $key . '="' . htmlentities($value) . '"';
		}
		$this->xml .= " />\n";
	}

	public function pop()
	{
		$element = array_pop($this->stack);
		$this->_indent();
		$this->xml .= "</$element>\n";
	}

	public function getXml()
	{
		return $this->xml;
	}

	public function act($element, $arr, $attri = array())
	{
		$cube = create_function('$n', 'return is_int($n) ? 1 : 0;');
		$b = array_map($cube, array_keys($arr));
		$onlyIntKey = (array_sum($b) == count($b) ? true : false);

		if ($onlyIntKey === false)
			$this->push($element, $attri);
		foreach ($arr as $k => $v) {
			if (is_array($v) === false && is_string($k)) {
				$this->element($k, $v);
			}
			else if (is_array($v) === true) {
				if (is_int($k))
					$this->act($element, $v, array('id' => ($k + 1)));
				else
					$this->act($k, $v);
			}
		}
		if ($onlyIntKey === false)
			$this->pop();
	}

}
