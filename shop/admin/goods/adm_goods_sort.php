<?php

$location = '��ǰ���� > �з������� ��ǰ����';
include dirname(__FILE__).'/../_header.php';

$goodsSort = Core::loader('GoodsSort');

if ($_GET['cate']) {
	$category = array_pop(array_notnull($_GET['cate']));
}

if (!$_GET['limitRows']) $limitRows = $goodsSort->limitSet[0];
else if ($_GET['limitRows'] > $goodsSort->limitSet[count($goodsSort->limitSet)-1]) $limitRows = $goodsSort->limitSet[count($goodsSort->limitSet)-1];
else $limitRows = $_GET['limitRows'];

?>

<link rel="stylesheet" type="text/css" href="./css/adm_goods_sort.css"/>
<script type="text/javascript" src="./js/adm_goods_sort.js"></script>

<div class="title title_top">
	�з������� ��ǰ����
	<span>�� �з��������� ��ǰ���������� ���Ͻ� �� �ֽ��ϴ�</span>
	<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=8')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
</div>

<form id="goods-search-form">
	<table class="tb">
		<colgroup>
			<col class="cellC"/>
			<col class="cellL"/>
		</colgroup>
		<tr>
			<td>�з�����</td>
			<td><script type="text/javascript">new categoryBox('category[]', 4, '<?php echo $category; ?>', 'multiple');</script></td>
		</tr>
	</table>
	<div class="button_top"><input type=image src="../img/btn_search2.gif"/></div>

	<div id="goods-sort-type-description"></div>
	<script type="text/html" id="goods-sort-type-description-template">
		<div class="sort-type-status">
			<span class="category-name">"#{categoryLocation}"</span>ī�װ��� ��ǰ���� Ÿ�� :
			<a href="#MSG01" class="sort-type help-link auto">�ڵ�����</a>
			<span class="description auto">(�ڵ����� ���¿����� ���������� ������ �� ������, �������θ� ���� �����մϴ�.)</span>
			<a href="#MSG01" class="sort-type help-link manual">��������</a>
			<span class="description manual">(���������� �������θ� ������ �� �ֽ��ϴ�.)</span>
		</div>
		<div class="manual-sort-on-link-goods-position-form">
			ī�װ��� ��ǰ ���� ���� ��
			<select id="manual-sort-on-link-goods-position">
				<option value="LAST">�� �ڿ� ����</option>
				<option value="FIRST">�� �տ� ����</option>
			</select>
		</div>
		<div class="change-category-sort-type">
			��ǰ ����Ÿ�� �����ϱ� :
			<button id="change-category-sort-type-manual" class="auto" type="button">���������� �����ϱ�</button>
			<button id="change-category-sort-type-auto" class="manual" type="button">�ڵ������� �����ϱ�</button>
		</div>
		<div class="sort-type-description extext">
			<span class="bold">�ڵ�����</span> : ���� �ֱٿ� ī�װ��� ��ϵ� ��ǰ������(�ֱ� ��ϵ� ��ǰ�� �Ǿ�) �����Ǿ� ��µ˴ϴ�.<br/>
			<span class="bold">��������</span> : ī�װ��� ����� ������ ��� ���� ��ڰ� ������ ������� ��ǰ�� ���� �մϴ�. ī�װ��� ���� ����� ��ǰ�� ����Ʈ�� ���� �������� ��µ˴ϴ�.
		</div>
	</script>

	<ul id="goods-sort-guide">
		<li>
			1�� �з�, 2�� �з�, 3�� �з�, 4�� �з����� ���� ���������� ������ �� �ֽ��ϴ�.
		</li>
		<li>
			��ǰ ����<br/>
			- ����Ʈ �� ���� �� : ������ ��ǰ�� �̹���, ��ǰ���� ������ �� ������ Ŭ���ϸ� �ش� ��ǰ�� ���õ˴ϴ�.<br/>
			- ������ �� ���� �� : ������ ��ǰ�� ��ǰ���� ������ �̹����� �� ������ Ŭ���ϸ� �ش� ��ǰ�� ���õ˴ϴ�.<br/>
			- �������� : ���������� ù��° ��ǰ�� �����ϰ�, ������ ��ǰ�� �����ϸ�, �ش� �����ȿ� �ִ� ��ǰ�� ��� ���õ˴ϴ�.
		</li>
		<li>
			���û�ǰ �̵�<br/>
			- Ű���� ��� �� : ��ǰ/���� ���� ��, ���� �̵�Ű �� ��, �Ǵ� �¿� �̵�Ű �� �� �� ��ǰ�� ��ġ�� �̵��մϴ�.<br/>
			<span style="margin-right: 100px;"></span>HomeŰ�� ������ ���� ������ �� ��, EndŰ�� ������ ���� ������ �� �Ʒ��� �̵��մϴ�.<br/>
			- ���콺 ��� �� : ��ǰ/���� ���� ��, ���� �� ������ ���콺�� �巡�� �Ͽ� ��ǰ�� ��ġ�� �̵��մϴ�.
		</li>
		<li>
			���� ���<br/>
			- ���ϻ�ǰ ���� �� : ���õ� ��ǰ/������ �ٽ� Ŭ���ϸ� ���� �� ������ �����˴ϴ�.
			- �ټ���ǰ ���� �� : ��ǰ����Ʈ�� Ŭ���ϸ� ���� �� ������ �����˴ϴ�.
		</li>
		<li>
			���û�ǰ ������ �̵�<br/>
			- ���û�ǰ ������ �̵� ����� ����Ͽ� �̵� �� ���� ���õ� ��ǰ�� ������ �������� �̵��Ͽ� ����˴ϴ�.(���� ���θ� ������������ �ݿ� ��)<br/>
			- ���û�ǰ�� �ٸ��������� �̵��ϱ� ���� ������������ �۾��� ������ �����ؾ� �մϴ�.
		</li>
		<li>
			������ ������� ���θ��� �ݿ��� ���� ���� ���� <a href="./soldout.php" target="_blank" class="adm_goods_sort_link">[ǰ����ǰ ��������]</a> ���������� "�з������� ǰ����ǰ ���� ����"�׸��� Ȯ���Ͽ� ���ñ� �ٶ��ϴ�.<br/>
			(�� �������� ��ǰ����Ʈ���� ǰ����ǰ ���������� ������ �ݿ����� �ʽ��ϴ�.)<br/>
		</li>
		<li>
			ǰ�����ο� ������� �������� ���� �� ������ �߻��ϰų�, ���۵� �� ���� <a id="optimize-manual-sort" href="javascript:void(0);" class="adm_goods_sort_link">[�������� ����ȭ]</a>�� Ŭ���Ͽ� �� ī�װ��� ���������� ����ȭ ���Ѻ��ñ� �ٶ��ϴ�.
		</li>
		<li>
			<button id="optimize-manual-sort-button" type="button"> �������� ����ȭ </button>
		</li>
	</ul>

	<table id="list-display-option" class="tb">
		<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
		<tr>
			<td width="120" nowrap>����Ʈ ���� ����</td>
			<td class="noline">
				<input type="hidden" name="defaultViewType" value="<?php echo $goodsSort->getConfig("viewType"); ?>"/>
				<input type="hidden" name="defaultImageSize" value="<?php echo $goodsSort->getConfig("imageSize"); ?>"/>
				<input type="hidden" name="defaultLimitRows" value="<?php echo $goodsSort->getConfig("limitRows"); ?>"/>
				<span class="view-type-selector">
					<input type="radio" id="view-type-list" name="viewType" value="LIST"/>
					<label for="view-type-list">����Ʈ �� ����</label>
					<input type="radio" id="view-type-gallery" name="viewType" value="GALLERY"/>
					<label for="view-type-gallery">������ �� ����</label>
				</span>
				<span class="image-size-selector">
					&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
					��ǰ�̹��� ������
					<select name="imageSize">
						<option value="25">25px</option>
						<option value="50">50px</option>
						<option value="100">100px</option>
					</select>
				</span>
				<span class="limit-rows-selector">
					&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
					��¼�
					<select name="limitRows">
					<?php foreach ($goodsSort->limitSet as $limit) { ?>
					<option value="<?php echo $limit; ?>"><?php echo $limit; ?>�� ���</option>
					<?php } ?>
					</select>
				</span>
				<span class="action">
					&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
					<button id="save-list-display-option" type="button">��������</button>
				</span>
			</td>
		</tr>
	</table>

	<div class="move-selection-box"></div>
	<script type="text/html" id="move-selection-box-template">
		���û�ǰ ������ �̵� :
		<select class="move-selection">
			<option value="">-- ���� --</option>
			<option value="firstTop">ù ������ �� ������ �̵� ��</option>
			<option value="nextTop">���� ������ �� ������ �̵� ��</option>
			<option value="nextBottom">���� ������ �� �ڷ� �̵� ��</option>
			<option value="prevTop">���� ������ �� ������ �̵� ��</option>
			<option value="prevBottom">���� ������ �� �ڷ� �̵� ��</option>
			<option value="lastBottom">������ ������ �� ������ �̵� ��</option>
		</select>
		<span class="extext" style="font-size: 11px;">������ �̵� ���� ��, �ٷ� ����Ǿ� ���θ��� �ݿ��˴ϴ�.</span>
	</script>
</form>

<form id="goods-sort-form" method="post" action="indb.php">
	<input type="hidden" name="tplSkin" value="<?php echo $cfg['tplSkin']; ?>">
	<input type="hidden" name="mode" value="sortGoods">
	<div id="goods-content" name="#goods-content">
		<table class="head" width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr><td class="rnd" colspan="9"></td></tr>
			<tr class="rndbg">
				<th class="no">��ȣ</th>
				<th class="image">��ǰ�̹���</th>
				<th class="name">��ǰ��</th>
				<th class="option">�ɼ�:�ɼǰ�</th>
				<th class="soldout">ǰ������</th>
				<th class="open">��������</th>
				<th class="sell-stock">�Ǹ����</th>
				<th class="real-stock">�����</th>
				<th class="price">�Ǹűݾ�</th>
			</tr>
			<tr><td class="rnd" colspan="9"></td></tr>
		</table>
		<ol class="body" start="1"></ol>
		<div class="foot"></div>
		<script type="text/html" id="result-row-template">
			<li class="result">#{message}</li>
		</script>
		<script type="text/html" id="soldout-image-template">
			<img src="../../data/skin/#{tplSkin}/img/icon/good_icon_soldout.gif"/>
		</script>
		<script type="text/html" id="goods-row-option-template">
			<div class="option-item">
				<span class="option-item-name">#{optionName}</span> :
				<span class="option-item-values">#{optionValues}</span>
			</div>
		</script>
		<script type="text/html" id="goods-row-template">
			<li id="data-#{sno}" class="data" data-sno="#{sno}" data-goodsno="#{goodsno}" data-sort="#{sort}" data-origin-sort="#{sort}" data-origin-open="#{open}" data-index="#{index}">
				<div class="field no">#{_no}</div>
				<div class="field image">
					<a href="../../goods/goods_view.php?goodsno=#{goodsno}" target="_blank">#{imageTag}</a>
				</div>
				<div class="field name">
					<a href="javascript:void(0);" onclick="popup('popup.register.php?mode=modify&goodsno=#{goodsno}', 825, 600);">#{goodsnm}</a>
				</div>
				<div class="field option">#{option}</div>
				<div class="field soldout">#{soldoutImage}</div>
				<div class="field open">
					<select name="open">
						<option value="1"#{open1Selected}>YES</option>
						<option value="0"#{open2Selected}>NO</option>
					</select>
				</div>
				<div class="field sell-stock">#{sellstock}</div>
				<div class="field real-stock">#{realstock}</div>
				<div class="field price">#{price}��</div>
			</li>
		</script>
		<script type="text/html" id="page-anchor-template">
			<a class="page normal" data-page="#{pageNum}" href="#goods-content">[#{pageNum}]</a>
		</script>
		<script type="text/html" id="prev-page-anchor-template">
			<a class="page normal" data-page="#{pageNum}" href="#goods-content">����</a>
		</script>
		<script type="text/html" id="next-page-anchor-template">
			<a class="page normal" data-page="#{pageNum}" href="#goods-content">����</a>
		</script>
		<script type="text/html" id="active-page-anchor-template">
			<span class="page activate" data-page="#{pageNum}">#{pageNum}</span>
		</script>
	</div>

	<div class="move-selection-box"></div>
	
	<div class="button">
		<input type="image" src="../img/btn_save.gif"/>
		<img id="cancel-modified" src="../img/btn_cancel.gif"/>
	</div>
</form>

<div id="MSG01" name="#MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex" style="font-family: Dotum; font-size: 11px;">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�з������������� �����ڿ��� �����ϴ� ��ǰ�� ȿ�������� ������ ���� �����ϼ���.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�����ڵ��� ���� Ư���з����� ��ǰ�� ��ȸ�ϰ� �����ǿ��� ���� �Ǵµ� �̶� ��ǰ�� ������ �߿��մϴ�.<td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ڵ����� ���� : ��ǰ�� ��ϼ������ ����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�������� ���� : �������� ���������� �����Ͻ� �� ������ 1��, 2��, 3��, 4�� �з����� ���������� �����Ͻ� �� �ֽ��ϴ�.<td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�� [����] ��ư�� Ŭ���Ͽ� �۾������� ������ �ּ���.<td></tr>
</table>
</div>
<script type="text/javascript">cssRound("MSG01");</script>

<? include "../_footer.php"; ?>