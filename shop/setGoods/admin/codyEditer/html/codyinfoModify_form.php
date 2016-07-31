<!DOCTYPE HTML>
<html>
<head>
	<meta charset="euc-kr"/>		
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link rel="stylesheet" type="text/css" href="../../css/editer.css"/>
	<link rel="stylesheet" type="text/css" href="../../css/style.css"/>
	<script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
	<title>코디 내용 입력</title>
	<script>
		function subit(){
			if(jQuery('#cody_name_web').val() == ""){
				alert('코디이름을 입력하세요.');
				return;
			}
			jQuery('#codycontent').val(jQuery('#codycontent_web').val());
			jQuery('#cody_name').val(jQuery("#cody_name_web").val());			
			jQuery('#state').val(jQuery('#state_web:checked').val());
		
			jQuery('form').submit();			
		}
	</script>
</head>
<body>
<div id="wrap">
	<div id="header">
		<div class="title"><div style="padding-top:16px;float:left"><b>CODY 만들기</b></div><div class="naviBar"></div></div>		
	</div>
	<div id="content2">
		<div id="layerBox2">
			<div id="templateForm" class="htmlArea2">
				<img src="../../data/org/<?=$thumnail_name?>">
			</div>
		</div>

		<div id="editor2">
			<div class="editorHeader">
				<div class="codiname">코디이름 <input type="text" id="cody_name_web" name="cody_name_web" value="<?=$cody_name?>" class="input2" style="width:335px"></div>
				<div class="textarea2">
					<textarea id="codycontent_web" name="codycontent_web" style="width:99%;height:490px" type=editor required label="내용"><?=$memo?></textarea>
					
				</div>
				<div class="codiname">상태 <input type="radio" id="state_web" name="state_web" value="Y" <?=$stateY?>> YES <input type="radio" id="state_web" name="state_web" value="N" <?=$stateN?>> NO </div>
			</div>
		</div>
	</div>
	<div class="clear"></div>

	<div id="footer">
		<div style="margin-top:22px;text-align:center;font:11px dotum;color:#018fde;height:62px">
			
		</div>
		<div class="naviBtn" ><a href="javascript:subit()"><img src="../../images/editer/btn_finish.gif"/></a></div>
	</div>
</div>
<div id="print"></div>
		
<form name="edithtml" method="post" action="./indb.php">
	<input type="hidden" id="fn" name="fn" value="M">
	<input type="hidden" id="idx" name="idx" value="<?=$idx?>">			
	<input type="hidden" id="state" name="state" value="">					
	<input type="hidden" id="codycontent" name="codycontent">	
	<input type="hidden" id="cody_name" name="cody_name">	
</form>

</body>
</html>
