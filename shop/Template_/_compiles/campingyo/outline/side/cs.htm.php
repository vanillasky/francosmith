<?php /* Template_ 2.2.7 2014/03/30 13:43:31 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/outline/side/cs.htm 000003211 */  $this->include_("dataBanner");?>
<!-- �������� �޴� ���� -->
<div style="width:190px; border-bottom:solid 1px #ccc; padding:17px 0 0 0; margin:0;">
	<div style="padding:0px 0px 10px 17px; font-size:12px; font-weight:bold; color:#333; border-bottom:solid 1px #ccc;">��������</div>
	<div style="padding:10px 0 3px 8px;">
	<table cellpadding=0 cellspacing=7 border=0>
	<tr>
		<td><a href="<?php echo url("service/faq.php")?>&" class="lnbmenu">��FAQ</a></td>
	</tr>
	<tr>
		<td><a href="<?php echo url("service/guide.php")?>&" class="lnbmenu">���̿�ȳ�</a></td>
	</tr>
	<tr>
		<td><a href="<?php echo url("mypage/mypage_qna.php")?>&" class="lnbmenu">��1:1���ǰԽ���</a></td>
	</tr>
	<tr>
		<td><a href="<?php echo url("member/find_id.php")?>&" class="lnbmenu">��IDã��</a></td>
	</tr>
	<tr>
		<td><a href="<?php echo url("member/find_pwd.php")?>&" class="lnbmenu">����й�ȣã��</a></td>
	</tr>
	<tr>
		<td><a href="<?php echo url("member/myinfo.php")?>&" class="lnbmenu">������������</a></td>
	</tr>
	</table>
	</div>
</div>
<!-- �������� �޴� �� -->
<!-- ���ο��� �������� 01 : Start -->
<div style="width:190px; height:95px; background:url(/shop/data/skin/campingyo/img/main/bn_cs.jpg) no-repeat;">
	<div style="padding:19px 0px 2px 66px;"><img src="/shop/data/skin/campingyo/img/main/txt_cs.gif"></div>
	<div style="padding-left:66px; font-size:14px; font-weight:bold; line-height:23px; color:#333; font-family:Tahoma, Geneva, sans-serif"><?php echo $GLOBALS["cfg"]['compPhone']?> </div>
	<dl style="margin:0px; padding-left:66px; color:#888; font-size:11px;">
		<dd style="margin:0px; line-height:12px;">MON - FRI</dd>
		<dd style="margin:0px; line-height:12px;">10:00 - 18:00</dd>
	</dl>
</div>
<!-- ���ο��� �������� 01 : End -->

<!-- �����ڿ��� SMS������ ��� : ���������� '�����ΰ��� > ��Ÿ������������ > ��Ÿ/�߰�������(proc) > �����ڿ��� SMS��㹮���ϱ� - ccsms.htm' �� �ֽ��ϴ�. -->
<!-- �Ʒ� ����� �⺻������ ȸ���鸸 ���̵��� �Ǿ��ִ� �ҽ��Դϴ�.
���� ��ȸ���鵵 �� ����� ����ϰ� �Ϸ��� �Ʒ� �ҽ��߿�,  \{ # ccsms \}  ��κи� ���ܳ��� �Ʒ��� �ҽ��� �����Ͻø� �˴ϴ�.
���� �̱���� ����Ϸ��� 'ȸ������ > SMS����Ʈ����' ���� ����Ʈ������ �Ǿ��־�߸� �����մϴ�. -->

<?php if($GLOBALS["sess"]){?>
<?php $this->print_("ccsms",$TPL_SCP,1);?>

<?php }?>

<!-- ���ο��ʹ�� : Start -->
<table cellpadding="0" cellspacing="0" border="0"width=100%>
<tr><td align="left"><!-- (��ʰ������� ��������) --><?php if((is_array($TPL_R1=dataBanner( 4))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
<tr><td align="left"><!-- (��ʰ������� ��������) --><?php if((is_array($TPL_R1=dataBanner( 5))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
</table>
<!-- ���ο��ʹ�� : End -->

<div style="padding-top:80px"></div>