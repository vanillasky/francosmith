<?php

$location = "����ϼ����� > ����ϼ� �з������� ��ǰ ����";
include "../_header.php";
include "../../conf/config.mobileShop.php";
include "../../conf/config.mobileShop.category.php";

$goodsDisplay = Core::loader('Mobile2GoodsDisplay');

if(!$cfgMobileDispCategory['disp_goods_count']) $cfgMobileDispCategory['disp_goods_count'] = 10;
$selected['disp_goods_count'][$cfgMobileDispCategory['disp_goods_count']] = 'selected="selected"';

{ // ��� ī��Ʈ ����

	$goods_count = array(
		10,20,30,50,100
	);
	sort ( $goods_count );

}
?>

<style type="text/css">
a.extext:hover{
	color: #000000;
}
</style>

<form name=form method=post action="indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="disp_category_set">

<div class="title title_top">����ϼ� �з������� ��ǰ ����</div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>�з�������<br/>��ǰ��� ��</td>
	<td class="noline">
		<div>
			<select name="disp_goods_count">
			<?php foreach($goods_count as $count){?>
			<option value="<?php echo $count;?>" <?=$selected['disp_goods_count'][$count]?>><?php echo $count;?>��</option>
			<?php }?>
			</select>
<!--			<label for="vtype-main-pc">�¶��� ���θ�(PC����)�� �����ϰ� ���� ��ǰ���� ����</label>-->
			<br/>
			<span class="extext">* �з��������� �̵� ��, �з����������� ������ ��ư Ŭ�� �� �ҷ��� ��ǰ ������ �����մϴ�.<br>�ʹ� ���� ��ǰ�� �ѹ��� �ҷ��� ��� ������ �ε� �ӵ��� ������ �� �ֽ��ϴ�.</span>
		</div>
	</td>
</tr>
</table>
<div class="button">
<input type=image src="../img/btn_register.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>

<? include "../_footer.php"; ?>