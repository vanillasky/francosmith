<?php
/**
 * Clib_IApi_Type_Convert
 * @author extacy @ godosoft development team.
 */
class Clib_IApi_Type_Convert
{
	const XML = 'xml';
	const JSON = 'json';
	const SERIALIZE = 'serialize';
	const TEXT = 'text';

	private $_default_type = self::XML;

	public function __construct()
	{
	}

	/**
	 *
	 * @param string $data_type, array $data
	 * @return string $return_data;
	 */
	public function typeToArr($data_type, $data)
	{
		$return_data = "";
		if ( ! $data_type)
			$data_type = $this->_default_type;

		switch ($data_type) {
			case self::XML :
				$return_data = $this->xmlToArray($data);
				break;
			case self::JSON :
				$return_data = json_decode($data);
				break;
			case self::SERIALIZE :
				$return_data = unserialize($data);
				break;
			case self::TEXT :
				break;
		}

		return $return_data;
	}

	/**
	 *
	 * @param string $data_type, array $data
	 * @return string $return_data;
	 */
	public function arrToType($data_type, $data)
	{
		$return_data = "";
		$arr_data_type = array(
			'xml',
			'json',
			'serialize',
		);

		if ( ! in_array($data_type, $arr_data_type))
			$data_type = $this->_default_type;
		switch ($data_type) {
			case self::XML :
				$return_data = $this->arrayToXml($data);
				break;
			case self::JSON :
				$return_data = json_encode($data);
				break;
			case self::SERIALIZE :
				$return_data = serialize($data);
				break;
			case self::TEXT :
				break;
		}

		return $return_data;
	}

	public function arrayToXml($data)
	{
		$xml = Clib_Application::iapi('xml_writer');

		if (is_array($data) && ! empty($data)) {

			foreach ($data as $key => $val) {

				if (is_array($val)) {
					if ( ! is_numeric($key)) {
						$tmp_key = str_replace('_data', '', $key);
						$xml->push($key);
					}
					else {
						$tmp_arr['idx'] = $key + 1;
						$xml->push($tmp_key, $tmp_arr);
					}

					$this->_itemToXml($val, $xml, $tmp_key);
					$xml->pop();
				}
				else {
					$xml->element($key, $val);
				}
			}
		}
		return $xml->getXml();
	}

	protected function _itemToXml($data, $xml, $tmp_key)
	{
		if (is_array($data) && ! empty($data)) {
			foreach ($data as $key => $val) {
				if (is_array($val)) {
					if ( ! is_numeric($key)) {
						$tmp_key = str_replace('_data', '', $key);
						$xml->push($key);
					}
					else {
						$tmp_arr['idx'] = $key + 1;
						$xml->push($tmp_key, $tmp_arr);
					}

					$this->_itemToXml($val, $xml, $tmp_key);
					$xml->pop();
				}
				else {
					$xml->element($key, $val);
				}
			}
		}
	}

	/**
	 *
	 * @param string $xml_contents
	 * @return array $return_data;
	 */
	public function xmlToArray($xml_contents)
	{
		$parser = Clib_Application::iapi('xml_parser');
		$parser->setXml($xml_contents);
		$parser->Parse();

		return $this->_xmlObjToArray($parser->document);
	}

	/**
	 *
	 * @param object $obj
	 * @return array $return_data;
	 */
	protected function _xmlObjToArray($obj)
	{
		$return_data = Array();

		if (is_Array($obj->tagChildren) && ! empty($obj)) {
			$root_key = $obj->tagName;

			if (count($obj->tagChildren) > 0) {
				$return_data[$root_key] = $this->_xmlItemToArray($obj->tagChildren);
			}
			else {
				$return_data[$root_key] = $tag_child->tagData;
			}
		}
		return $return_data;
	}

	/**
	 *
	 * @param object $obj
	 * @return array $return_data;
	 */
	protected function _xmlItemToArray($item)
	{
		$return_data = Array();
		foreach ($item as $tag_child) {
			if (count($tag_child->tagChildren) > 0) {
				if ($tag_child->tagAttrs['idx']) {
					$return_data[$tag_child->tagName][] = $this->_xmlItemToArray($tag_child->tagChildren);
				}
				else {
					$return_data[$tag_child->tagName] = $this->_xmlItemToArray($tag_child->tagChildren);
				}
			}
			else {
				if ($tag_child->tagAttrs['idx']) {
					$return_data[$tag_child->tagName][] = $this->_xmlItemToArray($tag_child->tagChildren);
				}
				$return_data[$tag_child->tagName] = $tag_child->tagData;
			}
		}
		return $return_data;
	}

}
