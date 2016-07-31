<?php
/**
 * Clib_Form_Element_Abstract
 * @author extacy @ godosoft development team.
 */
abstract class Clib_Form_Element_Abstract
{
	/**
	 * element's attributes
	 * @var array
	 */
	private $_attributes;

	/**
	 * element's validateOptions
	 * @var array
	 */
	private $_validations;

	/**
	 * Construct
	 * @return void
	 */
	public function __construct()
	{
		$this->_attributes = array();
	}

	/**
	 * 값을 리턴
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->getAttribute('_value');
	}

	/**
	 * 속성을 리턴
	 * @return string
	 */
	protected function getAttributeTag()
	{
		$pair = array();

		$arguments = func_get_args();
		$exclude = array_merge(array(
			'_value',
			'default_value',
			'checked_value',
		), $arguments);

		foreach ($this->getAttributes() as $k => $v) {
			if (in_array($k, $exclude))
				continue;
			if ($k == 'checked' && ! $v)
				continue;
			$pair[] = sprintf('%s="%s"', $k, str_replace('"', '\"', $v));
		}

		return implode(' ', $pair);
	}

	/**
	 * 속성 추가
	 * @param string $name
	 * @param mixed $value
	 * @return Clib_Form_Element_Abstract
	 */
	public function addAttribute($name, $value)
	{
		$this->_attributes[$name] = $value;
		return $this;
	}

	/**
	 * 속성 추가
	 * @param array $attributes
	 * @return Clib_Form_Element_Abstract
	 */
	public function addAttributes($attributes)
	{
		foreach ($attributes as $k => $v) {
			$this->addAttribute($k, $v);
		}

		return $this;
	}

	/**
	 * 속성 존재 여부 리턴
	 * @param string $name
	 * @return boolean
	 */
	public function hasAttribute($name)
	{
		return array_key_exists($name, $this->_attributes);
	}

	/**
	 * 존재하는 속성의 값을 갱신
	 * @param string $name
	 * @param mixed $value
	 * @return Clib_Form_Element_Abstract
	 */
	public function setAttribute($name, $value)
	{
		if ($this->hasAttribute($name)) {
			$this->addAttribute($name, $value);
		}

		return $this;
	}

	/**
	 * 속성 값을 리턴
	 * @param string $name
	 * @return mixed
	 */
	public function getAttribute($name)
	{
		return $this->_attributes[$name];
	}

	/**
	 * 모든 속성을 리턴
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->_attributes;
	}

	/**
	 * html 태그로 변환된 문자열 리턴
	 * @return string
	 */
	public function getTag()
	{
		if ( ! $this->hasAttribute('value')) {
			$this->addAttribute('value', $this->getValue());
		}
		else {

			$value = $this->getAttribute('value');

			if ($value instanceof Clib_Collection_Abstract || $value instanceof Clib_Model_Abstract) {
				$this->setAttribute('value', $value->toArray(array(
					$value,
					'getIdNamePair'
				)));
			}
		}

		return $this->getTagHtml();
	}

	/**
	 * html 태그로 변환된 문자열 리턴
	 * @return
	 */
	abstract public function getTagHtml();

	/**
	 * validation 추가
	 * @return
	 */
	public function setValidateOption($validation)
	{
		foreach ($validation as $k => $v) {
			$this->addValidation($k, $v);
		}

		return $this;
	}

	/**
	 * validation 추가
	 * @param string $name
	 * @param mixed $value
	 * @return Clib_Form_Element_Abstract
	 */
	public function addValidation($name, $value)
	{
		$this->_validations[$name] = $value;
		return $this;
	}

	/**
	 * validation 값을 리턴
	 * @param string $name
	 * @return mixed
	 */
	public function getValidations($name)
	{
		return $this->_validations;
	}

}
