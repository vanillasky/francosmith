<?php
$SET_HTML_DEFINE = true;
include '../_header.popup.php';
$hashtag = Core::loader('hashtag');

$hashtagData = array();
$hashtagData = $hashtag->getHashtagList('userLayout', array());
?>
<style>
html { height: 100%; }
body { margin: 0px; height: 100%; overflow-y: hidden;  }
.hashtagLayout { height: 800px; width: 1000px; }
.hashtagLayout .hashtagLayout-outline { background: #f6f6f6; height: 40px; }
.hashtagLayout .hashtagLayout-outline .hashtagLayout-title {
	margin:0px 0px 0px 15px;
	font-weight:bold;
	font-family:Dotum;
	font-size:14px;
	letter-spacing:-1px;
}
.hashtagLayout .hashtagLayout-outline .hashtagLayout-title .hashtagLayout-titleArrow { font-size: 12px; margin-right: 7px; }
.hashtagLayout .hashtagLayout-outline .hashtagLayout-addBtnArea { text-align: center; height: 400px; width: 100%; }
.hashtagLayout .hashtagLayout-outline .hashtagLayout-addBtnArea img { border: 0px; cursor: pointer; }
.hashtagLayout .hashtagLayout-hashtagDisplayArea { border: 1px solid #cccccc; padding: 0px; margin: 0px;}

.hashtagLayout .hashtagLayout-displayControllInfo { padding: 5px; height: 40px; width: 450px; background-color: #ffdc6d; vertical-align: top;}
.hashtagLayout .hashtagLayout-displayControllInfo div:first-child { font-weight: bold; }
.hashtagLayout .hashtagLayout-displayLayout { padding: 5px; height: 590px; width: 450px; overflow-y: auto; overflow-x: hidden; }
.hashtagLayout .hashtagLayout-displayInfo { padding: 5px; height: 40px; width: 450px; background-color: #D5D5D5; color: #627dce; }
.hashtagLayout .hasatag-buttonArea table { text-align: center; height: 60px; background:#f6f6f6; }
.hashtagLayout .hashtagLayout-hashtagListBox { width: 460px; height: 630px; margin-top: 10px; padding: 5px; overflow-y: auto; }
.hashtagLayout .hashtagLayout-hashtagSearch { height:30px; width: 100%; margin-top: 10px; padding: 5px; }
.hashtagLayout .hashtagLayout-hashtagSearch #hashtagSearch { border: none; width: 130px; height: 16px; line-height: 16px; }
.hashtagLayout-focusDiv { background-color: #e8f5bb; }
.hashtagInputText { border: 1px #BDBDBD solid; width: 145px; float: left; height: 18px; }
.hashtagInputTextButton { margin-left: 3px; }
</style>
<link href="<?php echo $cfg['rootDir']; ?>/lib/js/jquery-ui-1.10.4.custom.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo $cfg['rootDir']; ?>/proc/hashtag/hashtagControl.js?actTime=<?php echo time(); ?>"></script>

<table cellpadding="0" cellspacing="0" width="100%" border="0" class="hashtagLayout">
<colgroup>
	<col style="width:460px;" />
	<col />
	<col style="width:460px;" />
</colgroup>
<tr>
	<td class="hashtagLayout-outline"><div class="hashtagLayout-title"> <span class="hashtagLayout-titleArrow">▼</span>전체 해시태그 리스트 </div></td>
	<td rowspan="3" valign="middle" class="hashtagLayout-outline">
		<table cellpadding="0" cellspacing="0" class="hashtagLayout-addBtnArea">
		<tr>
			<td><img src="../img/btn_add.gif" id="addHashtagBtn" /></td>
		</tr>
		</table>
	</td>
	<td class="hashtagLayout-outline"><div class="hashtagLayout-title"> <span class="hashtagLayout-titleArrow">▼</span>노출되는 해시태그 리스트</div></td>
</tr>
<tr>
	<!-- 전체 해시태그 리스트-->
	<td valign="top" class="hashtagLayout-hashtagDisplayArea">
		<div class="hashtagLayout-hashtagSearch">
			<div class="hashtagInputText">#<input type="text" name="hashtagSearch" id="hashtagSearch" /></div>
			<img src="../img/btn_search3.png" border="0" class="hand hashtagInputTextButton" id="hashtagSearchBtn" alt="검색" align="absmiddle" />
		</div>
		<div id="hashtagListBox" class="hashtagLayout-hashtagListBox"></div>
	</td>
	<!-- 전체 해시태그 리스트-->

	<!-- 노출되는 해시태그 리스트-->
	<td valign="top" class="hashtagLayout-hashtagDisplayArea">
		<div class="hashtagLayout-displayControllInfo">
			<div>노출 순서 설정</div>
			<div>키보드 상하이동키 ↓↑ 정렬 후 [선택완료] 버튼을 클릭해야 순서가 반영됩니다.</div>
		</div>

		<form name="hashtagDisplayForm" id="hashtagDisplayForm">
		<div class="hashtagLayout-displayLayout" id="hashtagDisplayListBox"><?php foreach($hashtagData as $hashtagValue) echo $hashtagValue; ?></div>
		</form>

		<div class="hashtagLayout-displayInfo">※ 최대 50개까지 추가 가능하며, 노출 개수 설정에 따라 앞에 위치한 해시태그부터 설정된 개수만큼 노출됩니다.</div>
	</td>
	<!-- 노출되는 해시태그 리스트-->
</tr>
<tr class="hasatag-buttonArea">
	<td colspan="3">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>
				<img src="../img/btn_goodsConfirm.jpg" id="saveHashtagDisplayBtn" class="hand" />
				&nbsp;
				<img src="../img/btn_closePopup.jpg" id="closeHashtagDisplayBtn" class="hand" />
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>

<script type="text/javascript">
jQuery(document).ready(HashtagPopupDisplayController);
</script>
<?php include '../footer.popup.php'; ?>