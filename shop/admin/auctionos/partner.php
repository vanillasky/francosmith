<?

@include "../../conf/auctionos.php";
@include "../../conf/fieldset.php";

$location = "��ٿ� > ��ٿ� ����";
include "../_header.php";

if(!$partner['auctionshopid'])$partner['auctionshopid'] = "GODO".$godo[sno];
$fexist = file_exists('../../data/auctionos/godo/'.$partner['auctionshopid'].'/auctionos.php');
$fexist2 = file_exists('../../data/auctionos/godo/'.$partner['auctionshopid'].'/auctionos2.php');

$useYn = $partner['useYn'];
$checked['useYn'][$useYn] = "checked";
?>
<div class="title title_top">��ٿ� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=20')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form name=form method=post action="indb.php">
<input type=hidden name=mode value="auctionos">

<table class="tb" border="0">
<col class="cellC"><col class="cellL">
<input type="hidden" name="partner[auctionshopid]" value="<?=$partner['auctionshopid']?>" class="lline">
<tr>
	<td>��뿩��</td>
	<td class="noline">
	<label><input type="radio" name="useYn" value="y" <?php echo $checked['useYn']['y'];?>/>���</label><label><input type="radio" name="useYn" value="n" <?php echo $checked['useYn']['n'];?> <?php echo $checked['useYn'][''];?> />������</label>
	</td>
</tr>
<tr>
	<td>�������Һ�����</td>
	<td><input type="text" name="partner[nv_pcard]" value="<?=$partner[nv_pcard]?>" class=lline><div class="extext" style="padding-top:5px;">��) ī���,�����ڰ�����  [������� �Է�.  ����ī�尡 �ִ� ���, �����ڰ������� ����� ī��� ǥ��]</div></td>

</tr>
<tr>
	<td>��ǰ�� �Ӹ��� ����</td>
	<td>
	<div><input type="text" name="partner[goodshead]" value="<?=$partner[goodshead]?>" class="lline"></div>
	<div class="extext" style="padding-top:5px;">* ��ǰ�� �Ӹ��� ������ ���� ġȯ�ڵ�</div>
	<div class="extext">- �Ӹ��� ��ǰ�� �Էµ� "������"�� �ְ� ���� �� : {_maker}</div>
	<div class="extext">- �Ӹ��� ��ǰ�� �Էµ� "�귣��"�� �ְ� ���� �� : {_brand}</div>
	</td>
</tr>
<?
list($grpnm,$grpdc) = $db->fetch("select grpnm,dc from ".GD_MEMBER_GRP." where level='".$joinset[grp]."'");
?>
<tr>
	<td>��ǰ���� ����</td>
	<td>
	<div class="noline"><b><?=$grpnm?></b> �������� <b><?=$grpdc?>%</b>�� ��ǰ���ݿ� ����Ǿ� ��ٿ��� ���� �˴ϴ�. <input type="image" src="../img/btn_naver_install.gif" align="absmiddle" border="0"></div>
	<div class="extext">��ٿ��� ����Ǵ� ��ǰ������ ����� ������ ���Խ� ȸ���׷��� �������� ����� ������ �˴ϴ�.</div>
	<div class="extext">���Խ� ȸ���׷� ������ <a href="../member/fieldset.php" class="extext" style="font-weight:bold">ȸ������ > ȸ�����԰���</a>���� ���� �����մϴ�.</div>
	<div class="extext">ȸ���׷��� ������ ������ <a href="../member/group.php" class="extext" style="font-weight:bold">ȸ������ > ȸ���׷���� </a>���� ���� �����մϴ�.</div>
	</td>
</tr>
</table>
</form>

<div id="MSG02">
<table cellpadding="1" cellspacing="0" border=0 class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�������Һ�������?: �� ī��纰 ������������ �Է��Ͻ� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�������Һ������� ī��簣 ������ %�� �ϸ�,��ü ī���� ��ī��� ǥ���մϴ�. ��) �Ｚ:3%����:6%����:12</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�������Һ������� �Է�/������ �Ʒ� ��ǰDB URL�� ���� ������Ʈ�� �������ϸ� ��ǰDB URL ���� �� ������ ������ �ʵ��� pcard�ʵ��� ������ ����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">����� �������Һ������� ���Ǿ�ٿ� ������Ʈ �ֱ⿡ ���� �ݿ��Ǿ����ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� ��ٿ��� ����Ǵ� ��ǰ������ �ٽ� ����Ͻô� ���� �ƴմϴ�.</td></tr>
<tr><td style="padding-left:10">���� ����� ���θ��� ��ǰ������ ���� ��ٿ��� �ֱ⿡ ����  �������ϴ�.</td></tr>
</table>
<br/>
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� ��ٿ����� ��ǰ�˻��� ���� �� �� �ֵ��� ��ǰ�� �Ӹ��� ������ Ȱ���ϼ���!</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">Ư�����ڸ� �Ӹ����� ����ϼŵ� Ư�� ���ڴ� ������� �ʽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� 1) ��ǰ�� �Ӹ��� ���� : ����</td></tr>
<tr>
	<td style="padding-left:10">
	<table style='border:1px solid #ffffff;width:400' class="small_ex">
	<col align="center" width="60"><col align="center" width="50"><col align="center" width="50"><col>
	<tr>
		<td>��ǰ��</td>
		<td>������</td>
		<td>�귣��</td>
		<td>���� ��ٿ� ���� ��ǰ��</td>
	</tr>
	<tr>
		<td>����û����</td>
		<td>������</td>
		<td>����</td>
		<td>����û����</td>
	</tr>
	</table>
	</td>
</tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� 2) ��ǰ�� �Ӹ��� ���� : ������ {_maker} {_brand}</td></tr>
<tr>
	<td style="padding-left:10">
	<table style='border:1px solid #ffffff;width:400' class="small_ex">
	<col align="center" width="60"><col align="center" width="50"><col align="center" width="50"><col>
	<tr>
		<td>��ǰ��</td>
		<td>������</td>
		<td>�귣��</td>
		<td>���� ��ٿ� ���� ��ǰ��</td>
	</tr>
	<tr>
		<td>����û����</td>
		<td>������</td>
		<td>����</td>
		<td>������ ������ ���� ����û����</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<script>cssRound('MSG02')</script>
</div>

<p>

<table width="100%" cellpadding="0" cellspacing="0">
<col class="cellC"><col style="padding:5px 10px;line-height:140%">
<tr class="rndbg">
	<th>��ü</th>
	<th>��ǰ DB URL [������ �̸�����]</th>
	<th>�ֱ� ������Ʈ�Ͻ�</th>
	<th>������Ʈ</th>
</tr>
<tr><td class="rnd" colspan="10"></td></tr>
<!--
<tr>
	<td>���� ���¼���<br>��ǰDB URL������</td>
	<td>
	<div><font color="#57a300">[��ü��ǰ]</font> <?if( file_exists('../../conf/engine/auctionos_all.php') && $fexist ){?><a href="../../data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos.php?mode=all" target=_blank><font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos.php?mode=all</font> <img src="../img/btn_naver_view.gif" align="absmiddle"></a><?}else{?>������Ʈ�ʿ�<?}?></font></div>
	<div><font color="#57a300">[����ǰ]</font> <?if(file_exists('../../conf/engine/auctionos_summary.php') && $fexist){?><a href="../../data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos.php?mode=summary" target=_blank><font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos.php?mode=summary</font> <img src="../img/btn_naver_view.gif" align="absmiddle"></a><?}else{?>������Ʈ�ʿ�<?}?></font></div>
	<div><font color="#57a300">[�űԻ�ǰ]</font> <?if($fexist){?><a href="../../data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos.php?mode=new" target=_blank><font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos.php?mode=new</font> <img src="../img/btn_naver_view.gif" align="absmiddle"></a><?}else{?>������Ʈ�ʿ�<?}?></font></div>
	</td>
	<td align="center"><font class="ver81">
		<?if(file_exists('../../conf/engine/auctionos_all.php'))echo date('Y-m-d h:i:s',filectime ( '../../conf/engine/auctionos_all.php'));?>
	</td>
	<td align="center">
		<a href="../../partner/auctionos.engine.php?mode=all" target='ifrmHidden'><img src="../img/btn_price_update.gif"></a>
	</td>
</tr>
-->
<tr><td colspan="12" class="rndline"></td></tr>
<tr>
	<td>��ٿ�<br>��ǰDB URL������</td>
	<td>
	<div><font color="#57a300">[��ü��ǰ]</font> <?if( file_exists('../../conf/engine/auctionos2_all.php') && $fexist2 ){?><a href="../../data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos2.php?mode=all" target=_blank><font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos2.php?mode=all</font> <img src="../img/btn_naver_view.gif" align="absmiddle"></a><?}else{?>������Ʈ�ʿ�<?}?></font></div>
	<div><font color="#57a300">[����ǰ]</font> <?if(file_exists('../../conf/engine/auctionos2_summary.php') && $fexist2){?><a href="../../data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos2.php?mode=summary" target=_blank><font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos2.php?mode=summary</font> <img src="../img/btn_naver_view.gif" align="absmiddle"></a><?}else{?>������Ʈ�ʿ�<?}?></font></div>
	<div><font color="#57a300">[�űԻ�ǰ]</font> <?if($fexist2){?><a href="../../data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos2.php?mode=new" target=_blank><font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos2.php?mode=new</font> <img src="../img/btn_naver_view.gif" align="absmiddle"></a><?}else{?>������Ʈ�ʿ�<?}?></font></div>
	</td>
	<td align="center"><font class="ver81">
		<?if(file_exists('../../conf/engine/auctionos2_all.php'))echo date('Y-m-d h:i:s',filectime ( '../../conf/engine/auctionos2_all.php'));?>
	</td>
	<td align="center">
		<? if ($godo[ecCode]=="self_enamoo_season" && $partner['useYn'] != 'y'){?>
		<img src="../img/btn_price_update.gif" style="cursor:hand;" onclick="javascript:alert('��뿩�θ� ������� �������ֽñ� �ٶ��ϴ�.');">
		<? }else{ ?>
		<a href="../../partner/auctionos2.engine.php?mode=all" target='ifrmHidden'><img src="../img/btn_price_update.gif"></a>
		<? } ?>
	</td>
</tr>
<tr><td colspan="12" class="rndline"></td></tr>
</table>
<div class="small1" ><img src="../img/icon_list.gif" align="absmiddle"><b><font color="ff6600">��ǰ���� ����ó� ��ǰ DB URL�� ���� ���� �ÿ��� �ݵ�� ������Ʈ��ư�� �����ּ���</font></B></div>
<div style="padding-top:2"></div>
<table align="center">
<tr><td width="500">
 <div align="center" class="small1" style='padding-bottom:3'><font color="6d6d6d">������Ʈ�� ����Ǹ� �Ʒ� �ٸ� ���� �������� ���̰� �˴ϴ�.<br>�Ϸ�޽����� ��µɶ����� �ٸ� ������ �ﰡ�Ͽ��ֽʽÿ�.</font></div>
		<div style="height:8px;font:0;background:#f7f7f7;border:2 solid #cccccc">
		<div id=progressbar style="height:8px;background:#FF4E00;width:0"></div>
 </div>
</td></tr>
</table>
<div align="center"><a href="https://amc.about.co.kr/" target="_blank"><img src="../img/about/btn_about_go.gif" border="0"></a></div>
<p>
<div id="MSG01">
<table cellpadding="2" cellspacing="0" border=0 class="small_ex">
<tr><td>
<div style="padding-top:6;"></div>
<img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe">��ǰDB URL�̶�?</span><BR>
&nbsp;&nbsp;����� ���θ��� ��ǰ����Ÿ ������ ���� ��ٿ��� ����ǵ��� �ϴ�<br>
&nbsp;&nbsp;"<B>������ ��ǰ���� ����Ÿ�� �Ѱ��� ���ִ� �������� �ּҰ�</B>"�Դϴ�.<br>
&nbsp;&nbsp;MMC�� ��ϵ� ��ǰDB URL�� �������� ���θ� ��ǰ�� �ڵ����� ���� ��ٿ����� �������� ������ �մϴ�.<br>
<br>
<div style="padding-top:6;"></div>
<img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe">������ �̸������?</span><BR>
&nbsp;&nbsp;���� ������ ��ǰDB URL�������� ������ Ȯ���� �� �ֽ��ϴ�.
<br>
<div style="padding-top:6;"></div>
<img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe">������Ʈ��?</span><BR>
&nbsp;&nbsp;���θ� ��ǰ������ �������� ���ļ��� ��ǰ���� ���� ������Ʈ�� �ʿ�� �ϰ� �Ǹ� �̶� ������ ������Ʈ�� Ŭ���Ͽ� ��ǰ DB URL �������� ���� ������Ʈ�� �����Ͻø�<BR>
&nbsp;&nbsp;��ǰ DB URL �������� ������Ʈ�� �Ǹ� ���������δ� ���� ��ٿ��� ������Ʈ �ֱ⿡ ���� ���� ��ٿ��� ��ǰ������ ������Ʈ �˴ϴ�.<BR>
&nbsp;&nbsp;-����: ���θ���ǰ�������� �� ������Ʈ ���� �� ���� ��ٿ� ������Ʈ(���� ��ٿ� ������Ʈ �ֱ⿡ ���� �ݿ�)
<br>
<div style="padding-top:6;"></div>
<img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe">���� ��ٿ� �������Һ�������?</span><BR>
&nbsp;&nbsp;�������Һ������� �Է�/������ ��ǰDB URL�� ������Ʈ�� �����ϸ� ��ǰDB URL ���� �� ������ ������ �ʵ��� pcard�ʵ��� ������ ����˴ϴ�.<BR>
&nbsp;&nbsp;����� �������Һ������� ���� ��ٿ� ������Ʈ �ֱ⿡ ���� ���ļ��ο� �ݿ��Ǿ����ϴ�.
</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>
<? include "../_footer.php"; ?>