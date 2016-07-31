<?php

$location = '��Ÿ���� > ������Ʈ �ӵ� ��� ����';
include '../_header.php';

$dbCache = Core::loader('dbcache');
$dbCacheConfig = $dbCache->loadConfig();

?>
<script type="text/javascript">
var IntervarId;
jQuery(document).ready(function(){
	jQuery("#cache-use-type-none").click(function(){
		jQuery("#db-cache-form").addClass("none").removeClass("default");
	});
	jQuery("#cache-use-type-default").click(function(){
		jQuery("#db-cache-form").removeClass("none").addClass("default");
	});

	jQuery("#cache-use-type-<?php echo $dbCacheConfig['cacheUseType']; ?>").click();
	jQuery("#clear-cache").click(function(){
		ifrmHidden.location.href = "./adm_etc_cache_db.indb.php?mode=clearCache";
	});

});
</script>
<style type="text/css">
	#cache-target-db, #cache-target-mobile-db {
		padding-left: 0;
	}
	#cache-target-db li, #cache-target-mobile-db li {
		overflow: hidden;
		margin: 10px 0;
	}
	#cache-target-db li span, #cache-target-mobile-db li span {
		float: left;
	}
	#cache-target-db li select.page-expire-interval, #cache-target-mobile-db li select.page-expire-interval, button.clear-cache {
		float: right;
	}
	#db-cache-form.none tr.enable {
		display: none;
	}
	#db-cache-form.default .expire-interval-guide, #db-cache-form.default button.clear-cache, #db-cache-form.default .page-expire-interval {
		display: none;
	}
	#clear-cache {
		cursor: pointer;
	}
	button.clear-cache {
		display: block;
		background-image: url("../img/btn_renew.gif");
		background-repeat: no-repeat;
		border: none;
		text-indent: -1000px;
		width: 64px;
		height: 14px;
		margin: 3px;
		cursor: pointer;
	}
</style>

<div class="title title_top">
	������Ʈ �ӵ� ��� ����
	<a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=basic&no=44')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>

<div style="border: solid #000 1px; padding: 15px; margin-top: 10px; padding-right:10px;">
	<div style="color:#666666;">
	������Ʈ �ӵ� ��� ������ �ֿ� ���������� ��ĳ��(cache)������� �����Ͽ� �湮���� �ش� ������ ���� �� �̸� ����� ��ȸ ��� ������ ǥ�������ν�,
	������ �����ϸ� ������ �� �ְ� ��ȸ�ӵ��� ������ ������ ����ȯ���� ���� �� �ִ� ����Դϴ�.
	</div>
	<div style="padding-top:7px; color:#ff0000;">
	* ���� : ĳ�� ���� �� ����� ������ ǥ���ϱ� ������ ó���ӵ��� ���������� �����ֱ⸸ŭ ���θ� ȭ�鿡 �ݿ��Ǵ� �ð� ���̰� �߻��� �� �ֽ��ϴ�.
	��, ��ǰ�� ǰ�� �� �ֿ� �׸� ���ؼ��� ĳ�� ���� �ֱ�� ������� ����� ������ ��� �ݿ��˴ϴ�.
	<a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=basic&no=44')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
	</div>
</div>
<div style="padding-top:10px;"></div>

<form id="db-cache-form" class="admin-form" method="post" target="ifrmHidden" action="adm_etc_cache_db.indb.php">
	<input type="hidden" name="mode" value="save"/>
	<table class="admin-form-table" style="width: 700px;">
		<tr>
			<th style="width: 150px;">��뿩��</th>
			<td colspan="2">
				<input id="cache-use-type-none" type="radio" name="cacheUseType" value="none"/>
				<label for="cache-use-type-none">������</label>
				<input id="cache-use-type-default" type="radio" name="cacheUseType" value="default"/>
				<label for="cache-use-type-default">�����</label>
			</td>
		</tr>
		<tr class="enable">
			<th>���� �ֱ�</th>
			<td colspan="2">
				<b>30��</b>���� �ڵ� ���ŵ˴ϴ�.
				<img id="clear-cache" src="../img/btn_renew.gif"/>
			</td>
		</tr>
		<tr class="enable">
			<th rowspan="2">ĳ�� ���� ��� ������</th>
			<td style="background-color: #f6f6f6; text-align:center; width:300px;">�¶��μ�</td>
			<td style="background-color: #f6f6f6; text-align:center; width:300px;">����ϼ�</td>
		</tr>
		<tr class="enable">
			<td>
				<ul id="cache-target-db">
					<li>�����λ�ǰ����</li>
					<li>��ī�װ� ��ǰ ����Ʈ</li>
					<li>�����û�ǰ/��ǰ����/��ǰ�ı� ����Ʈ<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(��ǰ�������� ��)</li>
				</ul>
			</td>
			<td>
				<ul id="cache-target-mobile-db">
					<li>�����λ�ǰ����</li>
					<li>��ī�װ� ��ǰ ����Ʈ</li>
					<li>����ǰ����/��ǰ�ı� ����Ʈ<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(��ǰ�������� ��)</li>
				</ul>
			</td>
		</tr>
	</table>

	<div style="width: 700px; padding: 20px; text-align: center;">
		<input type="image" src="../img/btn_save.gif">
	</div>
</form>
</div>

<ul class="admin-simple-faq" style="margin-top: 15px;">
	<li style="margin-top: 5px; list-style: none; background: none;">
		<div style="font-weight: bold;">ĳ�ö�?</div>
		<div>
			: ���� ����ϴ� �����ͳ� ������� �̸� ������ �� ������ ������ ��û�� �� ����� ������ ǥ�������ν� ó�� �ӵ��� ����ų �� �ִ� ����Դϴ�.
		</div>
		<div style="margin-top: 10px;">���������ϱ� : Ŭ�� �� ���� ��ǰ�����͸� �������� ĳ�ÿ� �ݿ��մϴ�.</div>
	</li>
</ul>

<?php

include '../_footer.php';

?>