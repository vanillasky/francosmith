<?php
/**
 * Clib_Form_Element_Checkbox
 * @author extacy @ godosoft development team.
 */
class Clib_Form_Element_Checkbox extends Clib_Form_Element_Abstract
{
	private function _setCheckedValue()
	{
		$this->addAttribute('default_value', $this->hasAttribute('default_value') ? $this->getAttribute('default_value') : null);
		$this->addAttribute('checked_value', $this->getAttribute('default_value'));

		foreach ((array) $this->getAttribute('value') as $v) {
			if (in_array($v, $this->getValue())) {
				$arr_v[] = $v;
			}
		}

		if ( ! empty($arr_v)) {
			$this->setAttribute('checked_value', $arr_v);
		}
	}

	public function getTagHtml()
	{
		$value = $this->getAttribute('value');

		if (is_array($value) && ! empty($value)) {

			$this->_setCheckedValue();

			$tags = array();
			foreach ($value as $label => $v) {

				$checked = in_array($v, $this->getAttribute('checked_value')) ? 'checked' : '';

				if ($v !== 0 && $v !== '0' && ! $v) {
					$id = $this->getAttribute('id');
				}
				else {
					$id = $this->getAttribute('id') . '_' . $v;
				}

				$tags[$label] = sprintf('<input name="%s" value="%s" id="%s" %s %s >', sprintf('%s[%s]', $this->getAttribute('name'), $label), $v, $id, $this->getAttributeTag('value', 'id'), $checked);
			}

			return $tags;
		}
		else {
			$checked = $value == $this->getValue() ? 'checked' : '';
			$tag = sprintf('<input %s %s />', $this->getAttributeTag(), $checked);
			return $tag;
		}

	}

}
