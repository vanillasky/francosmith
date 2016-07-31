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
	alert('저장되었습니다');
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
		alert("숫자만 입력하셔야합니다");
		obj.width.focus();
		return false;
	}

	if(!onlynum.test(obj.height.value))
	{
		alert("숫자만 입력하셔야합니다");
		obj.height.focus();
		return false;
	}
	return true;
}

</script>

<div class="title title_top">움직이는 배너 : Script Rotator<span></span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=18')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

<table border="4" bordercolor="#dce1e1" style="border-collapse:collapse;margin-bottom:20px;" width="100%">
<tr><td style="padding:7px 0 10px 10px; color:#666666;">
<div style="padding-top:5px;"><font class="g9" color="#0074BA"><b>※ 일부 브라우저 에서는 움직이는 배너의 효과기능을 지원하지 않습니다.</b></font></div>
<div style="padding-top:7px;">(미지원 브라우저를 사용하는 PC에서 쇼핑몰 접근시 설정된 배너 움직임 효과 가 보여지지 않습니다.)</u></div>
<div style="padding-top:7px;">- 지원 브라우져 : IE 6 ~ IE 9 </u></div>
<div style="padding-top:7px;">- 미지원 브라우져 : IE 10 이상 , Chrome , Firefox , Safari , Opera</u></div>
</td></tr>
</table>

<table width="100%">
<tr>
	<td class="pageInfo">효과 미리보기</td>
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
	<td>사이즈</td>
	<td colspan=3>
	<input type="text" name="width" style='width:50px' value="<?=$banner_info['width']?>"> x
	<input type="text" name="height" style='width:50px' value="<?=$banner_info['height']?>">
	</td>


<tr>
	<td>숫자표시</td>
	<td>
	<input type="radio" name="numDisplay" value="yes" style="border:0px" <?if($banner_info['numDisplay']){?>checked<?}?> >예 &nbsp;
	<input type="radio" name="numDisplay" value="" style="border:0px" <?if(!$banner_info['numDisplay']){?>checked<?}?>>아니요
	</td>

	<td>대기시간</td>
	<td>
	<select name="wait">
	<option value="1000">1초</option>
	<option value="1500">1.5초</option>
	<option value="2000">2초</option>
	<option value="3000">3초</option>
	<option value="4000">4초</option>
	</select>
	</td>

</tr>
<tr>
<td>효과</td>
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
	<td>효과설정</td>
	<td>
	효과시간 :
	<select name="Duration"  onchange="changeSetting()">
	<option value="0.5">0.5초</option>
	<option value="0.7">0.7초</option>
	<option value="1">1초</option>
	<option value="1.3">1.3초</option>
	<option value="1.5">1.5초</option>
	</select><br>

	<div id="BarnSetting" style="display:none">
		움직임 :
		<select name="BarnSetting[]" onchange="changeSetting()">
		<option value="motion=out">안쪽에서 바깥쪽으로</option>
		<option value="motion=in">바깥쪽에서 안쪽으로</option>
		</select><br>

		흐름 :
		<select name="BarnSetting[]" onchange="changeSetting()">
		<option value="orientation=vertical">가로</option>
		<option value="orientation=horizontal">세로</option>
		</select>
	</div>

	<div id="BlindsSetting" style="display:none">
		갯수 :
		<select name="BlindsSetting[]" onchange="changeSetting()">
		<option value="Bands=2">2</option>
		<option value="Bands=4">4</option>
		<option value="Bands=6">6</option>
		<option value="Bands=8">8</option>
		<option value="Bands=10">10</option>
		</select><br>

		방향 :
		<select name="BlindsSetting[]"  onchange="changeSetting()">
		<option value="direction=up">위</option>
		<option value="direction=down">아래</option>
		<option value="direction=left">왼쪽</option>
		<option value="direction=right">오른쪽</option>
		</select>
	</div>

	<div id="CheckerboardSetting" style="display:none">
		방향 :
		<select name="CheckerboardSetting[]" onchange="changeSetting()">
		<option value="Direction=up">위</option>
		<option value="Direction=down">아래</option>
		<option value="Direction=left">왼쪽</option>
		<option value="Direction=right">오른쪽</option>
		</select><br>

		가로갯수 :
		<select name="CheckerboardSetting[]" onchange="changeSetting()">
		<option value="SquaresX=2">2</option>
		<option value="SquaresX=4">4</option>
		<option value="SquaresX=6">6</option>
		<option value="SquaresX=8">8</option>
		<option value="SquaresX=10">10</option>
		<option value="SquaresX=12">12</option>
		</select>
		&nbsp;
		세로갯수 :
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
		오버랩 :
		<select name="FadeSetting[]" onchange="changeSetting()">
		<option value="Overlap=0.00">0.00</option>
		<option value="Overlap=0.25">0.25</option>
		<option value="Overlap=0.50">0.50</option>
		<option value="Overlap=0.75">0.75</option>
		<option value="Overlap=1.00">1.00</option>
		</select>

	</div>

	<div id="GradientWipeSetting" style="display:none">
		그라디언트크기 :
		<select name="GradientWipeSetting[]" onchange="changeSetting()">
		<option value="GradientSize=0.00">0.00</option>
		<option value="GradientSize=0.25">0.25</option>
		<option value="GradientSize=0.50">0.50</option>
		<option value="GradientSize=0.75">0.75</option>
		<option value="GradientSize=1.00">1.00</option>
		</select><br>
		스타일 :
		<select name="GradientWipeSetting[]" onchange="changeSetting()">
		<option value="wipestyle=0">왼쪽에서 오른쪽</option>
		<option value="wipestyle=1">위에서 아래로</option>
		</select><br>
		방향 :
		<select name="GradientWipeSetting[]" onchange="changeSetting()">
		<option value="motion=forward">앞으로</option>
		<option value="motion=reverse">반대로</option>
		</select><br>
	</div>

	<div id="InsetSetting" style="display:none">

	</div>

	<div id="IrisSetting" style="display:none">
		스타일 :
		<select name="IrisSetting[]" onchange="changeSetting()">
		<option value="irisstyle=DIAMOND">다이아몬드</option>
		<option value="irisstyle=CIRCLE">원</option>
		<option value="irisstyle=CROSS">크로스</option>
		<option value="irisstyle=PLUS">플로스</option>
		<option value="irisstyle=SQUARE">사각형</option>
		<option value="irisstyle=STAR">별</option>
		</select><br>
		방향 :
		<select name="IrisSetting[]" onchange="changeSetting()">
		<option value="motion=in">앞으로</option>
		<option value="motion=out">반대로</option>
		</select><br>
	</div>

	<div id="PixelateSetting" style="display:none">
		최고픽셀크기 :
		<select name="PixelateSetting[]" onchange="changeSetting()">
		<option value="MaxSquare=2">2</option>
		<option value="MaxSquare=5">5</option>
		<option value="MaxSquare=10">10</option>
		<option value="MaxSquare=20">20</option>
		</select>
	</div>

	<div id="RadialWipeSetting" style="display:none">
		스타일 :
		<select name="RadialWipeSetting[]" onchange="changeSetting()">
		<option value="wipestyle=CLOCK">CLOCK</option>
		<option value="wipestyle=WEDGE">WEDGE</option>
		<option value="wipestyle=RADIAL">RADIAL</option>
		</select>
	</div>

	<div id="RandomBarsSetting" style="display:none">
		스타일 :
		<select name="RandomBarsSetting[]" onchange="changeSetting()">
		<option value="orientation=vertical">세로</option>
		<option value="orientation=horizontal">가로</option>
		</select>
	</div>


	<div id="RandomDissolveSetting" style="display:none">

	</div>


	<div id="SlideSetting" style="display:none">
		스타일 :
		<select name="SlideSetting[]" onchange="changeSetting()">
		<option value="slidestyle=PUSH">밀기</option>
		<option value="slidestyle=HIDE">숨기기</option>
		<option value="slidestyle=SWAP">겹치기</option>
		</select>
		<br>

		분열 :
		<select name="SlideSetting[]" onchange="changeSetting()">
		<option value="Bands=1">1</option>
		<option value="Bands=3">3</option>
		<option value="Bands=5">5</option>
		<option value="Bands=10">10</option>
		<option value="Bands=25">25</option>
		</select>
	</div>



	<div id="SpiralSetting" style="display:none">
		그리드사이즈X :
		<select name="SpiralSetting[]" onchange="changeSetting()">
		<option value="GridSizeX=8">8</option>
		<option value="GridSizeX=16">16</option>
		<option value="GridSizeX=32">32</option>
		</select>
		<br>

		그리드사이즈Y :
		<select name="SpiralsSetting[]" onchange="changeSetting()">
		<option value="GridSizeY=8">8</option>
		<option value="GridSizeY=16">16</option>
		<option value="GridSizeY=32">32</option>
		</select>
	</div>

	<div id="StretchSetting" style="display:none">
		스타일 :
		<select name="StretchSetting[]" onchange="changeSetting()">
		<option value="stretchstyle=SPIN">spin</option>
		<option value="stretchstyle=HIDE">hide</option>
		<option value="stretchstyle=PUSH">push</option>
		</select>
	</div>

	<div id="StripsSetting" style="display:none">
		움직임 :
		<select name="StripsSetting[]" onchange="changeSetting()">
		<option value="motion=leftup">왼쪽에서 위로</option>
		<option value="motion=leftdown">왼쪽에서 아래로</option>
		<option value="motion=rightup">오른쪽에서 위로</option>
		<option value="motion=rightdown">오른쪽에서 아래로</option>
		</select>
	</div>

	<div id="WheelSetting" style="display:none">
		분열 :
		<select name="WheelSetting[]" onchange="changeSetting()">
		<option value="spokes=2">2</option>
		<option value="spokes=4">4</option>
		<option value="spokes=6">6</option>
		<option value="spokes=8">8</option>
		<option value="spokes=10">10</option>
		</select>
	</div>

	<div id="ZigzagSetting" style="display:none">
		그리드사이즈X :
		<select name="ZigzagSetting[]" onchange="changeSetting()">
		<option value="GridSizeX=8">8</option>
		<option value="GridSizeX=16">16</option>
		<option value="GridSizeX=32">32</option>
		</select>
		<br>

		그리드사이즈Y :
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
			<td>이미지{$k}</td>
			<td colspan='3'>
			파일 : <input type='file' name='image[$k]'>
			";
		if($v)
		{
			echo "<input type='checkbox' name='image_del[]' value='$k' style='border:0px'>삭제
			&nbsp; <a href='../../data/scriptrotator/$k.jpg' target='_blank'>자세히보기</a>
			";
		}
		echo "<br>
		링크주소 : <input type='text' name='link[$k]' style='width:400px' value='".$banner_info['link'][$k]."'>
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
디자인코디에서 사용가능한 치환코드 : &nbsp;  {=scriptRotator()}
</div>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />Script Rotator는 IE에서만 정상작동됩니다</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />Script Rotator는 IE9 까지만 지원합니다. IE10 에서는 필터 효과가 지원되지 않습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />한 페이지에 2개이상 넣으실수 없습니다</td></tr>
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
