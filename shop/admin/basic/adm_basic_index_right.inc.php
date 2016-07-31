<?
if ( @filectime("../../conf/mini_memo.php") ) {
	$memo_date = date("Y-m-d",@filectime("../../conf/mini_memo.php"));
	$memo_time = date("H:i:s",@filectime("../../conf/mini_memo.php"));
}
?>
<!-- 메모창 -->
<div class="memo">
	<form name="fm_memo" method="post" action="indb.php" target="ifrmHidden">
	<input type="hidden" name="mode" value="memo" />
	<input type="hidden" name="miniMemo" value="" />
	<div class="top"><p>최종저장 <span><strong><?=$memo_date?></strong> <?=$memo_time?></span></p></div>
	<div class="middle">
		<div class="textMiddle" id="memoContent" contenteditable="true"><? @include "../../conf/mini_memo.php";?></div>
	</div>
	<div class="bottom">
		<div class="btn">
			<button type="button" class="save" id="memoSave" title="저장하기">저장하기</button>
			<div>
				<button type="button" class="del" id="memoDel" title="모두지우기">모두지우기</button>
			</div>
		</div>
	</div>	
	</form>
</div>
<!-- 메모창 -->

<!-- 우측배너 -->
<div class="banner">
	<div id="panel_RIGHT"></div>
</div>
<!-- 우측배너 -->