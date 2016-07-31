<?php
/**
 * Clib_Form_Abstract
 * Form Element ÇïÆÛ
 * @author extacy @ godosoft development team.
 */
abstract class Clib_Form_Abstract
{
	/**
	 * @var
	 */
	private $_defaultOption = array();

	/**
	 * @var
	 */
	private $_data;

	/**
	 * @var
	 */
	private $_elements = array();

	/**
	 * @var
	 */
	private $_undefined = array();

	/**
	 * @var
	 */
	private $_validateMsg = array();

	/**
	 *
	 * @param object $data [optional]
	 * @return
	 */
	final public function __construct($data = null)
	{
		if ($data) {
			$this->setData($data);
		}

		$this->_loadDefaultOption();
		$this->initialize();

	}

	/**
	 *
	 * @return
	 */
	final public function __descturct()
	{
	}

	/**
	 *
	 * @return
	 */
	abstract protected function initialize();

	private function _loadDefaultOption()
	{
		$ini = dirname(__FILE__) . '/data/config.ini';

		if (is_file($ini) && is_readable($ini)) {
			$this->_defaultOption = (array) parse_ini_file($ini, true);
		}

	}

	protected function getOption($key)
	{
		if (isset($this->_defaultOption[$key])) {
			return $this->_defaultOption[$key];
		}
		else {
			return null;
		}
	}

	/**
	 *
	 * @param object $data
	 * @return
	 */
	public function setData($data)
	{
		if ($this->_data === null) {
			$this->_data = $this->convertOldData($data);
		}
		else {
			// nothing to do.
		}

		return $this;

	}

	protected function convertOldData($data)
	{
		return $data;
	}

	/**
	 *
	 * @param object $name
	 * @param object $arguments
	 * @return
	 */
	public function __call($name, $arguments)
	{
		$name = strtolower($name);

		if (in_array($name, array(
			'input',
			'textarea',
			'radio',
			'hidden',
			'select',
			'checkbox',
			'date',
			'addElement'
		))) {

			$columnName = $arguments[0];
			$attributes = $arguments[1];
			$validation = $arguments[2];

			if ( ! isset($attributes['type'])) {
				if (($name == 'input' || $name == 'addElement')) {
					$attributes['type'] = 'text';
				}
				else {
					$attributes['type'] = $name;
				}
			}

			return $this->_addElement($columnName, $attributes, $validation);

		}
		else {
			// not support type.

		}

		return $this;

	}

	/**
	 *
	 * @param object $columnName
	 * @param object $attributes [optional]
	 * @param object $validation [optional]
	 * @return
	 */
	private function _addElement($columnName, $attributes = array(), $validation = array())
	{
		$elementName = 'Clib_Form_Element_' . ucfirst($attributes['type']);
		$element = new $elementName();

		if ( ! isset($attributes['id'])) {
			$element->addAttribute('id', $columnName);
		}

		switch ($attributes['type']) {
			case 'date' :
				$element->addAttribute('onclick', 'calendar(event);');
				$element->addAttribute('onkeydown', 'onlynumber();');
			default :
				break;
		}

		$element->addAttribute('name', $columnName);
		$element->addAttributes($attributes);

		if ( ! empty($validation)) {
			$element->setValidateOption($validation);
		}

		$this->_elements[$columnName] = $element;

		return $this;
	}

	private function _getAllElements()
	{
		return (array)$this->_elements;
	}

	public function getElement($columnName)
	{
		return $this->_getElement($columnName);
	}

	private function _getElement($columnName)
	{
		if (isset($this->_elements[$columnName])) {
			$element = $this->_elements[$columnName];
			return $element;
		}
		else {
			$this->_undefined[$columnName] = true;
			return false;
		}
	}

	/**
	 *
	 * @param object $columnName
	 * @return
	 */
	public function getValue($columnName = '')
	{
		if ($columnName) {
			return $this->_data[$columnName];
		}
		else {
			return $this->_data;
		}
	}

	/**
	 *
	 * @param object $columnName
	 * @return
	 */
	public function setValue($columnName, $value)
	{
		$this->_data[$columnName] = $value;
		return $this;
	}

	/**
	 *
	 * @param object $columnName
	 * @return
	 */
	public function getTag($columnName)
	{
		if ($element = $this->_getElement($columnName)) {
			$value = htmlspecialchars($this->getValue($columnName));
			$element->addAttribute('_value', $value);
			return $element->getTag();
		}
		else {
			return false;
		}
	}

	/**
	 *
	 * @param
	 * @return
	 */
	public function validate()
	{
		return Clib_Application::iapi('validate')->paramCheckForm($this);
	}

	/**
	 *
	 * @return array $returnValidation
	 */
	public function getElementsValidation()
	{
		$elements = $this->_getAllElements();
		foreach ($elements as $key => $element) {

			if ($validation = $element->getValidations()) {
				$returnValidation[$key] = $validation;
			}
		}

		return $returnValidation;
	}

	/**
	 * @param string $msg
	 * @return
	 */
	public function setValidateMsg($msg)
	{
		$this->_validateMsg = $msg;
	}

	/**
	 *
	 * @return string $this->_validateMsg
	 */
	public function getValidateMsg()
	{
		return $this->_validateMsg;
	}

}
