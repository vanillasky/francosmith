<?
// Simon Willison, 16th April 2003
// Based on Lars Marius Garshol's Python XMLWriter class
// See http://www.xml.com/pub/a/2003/04/09/py-xml.html

class XmlWriter_py {
	var $xml;
	var $indent;
	var $stack = array();
	function XmlWriter_py($indent = '') {
		$this->indent = $indent;
		$this->xml = '<?xml version="1.0" encoding="euc-kr"?>'."\n";
	}
	function _indent() {
		for ($i = 0, $j = count($this->stack); $i < $j; $i++) {
			$this->xml .= $this->indent;
		}
	}
	function push($element, $attributes = array()) {
		$this->_indent();
		$this->xml .= '<'.$element;
		foreach ($attributes as $key => $value) {
			$this->xml .= ' '.$key.'="'.htmlentities($value).'"';
		}
		$this->xml .= ">\n";
		$this->stack[] = $element;
	}
	function element($element, $content, $attributes = array()) {
		$this->_indent();
		$this->xml .= '<'.$element;
		foreach ($attributes as $key => $value) {
			$this->xml .= ' '.$key.'="'.htmlentities($value).'"';
		}
		if (ctype_alnum($content) === false) $content = "<![CDATA[{$content}]]>";
		$this->xml .= '>'.$content.'</'.$element.'>'."\n";
	}
	function emptyelement($element, $attributes = array()) {
		$this->_indent();
		$this->xml .= '<'.$element;
		foreach ($attributes as $key => $value) {
			$this->xml .= ' '.$key.'="'.htmlentities($value).'"';
		}
		$this->xml .= " />\n";
	}
	function pop() {
		$element = array_pop($this->stack);
		$this->_indent();
		$this->xml .= "</$element>\n";
	}
	function getXml() {
		return $this->xml;
	}
	function act($element, $arr, $attri=array()) {
		$cube = create_function('$n', 'return is_int($n) ? 1 : 0;');
		$b = array_map($cube, array_keys($arr));
		$onlyIntKey = (array_sum($b) == count($b) ? true : false);

		if ($onlyIntKey === false) $this->push($element, $attri);
		foreach ($arr as $k => $v) {
			if (is_array($v) === false && is_string($k)){
				$this->element($k, $v);
			}
			else if (is_array($v) === true){
				if (is_int($k)) $this->act($element, $v, array('id' => ($k+1)));
				else $this->act($k, $v);
			}
		}
		if ($onlyIntKey === false) $this->pop();
	}
}

/*
$xml = new XmlWriter();
$array = array(
	'item' => array(
		array('code'=>'001','name'=>'tom','etc'=>'hi!','do not',),
		array('code'=>'002','name'=>'alice','etc'=>'nice!',),
		'do not',
	)
);

$xml->act('result', $array);

print $xml->getXml();

//-----------------------------
//<?xml version="1.0" encoding="utf-8"?>
//		<result>
//		  <item>
//		    <code>001</code>
//		    <name>tom</name>
//		    <etc>hi!</etc>
//		  </item>
//		  <item>
//		    <code>002</code>
//		    <name>alice</name>
//		    <etc>nice!</etc>
//		  </item>
//		</result>
//----------------------------
*/

?>