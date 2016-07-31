<?php
/**
 * Clib_Form_Element_Select
 * @author extacy @ godosoft development team.
 */
class Clib_Form_Element_Select extends Clib_Form_Element_Abstract
{
	private function _setCheckedValue()
	{
		$this->addAttribute('default_value', $this->hasAttribute('default_value') ? $this->getAttribute('default_value') : null);
		$this->addAttribute('checked_value', $this->getAttribute('default_value'));

		foreach ((array) $this->getAttribute('value') as $v) {
			if ($v == $this->getValue()) {
				$this->setAttribute('checked_value', $v);
				break;
			}
		}
	}

	public function getTagHtml()
	{
		$this->_setCheckedValue();

		$value = $this->getAttribute('value');

		if (is_string($value) && preg_match('/^([0-9]+):([0-9]+)$/', $value, $matches)) {
			$value = range($matches[1], $matches[2]);
		}
		else {
			// nothing to do.
		}

		$tags = array();
		$tags[] = sprintf('<select %s>', $this->getAttributeTag('type', 'value'));
		foreach ((array) $value as $label => $v) {
			if ($this->getAttribute('checked_value') == $v) {
				//debug($this);
			}

			$checked = $this->getAttribute('checked_value') == $v ? 'selected' : '';
			$tags[] = sprintf('<option value="%s" %s>%s</option>', $v, $checked, $label);
		}
		$tags[] = '</select>';

		return implode('', $tags);

	}

}
