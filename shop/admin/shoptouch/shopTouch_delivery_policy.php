<?php
$location = "���θ� App���� > �����å�ȳ� ����";
include "../_header.php";
@include_once "../../lib/pAPI.class.php";
@include_once "../../lib/json.class.php";
$pAPI = new pAPI();
$json = new Services_JSON(16);

$expire_dt = $pAPI->getExpireDate();
if(!$expire_dt) {
	msg('���� ��û�Ŀ� ��밡���� �޴��Դϴ�.', -1);
}

$now_date = date('Y-m-d 23:59:59');
$tmp_now_date = date('Y-m-d 23:59:59', mktime(0,0,0, substr($now_date, 5, 2), substr($now_date, 8, 2) - 30, substr($now_date, 0, 4)));
if($expire_dt < $tmp_now_date) {
	msg('���� ���Ⱓ ������ 30���� ���� ���񽺰� ���� �Ǿ����ϴ�.\n���񽺸� �ٽ� ��û�� �ֽñ� �ٶ��ϴ�.', -1);
}


$d_info_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'shoptouch', 'delivery_info');
$d_res = $db->_select($d_info_query);
$delivery_info = $d_res[0]['value'];

$r_info_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND menu=[s]', 'shoptouch', 'return_info');
$r_res = $db->_select($r_info_query);
$return_info = $r_res[0]['value'];

if(!$delivery_info) $delivery_info = "�� ��ǰ�� ��� ������� ���Դϴ�.(�Ա� Ȯ�� ��) ��ġ ��ǰ�� ��� �ټ� �ʾ����� �ֽ��ϴ�.[��ۿ������� �ֹ�����(�ֹ�����)�� ���� �������� �߻��ϹǷ� ��� ����ϰ��� ���̰� �߻��� �� �ֽ��ϴ�.]
�� ��ǰ�� ��� �������� �� �Դϴ�.
��� �������̶� �� ��ǰ�� �ֹ� �Ͻ� ���Ե鲲 ��ǰ ����� ������ �Ⱓ�� �ǹ��մϴ�. (��, ���� �� �������� �Ⱓ ���� �����ϸ� ���� �ֹ��� ��� �Ա��� ���� �Դϴ�.)";


if(!$return_info) $return_info = "��ǰ û��öȸ ���ɱⰣ�� ��ǰ �����Ϸ� ���� �� �̳� �Դϴ�.
��ǰ ��(tag)���� �Ǵ� �������� ��ǰ ��ġ �Ѽ� �ÿ��� �� �̳��� ��ȯ �� ��ǰ�� �Ұ����մϴ�.
���ܰ� ��ǰ, �Ϻ� Ư�� ��ǰ�� �� ���ɿ� ���� ��ȯ, ��ǰ�� ������ ��ۺ� �δ��ϼž� �մϴ�(��ǰ�� ����,��ۿ����� ����)
�Ϻ� ��ǰ�� �Ÿ� ���, ��ǰ���� ���� �� ������ �������� ������ ������ �� �ֽ��ϴ�.
�Ź��� ���, �ǿܿ��� ��ȭ�Ͽ��ų� ��������� �ִ� ��쿡�� ��ȯ/��ǰ �Ⱓ���� ��ȯ �� ��ǰ�� �Ұ��� �մϴ�.
����ȭ �� ���� �ֹ����ۻ�ǰ(������,�ߺ�,������ ����)�� ��쿡�� ���ۿϷ�, �μ� �Ŀ��� ��ȯ/��ǰ�Ⱓ���� ��ȯ �� ��ǰ�� �Ұ��� �մϴ�.
����,��ǰ ��ǰ�� ���, ��ǰ �� �� ��ǰ�� �ڽ� �Ѽ�, �н� ������ ���� ��ǰ ��ġ �Ѽ� �� ��ȯ �� ��ǰ�� �Ұ��� �Ͽ���, ���� �ٶ��ϴ�.
�Ϻ� Ư�� ��ǰ�� ���, �μ� �Ŀ��� ��ǰ ���ڳ� ������� ��츦 ������ ������ �ܼ����ɿ� ���� ��ȯ, ��ǰ�� �Ұ����� �� �ֻ����, �� ��ǰ�� ��ǰ�������� �� �����Ͻʽÿ�.";
?>
<?
if($expire_dt < $now_date) {
	@include('shopTouch_expire_msg.php');
}
?>
<form name=form method=post action="indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="delivery_policy_set">

<div class="title title_top">�����å �ȳ� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=17')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<div class="title_sub" style="margin:0">��� �ȳ�<span>���θ� App ��ǰ �� ���������� ������ �����å �ȳ� �Դϴ�. <font class=extext>(ġȯ�ڵ��� ������ �����ΰ��� ��ǰ ��ȭ�� ���� ���� Ȯ�� �Ͻ� �� �ֽ��ϴ�.)</font></span></div>
<div style="width:100%;padding:10px;">
	<textarea name="delivery_fix" style="width:100%;scroll:auto;height:50px;" readonly>��ۺ� : �⺻��۷�� {?_set.delivery.default}{=number_format(_set.delivery.default)}��{:}����{/} �Դϴ�. (����,�갣,���� �Ϻ������� ��ۺ� �߰��� �� �ֽ��ϴ�) {?_set.delivery.free}&nbsp;{=number_format(_set.delivery.free)}�� �̻� ���Ž� �������Դϴ�.{/}</textarea>
	<textarea name="delivery_info" style="width:100%;scroll:auto;height:150px;"><?=$delivery_info?>
	</textarea>
</div>
<div style="width:100%;height:20px;"></div>
<div class="title_sub" style="margin:0">��ȯ�׹�ǰ �ȳ�</div>
<div style="width:100%;padding:10px;">
	<textarea name="return_info" style="width:100%;scroll:auto;height:150px;"><?=$return_info?></textarea>
</div>
<div class="button">
<input type=image src="../img/btn_modify.gif">
</div>
</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���θ� App�� ��ǰ�� ȭ���� �̿�ȳ� �ǿ� ������ �����å�� ���� ���� �Ͻ� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>