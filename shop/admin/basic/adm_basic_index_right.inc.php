<?
if ( @filectime("../../conf/mini_memo.php") ) {
	$memo_date = date("Y-m-d",@filectime("../../conf/mini_memo.php"));
	$memo_time = date("H:i:s",@filectime("../../conf/mini_memo.php"));
}
?>
<!-- �޸�â -->
<div class="memo">
	<form name="fm_memo" method="post" action="indb.php" target="ifrmHidden">
	<input type="hidden" name="mode" value="memo" />
	<input type="hidden" name="miniMemo" value="" />
	<div class="top"><p>�������� <span><strong><?=$memo_date?></strong> <?=$memo_time?></span></p></div>
	<div class="middle">
		<div class="textMiddle" id="memoContent" contenteditable="true"><? @include "../../conf/mini_memo.php";?></div>
	</div>
	<div class="bottom">
		<div class="btn">
			<button type="button" class="save" id="memoSave" title="�����ϱ�">�����ϱ�</button>
			<div>
				<button type="button" class="del" id="memoDel" title="��������">��������</button>
			</div>
		</div>
	</div>	
	</form>
</div>
<!-- �޸�â -->

<!-- ������� -->
<div class="banner">
	<div id="panel_RIGHT"></div>
</div>
<!-- ������� -->