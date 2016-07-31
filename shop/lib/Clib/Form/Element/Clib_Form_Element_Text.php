<?php
/**
 * Clib_Form_Element_Text
 * @author extacy @ godosoft development team.
 */
class Clib_Form_Element_Text extends Clib_Form_Element_Abstract
{
	public function getTagHtml()
	{
		$value = $this->getAttribute('value');

		if (is_array($value) && ! empty($value)) {
			$tags = array();
			foreach ($value as $k => $v) {

				$checked = in_array($v, $this->getAttribute('checked_value')) ? 'checked' : '';

				if ($v !== 0 && $v !== '0' && ! $v) {
					$id = $this->getAttribute('id');
				}
				else {
					$id = $this->getAttribute('id') . '_' . $v;
				}

				$tags[] = sprintf('<input name="%s" value="%s" id="%s" %s %s >', sprintf('%s[%s]', $this->getAttribute('name'), $k), $v, $id, $this->getAttributeTag('value', 'id'), $checked);

			}
			return $tags;

		}
		else {
			$tag = sprintf('<input %s />', $this->getAttributeTag());
			return $tag;
		}
	}

}
