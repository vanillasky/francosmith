<?

@include "../../conf/partner.php";

$location = "���̹� ���� > ���̹� ���� ����";
include "../_header.php";
include "../../lib/naverPartner.class.php";

$naver = new naverPartner();

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
<div style="padding-top:7"><font class=g9 color=666666>���̹� ���� ��ǰDB URL ������ ���׷��̵�(2.0 �� 3.0) �Ǿ����ϴ�.</font></div>
<div style="padding-top:5"><font class=g9 color=666666>���׷��̵�� ���� ������� ���ǻ��� �Դϴ�. �ݵ�� Ȯ���Ͻ� �� ������ �ֽñ� �ٶ��ϴ�.</font></div><br>
<div style="padding-top:5"><b>1) ���� ����(v1.0, v2.0) �̿� ���� ��� 2017�� 8�� 18�ϱ��� ���������� �̿��Ͻ� �� �ֽ��ϴ�.</b></font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;- 2017�� 8�� 18�ϱ����� ������ ����Ͻô� 1.0, 2.0 �������ε� ���̹� ���� ���񽺸� ���������� �̿��Ͻ� �� �ֽ��ϴ�.</font></div><br>
<div style="padding-top:5"><b>2) ��, 2017�� 8�� 18�� ���Ŀ��� ���� ���� (v1.0, v2.0)�� ���񽺰� ����ǹǷ� �ش� ���� ������ v3.0���� ������ �����Ͽ��� �մϴ�.</b></font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;*���� ���� ���*&nbsp;</font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;���� ���̹� ������Ʈ���� > ��ǰ���� > ������Ʈ ��Ȳ > ���θ� ��ǰDB(EP) URL���� 3.0 ���� ���� �����մϴ�.<a href="https://adcenter.shopping.naver.com/member/login/form.nhn" target="_blank"><font color=blue>[�����ϱ�]</font></a></font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;���̹����� 3.0 ���� ������ �� �� �ַ�� ������ ������������ 3.0 ���� ������ ���ּž� �մϴ�. ���ù��� : �� �������� 02-567-3719</font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;�� ������ ���̹� ���� ������ ���̹� ���ο� ������ EP������ �����ؾ� �մϴ�. <font color=red><u>�����ϰ� �������� ���� ��� ��ǰ Data ���ſ� ������ �߻��մϴ�.</u></font></font></div><br>
<div style="padding-top:5"><b>3) �ű� �����ڴ� v3.0(�ű�)�� �����Ͽ� �ֽñ� �ٶ��ϴ�.</b></font></div>
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
	<td class="noline"><input type="radio" name="naver_version" value="1" <?=$checked['naver_version']['1']?> onclick="version();">v1.0(����)&nbsp;&nbsp;<input type="radio" name="naver_version" value="2" <?=$checked['naver_version']['2']?> onclick="version();">v2.0(����)&nbsp;&nbsp;<input type="radio" name="naver_version" value="3" <?=$checked['naver_version']['3']?> <?=$checked['naver_version']['']?> onclick="version();">v3.0(�ű�) &nbsp; <span class="extext" style="font-weight:bold">�������� �ȳ������� �ݵ�� �о��ֽñ� �ٶ��ϴ�.</span></td>
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
	<td><input type=text name=partner[nv_pcard] value="<?=$partner[nv_pcard]?>" class=lline>
	<div class="extext">��) ���� ����(v1.0, 2.0) : �Ｚ3/����3/����6<br/>&nbsp;&nbsp;&nbsp;�ű� ����(v3 . 0) : �Ｚī��^2~3|����ī��^2~3|KB����ī��^2~6</div></td>
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

<div id="shoppingGoodsDiv" style="width:800px;">
<div class="title title_top">���̹� ���� ��ǰ ���� ����<span> ���̹� ���ο� ������ ��ǰ�� �����մϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=35')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></span></div>
<? if ($naver->migrationCheck() == false) { ?>
<table border=4 bordercolor=#dce1e1 style="border-collapse:collapse; width: 800px;">
<tr><td style="padding:7 0 10 10">
<div style="padding-top:5"><b><font color="#bf0000">*�ʵ�*</div>
<div style="padding-top:7"><font class=g9 color=666666>���̹� ���� ���ؿ� ����, ������ ������ ��ǰ�� �ִ� 50���� �Դϴ�.</font></div>
<div style="padding-top:5"><font class=g9 color=666666>����, �Ʒ��� ������ ���� <b>���̹� ���� ��ǰ DB�� 499,000�� ���Ϸ� �����ϴ� ����� �����ϰ� �ֽ��ϴ�.</b></font></div>
<div style="padding-top:5"><font class=g9 color=666666>(50������ �ʰ��ϸ� ���̹� ���� ���񽺰� �����Ǿ� ������ ����� ���Ͽ� 499,000�� ���� ����Ͻ� �� �ֽ��ϴ�.)</font></div>
<div style="padding-top:5"><font size=2 color=#627dce><b><br>�� �� ��ǰ���� 499,000���� ���� �ʴ� ��쿡�� ���� ���� ���̵� ���������� ���̹� ������ �̿��Ͻ� �� �ֽ��ϴ�.</b></font></div>
</td></tr>
</table>

<div style="padding-top:10"></div>

<form name=frm method=post action="indb.php" target="ifrmHidden">
<input type=hidden name=mode value="naverShopingGoods">
<table style="border:1px solid #d5d5d5; border-collapse:collapse; width: 800px;">
<col class=cellC><col class=cellL>
<tr>
	<td style="border:1px solid #d5d5d5; width:130px;">���� ī�װ� ����</td>
	<td>
		<div style="padding:5 5 5 5">�� ���� ī�װ� ����</div>
		<table style="border:1px solid #d5d5d5; margin-left:5px; margin-right: px; width:650px;">
			<tr>
				<td>
					<div class="extext" style="padding-top:5">2�� �з����� ������ �� ������ ������ ī�װ��� ���� ��ǰ�� 499,000���� �ʰ��� �� �����ϴ�.</div>
					<div class="extext">������ ī�װ��� ��ǰ�� 499,000���� �ʰ� �� <b>�ֱ� ��ǰ ������ڼ�����</b> 499,000�� ���Ϸ� ���� ��ǰ�� �����˴ϴ�.</div>
					<div class="extext">���� ī�װ��� �������� ���� ���, ��ü ��ǰ���� <b>�ֱ� ��ǰ ������ڸ� ����</b>���� ��ǰ�� �����մϴ�.</div>
				</td>
			</tr>
		</table>
		<div style="padding:5 5 5 5"><font size="4">�� ��ǰ�� : <span id="goodsAllCount"><font color=red><b>�ε���...</font></b></span> ��</font> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:red;">** �� ��ǰ���� ǰ����ǰ�� ��������ǰ�� ������ �����Դϴ�.</span></div>
		<div style="margin-left: 5px; margin-right: 5px; margin-bottom: 5px;">
		<div valign="top"><script type="text/javascript">new categoryBox('cate[]',2,'','naver','frm');</script></div>
		</div>
		<div class="extext" style="text-align: right; padding:5 5 5 5;"> - ��ǰ���� �ټ��� ī�װ��� ����� �� �ֱ� ������, ī�װ��� ��ǰ���� ������ �� ��ǰ������ ���� �� �ֽ��ϴ�.</div>
		<div style="text-align: center;"><a href="javascript:categoryAdd();"><img src='../img/btn_naver_category.gif' align=absmiddle></a></div>
		<div style="text-align: right;"><a href="javascript:deleteAll();"><img src='../img/btn_naver_delete.gif' align=absmiddle></a></div>
		<div id="selectCategory" style="border:1px solid #d5d5d5; padding:5; height: auto; min-height: 100px; margin-top:10px; margin-left:5px; margin-right:5px; margin-bottom:5px;"><font size=4 color=red><b>�ε���...</font></b></div>
		<div style="text-align: center;"><a href="javascript:categoryCalc();"><img src='../img/btn_naver_count.gif' align=absmiddle></a></div>
		<div style="padding:5 5 5 5">�� ������ ī�װ� ( ����� ��ǰ�� : <span id="goodsCount">-</span> / 499,000 ���õ� ī�װ� �� �ߺ��� ��ǰ�� : <span id="duplicateGoodsCount">-</span> ��)</div>
		<div style="padding:5 5 5 5; color:red;">** ������ ī�װ� �� �ߺ��� ��ǰ�� �����մϴ�. </div>
		<div style="padding:0 5 5 5; color:red;">** �ʰ� �� �ֱ� ��ǰ������ڼ����� 499,000�� ���Ϸ� �����մϴ�.</div>
		<div style="padding:0 5 5 5;"><font size=2 color=#627dce>�� ����� ��ǰ�� Ȯ�� �� ���������� ������ �ּ���.</font></div>
	</td>
</tr>

</table>
<div class="noline" style="text-align: center; padding: 10px; width: 800px;">
	<a href="javascript:check();"><img src="../img/btn_naver_install.gif" align=��absmiddle��></a>
</div>
</form>
<div id="overlayDiv" style="filter:alpha(opacity=80); opacity:0.95; background:#44515b; position:absolute; text-align:center; display:table;">
<span style="display:table-cell; vertical-align:middle; color:white; font-size:12pt;"><b>���̹� ���� EP���� ���� ����� �����Ͽ����ϴ�.<br>���̱׷��̼��� �Ͻø� ������ ���� ���� ���������� ���̹� ���� EP������ ������ �� �ֽ��ϴ�.<br>�Ʒ� ���̱׷��̼� ��ư�� Ŭ���Ͻþ� ���̱׷��̼��� �������ֽñ� �ٶ��ϴ�.<br>�� ���̱׷��̼� �۾����� ���� �ð��� �ҿ�˴ϴ�.<br>�� ���̱׷��̼� �Ŀ��� ���̹� ���� ��ǰ ���� �޴����� �ش� ����� ����Ͻ� �� �ֽ��ϴ�.<br></b>
<a href="javascript:migration();"><img style="margin-top:20px;" src="../img/btn_naver_shopping_migration.png"></a>
</span>
</div>
</div>
<?}else{?>
<div class="extext" style="margin-bottom:50px;">�ش� ������ <a href="naver_shopping_setting.php" style="color:#627dce"><b><u>[���̹� ���� ��ǰ ����]</u></b></a> �޴����� �����մϴ�.</div>
<?}?>
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
	<img src="../img/btn_naver_view.gif" align=absmiddle></a><br><br>
	<div class="extext">[����ǰ]�� ��� ���� EP ����(v1.0,v2.0)������ ����ϴ� ������� ��v3.0(�ű�)�� ���� �� ������� �ʽ��ϴ�.</span>
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
<script type="text/javascript" src="../godo.loading.indicator.js"></script>
<script>
var selectedGoodsCount = 0;		// ����� ��ǰ ��
var duplicateGoodsCount = 0;	// �ߺ��� ��ǰ �� ����
var goodsCountCheck = 0;		// ����� ��ǰ�� Ȯ�� üũ
var cateValues = new Array();	// ���� �Ǿ��ִ� ī�װ� ��ȣ
var outsideServer = '<?=$outsideServer?>';
window.onload = function(){
	if (outsideServer == false) {
		version();
	}
	else {}

	<? if ($naver->migrationCheck() == false) { ?>overlay(); <?}?>
}

function version() {
	if (outsideServer == true) {
		return;
	}
	var f = document.form;
	if (f.naver_version[0].checked) {
		document.getElementById('auto_create').style.display = "none";
	}
	else {
		document.getElementById('auto_create').style.display = "";
	}
}

// ����� ��ǰ�� Ȯ�� ���� üũ
function check() {
	if (goodsCountCheck == 0) {
		alert('����� ��ǰ�� Ȯ�� �� �ش� �������� ������ �����մϴ�.');
		return;
	}
	else {
		document.frm.submit();
	}
}

// ��ϵ� ��ǰ�� ���
function goodsCalc() {
	var ajax = new Ajax.Request('../naver/naver_category_calc.php',
	{
		method: 'POST',
		parameters: 'mode=goods',
		onComplete: function () {
			var req = ajax.transport;
			if (req.status !== 200 || req.responseText === '' || req.responseText === 'fail') {
				alert("����� �����Ͽ����ϴ�.\n�����Ϳ� �����Ͽ� �ּ���.");
				return;
			}

			document.getElementById('goodsAllCount').innerHTML = comma(req.responseText);
			document.getElementById('goodsAllCount').style.color = 'red';
		},
		onFailure : function() {
			alert("����� �����Ͽ����ϴ�.\n�����Ϳ� �����Ͽ� �ּ���.");
			return;
		}
	});
}

// ����� ī�װ� ��� ���
function viewCategory() {
	var str = Array();
	var temp = Array();
	var categoryList = Array();

	var ajax = new Ajax.Request('../naver/naver_category_calc.php',
	{
		method: 'POST',
		datatype: 'array',
		onComplete: function () {
			var req = ajax.transport;
			if (req.status !== 200 || req.responseText === '' || req.responseText === 'fail') {
				alert("����� �����Ͽ����ϴ�.\n�����Ϳ� �����Ͽ� �ּ���.");
				return;
			}
			var parent = document.getElementById("selectCategory");
			parent.removeChild(parent.firstChild);
			categoryList = JSON.parse(req.responseText);

			for (i=0; i<categoryList.length; i++) {
				temp[i] = categoryList[i].split(',');
				str[i] = temp[i][1] + ' (' + comma(temp[i][2]) + '��)';

				duplicateGoodsCount += Number(temp[i][2]);
				cateValues[cateValues.length] = temp[i][0];
			}

			// ����� ��ǰ�� ��� �� ������ ī�װ� ����
			for (i=0; i<temp.length; i++) {
				categoryText = "<span id='" + temp[i][0] + "' style='display:inline-block; background-color:d5d5d5; padding:5 5 5 5; margin-top:3px; margin-bottom:3px; margin-right:5px;'>" + str[i];
				categoryText += "<input type=hidden name=category[] value='" + temp[i][0] + "' style='display:none'> ";
				categoryText += "<input type=hidden name=category_" + temp[i][0] + " value='" + temp[i][2] + "' style='display:none'> ";
				categoryText += "<a href='javascript:void(0)' onClick='categoryDelete(\"" + temp[i][0] + "\",\"" + temp[i][2] + "\")'><img src='../img/i_del.gif' align=absmiddle></a></span>";

				var selected = document.getElementById('selectCategory');
				selected.innerHTML += categoryText;
			}
		},
		onFailure : function() {
			alert("����� �����Ͽ����ϴ�.\n�����Ϳ� �����Ͽ� �ּ���.");
			return;
		}
	});
}

// ����� ī�װ� �߰�
function categoryAdd() {
	var selCate;						// ������ ī�װ� ��ȣ
	var str = new Array();				// ������ ī�װ� �̸�
	var obj = document.frm['cate[]'];	// ������ ī�װ�
	var valueTemp = new Array();
	var cnt;

	goodsCountCheck = 0;
	for (i=0;i<obj.length;i++) {
		if (obj[i].value) {
			valueTemp = obj[i].value.split(',');
			str[str.length] = valueTemp[2];
			selCate = valueTemp[0];
			cnt = valueTemp[1];
		}
	}

	if (!selCate) {
		alert('ī�װ��� �������ּ���');
		return;
	}

	// �ߺ� ī�װ� üũ
	for (i=0;i<cateValues.length;i++) {
		if (cateValues[i]) {
			var cateValue = cateValues[i];

			// ���� ī�װ��� �����߰ų� ���õǾ� �ִ� ī�װ��� ���� ī�װ��� ���� �������
			if (selCate == cateValue) {
				alert('�ش� ī�װ��� �̹� �߰��Ǿ� �ֽ��ϴ�.');
				return;
			}
			else if (selCate.length > 3 && selCate.substr(0,selCate.length-3) == cateValue) {
				alert('�ش� ī�װ����� ���� ī�װ��� �̹� �߰��Ǿ� �ֽ��ϴ�.');
				return;
			}
			// ���õ� ī�װ����� ���� ī�װ��� ���� �������
			else if (cateValue.substr(0,selCate.length) == selCate) {
				if (confirm("���� �߰��� ī�װ����� ���� ī�װ��� �����ϼ̽��ϴ�.\n���� ī�װ��� �����ϰ� ���� ī�װ��� �߰��Ͻðڽ��ϱ�?") == true) {
					// ���� ī�װ��� ������ �ϼ� ������ ã�Ƽ� ����
					var tempCate = cateValues.slice();	// �迭 ����
					for (j=0; j<tempCate.length; j++) {
						if (tempCate[j].substr(0,selCate.length) == selCate) {
							duplicateGoodsCount -= Number(document.getElementsByName("category_"+tempCate[j])[0].value);
							var parent = document.getElementById("selectCategory");
							var delCate = document.getElementById(tempCate[j]);
							parent.removeChild(delCate);
							cateValues.splice(cateValues.indexOf(tempCate[j]), 1);
						}
					}
					break;
				}
				else {
					return;
				}
			}
		}
	}

	// ī�װ��� (00��) ó�� ����
	str = str.join(" > ");
	str += ' ('+comma(cnt)+'��)';

	// ����� ��ǰ�� ��� �� ������ ī�װ� ����
	cateValues[cateValues.length] = selCate;
	duplicateGoodsCount += Number(cnt);

	categoryText = "<span id='" + selCate + "' style='display:inline-block; background-color:d5d5d5; padding:5 5 5 5; margin-top:3px; margin-bottom:3px; margin-right:5px;'>" + str;
	categoryText += "<input type=hidden name=category[] value='" + selCate + "' style='display:none'> ";
	categoryText += "<input type=hidden name=category_" + selCate + " value='" + cnt + "' style='display:none'> ";
	categoryText += "<a href='javascript:void(0)' onClick='categoryDelete(\"" + selCate + "\",\"" + cnt + "\")'><img src='../img/i_del.gif' align=absmiddle></a></span>";

	var selected = document.getElementById('selectCategory');
	selected.innerHTML += categoryText;
}

// ������ ī�װ� ����
function categoryDelete(selCate,cnt) {
	goodsCountCheck = 0;
	cateValues.splice(cateValues.indexOf(selCate), 1);
	duplicateGoodsCount -= cnt;

	// ���� ��ǰ �� ���
	var parent = document.getElementById("selectCategory");
	var delCate = document.getElementById(selCate);
	parent.removeChild(delCate);
}

//����� ��ǰ�� ���
function categoryCalc() {
	if (cateValues.length > 0) {
		// �ε� ó��
		nsGodoLoadingIndicator.init({});
		nsGodoLoadingIndicator.show();
		var ajax = new Ajax.Request('../naver/naver_category_calc.php',
		{
			method: 'POST',
			parameters: 'mode=category&category='+cateValues,
			onComplete: function () {
				nsGodoLoadingIndicator.hide();	// �ε���
				var req = ajax.transport;
				if (req.status !== 200 || req.responseText === '' || req.responseText === 'fail') {
					alert("����� �����Ͽ����ϴ�.\n�����Ϳ� �����Ͽ� �ּ���.");
					return;
				}

				selectedGoodsCount = req.responseText;
				document.getElementById('goodsCount').innerHTML = comma(selectedGoodsCount);

				if (selectedGoodsCount > 499000) {
					document.getElementById('goodsCount').style.color = 'red';
					document.getElementById('goodsCount').style.fontWeight = 'bold';
					alert("������ ��ǰ���� ���� ������ �Ѿ����ϴ�.\n(���õ� ��ǰ �� : "+selectedGoodsCount+" ��)\n�����Ͽ� �ֽñ� �ٶ��ϴ�.");
				}

				// �ߺ��� ��ǰ ���� ���
				document.getElementById("duplicateGoodsCount").innerHTML = comma(duplicateGoodsCount - selectedGoodsCount);
				goodsCountCheck = 1;
			},
			onFailure : function() {
				nsGodoLoadingIndicator.hide();	// �ε���
				alert("����� �����Ͽ����ϴ�.\n�����Ϳ� �����Ͽ� �ּ���.");
				return;
			}
		});
	}
	// ���õ� ī�װ��� ��� ���� ������ �ʱ�ȭ
	else {
		goodsCountCheck = 1;
		selectedGoodsCount = duplicateGoodsCount = 0;
		document.getElementById("goodsCount").innerHTML = selectedGoodsCount;
		document.getElementById("duplicateGoodsCount").innerHTML = duplicateGoodsCount;

		alert("���� ī�װ��� ������ �ּ���.");
		return;
	}
}

//������ ī�װ� ��ü ����
function deleteAll() {
	if (confirm('������ ī�װ��� �ʱ�ȭ �Ͻðڽ��ϱ�?') != true) {
		return;
	}

	goodsCountCheck = 1;
	var parent = document.getElementById("selectCategory");
	while(parent.firstChild) {
		parent.removeChild(parent.firstChild);
	}
	cateValues = Array();
	selectedGoodsCount = duplicateGoodsCount = 0;
	document.getElementById("goodsCount").innerHTML = selectedGoodsCount;
	document.getElementById("duplicateGoodsCount").innerHTML = duplicateGoodsCount;
}

function overlay() {
	var left = document.getElementById("shoppingGoodsDiv").offsetleft;
	var top = document.getElementById("shoppingGoodsDiv").offsetTop;
	var width = document.getElementById("shoppingGoodsDiv").offsetWidth;
	var height = document.getElementById("shoppingGoodsDiv").offsetHeight;

	document.getElementById("overlayDiv").style.left = left+200;
	document.getElementById("overlayDiv").style.top = top+130;
	document.getElementById("overlayDiv").style.width = width
	document.getElementById("overlayDiv").style.height = height;
}

function migration() {
	if (confirm('���̱׷��̼��� �۾� �ð��� �ټ� �ҿ�˴ϴ�. ����Ͻðڽ��ϱ�?')) {
		popupLayer('naver_shopping_migration.php',1000,800);
	}
	else {
		return false;
	}
}
</script>
<? include "../_footer.php"; ?>