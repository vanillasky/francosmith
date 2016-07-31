<?php
/**
 * Clib_Form_Element_Textarea
 * @author extacy @ godosoft development team.
 */
class Clib_Form_Element_Textarea extends Clib_Form_Element_Abstract
{
	public function getTagHtml()
	{
		$tag = sprintf('<textarea %s>%s</textarea>', $this->getAttributeTag('value', 'type'), $this->getValue());
		return $tag;
	}

}
