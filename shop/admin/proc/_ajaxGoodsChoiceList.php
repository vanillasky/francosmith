<?php
include dirname(__FILE__) . '/../lib.php';

$GoodsChoiceList = Core::loader('GoodsChoiceList');
echo $GoodsChoiceList->getRegisteredListHtml();
?>