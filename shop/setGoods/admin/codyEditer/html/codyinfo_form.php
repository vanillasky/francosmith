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
			jQuery('#codyhtml').val(jQuery("#templateForm").html());
			jQuery('#cody_name').val(jQuery("#cody_name_web").val());			
			
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
			<div id="templateForm" class="htmlArea">
				<?=$codyhtml?>
			</div>
		</div>


		<div id="editor2">
			<div class="editorHeader">
				<div class="codiname">코디이름 <input type="text" id="cody_name_web" name="cody_name_web" class="input2" style="width:335px"></div>
				<div class="textarea2">
					<textarea id="codycontent_web" name="codycontent_web" onclick="if(this.value=='코디 스토리를 입력해 주세요~~'){this.value=''}" style="width:99%;height:515px">코디 스토리를 입력해 주세요~~</textarea>					
				</div>
			</div>
		</div>
	</div>
	<div class="clear"></div>

	<div id="footer">
		<div style="margin-top:22px;text-align:center;font:11px dotum;color:#018fde;height:62px">
			<img src="../../images/editer/icon_alert.gif"/> 등록하기 완료시 이미지 수정이 불가능 합니다. 등록된 코디는 코디 리스트에서 등록여부를 확인하신 후 진열상태를 설정해 주세요.
		</div>
		<div class="naviBtn" ><a href="javascript:alert('작업된 코디 이미지가 초기화 됩니다.');history.back(-1);"><img src="../../images/editer/btn_new.gif"/></a><!--img src="../images/editer/btn_preview.gif"/--><a href="javascript:subit()"><img src="../../images/editer/btn_register.gif"/></a></div>
	</div>
</div>
<div id="print"></div>
		
<form name="edithtml" method="post" action="./indb.php">
	<input type="hidden" id="fn" name="fn" value="I">
	<input type="hidden" id="codyhtml" name="codyhtml">			
	<input type="hidden" id="T_img_cnt" name="T_img_cnt" value="<?=$T_img_cnt?>">			
	<input type="hidden" id="imgnm" name="imgnm" value="<?=$imgnm?>">
	<input type="hidden" id="imgno" name="imgno" value="<?=$imgno?>">
	<input type="hidden" id="imgnmsize" name="imgnmsize" value="<?=$imgnmsize?>">
	<input type="hidden" id="imgRotate" name="imgRotate" value="<?=$imgRotate?>">
	<input type="hidden" id="imgPosition" name="imgPosition" value="<?=$imgPosition?>">
	<input type="hidden" id="divArea" name="divArea" value="<?=$divArea?>">
	<input type="hidden" id="Divpos" name="Divpos" value="<?=$Divpos?>">	
	<input type="hidden" id="TP_id" name="TP_id" value="<?=$TP_id?>">
	<input type="hidden" id="campusSize" name="campusSize" value="<?=$campusSize?>">
	<input type="hidden" id="codycontent" name="codycontent">	
	<input type="hidden" id="cody_name" name="cody_name">	
</form>

</body>
</html>
