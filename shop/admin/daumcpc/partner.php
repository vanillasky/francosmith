<?

@include "../../conf/daumCpc.cfg.php";

$location = "���� �����Ͽ� > �����Ͽ� �����ϱ�";
include "../_header.php";

if(!$daumCpc['useYN']) $daumCpc['useYN']='N';
if($daumCpc['useYN']) $checked['useYN'][$daumCpc['useYN']]='checked';
?>
<script type="text/javascript">
function copy_txt(val){
	window.clipboardData.setData('Text', val);
}
function check_use(){
	var conf = document.getElementById('configration');
	var chk = document.getElementsByName('useYn');
	var obj = document.getElementsByName('daumCpc[useYN]')[0];
	if(chk[0].checked == true){
		conf.disabled = false;
		obj.value='Y';
	}else{
		conf.disabled = true;
		obj.value='N';
	}
}
function review_init(){
	document.form.mode.value = 'review_init';
	document.form.submit();
	document.form.mode.value = 'daumCpc';
}
window.onload = function(){
	check_use();
}
</script>
<div style="width:800px">
<div class="title title_top">�����Ͽ� �����ϱ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=18')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table class="tb" border="0">
<col class="cellC"><col class="cellL">
<?
@include "../../conf/fieldset.php";
list($grpnm,$grpdc) = $db->fetch("select grpnm,dc from ".GD_MEMBER_GRP." where level='".$joinset[grp]."'");
$url = "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/partner/daumCpc.php";
$allUrl = "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/partner/daum_all.php";
$sumUrl = "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/partner/daum_some.php";
$reviewUrl = "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/partner/daum_review.php";
?>
<tr>
	<td>��� ����</td>
	<td class="noline">
		<input type="radio" name="useYn" value="Y" onclick="check_use();" <?=$checked['useYN']['Y']?>> ��� &nbsp;<input type="radio" name="useYn" value="N" onclick="check_use();" <?=$checked['useYN']['N']?>> �̻��
	</td>
</tr>
</table>
<p/>
<form name="form" method="post" action="indb.php" target="ifrmHidden" >
<input type="hidden" name="mode" value="daumCpc">
<input type="hidden" name="daumCpc[useYN]" value="<?=$daumCpc['useYN']?>">
<div id="configration">
<table class="tb" border="0">
<col class="cellC"><col class="cellL">
<tr>
	<td>��ǰ���� ����</td>
	<td class="noline">
	<div><b><?=$grpnm?></b> �������� <b><?=number_format($grpdc)?>%</b>�� ��ǰ���ݿ� ����Ǿ� �����Ͽ쿡 ���� �˴ϴ�.</div>
	<div class="extext" style="padding:5 0 0 0">�����Ͽ쿡�� ����Ǵ� ��ǰ������ ����� ������ ���Խ� ȸ���׷��� �������� ����� ������ �˴ϴ�.</div>
	<div class="extext" style="padding:5 0 0 0">���� �����Ͽ� ��å�� ���� ���� ������ ������ <b>��ǰ���ݿ� ������� �ʽ��ϴ�.</b></div>
	<div class="extext" style="padding:1 0 0 0">- �ű԰���ȸ����� ����</div>
	<div class="extext" style="padding:1 0 0 0">- ��� �ݾ� ���� ���� (5���� �̻� ���� �ÿ��� ����� �� �ִ� ���� ��)</div>
	<div class="extext" style="padding:1 0 0 0">- ���� ���� ���� ���� (������ �Աݿ����� ����� �� �ִ� ���� ��)</div>
	<div class="extext" style="padding:1 0 0 0">- �ٿ�ε� Ƚ�� ���� ���� (1ȸ�� �ٿ�޾� ����� �� �ִ� ���� ��, �ٿ�ε� Ƚ�� ������ �ִ� ����)</div>
	<div class="extext" style="padding:5 0 0 0">���Խ� ȸ���׷� ������ <a href="../member/fieldset.php" class="extext" style="font-weight:bold">ȸ������ > ȸ�����԰���</a>���� ���� �����մϴ�.</div>
	<div class="extext" style="padding:1 0 0 0">ȸ���׷��� ������ ������ <a href="../member/group.php" class="extext" style="font-weight:bold">ȸ������ > ȸ���׷���� </a>���� ���� �����մϴ�.</div>
	</td>
</tr>
<tr>
	<td>���������Ͽ�<br/>�������Һ�����</td>
	<td>
	<input type="text" name="daumCpc[nv_pcard]" value="<?=$daumCpc['nv_pcard']?>" class="lline">
	<div class="extext" style="padding:5 0 0 0">��) �Ｚ2~3/�Ե�3/����6</div>
	</td>
</tr>
<tr>
	<td>���������Ͽ�<br />��ǰ�� �Ӹ��� ����</td>
	<td>
	<div><input type=text name="daumCpc[goodshead]" value="<?=$daumCpc['goodshead']?>" class="lline"></div>
	<div class="extext" style="padding:5 0 0 0">* ��ǰ�� �Ӹ��� ������ ���� ġȯ�ڵ�</div>
	<div class="extext" style="padding:1 0 0 0">- �Ӹ��� ��ǰ�� �Էµ� "������"�� �ְ� ���� �� : {_maker}</div>
	<div class="extext" style="padding:1 0 0 0">- �Ӹ��� ��ǰ�� �Էµ� "�귣��"�� �ְ� ���� �� : {_brand}</div>
	</td>
</tr>
</table>
<p/>
<table width=100% cellpadding=0 cellspacing="0">
<col class="cellC"><col style="padding:5px 10px;line-height:140%">
<tr class="rndbg">
	<th colspan="2" align="center">��ǰ DB URL</th>
</tr>
<tr><td class="rnd" colspan="10"></td></tr>
<tr>
	<td>���� �����Ͽ�<br>��ǰ DB URL</td>
	<td>
	<div style="clear:both">
		<div style="color:#57a300;float:left;">[��ü��ǰ]</div>
		<div class="ver8" style="float:left;width:500px;padding:2"><?php echo $allUrl;?></div>
		<div style="float:left;"><a href="../../partner/daum_all.php" target=_blank><img src="../img/btn_naver_view.gif" align="absmiddle"></a></div>
	</div>
	<div style="clear:both">
		<div style="color:#57a300;float:left;">[��ü��ǰ(��)]</div>
		<div class="ver8" style="float:left;width:479px;padding:2"><?php echo $url;?></div>
		<div class="ver8" style="float:left;"><a href="../../partner/daumCpc.php" target=_blank><img src="../img/btn_naver_view.gif" align="absmiddle"></a></div>
	</div>
	<div style="clear:both">
		<div style="color:#57a300;float:left;">[����ǰ]</div>
		<div class="ver8" style="float:left;width:500px;padding:2"><?php echo $sumUrl;?></div>
		<div class="ver8" style="float:left;"><a href="../../partner/daum_some.php" target=_blank><img src="../img/btn_naver_view.gif" align="absmiddle"></a></div>
	</div>
	<div style="clear:both">
		<div style="color:#57a300;float:left;">[��ü��ǰ��]</div>
		<div class="ver8" style="float:left;width:488px;padding:2"><?php echo $reviewUrl.'?total=y';?></div>
		<div class="ver8" style="float:left;"><a href="../../partner/daum_review.php?total=y" target=_blank><img src="../img/btn_naver_view.gif" align="absmiddle"></a></div>
	</div>
	<div style="clear:both">
		<div style="color:#57a300;float:left;">[����ǰ��]</div>
		<div class="ver8" style="float:left;width:488px;padding:2"><?php echo $reviewUrl;?></div>
		<div class="ver8" style="float:left;"><a href="../../partner/daum_review.php" target=_blank><img src="../img/btn_naver_view.gif" align="absmiddle"></a></div>
	</div>
	</td>
</tr>
<tr><td colspan=12 class=rndline></td></tr>
</table>
</div>

<table class="tb" border="0">
<col class="cellC"><col class="cellL">
<tr>
	<td>��ǰ�� ���� �ʱ�ȭ</td>
	<td class="noline">
		<div><a href="javascript:review_init();"><img src="../img/btn_review_init.png" align="absmiddle"></a></div>
		<div class="extext" style="padding:5 0 0 0">������ ��ϵǾ��� ��ǰ���� ���������Ͽ쿡 ������� ���� �� ��ǰ�� ���� �ʱ�ȭ�� ���Ѻ��ñ� �ٶ��ϴ�.</div>
		<div class="extext" style="padding:5 0 0 0">��ǰ�� ���� �ʱ�ȭ �� ���� ������ ��ü ��ǰ���� ������Ʈ �˴ϴ�.</div>
	</td>
</tr>
</table>

<div style="padding-top:10px;">
<div class="red" style="border: solid #dce1e1 4px; padding: 10px;">�� ����!: gif�������� �� ��ǰ�̹����� ���� �����Ͽ�(�����Ͽ� ��å�� ����)�� ������ ���� �ʽ��ϴ�.
<div class="red" style="padding-left:50">jpg �� �ٸ� �̹����������� ��ǰ�̹����� ���� �� �����Ͽ� ������ ���ֽñ� �ٶ��ϴ�.</div></div>
</div>

<div class=button>
<input type=image src="../img/btn_register.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>
</form>
<p/>
<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���������Ͽ� �������Һ�������?: �� ī��纰 ������������ �Է��Ͻ� �� �ֽ��ϴ�. ��) �Ｚ2~3/�Ե�3/����6</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">����� �������Һ������� ���ļ��� ������Ʈ �ֱ⿡ ���� ���ļ��ο� �ݿ��Ǿ����ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���������Ͽ쿡 ����Ǵ� ��ǰ������ �ٽ� ����Ͻô� ���� �ƴմϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� ����� ���θ��� ��ǰ������ ���������Ͽ찡 ���� ������ �ڵ����� �������ϴ�.</td></tr>
</table>
<br/>
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���������Ͽ쿡�� ��ǰ�˻��� ���� �� �� �ֵ��� ��ǰ�� �Ӹ��� ������ Ȱ���ϼ���!</td></tr>
<tr><td style="padding-left:10">���� 1) ��ǰ�� �Ӹ��� ���� : ����</td></tr>
<tr>
	<td style="padding-left:10">
	<table style='border:1px solid #ffffff;width:400' class="small_ex">
	<col align="center" width="60"><col align="center" width="50"><col align="center" width="50"><col>
	<tr>
		<td>��ǰ��</td>
		<td>������</td>
		<td>�귣��</td>
		<td>���� ��ǰ��</td>
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
<tr><td style="padding-left:10">���� 2) ��ǰ�� �Ӹ��� ���� : [������ / {_maker} / {_brand}]</td></tr>
<tr>
	<td style="padding-left:10">
	<table style='border:1px solid #ffffff;width:400' class="small_ex">
	<col align="center" width="60"><col align="center" width="50"><col align="center" width="50"><col>
	<tr>
		<td>��ǰ��</td>
		<td>������</td>
		<td>�귣��</td>
		<td>���� ��ǰ��</td>
	</tr>
	<tr>
		<td>����û����</td>
		<td>������</td>
		<td>����</td>
		<td>[������ / ������ / ����] ����û����</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</div>
<script>cssRound('MSG01')</script>
</div>
<? include "../_footer.php"; ?>