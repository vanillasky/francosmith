<?
@include "../../conf/partner.php";
$location = "���̿��� ��ũ�� ���� > ���̿��� ��ũ�� ����/����";
include "../_header.php";
$curl = "../../conf/nateClipping.cfg.php";
if(file_exists($curl)) require $curl;

if(!$nateClipping['imgWidth'])$nateClipping['imgWidth']=$cfg['img_m'];
if(!$nateClipping['imgHeight'])$nateClipping['imgHeight']=$cfg['img_m'];

if($nateClipping['status'] == 2)$tag[0]="<img src=\"../img/natescrap2.gif\"/>";
else $tag[0]="<img src=\"../img/natescrap2off.gif\"/>";
if($nateClipping['status'] == 3){
	$tag[1]="<img src=\"../img/../img/natescrap3link.gif\"/>";
	$tag[2]="<img src=\"../img/natescrap3.gif\"/>";
}else{
	$tag[1]="<img src=\"../img/../img/natescrap3link.gif\"/>";
	$tag[2]="<img src=\"../img/natescrap3off.gif\"/>";
}
if($nateClipping['status'] == 4){
	$tag[1]="<img src=\"../img/natescrap4link.gif\"/>";
	$tag[2]="<img src=\"../img/natescrap4.gif\"/>";
}

?>
<script type="text/javascript">
function chk_proContentsLink(){
	var chk = document.getElementById('chklink');
	var obj = document.getElementById('proContentsLink');
	if(chk.checked == true){
		obj.value = "<?php echo $cfg['shopUrl'];?>";
	}
}
function copy_txt(val){
	window.clipboardData.setData('Text', val);
}
</script>
<div style="width:800">
<div class="title title_top">���̿��� ��ũ�� ���� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=19')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<div style="float:left;"><img src="../img/natescrap0.gif"/></div>
<div style="float:left;padding:0 20 0 20"><img src="../img/shopplus_process_arrow.gif"/></div>
<div style="float:left;"><?php echo $tag[0];?></div>
<div style="float:left;padding:0 20 0 20"><img src="../img/shopplus_process_arrow.gif"/></div>
<div style="float:left"><?php echo $tag[1];?></div>
<div style="float:left;padding:0 20 0 20"><img src="../img/shopplus_process_arrow.gif"/></div>
<div style="float:left"><?php echo $tag[2];?></div>
<div style="clear:both;" id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class="small_ex">
<?php if($nateClipping['status']!=3){?>
<tr>
<td>
	<div>���̿��� ��ũ�� ���� ��û�� �Ͻø� ���̿����� ����ڰ� ��û ���θ��� ���Ͽ� �ɻ翡 ���ϴ�.</div>
	<div>�ɻ�Ⱓ�� ��2�� ���� �ҿ�Ǹ�, �ɻ縦 ����Ͽ� ������ ������ �ٷ� ���̿��� ��ũ�� ���񽺸� ����Ͻ� �� �ֽ��ϴ�.</div>
</td>
</tr>
<?php }?>
<?php if($nateClipping['status']==3){?>
<tr><td><div>���������� ���� ������ �����̽��ϴ�.</div>
<div>�ش� ���θ��� ��ũ�� ���� ġȯ�ڵ带 �Է��Ͻø� �ٷ� ���̿��� ��ũ�� ���񽺸� �̿��Ͻ� �� �ֽ��ϴ�.</div></td></tr>
<?php }?>
</table>
</div>
<script>cssRound('MSG01')</script>
<div style="height:20"></div>
<input type="hidden" name="copy" id="copy" value=""/>
<form method="post" action="indb.php" enctype="multipart/form-data" target="ifrmHidden" <?php if($nateClipping['status']!='3')echo "disabled";?>>
<div class="title title_top">���θ��� ���̿��� ��ũ�� ��ư �����ϱ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=19')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>��ũ�� ��ư �̹���</td>
	<td>
	<div style="padding:2 0 0 0">
	<?php if($nateClipping['scrapBt']){?>
	<img src="../../data/skin/<?php echo $cfg['tplSkin'];?>/img/<?php echo $nateClipping['scrapBt'];?>" width="127" border="0"/>
	<?php }else{?>
	<img src="../img/natescrab_btn.gif" width="127" border="0"/>
	<?php }?>
	</div>
	<div style="padding:0 0 2 0">
	<input type="file" name="scrapbt"/> <span class="small1 extext">(��������� 127 x 20)</span>
	</div>
	</td>
</tr>
<tr>
	<td height="50">��ũ�� ��� ġȯ�ڵ�</td>
	<td>
	<div div style="padding-top:5;">{cyworldScrap} <img class="hand" src="../img/i_copy.gif" onclick="copy_txt('{cyworldScrap}')" alt="�����ϱ�" align="absmiddle"/></div>
	<div style="padding-top:10;" class="small1 extext">
	<div>�����Ͻ� <b>ġȯ�ڵ�</b>�� ��ǰ��ȭ�鿡 �����Ͻø� ���̿��� ��ũ�� ����� �����մϴ�.</div>
	</div>
	</td>
</tr>
<tr>
	<td><div>��ũ�� ��� ġȯ�ڵ�</div><div style="padding:5 0 5 0">���� ���</div></td>
	<td>
	<div style="padding-top:5">���ԵǴ� �ҽ� ������ : <a href="../../admin/design/codi.php" target="_blank">"���θ� ������ > �����ΰ���"</a> ���� Ʈ�� �޴����� "��ǰ > ��ǰ��ȭ��" �޴� Ŭ��</div>
	<div style="padding:5 0 5 0">ġȯ�ڵ� ���� ��ġ : [�ٷα���] ��ư �� �Ǵ� �Ʒ��� ġȯ�ڵ� ������ �����մϴ�.</div>
	</td>
</tr>
</table>
<p/>
<div class="title title_top">���̿��忡 ��ũ���Ǵ� ��ǰ ������ �����ϱ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=19')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>��ǰ �̹���</td>
	<td>
	<div style="padding:5 0 0 0">���̹����� �ڵ� ����</div>
	<div style="padding:2 0 0 0" class="small1 extext">��, �̹���ȣ���� �ּ�(URL)�� ��ǰ�̹����� ����Ͻ� ��� ��ũ���Ǵ� �̹��� ����� �����ϼž� �մϴ�.</div>
	<div style="padding:3 0 2 0">�̹���ȣ���ÿ� ��ǰ�̹��� ������ : <input type="text" size="3" name="imgWidth" value="<?php echo $nateClipping['imgWidth'];?>"/> x <input type="text" name="imgHeight" value="<?php echo $nateClipping['imgHeight'];?>" size="3"/></div>
	</td>
</tr>
<tr>
	<td height="30">��ǰ ����</td>
	<td>ª������,�ǸŰ�,�������� �ڵ� ����</td>
</tr>
</table>
<p/>
<div class="title title_top">���̿��忡 ��ũ���Ǵ� �߰� ������ �����ϱ� (���� ���) <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=19')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<div style="font:8pt dotum;color:#6D6D6D;padding:0 0 3 0"><span>���̿��� ��ũ�� ����� ��ũ�� �Ǵ� ��ǰ���� �� �߰��� ���θ��� ȫ���Ͻ� �� �ֽ��ϴ�. �Ʒ����� �߰� ������ �����ϼ���.</span></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>�ΰ� �̹���</td>
	<td>
	<div style="padding:2 0 0 0">
	<?php if($nateClipping['logo']){?>
	<img src="../../data/skin/<?php echo $cfg['tplSkin'];?>/img/<?php echo $nateClipping['logo'];?>" width="50" border="0"/>
	<?php }else{?>
	<img src="http://gongji.godo.co.kr/userinterface/clipping/images/logo_godo.gif" width="50" border="0"/>
	<?php }?>
	</div>
	<div style="padding:0 0 2 0">
	<input type="file" name="logo"/> <span class="small1 extext">(�̹��� ������ 50 x 20)</span>
	</div>
	</td>
</tr>
<tr>
	<td>���θ� ���� �Ұ�</td>
	<td>
	<div style="padding:2 0 0 0">
	<input type="text" name="proContents" style="width:350px" maxlength="100" value="<?php echo $nateClipping['proContents'];?>"/>
	</div>
	<div style="padding:0 0 2 0">
	<div class="noline"><label><input type="checkbox" id="chklink" name="chklink" onclick="chk_proContentsLink()"/> ��ǥ�����ε��</label></div>
	<div>��ũ  : http://<input type="text" style="width:313px" id="proContentsLink" name="proContentsLink" value="<?php echo $nateClipping['proContentsLink'];?>"/></div>
	</div>
	</td>
</tr>

</table>
<div class=button>
<?php if($nateClipping['status']!='3'){?>
<img type=image src="../img/btn_save.gif">
<?php }else{?>
<input type="image" src="../img/btn_save.gif">
<?php }?>
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>
</form>
<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>��ũ�� �̹����� ��Ų���� �����Ͻ� �� �ֽ��ϴ�.</td></tr>
<tr><td>���� ���°� ���񽺽����� ��쿡�� ��ũ�� ����� Ȱ��ȭ �˴ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>

</div>
<? include "../_footer.php"; ?>