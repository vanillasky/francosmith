<?

include "../../conf/config.pay.php";

$set = $set['tax'];

if(!$set['tax_delivery']) $set['tax_delivery'] = "n";

$checked['useyn'][$set[useyn]] = "checked";
$checked['step'][$set[step]] = "checked";
$checked['tax_delivery'][$set['tax_delivery']] = "checked";

$checked['use_a'][$set[use_a]] = "checked";
$checked['use_c'][$set[use_c]] = "checked";
$checked['use_o'][$set[use_o]] = "checked";
$checked['use_v'][$set[use_v]] = "checked";

?>

<form method=post action="../order/tax_indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="tax">

<div class="title title_top">���ݰ�꼭����<span>ȸ������ ����Ǵ� ���ݰ�꼭 ���� ��å�Դϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=7')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>���� ��뿩��</td>
	<td class=noline>
	<input type=radio name=useyn value='y' <?=$checked['useyn']['y']?>> ���
	<input type=radio name=useyn value='n' <?=$checked['useyn']['n']?>> ������
	</td>
</tr>
<tr>
	<td>���� ��������</td>
	<td class=noline>
	<input type=checkbox name=use_a <?=$checked['use_a']['on']?>> �������Ա�
	<input type=checkbox name=use_c <?=$checked['use_c']['on']?> disabled> �ſ�ī��
	<input type=checkbox name=use_o <?=$checked['use_o']['on']?>> ������ü
	<input type=checkbox name=use_v <?=$checked['use_v']['on']?>> �������
	</td>
</tr>
<tr>
	<td>���� ���۴ܰ�</td>
	<td class=noline>
	<input type=radio name=step value='1' <?=$checked['step']['1']?>> �Ա�Ȯ��
	<input type=radio name=step value='2' <?=$checked['step']['2']?>> ����غ���
	<input type=radio name=step value='3' <?=$checked['step']['3']?>> �����
	<input type=radio name=step value='4' <?=$checked['step']['4']?>> ��ۿϷ�
	</td>
</tr>
<tr>
	<td>��ۺ� ���Կ���</td>
	<td class=noline>
	<input type=radio name=tax_delivery value='y' <?=$checked['tax_delivery']['y']?>> ��ۺ� ����
	<input type=radio name=tax_delivery value='n' <?=$checked['tax_delivery']['n']?>> ��ۺ� ������
	</td>
</tr>
<tr>
	<td>�ΰ��̹���</td>
	<td>
	<input type="file" name="seal_up" size="50" class=line><input type="hidden" name="seal" value="<?=$set[seal]?>">
	<a href="javascript:webftpinfo( '<?=( $set[seal] != '' ? '/data/skin/' . $cfg['tplSkin'] . '/img/common/' . $set[seal] : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="�̹��� ����" align="absmiddle"></a>
	<? if ( $set[seal] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="seal_del" value="Y">����</span><? } ?>
	</td>
</tr>
</table>

<div class=button>
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ſ�ī�� �����ֹ��� ���ݰ�꼭�� �������� �ʽ��ϴ�.</td></tr>
<tr><td style="padding-left:7pt;">2004�� ������ �ΰ���ġ������ ���ϸ�, 2004�� 7�� 1�� ���� �ſ�ī��� ������ �ǿ� ���ؼ��� ���� ��꼭 ������ �Ұ�</font>�ϸ� �ſ�ī�� ������ǥ�� �ΰ���ġ�� �Ű�</font>�� �ϼž� �մϴ�.<br>
[ �ΰ���ġ���� ����� 57�� ���ù��� ���� ]</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ΰ��̹����� ������� ����/���� 74 pixel�� ����ð�, ���������� JPG �Ǵ� GIF���Ϸ� ���弼��.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Ϲݼ��ݰ�꼭 ������ �ȳ�
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>���ݰ�꼭�� ����� �ۼ��� �� ����߼��̳� ���� �����ϴ� ���� ���ݰ�꼭�� ���մϴ�.</li>
<li>���������� ���� ���ݰ�꼭�� �ս��� �ۼ�/���� �� �� �ֵ��� ����Ʈ ����� �����ϰ� �ֽ��ϴ�.</li>
</ol>
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>