<?php
/**
 * Clib_Form_Element_Hidden
 * @author extacy @ godosoft development team.
 */
class Clib_Form_Element_Hidden extends Clib_Form_Element_Abstract
{
	public function getTagHtml()
	{
		$tag = sprintf('<input %s />', $this->getAttributeTag());
		return $tag;
	}

}
