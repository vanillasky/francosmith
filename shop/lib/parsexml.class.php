<?
class XMLParser {

	var $parser;
	var $out;
	var $encoding = "ISO-8859-1";
	var $data;

	function XMLParser(){
		$this->_create();
	}

	function _create(){
		$this->parser = @xml_parser_create($this->encoding);

		if (is_resource($this->parser)) {
//			xml_parser_set_option($this->parser,XML_OPTION_CASE_FOLDING,0);
			xml_parser_set_option($this->parser,XML_OPTION_SKIP_WHITE,1);
			xml_set_object($this->parser,&$this);
			xml_set_element_handler($this->parser, "startHandler", "endHandler");
			xml_set_character_data_handler($this->parser, "cdataHandler");
			return true;
		}
		return false;

	}

	function free(){
		if (is_resource($this->parser)) {
			xml_parser_free($this->parser);
			unset( $this->parser );
		}
		return null;
	}

	function startHandler($parser, $element, $attr) {
		$this->tag = $element;
	}

	function endHandler($parser, $element){

	}

	function cdataHandler($parser, $cdata) {

		if(($cdata = trim($cdata)) == "") return;
		$this -> data .= $this -> tag ."1^1".$cdata."2^2";
		return true;

	}

	function parse($data){
		xml_parse($this->parser,$data);
		return true;
	}

	function parseOut(){
		if($this->data) $this->data = substr($this->data,0,-3);
		$arr = explode("2^2",$this->data);
		for($i=0;$i<count($arr);$i++){
			$tmp = explode("1^1",$arr[$i]);
			$arr2[$i][tag] = $tmp[0];
			$arr2[$i][val] = $tmp[1];
		}
		return $arr2;
	}
}
/*
사용의 예
$file = "http://".$cfg[shopUrl]."/cband-status-me?xml";

$buffer = file_get_contents($file); xml파일 읽기
$xml = new XMLParser(); xml파싱 클래스 정의
$xml->parse($buffer); xml파싱
$data = $xml->parseOut(); xml파싱 결과를 array로 받는다
*/
?>