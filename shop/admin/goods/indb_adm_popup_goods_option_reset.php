<?php
include "../lib.php";

Clib_Application::execute('admin_goods/resetOption');

echo '
<script type="text/javascript">
parent.location.reload();
</script>
';
