<?php
/**
 * 멀티팝업 출력용 DATA
 * @author cjb3333 , artherot @ godosoft development team.
 */

include '../_header.php';

// 멀티 팝업 Class
$multipopup	= Core::loader('MultiPopup');
echo $multipopup->ajaxDataPopup();
?>