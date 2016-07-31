<?

include "../_header.popup.php";

if (file_exists(dirname(__FILE__).'/../../conf/design_meta_'.$cfg['tplSkinWork'].'.php')) {
	include dirname(__FILE__).'/../../conf/design_meta_'.$cfg['tplSkinWork'].'.php';
	if ($skinType === 'dtd') {
		include dirname(__FILE__).'/adm_design_animation_banner.php';
	}
	exit;
}
//include "../../conf/design_skin_" . $cfg['tplSkinWork'] . ".php";
$rollbannerPath = "../../conf/design.rollbaner.dat";
if(is_file($rollbannerPath))
{
	$banner_info = unserialize(file_get_contents($rollbannerPath));
}

if(!$banner_info['effect'])
{
	$banner_info['effect']='Barn';
}


$banner_image=array();
for($i=1;$i<=9;$i++)
{
	if(is_file('../../data/scriptrotator/'.$i.'.jpg'))
	{
		$banner_image[$i]=true;
	}
	else
	{
		$banner_image[$i]=false;
	}
}



if($_POST['save']=='iebanner')
{
	$saveArray=array(
		'width'=>(int)$_POST['width'],
		'height'=>(int)$_POST['height'],
		'effect'=>$_POST['effect'],
		'duration'=>(float)$_POST['Duration'],
		'wait'=>(float)$_POST['wait'],
		'numDisplay'=>$_POST['numDisplay'],
		'setting'=>$_POST[$_POST['effect'].'Setting'],
		'link'=>$_POST['link']
	);

	$handle = fopen($rollbannerPath, 'w');
	fwrite($handle,serialize($saveArray));
	fclose($handle);

	if($_POST['image_del'])
	{
		foreach($_POST['image_del'] as $v)
		{
			@unlink('../../data/scriptrotator/'.$v.'.jpg');
		}
	}

	for($i=1;$i<=9;$i++)
	{
		if($_FILES['image']['error'][$i]==0)
		{

			if(!is_dir('../../data/scriptrotator'))
			{
				@mkdir('../../data/scriptrotator');
				@chmod('../../data/scriptrotator',0777);
			}
			@move_uploaded_file($_FILES['image']['tmp_name'][$i],'../../data/scriptrotator/'.$i.'.jpg');
			@chmod('../../data/scriptrotator/'.$i.'.jpg',0777);
		}
	}

	echo "
	<script>
	alert('����Ǿ����ϴ�');
	self.location.href='iframe.rollbanner.php';
	</script>
	";
	exit;

}





?>
<script src="../../lib/js/ierotator.js" type="text/javascript"></script>
<script>
function chkForm2(obj) {
	var onlynum=/^[0-9]+$/;
	if(!onlynum.test(obj.width.value))
	{
		alert("���ڸ� �Է��ϼž��մϴ�");
		obj.width.focus();
		return false;
	}

	if(!onlynum.test(obj.height.value))
	{
		alert("���ڸ� �Է��ϼž��մϴ�");
		obj.height.focus();
		return false;
	}
	return true;
}

</script>

<div class="title title_top">�����̴� ��� : Script Rotator<span></span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=18')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

<table border="4" bordercolor="#dce1e1" style="border-collapse:collapse;margin-bottom:20px;" width="100%">
<tr><td style="padding:7px 0 10px 10px; color:#666666;">
<div style="padding-top:5px;"><font class="g9" color="#0074BA"><b>�� �Ϻ� ������ ������ �����̴� ����� ȿ������� �������� �ʽ��ϴ�.</b></font></div>
<div style="padding-top:7px;">(������ �������� ����ϴ� PC���� ���θ� ���ٽ� ������ ��� ������ ȿ�� �� �������� �ʽ��ϴ�.)</u></div>
<div style="padding-top:7px;">- ���� ������ : IE 6 ~ IE 9 </u></div>
<div style="padding-top:7px;">- ������ ������ : IE 10 �̻� , Chrome , Firefox , Safari , Opera</u></div>
</td></tr>
</table>

<table width="100%">
<tr>
	<td class="pageInfo">ȿ�� �̸�����</td>
</tr>
</table>


<div id="ie_banner" style="position:relative;display:none">
	<div>
		<a href="#"><img src="sample/banner1.jpg"></a>
	</div>

	<div>
		<a href="#"><img src="sample/banner2.jpg"></a>
	</div>

	<div>
		<a href="#"><img src="sample/banner3.jpg"></a>
	</div>

	<div>
		<a href="#"><img src="sample/banner4.jpg"></a>
	</div>

</div>

<br><br>
<form id="frmScriptRotator" action="iframe.rollbanner.php" method="post" enctype="multipart/form-data" onsubmit="return chkForm2(this)">
<input type="hidden" name="save" value="iebanner">
<table class="tb">
<col class="cellC"><col class="cellL" style="width:250px">
<col class="cellC"><col class="cellL">
<tr>
	<td>������</td>
	<td colspan=3>
	<input type="text" name="width" style='width:50px' value="<?=$banner_info['width']?>"> x
	<input type="text" name="height" style='width:50px' value="<?=$banner_info['height']?>">
	</td>


<tr>
	<td>����ǥ��</td>
	<td>
	<input type="radio" name="numDisplay" value="yes" style="border:0px" <?if($banner_info['numDisplay']){?>checked<?}?> >�� &nbsp;
	<input type="radio" name="numDisplay" value="" style="border:0px" <?if(!$banner_info['numDisplay']){?>checked<?}?>>�ƴϿ�
	</td>

	<td>���ð�</td>
	<td>
	<select name="wait">
	<option value="1000">1��</option>
	<option value="1500">1.5��</option>
	<option value="2000">2��</option>
	<option value="3000">3��</option>
	<option value="4000">4��</option>
	</select>
	</td>

</tr>
<tr>
<td>ȿ��</td>
	<td>
	<select name="effect" onchange="effectChange();changeSetting();">
	<OPTION value='Barn'>Barn</OPTION>
	<OPTION value='Blinds'>Blinds</OPTION>
	<OPTION value='Checkerboard'>Checkerboard</OPTION>
	<OPTION value='Fade'>Fade</OPTION>
	<OPTION value='GradientWipe'>GradientWipe</OPTION>
	<OPTION value='Inset'>Inset</OPTION>
	<OPTION value='Iris'>Iris</OPTION>
	<OPTION value='Pixelate'>Pixelate</OPTION>
	<OPTION value='RadialWipe'>RadialWipe</OPTION>
	<OPTION value='RandomBars'>Random Bars</OPTION>
	<OPTION value='RandomDissolve'>Random Dissolve</OPTION>
	<OPTION value='Slide'>Slide</OPTION>
	<OPTION value='Spiral'>Spiral</OPTION>
	<OPTION value='Stretch'>Stretch</OPTION>
	<OPTION value='Strips'>Strips</OPTION>
	<OPTION value='Wheel'>Wheel</OPTION>
	<OPTION value='Zigzag'>Zigzag</OPTION>
	</select>
	</td>
	<td>ȿ������</td>
	<td>
	ȿ���ð� :
	<select name="Duration"  onchange="changeSetting()">
	<option value="0.5">0.5��</option>
	<option value="0.7">0.7��</option>
	<option value="1">1��</option>
	<option value="1.3">1.3��</option>
	<option value="1.5">1.5��</option>
	</select><br>

	<div id="BarnSetting" style="display:none">
		������ :
		<select name="BarnSetting[]" onchange="changeSetting()">
		<option value="motion=out">���ʿ��� �ٱ�������</option>
		<option value="motion=in">�ٱ��ʿ��� ��������</option>
		</select><br>

		�帧 :
		<select name="BarnSetting[]" onchange="changeSetting()">
		<option value="orientation=vertical">����</option>
		<option value="orientation=horizontal">����</option>
		</select>
	</div>

	<div id="BlindsSetting" style="display:none">
		���� :
		<select name="BlindsSetting[]" onchange="changeSetting()">
		<option value="Bands=2">2</option>
		<option value="Bands=4">4</option>
		<option value="Bands=6">6</option>
		<option value="Bands=8">8</option>
		<option value="Bands=10">10</option>
		</select><br>

		���� :
		<select name="BlindsSetting[]"  onchange="changeSetting()">
		<option value="direction=up">��</option>
		<option value="direction=down">�Ʒ�</option>
		<option value="direction=left">����</option>
		<option value="direction=right">������</option>
		</select>
	</div>

	<div id="CheckerboardSetting" style="display:none">
		���� :
		<select name="CheckerboardSetting[]" onchange="changeSetting()">
		<option value="Direction=up">��</option>
		<option value="Direction=down">�Ʒ�</option>
		<option value="Direction=left">����</option>
		<option value="Direction=right">������</option>
		</select><br>

		���ΰ��� :
		<select name="CheckerboardSetting[]" onchange="changeSetting()">
		<option value="SquaresX=2">2</option>
		<option value="SquaresX=4">4</option>
		<option value="SquaresX=6">6</option>
		<option value="SquaresX=8">8</option>
		<option value="SquaresX=10">10</option>
		<option value="SquaresX=12">12</option>
		</select>
		&nbsp;
		���ΰ��� :
		<select name="CheckerboardSetting[]" onchange="changeSetting()">
		<option value="SquaresY=2">2</option>
		<option value="SquaresY=4">4</option>
		<option value="SquaresY=6">6</option>
		<option value="SquaresY=8">8</option>
		<option value="SquaresY=10">10</option>
		<option value="SquaresY=12">12</option>
		</select>
	</div>


	<div id="FadeSetting" style="display:none">
		������ :
		<select name="FadeSetting[]" onchange="changeSetting()">
		<option value="Overlap=0.00">0.00</option>
		<option value="Overlap=0.25">0.25</option>
		<option value="Overlap=0.50">0.50</option>
		<option value="Overlap=0.75">0.75</option>
		<option value="Overlap=1.00">1.00</option>
		</select>

	</div>

	<div id="GradientWipeSetting" style="display:none">
		�׶���Ʈũ�� :
		<select name="GradientWipeSetting[]" onchange="changeSetting()">
		<option value="GradientSize=0.00">0.00</option>
		<option value="GradientSize=0.25">0.25</option>
		<option value="GradientSize=0.50">0.50</option>
		<option value="GradientSize=0.75">0.75</option>
		<option value="GradientSize=1.00">1.00</option>
		</select><br>
		��Ÿ�� :
		<select name="GradientWipeSetting[]" onchange="changeSetting()">
		<option value="wipestyle=0">���ʿ��� ������</option>
		<option value="wipestyle=1">������ �Ʒ���</option>
		</select><br>
		���� :
		<select name="GradientWipeSetting[]" onchange="changeSetting()">
		<option value="motion=forward">������</option>
		<option value="motion=reverse">�ݴ��</option>
		</select><br>
	</div>

	<div id="InsetSetting" style="display:none">

	</div>

	<div id="IrisSetting" style="display:none">
		��Ÿ�� :
		<select name="IrisSetting[]" onchange="changeSetting()">
		<option value="irisstyle=DIAMOND">���̾Ƹ��</option>
		<option value="irisstyle=CIRCLE">��</option>
		<option value="irisstyle=CROSS">ũ�ν�</option>
		<option value="irisstyle=PLUS">�÷ν�</option>
		<option value="irisstyle=SQUARE">�簢��</option>
		<option value="irisstyle=STAR">��</option>
		</select><br>
		���� :
		<select name="IrisSetting[]" onchange="changeSetting()">
		<option value="motion=in">������</option>
		<option value="motion=out">�ݴ��</option>
		</select><br>
	</div>

	<div id="PixelateSetting" style="display:none">
		�ְ��ȼ�ũ�� :
		<select name="PixelateSetting[]" onchange="changeSetting()">
		<option value="MaxSquare=2">2</option>
		<option value="MaxSquare=5">5</option>
		<option value="MaxSquare=10">10</option>
		<option value="MaxSquare=20">20</option>
		</select>
	</div>

	<div id="RadialWipeSetting" style="display:none">
		��Ÿ�� :
		<select name="RadialWipeSetting[]" onchange="changeSetting()">
		<option value="wipestyle=CLOCK">CLOCK</option>
		<option value="wipestyle=WEDGE">WEDGE</option>
		<option value="wipestyle=RADIAL">RADIAL</option>
		</select>
	</div>

	<div id="RandomBarsSetting" style="display:none">
		��Ÿ�� :
		<select name="RandomBarsSetting[]" onchange="changeSetting()">
		<option value="orientation=vertical">����</option>
		<option value="orientation=horizontal">����</option>
		</select>
	</div>


	<div id="RandomDissolveSetting" style="display:none">

	</div>


	<div id="SlideSetting" style="display:none">
		��Ÿ�� :
		<select name="SlideSetting[]" onchange="changeSetting()">
		<option value="slidestyle=PUSH">�б�</option>
		<option value="slidestyle=HIDE">�����</option>
		<option value="slidestyle=SWAP">��ġ��</option>
		</select>
		<br>

		�п� :
		<select name="SlideSetting[]" onchange="changeSetting()">
		<option value="Bands=1">1</option>
		<option value="Bands=3">3</option>
		<option value="Bands=5">5</option>
		<option value="Bands=10">10</option>
		<option value="Bands=25">25</option>
		</select>
	</div>



	<div id="SpiralSetting" style="display:none">
		�׸��������X :
		<select name="SpiralSetting[]" onchange="changeSetting()">
		<option value="GridSizeX=8">8</option>
		<option value="GridSizeX=16">16</option>
		<option value="GridSizeX=32">32</option>
		</select>
		<br>

		�׸��������Y :
		<select name="SpiralsSetting[]" onchange="changeSetting()">
		<option value="GridSizeY=8">8</option>
		<option value="GridSizeY=16">16</option>
		<option value="GridSizeY=32">32</option>
		</select>
	</div>

	<div id="StretchSetting" style="display:none">
		��Ÿ�� :
		<select name="StretchSetting[]" onchange="changeSetting()">
		<option value="stretchstyle=SPIN">spin</option>
		<option value="stretchstyle=HIDE">hide</option>
		<option value="stretchstyle=PUSH">push</option>
		</select>
	</div>

	<div id="StripsSetting" style="display:none">
		������ :
		<select name="StripsSetting[]" onchange="changeSetting()">
		<option value="motion=leftup">���ʿ��� ����</option>
		<option value="motion=leftdown">���ʿ��� �Ʒ���</option>
		<option value="motion=rightup">�����ʿ��� ����</option>
		<option value="motion=rightdown">�����ʿ��� �Ʒ���</option>
		</select>
	</div>

	<div id="WheelSetting" style="display:none">
		�п� :
		<select name="WheelSetting[]" onchange="changeSetting()">
		<option value="spokes=2">2</option>
		<option value="spokes=4">4</option>
		<option value="spokes=6">6</option>
		<option value="spokes=8">8</option>
		<option value="spokes=10">10</option>
		</select>
	</div>

	<div id="ZigzagSetting" style="display:none">
		�׸��������X :
		<select name="ZigzagSetting[]" onchange="changeSetting()">
		<option value="GridSizeX=8">8</option>
		<option value="GridSizeX=16">16</option>
		<option value="GridSizeX=32">32</option>
		</select>
		<br>

		�׸��������Y :
		<select name="ZigzagSetting[]" onchange="changeSetting()">
		<option value="GridSizeY=8">8</option>
		<option value="GridSizeY=16">16</option>
		<option value="GridSizeY=32">32</option>
		</select>
	</div>

	</td>
	<?php
	foreach($banner_image as $k=>$v)
	{
		echo "
			<tr>
			<td>�̹���{$k}</td>
			<td colspan='3'>
			���� : <input type='file' name='image[$k]'>
			";
		if($v)
		{
			echo "<input type='checkbox' name='image_del[]' value='$k' style='border:0px'>����
			&nbsp; <a href='../../data/scriptrotator/$k.jpg' target='_blank'>�ڼ�������</a>
			";
		}
		echo "<br>
		��ũ�ּ� : <input type='text' name='link[$k]' style='width:400px' value='".$banner_info['link'][$k]."'>
		</td>
		</tr>";
	}


	?>


</tr>
</table>

<div class="button">
<input type=image src="../img/btn_register.gif">
</div>

</form>


<div style="border:1px solid #cccccc;background-color:#eeeeee;text-align:center;padding:10px 0px;font-size:13pt;font-weight:bold">
�������ڵ𿡼� ��밡���� ġȯ�ڵ� : &nbsp;  {=scriptRotator()}
</div>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />Script Rotator�� IE������ �����۵��˴ϴ�</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />Script Rotator�� IE9 ������ �����մϴ�. IE10 ������ ���� ȿ���� �������� �ʽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�� �������� 2���̻� �����Ǽ� �����ϴ�</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

</form>




<script>window.onload = function(){ UNM.inner();};</script>

<script>


var ier;

function startSetting() {

	var effectValue=[
	'<?=$banner_info['setting'][0]?>',
	'<?=$banner_info['setting'][1]?>',
	'<?=$banner_info['setting'][2]?>',
	'<?=$banner_info['setting'][3]?>'
	];

	var d=document.getElementById('frmScriptRotator');
	d.effect.value="<?=$banner_info['effect']?>";
	d.wait.value="<?=$banner_info['wait']?>";
	d.Duration.value="<?=$banner_info['duration']?>";

	var effect=d.effect.value;
	var i,l;
	var optStr='';
	if(document.getElementsByName(effect+'Setting[]').length>1)
	{
		l=d.elements[effect+'Setting[]'].length;
		for(i=0;i<l;i++)
		{
			;
			if(effectValue[i])
				d.elements[effect+'Setting[]'][i].value=effectValue[i];
		}

		for(i=0;i<l;i++)
		{
			optStr=optStr+d.elements[effect+'Setting[]'][i].value+',';
		}
	}
	else
	{
		if(effectValue[0])
			d.elements[effect+'Setting[]'].value=effectValue[0];
		if(d.elements[effect+'Setting[]'])
			optStr=optStr+d.elements[effect+'Setting[]'].value+',';
	}



	optStr=optStr+'Duration='+d.Duration.value;

	var config = {
		"id":"ie_banner",
		"effect":"FILTER: progid:DXImageTransform.Microsoft."+effect+"("+optStr+")",
		"width":400,
		"height":263,
		"wait":3000,
		"numimg":[
			["../../lib/js/ierotator/01.gif","../../lib/js/ierotator/01_over.gif"],
			["../../lib/js/ierotator/02.gif","../../lib/js/ierotator/02_over.gif"],
			["../../lib/js/ierotator/03.gif","../../lib/js/ierotator/03_over.gif"],
			["../../lib/js/ierotator/04.gif","../../lib/js/ierotator/04_over.gif"],
			["../../lib/js/ierotator/05.gif","../../lib/js/ierotator/05_over.gif"]
		],
		"numDisplay":'block'
	}
	ier = new ierotator(config);
	effectChange();
}

function changeSetting() {
	var d=document.getElementById('frmScriptRotator');
	var effect=d.effect.value;


	var i,l;
	var optStr='';

	if(document.getElementsByName(effect+'Setting[]').length>1)
	{
		l=d.elements[effect+'Setting[]'].length;
		for(i=0;i<l;i++)
		{
			optStr=optStr+d.elements[effect+'Setting[]'][i].value+',';
		}
	}
	else
	{
		if(d.elements[effect+'Setting[]'])
			optStr=optStr+d.elements[effect+'Setting[]'].value+',';
	}





	optStr=optStr+'Duration='+d.Duration.value;
	//alert("FILTER: progid:DXImageTransform.Microsoft."+effect+"("+optStr+")");
	ier.bannerArea.style.filter="FILTER: progid:DXImageTransform.Microsoft."+effect+"("+optStr+")"

}

function effectChange() {

	var d=document.getElementById('frmScriptRotator');
	var effect=d.effect.value;
	var i,l=d.effect.options.length;
	for(i=0;i<l;i++)
	{
		document.getElementById(d.effect.options[i].value+'Setting').style.display="none";
	}
	document.getElementById(effect+'Setting').style.display="block";
}



</script>

<script>
table_design_load();
startSetting();
setHeight_ifrmCodi();
</script>
