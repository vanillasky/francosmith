<?php /* Template_ 2.2.7 2014/08/27 19:05:42 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/outline/side/standard.htm 000003288 */  $this->include_("dataBank","dataBanner");?>
<?php if($TPL_VAR["todayshop_cfg"]['shopMode']!='todayshop'){?>
<div style="padding-top:10px;"></div>
<!-- ���� ���μҽ��� '��Ÿ/�߰�������(proc) > ī�װ��޴�- menuCategory.htm' �ȿ� �ֽ��ϴ� -->
<?php $this->print_("menuCategory",$TPL_SCP,1);?>

<?php }?>

<?php if($TPL_VAR["smartSearch_useyn"]=='y'){?>
<?php $this->print_("smartSearch",$TPL_SCP,1);?>

<?php }?>

<!-- ���ο��� ������ 01 : Start -->
<div style="width:190px;">
	<div style="margin-top:46px;"><img src="/shop/data/skin/freemart/img/main/sid_tit_cscenter.gif"></div>
	<div style="padding-top:10px; font-size:19px; font-weight:bold; line-height:23px; color:#626262; font-family:Tahoma, Geneva, sans-serif">070.6445.2648</div>
	<dl style="margin:0px; padding:10px 0px; color:#666; font-size:11px; line-height:12px;">
		<dd style="margin:0px;">MON - FRI <span style="color:#ed5d55;">am 10:00 - pm 18:00</span></dd>
		<dd style="margin:0px;">LUNCH <span style="color:#ed5d55;">am 12:00 - pm 13:00</span></dd>
		<dd style="margin:0px;">SAT, SUN, HOLIDAY <span style="color:#ed5d55;">off</span></dd>
	</dl>
</div>
<!-- ���ο��� ������ 01 : End -->
<div style="width:190px; height:1px; font-size:1px; border-top:solid 1px #e1e1e1;"></div>
<!-- �������Ա� : Start -->
<div style="width:190px; padding-top:13px;">
	<div><img src="/shop/data/skin/freemart/img/main/sid_tit_bank.gif"></div>
	<div style="padding-top:10px; font-size:11px; height:50px; color:#666;">
<?php if((is_array($TPL_R1=dataBank())&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {$TPL_S1=count($TPL_R1);$TPL_I1=-1;foreach($TPL_R1 as $TPL_V1){$TPL_I1++;?>
	<p style="margin:0px; padding:0px;"><?php echo $TPL_V1["bank"]?></p>
	<p style="margin:0px; padding:0px; font-weight:bold;"><?php echo $TPL_V1["account"]?></p>
	<p style="margin:0px; padding:0px;"><?php echo $TPL_V1["name"]?></p>
<?php if($TPL_I1!=$TPL_S1- 1){?>
	<p style="margin:0px; padding:0px; border-top:solid 1px #EBEBEB;height:1px;font-size:0px; margin:7px 0 6px"></p>
<?php }?>
<?php }}?>
	</div>
</div>
<!-- �������Ա� : End -->

<!-- ���ο��ʹ�� : Start -->
<table cellpadding="0" cellspacing="0" border="0"width=100%>
<tr><td align="left"><!-- (��ʰ������� ��������) --><?php if((is_array($TPL_R1=dataBanner( 4))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
<tr><td align="left"><!-- (��ʰ������� ��������) --><?php if((is_array($TPL_R1=dataBanner( 5))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
</table>
<!-- ���ο��ʹ�� : End -->

<div style="padding-top:30px"></div>

<!-- SNS �ǽð����� ����Ʈ : Start-->
<table cellpadding=0 cellspacing=0 border=0 width=100%>
<tr><td align=center><?php echo snsPosts( 1)?></td></tr>
</table>
<!-- SNS �ǽð����� ����Ʈ : End-->