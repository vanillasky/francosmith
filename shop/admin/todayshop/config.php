<?
$location = "�����̼� > �⺻��������";
include "../_header.php";

$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' ���� ��û�ȳ��� ���� �����ͷ� �������ֽñ� �ٶ��ϴ�.', -1);
}
$tsCfg = $todayShop->cfg;

if(!$tsCfg['useTodayShop']) $tsCfg['useTodayShop'] = 'n';
$checked['useTodayShop'][$tsCfg['useTodayShop']] = 'checked';

if(!$tsCfg['shopMode']) $tsCfg['shopMode'] = 'regular';
$checked['shopMode'][$tsCfg['shopMode']] = 'checked';

if(!$tsCfg['useEncor']) $tsCfg['useEncor'] = 'n';
$checked['useEncor'][$tsCfg['useEncor']] = 'checked';

if(!$tsCfg['useGoodsTalk']) $tsCfg['useGoodsTalk'] = 'n';
$checked['useGoodsTalk'][$tsCfg['useGoodsTalk']] = 'checked';

if(!$tsCfg['useSMS']) $tsCfg['useSMS'] = 'n';
$checked['useSMS'][$tsCfg['useSMS']] = 'checked';

if(!$tsCfg['useReserve']) $tsCfg['useReserve'] = 'n';
$checked['useReserve'][$tsCfg['useReserve']] = 'checked';

?>
<style type="text/css">
img {border:none;}
</style>
<script type="text/javascript">
function enableForm(mode) {
	var fobj = document.frmConfig;
	var el = fobj.document.getElementsByTagName("INPUT");
	for(var i = 0; i < el.length; i++) {
		if (!(el[i].name == "useTodayShop" || el[i].type == "image")) el[i].disabled = !mode;
	}
}

function copy_txt(val){
	window.clipboardData.setData('Text', val);
}
</script>

<div style="width:100%">
	<form name="frmConfig" method="post" action="indb.config.php" target="ifrmHidden" />
		<div class="title title_top">�������� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=2')"><img src="../img/btn_q.gif"></a></div>
		<table class=tb>
		<col class=cellC><col class=cellL>
		<tr>
			<td>�����̼� ��뼳��</td>
			<td class="noline">
				<label><input type="radio" name="useTodayShop" value="y" <?=$checked['useTodayShop']['y']?> onclick="enableForm(true)" />���</label>
				<label><input type="radio" name="useTodayShop" value="n" <?=$checked['useTodayShop']['n']?> onclick="enableForm(false)" />�̻��</label>
				<span class="small"><font class="extext">�����̼� ��뿩�θ� �����մϴ�.</font></span>
			</td>
		</tr>
		<tr>
			<td>���θ� ����ȭ�鼳��</td>
			<td class="noline">
				<label><input type="radio" name="shopMode" value="regular" <?=$checked['shopMode']['regular']?> />�Ϲݼ��θ� ���� ȭ���� ����մϴ�.</label>
				<label><input type="radio" name="shopMode" value="todayshop" <?=$checked['shopMode']['todayshop']?> />�����̼� ���� ȭ���� ����մϴ�.</label>
				<span class="small"><font class="extext">�������� ������ ȭ���� �����մϴ�.</font></span>
				<div class="small">
					<div><font class="extext">�Ϲݼ��θ����� ��ʸ�ũ�� ����Ͽ� �����̼��� ����� ��� �����̼� ������ ��ǰ url�� �����Ͽ� ������ �ֽñ� �ٶ��ϴ�.</font></div>
					<div><font class="extext">�����̼� ������ ��ǰ URL : http://<?=$_SERVER['HTTP_HOST'].$cfg['rootDir']?>/todayshop</font><img class="hand" src="../img/i_copy.gif" onclick="copy_txt('http://<?=$_SERVER['HTTP_HOST'].$cfg['rootDir']?>/todayshop')" alt="�����ϱ�" align="absmiddle" /></div>
				</div>
			</td>
		</tr>
		<tr>
			<td>���� ��� ����</td>
			<td class="noline">
				<label><input type="radio" name="useEncor" value="y" <?=$checked['useEncor']['y']?> />���</label>
				<label><input type="radio" name="useEncor" value="n" <?=$checked['useEncor']['n']?> />�̻��</label>
				<span class="small"><font class="extext">��������� ������ ������������� ����� ����˴ϴ�.</font></span>
			</td>
		</tr>
		<tr>
			<td>��ǰ��ũ ����</td>
			<td class="noline">
				<label><input type="radio" name="useGoodsTalk" value="y" <?=$checked['useGoodsTalk']['y']?> />���</label>
				<label><input type="radio" name="useGoodsTalk" value="n" <?=$checked['useGoodsTalk']['n']?> />�̻��</label>
				<span class="small"><font class="extext">��ǰ��ũ ��������� ������ ������������� ����˴ϴ�.</font></span>
			</td>
		</tr>
		<tr>
			<td>������ ���� ���</td>
			<td class="noline">
				<label><input type="radio" name="useReserve" value="y" <?=$checked['useReserve']['y']?> />���</label>
				<label><input type="radio" name="useReserve" value="n" <?=$checked['useReserve']['n']?> />�̻��</label>
				<span class="small"><font class="extext">�����̼� ��ǰ ���Ž� ������ ��� ���θ� �����մϴ�.</font></span>
			</td>
		</tr>
		</table>

		<div class="button">
			<input type=image src="../img/btn_register.gif">
			<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
		</div>

		<div style="padding-top:15px"></div>
	</form>
</div>

<div style="clear:both;" id=MSG01>
	<table cellpadding=1 cellspacing=0 border=0 class="small_ex">
	<tr>
		<td>
			<div>�����̼����񽺸� ��û�� ���� �Ϲݼ��θ��� �����̼��� ��� ����Ͻ� �� �ֽ��ϴ�.</div>
			<div>&nbsp;</div>
			<div>�� ��뿩�μ��� : �����̼� ���񽺸� ��û�Ͻ� ���� �⺻�������� ��뼳���� �Ͻ� �� �ֽ��ϴ�.</div>
			<div>���������� ������ ��� �Ϲ� ���θ��θ� ��Ͻ� �� �ֽ��ϴ�.</div>
			<div>&nbsp;</div>
			<div>�� ���θ� ����ȭ�� ���� : �����̼��� ��������� �����Ͻø� ���� ���θ� ������ �Բ� ���־�� �մϴ�.</div>
			<div>����ȭ�� ������ �Ϲݼ��θ��� �� ��� ���θ� ù ȭ���� �Ϲݼ��θ� ������ ��������, �����̼����� ������ ��� ���θ� ù ȭ�鿡 �����̼� ������ ����˴ϴ�.</div>
			<div>�� ���� ���θ��� �Բ� ����Ͽ� �̺�Ʈ �������� Ȱ���Ͻ� �� �ֽ��ϴ�. </div>
			<div>��) �Ϲ� ���θ� ���� �� ���, ��ũ ��ư�� �����Ͽ� �����̼����� ������ �� �ֽ��ϴ�.</div>
			<div>&nbsp;</div>
			<div>�� ���ݱ�� ���� : ���ݱ���� �̹� �ǸŰ� �Ϸ�� ��ǰ�� �����(��)�� �� �����ǻ簡 �ִ� ��� ���ݽ�û�� �� �� �ִ� ������� �� ����� ��뼳�� �Ͻø� ���ݱ���� ����� �������� ��Ÿ���ϴ�.</div>
			<div>&nbsp;</div>
			<div>�� ��ǰ��ũ���� : ��ǰ��ũ�� ��ǰ �������� �Һ��ڿ� �Һ���, �Һ��ڿ� �Ǹ��ڰ� �����Ӱ� Ŀ�´����̼��� �� �ִ� ��� ����Դϴ�.</div>
			<div>�� ����� �ʿ�� ���� ���� ��� '������'���� �����Ͻø� ������� �ʽ��ϴ�.</div>
			<div>&nbsp;</div>
			<div>�Ϲ� ���θ��� �������� ��������� �����ϼ̴ٸ�!</div>
			<div>���θ� ���ο� �����̼� ��ǰ�������� ����Ǵ� ��ũ ��ʿ����� ���弼��.</div>
			<div>&nbsp;</div>
			<div>�����̼� ���� ��� �����
			<div>���伥���� ��� �̹����� ����� �� <a href="javascript:popup('../todayshop/codi.banner.php',980,700)"><font class="extext_l">������>�ΰ�/��ʰ���</font></a>���� �ű� ��ʸ� ����Ͻø� �˴ϴ�.</div>
			<div>&nbsp;</div>
			<div>�� ������ ���� ��� ���� :  �����̼� ��ǰ ���Žÿ��� �������� ����� �� �ֵ��� �����ϴ� ����Դϴ�.<br>
			��������� ������ ���θ� �⺻ ������ ��å �Ǵ� ��ǰ���� ���� �Է��Ͽ� ����� �� �ֽ��ϴ�.</div>
		</td>
	</tr>
	</table>
</div>

<script type="text/javascript">
	enableForm(<?=($tsCfg['useTodayShop'] == 'y')? 'true' : 'false'?>);
	cssRound('MSG01');
</script>
<? include "../_footer.php"; ?>