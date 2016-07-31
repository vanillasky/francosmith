<!DOCTYPE html>
<html>
<head>
	<meta charset="euc-kr"/>	
	<meta http-equiv="X-UA-Compatible" content="IE=emulateIE9">
	<script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui-1.8.22.custom.min.js"></script>
	<script type="text/javascript" src="../../js/jQueryRotate.js"></script>
	<link rel="stylesheet" href="../../css/jquery-ui-1.8.22.custom.css">
	<link rel="stylesheet" type="text/css" href="../../css/editer.css"/>	
	<link rel="stylesheet" type="text/css" href="../../css/style.css"/>	
	<script type="text/javascript" src="../../js/editer.js"></script>
	
	<title>코디 에티터</title>
	<script id="thisScript">
		/*기본 template을 로드한다*/
		jQuery(document).ready(function(){
			template('codytype1_1');			
		});
	</script>		
</head>
<body>
<div id="wrap">
	<div id="header">
		<div class="title"><div style="padding-top:16px;float:left"><b>CODY 만들기</b></div><div class="naviBar"></div></div>		
	</div>
	<div id="content">
		<div id="layerType">
			<div style="margin-top:27px;text-align:center"><b>Template</b></div>
			<div style="text-align:center"><select id="group" name="group" onchange="searchTemplate(this.id)">
											<!-- <option value="1">1단</option> -->
											<option value="2" selected>2단</option>
											<option value="3">3단</option>
											<option value="4">4단</option>
											<option value="5">5단</option>											
										   </select>
			</div>
			<!-- 템플릿 미니어처 -->
			<ul id="layerSelect" class="layerSelect">
			<?	foreach($TPobjs as $TPobj){?>
					<li><a href="javascript:" onclick="template('<?=$TPobj->get('TP_id')?>');"><img src='../../data/tmplate_thumbnail/<?=$TPobj->get('TP_id')?>.gif' /></a></li>						
			<?	}?>
			</ul>
		</div>
		<div id="layerBox">
			<!-- 템플릿 위치-->
			
			<div id="templateForm" class="templateForm">
				
			</div>
			<!-- 템플릿 위치-->
			
			<!-- 이미지 컨트롤러 -->
			<div id="leftTool">
				<div style="font:12px arial;margin:10px 17px;">
					<div style="border: 0px solid #DDC5B5;width:100%;height30px">
						<table style="width:100%;height30px" >
						<tr>
							<td width="70"><div>Image rotate </div></td>
							<td><input type="text" id="amount" value="0" class="conValue" /></td>
							<td>
								<div id="scrollbar">
									<div id="slider-range-max"></div>
								</div>
							</td>
						</tr>
						</table>
					</div>
				</div>
				
				<div style="font:12px arial;margin:10px 17px;">
					<div style="width: 100%;">
						<table style="width:100%;height30px" >
						<tr>
							<td width="70"><div>Image size </div></td>
							<td><input type="text" id="imgamount" value="0" class="conValue" /></td>
							<td>
								<div id="imgscrollbar" >
									<div id="size-range-max"></div>
								</div>
							</td>
						</tr>
						</table>						
					</div>
				</div>
			</div>
			<div class="newBtn">
					<a href="javascript:" onclick="newcody()"><img src="../../images/editer/btn_new.gif"/></a>
					<a href="javascript:" onclick="codyImagesDel()"><img src="../../images/editer/btn_delete2.gif"/></a>
			</div>
			<!-- 이미지 컨트롤러 -->
		</div>

		<!-- right container -->
		<div id="editor">
		
			<div class="editorHeader">
				<div id="dynamic"></div>
				
				<div style="background:url(../../images/editer/titledot.gif) no-repeat top left;height:28px;width:335px;">
					<div style="padding-top:12px;padding-left:25px;font:14px dotum;color:#000000;font-weight:bold">상품추가</div>
				</div>

				<div id="codygoodstap">
					<form name="frmList">
					<div style="margin-top:10px">
						<script>new categoryBox('cate[]',4,'','','frmList');</script>
					</div>
					</form>
					<div style="margin-top:7px">
						<select id="sp" name="sp" class="selectOpt2">
							<option value="goodsnm">상품명</option>
							<option value="goodsno">상품번호</option>
							<option value="goodscd">상품코드</option>
							<option value="keyword">유사검색어</option>
						</select>
						<input id="st" name="st" type="text" class="input" style="width:189px" />&nbsp;<a href="javascript:" onclick="searchGoods('1')"><img src="../../images/editer/btn_search.gif" alt="" style="position:absolute"/></a>
					</div>
					<div id="searchItemBox" class="itemBox">
						<div id="searchGoods" style="margin-top:0px" class="textarea">
							<div style="width:217px;height:162px;margin:180px 0 0 60px">
								<img src="../../images/editer/img_guide_drag.gif"/>
							</div>
						</div>
						<div style="margin-top:13px;text-align:center;font:12px arial;color:#000000;">
							 
						</div>
					</div>
				</div>				
				<div style="margin-top:20px;text-align:center">
					<a href="javascript:subit()"><img src="../../images/editer/btn_next.gif" alt="" /></a>
				</div>
			</div>
		
		</div>
		<!-- right container -->

	</div>

	<div class="clear" /></div>
	<div id="print"></div>
		
	<form name="edithtml" method="post" action="indb.php">
		<input type="hidden" id="fn" name="fn" value="CI">
		<input type="hidden" id="codyhtml" name="codyhtml">			
		<input type="hidden" id="T_img_cnt" name="T_img_cnt">			
		<input type="hidden" id="imgnm" name="imgnm">
		<input type="hidden" id="imgno" name="imgno">
		<input type="hidden" id="imgnmsize" name="imgnmsize">
		<input type="hidden" id="imgRotate" name="imgRotate">
		<input type="hidden" id="imgPosition" name="imgPosition">
		<input type="hidden" id="divArea" name="divArea">
		<input type="hidden" id="Divpos" name="Divpos">
		<input type="hidden" id="TP_id" name="TP_id">
		<input type="hidden" id="campusSize" name="campusSize">		
	</form>

</body>
</html>
