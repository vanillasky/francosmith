<?php

$location = '�������� > �޴��� ���� ���� �ȳ�';
include dirname(__FILE__).'/../_header.php';

$requestVar = array(
	'code' => 'mobilians_info'
);

?>

<div class="title title_top">�޴��� ���� ���� �ȳ�</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?php echo http_build_query($requestVar); ?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>

<?php

include dirname(__FILE__).'/../_footer.php';

?>