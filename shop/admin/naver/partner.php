<?

@include "../../conf/partner.php";

$location = "���̹� ���� > ���̹� ���� ����";
include "../_header.php";

// ��ǰ���� ����
$inmemberdc = ($partner['unmemberdc'] == 'Y' ? 'N' : 'Y');
$incoupon = ($partner['uncoupon'] == 'Y' ? 'N' : 'Y');
$naver_version = $partner['naver_version'];
$useYn = $partner['useYn'];
$naver_event_common = ($partner['naver_event_common'] === 'Y' ? 'Y' : 'N');
$naver_event_goods = ($partner['naver_event_goods'] === 'Y' ? 'Y' : 'N');
$auto_create_use = ($partner['auto_create_use'] === 'Y' ? 'Y' : 'N');
$checked['cpaAgreement'][$partner['cpaAgreement']] = "checked";
$checked['inmemberdc'][$inmemberdc] = "checked";
$checked['incoupon'][$incoupon] = "checked";
$checked['naver_version'][$naver_version] = "checked";
$checked['useYn'][$useYn] = "checked";
$checked['naver_event_common'][$naver_event_common] = "checked";
$checked['naver_event_goods'][$naver_event_goods] = "checked";
$checked['auto_create_use'][$auto_create_use] = "checked";

//����ȣ����, �ܺ�ȣ����
$outsideServer = false;
if($godo['webCode'] == 'webhost_outside' || $godo['webCode'] == 'webhost_server'){
	$outsideServer = true;
}

if(isset($partner['cpaAgreementTime'])===false && $partner['cpaAgreement']==='true')
{
	$partner['cpaAgreementTime'] = date('Y.m.d h:i', filemtime(dirname(__FILE__).'/../../conf/partner.php'));
	require_once dirname(__FILE__).'/../../lib/qfile.class.php';
	$qfile = new qfile();
	$partner = array_map("addslashes",array_map("stripslashes",$partner));
	$qfile->open(dirname(__FILE__).'/../../conf/partner.php');
	$qfile->write("<? \n");
	$qfile->write("\$partner = array( \n");
	foreach ($partner as $k=>$v) $qfile->write("'$k' => '$v', \n");
	$qfile->write(") \n;");
	$qfile->write("?>");
	$qfile->close();
}
?>

<?php include dirname(__FILE__).'/../naverCommonInflowScript/configure.php'; ?>

<div class="title title_top">���̹� ���� ����<span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table border=4 bordercolor=#dce1e1 style="border-collapse:collapse; width: 800px;">
<tr><td style="padding:7 0 10 10">
<div style="padding-top:5"><b><font color="#bf0000">*�ʵ�*</font> ���̹� ���� �������� �ȳ��Դϴ�.</b></div>
<div style="padding-top:7"><font class=g9 color=666666>���̹� ���� ��ǰDB URL ������ ���׷��̵�(1.0 �� 2.0) �Ǿ����ϴ�.</font></div>
<div style="padding-top:5"><font class=g9 color=666666>���׷��̵�� ���� ������� ���ǻ��� �Դϴ�. �ݵ�� Ȯ���Ͻ� �� ������ �ֽñ� �ٶ��ϴ�.</font></div>
<div style="padding-top:5"><font class=g9 color=666666>1) ���̹� ���� 1.0 ��� ������ ���� ������ ������ �ʴ� ���</font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;- ������ ����Ͻô� 1.0�������ε� ���̹� ���� ���񽺸� �̿��Ͻ� �� �ֽ��ϴ�.</font></div>
<div style="padding-top:5"></div>
<div style="padding-top:5"><font class=g9 color=666666>2) ���̹� ���� 1.0 ���� 2.0���� �����ϰ��� �ϴ� ���</font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;- ���̹� ���� 2.0 ���� ������ �����մϴ�.</font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;��, ������ ���̹� ���� ������ ���̹� ���ο� ������ EP������ �����ؾ� �մϴ�. </font><font color="#bf0000"><U>�����ϰ� �������� ���� ��� ��ǰ Data�� ��� �����˴ϴ�.</U></font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;*���� ���� ���*&nbsp;</font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;���� ���̹� ������Ʈ���� > ��ǰ���� > ������Ʈ ��Ȳ > ���θ� ��ǰDB(EP) URL���� 2.0 ���� ���� ������ �� �ֽ��ϴ�.<a href="http://adadmin.shopping.naver.com/login/login_start" target="_blank">[�����ϱ�]</a></font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;���̹����� 2.0 ���� ������ �� �� �ַ�� ������ ������������ 2.0 ���� ������ ���ּž� �մϴ�. ���ù��� : �� �������� 02-567-3719</font></div>
<div style="padding-top:5"><font class=g9 color=666666>3) ���̹� ���� 2.0 ���� ��� �� ��ǰ �̹��� ����</font></div>
<div style="padding-top:5"><font class=g9 color=666666>
	&nbsp;&nbsp;&nbsp;&nbsp;- ���� �̹��� : ��ϵ� ��ǰ�� "Ȯ��(����)�̹���"�� ������.(��, "Ȯ��(����)�̹���"�� ���� ��� "���̹���"�� ��ü�Ͽ� ����.<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"���̹���"�� ���� ��� ���̹� ���� ��ǰ����� ���� �ʽ��ϴ�.)
</font></div>
<div style="padding-top:5"><font class=g9 color=666666>
	&nbsp;&nbsp;&nbsp;&nbsp;- �̹��� ������ : �ּ� 300 * 300 pixels �̻�(���� 500 * 500 pixels �̻�), �ִ� 1200 * 1200 pixels ����(1MB �̸�)
</font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;- �̹��� Ÿ�� : JPEG</font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;- Ȯ��(����)�̹����� ���̹����� ��ϵ��� ���� ��ǰ�� ���̹� ���ο� ���޵��� �ʽ��ϴ�.</font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;- �߰����� : ���� �ּ�ȭ �� �̹��� �߾� �����Ͽ� ����</font></div>
<div style="padding-top:5"><font class=g9 color=666666>
	&nbsp;&nbsp;&nbsp;&nbsp;�� ������ ������ �̹��� ������, �뷮, Ÿ���� ���� �ʰų� �ָ� ȿ���� ���� ��ǰ�� �̹����� ������ ���� �ܰ�����, ��������,<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;�������� ��ũ, �ؽ�Ʈ ���� ���ԵǾ� �ִ� �̹����� ������� ������, ���̹� ���ο��� ���� ó�� �� �� ������ �����Ͽ� �ֽñ� �ٶ��ϴ�.
</font></div>
</td></tr>
</table>

<div style="padding-top:10"></div>

<form name=form method=post action="indb.php" style="width: 800px;" id="naver-service-configure" target="ifrmHidden">

<style type="text/css">
div#cpa-terms{
	border: solid #dce1e1 4px;
	width: 800px;
	margin-bottom: 20px;
	padding: 10px;
}
div#cpa-terms h2{
	font-size: 15px;
}
div#cpa-terms.summary .hide{
	display: none;
}
div#cpa-terms.detail .hide{
	display: block;
}
div#cpa-terms div.view{
	text-align: right;
}
div#cpa-terms div.view button{
	padding: 3px;
	margin: 0;
	background-color: #f9feef;
	border: solid 1px #cccccc;
}
div#cpa-terms.summary div.view button.detail-view{
	display: inline;
}
div#cpa-terms.summary div.view button.summary-view{
	display: none;
}
div#cpa-terms.detail div.view button.detail-view{
	display: none;
}
div#cpa-terms.detail div.view button.summary-view{
	display: inline;
}
#premium-log-analyze-info{
	border: solid #dce1e1 4px;
	width: 800px;
	margin-bottom: 20px;
	padding: 10px;
}
</style>
<div id="cpa-terms" class="summary">
	<div>
		���̹� ���� CPA�� �˻������ָ� ���� �����̾� �α� �м��� ����Կ� �־� ���뽺ũ��Ʈ ��ġ�� ���� �����Ǵ� �׸��� �Ʒ��� �����ϴ�.<br/>
		�ش� �׸��� �������� ������ ��ũ��Ʈ ��ġ ���ǿ� ������� ���� ���̹����� �ش� ���� ������ �� ��쿡�� �Ͼ�ϴ�.
	</div>
	<h2>[���̹� ���� CPA ���� ���� �� �׸�]</h2>
	<ol>
		<div>CPA��, ���̹� ������ ���� ������ ����Ʈ���� �ֹ� �߻���, �ֹ��� ������ ������ �����ϴ� ����Դϴ�.</div>
		<div>��, CPA���� �ǹ��ϴ� ACTION�� �ֹ��߻��� �ǹ��մϴ�.</div></br>

		<li>
			<div>1. ������ ��������:</div>
			<div>- ���̹� ������ ���� ���ԵǴ� Ʈ����(Traffic)�� ���� ������ȯ ȿ������</div></br>
		</li>

		<li>
			<div>2. ���� ������ �׸�:</div>
			<div>- ������ ���θ����� �߻��ϴ� �̿��� �ֹ��� �Ͻ� / ��ȣ / ��ǰ / ���� / �ݾ� ��</div></br>
		</li>

		<li>
			<div>3. ���� ������ Ȱ�����:</div>
			<div>- ������ ������ ���� ����� ���̹� ���� ����� ���̹� (��) (���� 'ȸ��'�� ��)�� ���� �м� ������ Ȱ��</div>
			<div>- ������ ������ ���� ����� ���̹� ���� DB������ �������(��ŷ) ���� ��ҷ� Ȱ��</div></br>
		</li>

		<li class="hide">
			<div>4. ������ ���� ���� �ֿ����:</div>
			<div>- �����ִ� ȸ�簡 �����ϴ� ��ũ��Ʈ ��ġ���̵忡 ���� ���θ��� ��ũ��Ʈ�� ��ġ�մϴ�.</div>
			<div>- (���̹��� ���޵� ȣ���û縦 �̿��ϴ� ��� ȣ���û翡�� ȣ���û� �ַ�ǿ� ��ũ��Ʈ�� �ϰ� ��ġ�մϴ�. ���� �������ǽ� �ֹ������� ȸ�翡�� �����˴ϴ�.)</div>
			<div>- �����ִ� ȸ�簡 �����ϴ� ��ũ��Ʈ ��ġ���̵忡�� ���� ������ ���� ���å�� �ؼ��Ͽ��� �ϸ�, �����ְ� �ش� ���å ���ݽ� ȸ��� ������å�� ���� �����ָ� ������ �� �ֽ��ϴ�.</div>
			<div>- �����ִ� ȸ�簡 ��û�ϴ� ��� �������� ������ ������ ������ ���� ȸ�簡 ���� �Ⱓ�� ��Ŀ� ���� ���̹� ���� Ʈ����(Traffic)�� ���� ���θ����� �߻��� �ŷ�����(�ֹ��Ϸ�, �����Ϸ�)�� ���/ȯ��/��ǰ������ ȸ�翡�� �����մϴ�.</div>
			<div>- �����ִ� CPA ������ ���� ���� ���Ķ� ������ �ڽ��� �Ǵܿ� ���� ȸ�翡 ���� �����ϰ� ���θ� �� ��ũ��Ʈ�� ���������ν� �� ���Ǹ� öȸ�� �� �ֽ��ϴ�.</div>
			<div>- (���̹��� ���޵� ȣ���û� �����ִ� ���� öȸ�� ���� ��� ȸ�翡 �뺸�Ͽ� ���� öȸ�� ������ �� ������ ȣ���û翡�� �ֹ����� ������ �ߴ��ϰ� �˴ϴ�.)</div>
			<div>- ȸ��� ������ ������ ���� ���ǿ� ��ũ��Ʈ ��ġ�� �Ϸ�� ���ĺ��� CPA ������ ������ �����ϸ�, �����ְ� ���Ǹ� öȸ�ϰų� �������� ���å �������� ȸ�簡 ������ġ�ν� ������ ������ �ߴ��ϱ� ������ CPA �����͸� ��� ������ �� �ֽ��ϴ�.</div>
			<div>- ȸ��� ������ ���� �� ������ ��ũ��Ʈ ��ġ ���� ������ ��3�ڿ��� ��Ź�Ͽ� ó���� �� �ֽ��ϴ�.</div></br>
		</li>

		<li class="hide">
			<div>5. ������ ���� ������ ���� �׽�Ʈ:</div>
			<div>- CPA �������� ���� ������� ȸ��� �������� �������� ���ۿ��θ� �����ϱ� ���� �ֱ������� ����͸� �� �׽�Ʈ �ֹ��� �߻���ų �� �ֽ��ϴ�.</div>
			<div>- �ֹ� �׽�Ʈ�� 1ȸ �� 4~10�� ���� ����Ǹ�, �ش� �ֹ��� �׽�Ʈ �� ��� ��� ó���մϴ�.</div></br>
		</li>
	</ol>
	<div class="view">
		<button type="button" class="detail-view" onclick="document.getElementById('cpa-terms').className='detail';">�ڼ��� ����</button>
		<button type="button" class="summary-view" onclick="document.getElementById('cpa-terms').className='summary';">������</button>
	</div>
	<div>������ ��� CPA ������ ���� ���� �ֿ� ���׿� ����� ������ ������ ������ ���� �����մϴ�.</div>
</div>
<div id="premium-log-analyze-info" class="red">�����̾� �α׺м� ���� �����ڵ� �ݵ�� CPA�������ּž� �ϸ�, ���̹� ���� �̿��� �� �Ͻô� ��� �Ʒ� ������ ������ �Ͼ�� �ʽ��ϴ�.</div>

<input type=hidden name=mode value="naver">

<table class=tb border=0>
<col class=cellC><col class=cellL>
<?
@include "../../conf/fieldset.php";
list($grpnm,$grpdc) = $db->fetch("select grpnm,dc from ".GD_MEMBER_GRP." where level='".$joinset[grp]."'");
?>
<tr>
	<td>��뿩��</td>
	<td class="noline">
	<label><input type="radio" name="useYn" value="y" <?php echo $checked['useYn']['y'];?>/>���</label><label><input type="radio" name="useYn" value="n" <?php echo $checked['useYn']['n'];?> <?php echo $checked['useYn'][''];?> />������</label>
	</td>
</tr>
<tr>
	<td>CPA �ֹ�����<br/>���ǿ���</td>
	<td class="noline">
		<label><input type="checkbox" name="cpaAgreement" value="true" required="required" <?php echo $checked['cpaAgreement']['true']; ?>/> CPA �ֹ������� ������</label>
		<?php if(isset($partner['cpaAgreementTime'])){ ?>
		<span style="margin-left: 30px; color: #991299; font-weight: bold;">�����Ͻ� : <?php echo $partner['cpaAgreementTime']; ?></span>
		<?php } ?>
		<br/>
		<span class="extext">
			���̹����� CPA �ֹ������� �����Ͻ� ��쿡�� �ֹ��Ϸ�� �ֹ������� ���̹������� �����մϴ�.<br/>
			CPA �ֹ������� ���������� �̷�� ���߸� ���� CPA���� ������ȯ�� �̷������ �ֽ��ϴ�.<br/>
			�ֹ������� �����Ͻŵڿ��� �ݵ�� üũ�Ͽ��ֽñ� �ٶ��, CPA �ֹ����������� ���Ǵ� ���̹� ������Ʈ�������� �����ֽñ� �ٶ��ϴ�.<br/>
			<strong>���̹� ������Ʈ���� : 1588-3819</strong>
		</span>
	</td>
</tr>
<tr>
	<td>���̹� ����<br/>��������</td>
	<td class="noline"><input type="radio" name="naver_version" value="1" <?=$checked['naver_version']['1']?> <?=$checked['naver_version']['']?> onclick="version();">����(v1.0)&nbsp;&nbsp;<input type="radio" name="naver_version" value="2" <?=$checked['naver_version']['2']?> onclick="version();">�ű�(v2.0) &nbsp; <span class="extext" style="font-weight:bold">�������� �ȳ������� �ݵ�� �о��ֽñ� �ٶ��ϴ�.</span></td>
</tr>
<? if ($outsideServer === false) { ?>
<tr id="auto_create">
	<td>��ǰ EP<br/>�ڵ� ���� ����</td>
	<td class="noline">
		�� �ڵ� ���� ��� ��� ���� :
		<label><input type="radio" name="auto_create_use" value="Y" <?php echo $checked['auto_create_use']['Y'];?>/>���</label><label><input type="radio" name="auto_create_use" value="N" <?php echo $checked['auto_create_use']['N'];?> />������</label><br/>
		<div style="padding:3px 0px 5px 25px;">
			<span class="extext">���̹� ���ο��� ��ũ���ϴ� ������ 1�� 1ȸ, �ڵ����� �����մϴ�.</span><br>
			<span class="extext" style="font-weight:bold"> - ��ǰ�� �ſ� ���� ��� ������� ���� �� �� ���������� ������ �� �ֽ��ϴ�.</span>
		</div>
		�� ���� �ð��� ���� :
		<select name="auto_excute_time" style="width:80px;">
			<option value="00" <?=($partner['auto_excute_time'] === '00') ? 'selected' : ''?> <?=(!$partner['auto_excute_time']) ? 'selected' : ''?>>00��</option>
			<option value="01" <?=($partner['auto_excute_time'] === '01') ? 'selected' : ''?>>01��</option>
			<option value="02" <?=($partner['auto_excute_time'] === '02') ? 'selected' : ''?>>02��</option>
			<option value="03" <?=($partner['auto_excute_time'] === '03') ? 'selected' : ''?>>03��</option>
			<option value="04" <?=($partner['auto_excute_time'] === '04') ? 'selected' : ''?>>04��</option>
			<option value="05" <?=($partner['auto_excute_time'] === '05') ? 'selected' : ''?>>05��</option>
		</select><br/>
		<div style="padding:3px 0px 5px 25px;">
			<span class="extext">������ �ð��� ���̹� ���ο� ���� ��ǰ DB ������ �ڵ����� �����մϴ�.</span><br>
			<span class="extext">��ü��ǰ DB ������Ʈ �ֱ⸦ Ȯ���Ͽ� ������Ʈ �ð��븦 �����ϰ� �����Ͻñ� �ٶ��ϴ�.</span>
		</div>
	</td>
</tr>
<?}?>
<tr>
	<td>��ǰ���� ����</td>
	<td class="noline">
	<div class="extext" style="padding-bottom:5px;">���̹� ���ο� ����Ǵ� ���������� �����մϴ�.<br/>
		�Ϲ������� ���̹� ���ο� ����Ǵ� ������ ����� ������ ���̹� ���� ���Խ� ����� ȸ���׷� �������� ����� ������ ����˴ϴ�.<br/>
		���� ������ üũ ���� ���� ��� ���� �� �������� ������� ���� �ǸŰ��� ����˴ϴ�.
	</div>
	<div>
		<span class="noline"><input type="checkbox" name="inmemberdc" value="Y" <?=$checked['inmemberdc']['Y']?>/></span> ȸ���׷� ������ ����
		<div style="padding:3px 0px 0px 25px;">
			<div><b><?=$grpnm?></b> �������� <b><?=number_format($grpdc)?>%</b>�� ��ǰ���ݿ� ����Ǿ� ���̹� ���ο� ���� �˴ϴ�.</div>
			<div class="extext">���Խ� ȸ���׷� ������ <a href="../member/fieldset.php" class="extext" style="font-weight:bold">ȸ������ > ȸ�����԰���</a>���� ���� �����մϴ�.</div>
			<div class="extext">ȸ���׷��� ������ ������ <a href="../member/group.php" class="extext" style="font-weight:bold">ȸ������ > ȸ���׷���� </a>���� ���� �����մϴ�.</div>
		</div>
	</div>
	<div>
		<span class="noline"><input type="checkbox" name="incoupon" value="Y" <?=$checked['incoupon']['Y']?>/></span> ���� ����
		<div style="padding:3px 0px 0px 25px;">
			<div class="extext">������ <a href="../event/coupon.php" class="extext" style="font-weight:bold">���θ��/SNS > ��������Ʈ </a>���� ���� �����մϴ�.</div>
		</div>
	</div>
	</td>
</tr>
<tr>
	<td>���̹� ����<br />�������Һ�����</td>
	<td><input type=text name=partner[nv_pcard] value="<?=$partner[nv_pcard]?>" class=lline></td>
</tr>
<tr>
	<td>���̹� ����<br />��ǰ�� �Ӹ��� ����</td>
	<td>
	<div><input type=text name="partner[goodshead]" value="<?=$partner[goodshead]?>" class=lline></div>
	<div class="extext">* ��ǰ�� �Ӹ��� ������ ���� ġȯ�ڵ�</div>
	<div class="extext">- �Ӹ��� ��ǰ�� �Էµ� "������"�� �ְ� ���� �� : {_maker}</div>
	<div class="extext">- �Ӹ��� ��ǰ�� �Էµ� "�귣��"�� �ְ� ���� �� : {_brand}</div>
	</td>
</tr>
<tr>
	<td>���̹� ����<br />�̺�Ʈ ���� ����</td>
	<td>
	<div class="extext">Step1. ���θ� �̺�Ʈ ���� �Է� (�ִ� 100��)</div>
	<div style="padding:3px 0px 0px 25px;">
		<span class="noline"><input type="checkbox" name="naver_event_common" value="Y" <?=$checked['naver_event_common']['Y']?>/></span> ���� ���� ���
		<input type=text name="partner[eventCommonText]" value="<?=$partner[eventCommonText]?>" class=line style="width:80%">
		<span class="noline"><input type="checkbox" name="naver_event_goods" value="Y" <?=$checked['naver_event_goods']['Y']?>/></span> ��ǰ�� ���� ���
		<div style="padding:3px 0px 0px 25px;">
			<div>- "��ǰ��� > ��ǰ ���� �ϴ� > �̺�Ʈ ���� �Է� �׸�"�� ���� ������ �Է����ּ���. <a href="../goods/adm_goods_list.php" class="extext" style="font-weight:bold">[��ǰ���� �ٷΰ���]</a></div>
			<div>- �ϰ� ����� ���� ��� "�����Ͱ��� > ��ǰDB ���" ����� Ȱ�����ּ��� <a href="../goods/data_goodscsv.php " class="extext" style="font-weight:bold">[��ǰDB��� �ٷΰ���]</a></div>
		</div>
	</div></br>
	<div class="extext">Step2. ���̹� ���� �̺�Ʈ ���� ���� ����</div>
	<div style="padding:3px 0px 0px 25px;">
		<div>- ���̹� ������Ʈ���� ���� <a href="http://adcenter.shopping.naver.com" class="extext" style="font-weight:bold">adcenter.shopping.naver.com</a></div>
		<div>- ��ǰ���� > ��ǰ����������Ȳ > �̺�Ʈ�ʵ� ������� > ��Ͽ�û</div>
	</div>
	</td>
</tr>
</table>
<div class="noline" style="text-align: center; padding: 10px;">
	<a href="javascript:document.form.submit();"><img src="../img/btn_naver_install.gif" align=��absmiddle��></a>
</div>
</form>

<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>���̹� ���� �������Һ�������?: �� ī��纰 ������������ �Է��Ͻ� �� �ֽ��ϴ�. ��) �Ｚ3/����6/����12</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>�������Һ������� �Է�/������ �Ʒ� ��ǰDB URL�� ���� ������Ʈ�� �����ϸ� ��ǰDB URL ���� �� ������ ������ �ʵ��� pcard�ʵ��� ������ ����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>����� �������Һ������� ���̹� ���� ������Ʈ �ֱ⿡ ���� ���̹� ���ο� �ݿ��Ǿ����ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>���̹��� ����Ǵ� ��ǰ������ �ٽ� ����Ͻô� ���� �ƴմϴ�.</td></tr>
<tr><td style="padding-left:10">���� ����� ���θ��� ��ǰ������ ���̹��� ���� ������ �ڵ����� �������ϴ�.</td></tr>
</table>
<br/>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>���̹� ���ο��� ��ǰ�˻��� ���� �� �� �ֵ��� ��ǰ�� �Ӹ��� ������ Ȱ���ϼ���!</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>���� 1) ��ǰ�� �Ӹ��� ���� : ����</td></tr>
<tr>
	<td style="padding-left:10">
	<table style='border:1px solid #ffffff;width:400' class=small_ex>
	<col align="center" width="60"><col align="center" width="50"><col align="center" width="50"><col>
	<tr>
		<td>��ǰ��</td>
		<td>������</td>
		<td>�귣��</td>
		<td>���̹� ���� ��ǰ��</td>
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
<tr><td><img src="../img/icon_list.gif" align=absmiddle>���� 2) ��ǰ�� �Ӹ��� ���� : [������ / {_maker} / {_brand}]</td></tr>
<tr>
	<td style="padding-left:10">
	<table style='border:1px solid #ffffff;width:400' class=small_ex>
	<col align="center" width="60"><col align="center" width="50"><col align="center" width="50"><col>
	<tr>
		<td>��ǰ��</td>
		<td>������</td>
		<td>�귣��</td>
		<td>���̹� ���� ��ǰ��</td>
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
<script>cssRound('MSG02')</script>
</div>

<p>
<? if(in_array($naver_version,array('','1'))){	// ���� EP(v1.0) ?>
<table width=100% cellpadding=0 cellspacing=0>
<col class=cellC><col style="padding:5px 10px;line-height:140%">
<tr class=rndbg>
	<th>��ü</th>
	<th>��ǰ DB URL [������ �̸�����]</th>
	<th>�ֱ� ������Ʈ�Ͻ�</th>
	<th>������Ʈ</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>
<tr>
	<td>���̹� ����<br>��ǰDB URL������</td>
	<td>
	<font color="57a300">[��ü��ǰ]</font> <?if(file_exists('../../conf/engine/naver_all.php')){?><a href="../../partner/naver.php" target=_blank><font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/partner/naver.php</font> <img src="../img/btn_naver_view.gif" align=absmiddle></a><?}else{?>������Ʈ�ʿ�<?}?><br>

	<font color="57a300">[����ǰ]</font> <?if(file_exists('../../conf/engine/naver_summary.php')){?><a href="../../partner/naver.php?mode=summary" target=_blank><font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/partner/naver.php?mode=summary</font> <img src="../img/btn_naver_view.gif" align=absmiddle></a><?}else{?>������Ʈ�ʿ�<?}?><br>

	<font color="57a300">[�űԻ�ǰ]</font> <a href="../../partner/naver.php?mode=new" target=_blank><font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/partner/naver.php?mode=new</font> <img src="../img/btn_naver_view.gif" align=absmiddle></a>
	</td>
	<td align=center><font class=ver81>
		<?if(file_exists('../../conf/engine/naver_all.php'))echo date('Y-m-d h:i:s',filectime ( '../../conf/engine/naver_all.php'));?>
	</td>
	<td align=center>
		<a href="../../partner/engine.php?mode=all" target='ifrmHidden'><img src="../img/btn_price_update.gif"></a>
	</td>
</tr>
<tr><td colspan=12 class=rndline></td></tr>
</table>
<div class=small1 ><img src="../img/icon_list.gif" align=absmiddle><b><font color=ff6600>��ǰ���� ����ó� ��ǰ DB URL�� ���� ���� �ÿ��� �ݵ�� ������Ʈ��ư�� �����ּ���</font></B></div>
<div style="padding-top:2"></div>
<table align=center>
<tr><td width=500>
 <div align=center class=small1 style='padding-bottom:3'><font color=6d6d6d>������Ʈ�� ����Ǹ� �Ʒ� �ٸ� ���� �������� ���̰� �˴ϴ�.<br>�Ϸ�޽����� ��µɶ����� �ٸ� ������ �ﰡ�Ͽ��ֽʽÿ�.</font></div>
		<div style="height:8px;font:0;background:#f7f7f7;border:2 solid #cccccc">
		<div id=progressbar style="height:8px;background:#FF4E00;width:0"></div>
 </div>
</td></tr>
</table>
<? }else{	// �ű� EP(v2.0) ?>
<table width=100% cellpadding=0 cellspacing=0>
<col class=cellC><col style="padding:5px 10px;line-height:140%">
<tr class=rndbg>
	<th>��ü</th>
	<th>��ǰ DB URL [������ �̸�����]</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>
<tr>
	<td>���̹� ����<br>��ǰDB URL������</td>
	<td>
	<font color="57a300">[��ü��ǰ]</font> <a href="../../partner/naver.php" target=_blank>
	<font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/partner/naver.php</font>
	<img src="../img/btn_naver_view.gif" align=absmiddle></a><br>
	<font color="57a300">[����ǰ]</font> <a href="../../partner/naver.php?mode=summary" target=_blank>
	<font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/partner/naver.php?mode=summary</font>
	<img src="../img/btn_naver_view.gif" align=absmiddle></a>
	</td>
</tr>
<tr><td colspan=12 class=rndline></td></tr>
</table>
<? }?>

<br><br>
<!--
<table width=100% cellpadding=0 cellspacing=0>
<col class=cellC><col style="padding:5px 10px;line-height:140%">
<tr class=rndbg>
	<th>��ü</th>
	<th style="padding-right:150px">���� ��ǰ DB URL [������ �̸�����]</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>
<tr>
	<td>���̹� ����<br>��ǰDB URL������</td>
	<td>
	<font color="57a300">[��ü��ǰ]</font> <a href="../../partner/naver2_all.php" target=_blank>
	<font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/partner/naver2_all.php</font>
	<img src="../img/btn_naver_view.gif" align=absmiddle></a><br>
	<font color="57a300">[����ǰ]</font> <a href="../../partner/naver2_summary.php" target=_blank>
	<font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/partner/naver2_summary.php</font>
	<img src="../img/btn_naver_view.gif" align=absmiddle></a>
	</td>
</tr>
<tr><td colspan=12 class=rndline></td></tr>
</table>
<div class=small1 ><img src="../img/icon_list.gif" align=absmiddle><b><font color=ff6600>
���� ����� EP(Engine Page)�ּ��Դϴ�.
</font></B></div>
-->

<div style='padding:0 0 10 0; text-align:center;'>
<a href="http://marketing.godo.co.kr/board.php?id=notice&mode=view&postNo=178" target="_blank"><img src="../img/btn_naver_dbUrl.gif" border="0"></a>
<a href="https://adcenter.shopping.naver.com" target="_blank"><img src="../img/btn_naver_go.gif" border="0"></a>
</div>
<script>
var outsideServer = '<?=$outsideServer?>';
window.onload = function(){
	if (outsideServer == false) {
		version();
	}
	else {}
}

function version() {
	if (outsideServer == true) {
		return;
	}
	var f = document.form;
	if (f.naver_version[0].checked) {
		document.getElementById('auto_create').style.display = "none";
	}
	else if (f.naver_version[1].checked) {
		document.getElementById('auto_create').style.display = "";
	}
}
</script>
<? include "../_footer.php"; ?>