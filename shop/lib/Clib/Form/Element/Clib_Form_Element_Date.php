<?php
/**
 * Clib_Form_Element_Date
 * @author extacy @ godosoft development team.
 */
class Clib_Form_Element_Date extends Clib_Form_Element_Abstract
{
	public function getTagHtml()
	{
		$this->setAttribute('type', 'text');
		$this->setAttribute('class', (string)$this->getAttribute('class') . ' ac');
		$tag = sprintf('<input %s />', $this->getAttributeTag());
		return $tag;
	}

}
