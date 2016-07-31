<?php
/**
 * Clib_Form_Element_Radio
 * @author extacy @ godosoft development team.
 */
class Clib_Form_Element_Radio extends Clib_Form_Element_Abstract
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

		$tags = array();

		foreach ((array) $this->getAttribute('value') as $label => $v) {
			$checked = $this->getAttribute('checked_value') == $v ? 'checked' : '';

			if ($v !== 0 && $v !== '0' && ! $v) {
				$id = $this->getAttribute('id');
			}
			else {
				$id = $this->getAttribute('id') . '_' . $v;
			}

			$tags[$label] = sprintf('<input name="%s" value="%s" id="%s" %s %s />', $this->getAttribute('name'), $v, $id, $this->getAttributeTag('value', 'id'), $checked);
		}
		return $tags;
	}

}
