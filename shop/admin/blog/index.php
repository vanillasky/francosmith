<?php
$noleft=true;
$location = "��αװ���";
include "../_header.php";
$blogshop = new blogshop();
$callHeight = 'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/admin/proc/_iframeresize.php';

?>


<? if($blogshop->linked): ?>
	<iframe
	src="<?=$blogshop->config['iframe_url']?>?id=<?=$blogshop->config['id']?>&api_key=<?=$blogshop->config['api_key']?>&type=parentSeason2&callHeight=<?=urlencode($callHeight)?>"
	width="100%" frameborder=0 id="blogshop_iframe" scrolling="no" name="blogshop_iframe"></iframe>
<? else: ?>
	<div style="padding-left:12px; height:500px;">
		<div class="title title_top">��α� ��û</div>
		<table border=4 bordercolor=#dce1e1 style="border-collapse:collapse" width=700>
		<tr><td style="padding:7px 0 10px 10px"><b>�� ��α׼� ���񽺰� ����Ǿ� �ű� ��û �Ͻ� �� �����ϴ�.</b></td></tr>
		</table>
	</div>
<? endif; ?>



<? include "_blog_footer.php"; ?>
