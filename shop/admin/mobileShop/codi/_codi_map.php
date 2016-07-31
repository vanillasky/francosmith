<?

$float_type = in_array($data_file['outline_sidefloat'], array('left', 'right')) ? $data_file['outline_sidefloat'] : $data_default['outline_sidefloat'];

?>
<style type="text/css">
.codi_map { }
.codi_map img { vertical-align:middle }
.codi_map p { margin:0 0 5px 0;  }
.codi_map .ready { border:solid 7px #E6E6E6; }
.codi_map .editing { border:solid 7px #6B86D5; }

/*** 레이어 배치 ***/
.codi_map #frame_main { text-align:center; width:100%; }

/*** 레이어 세부설정 ***/
.codi_map #frame_main #frame_outHeader { height:25px; background:url("../img/map_header_bg_off.gif") no-repeat center bottom; }
.codi_map #frame_main #frame_outHeader div { padding-top:5px; }

.codi_map #frame_main #frame_header div,
.codi_map #frame_main #frame_footer div { height:38px; }
.codi_map #frame_main #frame_header div div,
.codi_map #frame_main #frame_footer div div { padding-top:8px;}

.codi_map #frame_main #frame_side div,
.codi_map #frame_main #frame_body div { margin:3px 0; height:58px; }
* html .codi_map #frame_main #frame_side div,
* html .codi_map #frame_main #frame_body div { height:72px; }
.codi_map #frame_main #frame_body div { margin-left:3px; }
* html .codi_map #frame_main #frame_body div { margin-left:0; }
.codi_map #frame_main #frame_side div div,
.codi_map #frame_main #frame_body div div { height:0; margin:0; padding:0;}
.codi_map #frame_main #frame_side div div { padding-top:7px;}
.codi_map #frame_main #frame_body div div { padding-top:14px;}

.codi_map #frame_main #frame_outFooter { height:25px; background:url("../img/map_footer_bg_off.gif") no-repeat center top; }
.codi_map #frame_main #frame_outFooter div { padding-top:2px; }

.codi_map #frame_scroll div { text-align:center; margin-left:3px; padding:24px 0 23px 0; }
* html .codi_map #frame_scroll div { margin-left:0; }
</style>


<div id="codi_map" class="codi_map">
	<div id="frame_main">

		<!-- outline/_header.htm : Start -->
		<div id="frame_outHeader">
			<div class="ver81">outline/_header.htm <a href="../mobileShop/iframe.codi.php?design_file=outline/_header.htm"><img src="../img/btn_html.gif" /></a></div>
		</div>
		<!-- outline/_header.htm : End -->

		<!-- 1. 상단디자인 : Start -->
		<div id="frame_header">
			<div class="ready">
				<div>
				<img src="../img/codi_main_01_on.gif">
				<input type="hidden" name="outline_header" id="outline_header" value="outline/_header.htm ">outline/_header.htm
				<a href="../mobileShop/iframe.codi.php?design_file=outline/_header.htm"><img src="../img/btn_html.gif"></a>
				</div>
			</div>
		</div>
		<!-- 1. 상단디자인 : End -->

		<!-- 2. 본문디자인 : Start -->
		<div id="frame_body"><div class="ready"><div><img src="../img/codi_main_03_on.gif"> <A HREF="../mobileShop/iframe.codi.php?design_file=index.htm"><img src="../img/btn_html.gif" /></A></div></div></div>
		<!-- 2. 본문디자인 : End -->

		<!-- 3. 하단디자인 : Start -->
		<div id="frame_footer">
			<div class="ready">
				<div>
				<img src="../img/codi_main_04_on.gif">
				<input type="hidden" name="outline_footer" id="outline_footer" value="outline/_footer.htm">outline/_footer.htm
				<a href="../mobileShop/iframe.codi.php?design_file=outline/_footer.htm"><img src="../img/btn_html.gif"></a>
				</div>
			</div>
		</div>
		<!-- 3. 하단디자인 : End -->

		<!-- outline/_footer.htm : Start -->
		<div id="frame_outFooter">
			<div class="ver81">outline/_footer.htm <a href="../mobileShop/iframe.codi.php?design_file=outline/_footer.htm"><img src="../img/btn_html.gif" /></a></div>
		</div>
		<!-- outline/_footer.htm : End -->

	</div>

	<div style="clear:both;"></div>
</div>


<script language="javascript">

var design_file = '<?=$_GET['design_file']?>';
var form_type = '<?=$form_type?>';
var float_type = '<?=$float_type?>';

/* 측면디자인 위치 */
DCMAPM.file_float(float_type);

/* 본문디자인 파일명 출력 */
if (form_type == 'file'){
	// link aname '편집하기'
	_ID('frame_body').getElementsByTagName('a')[0].href = '#codi_info';

	// print file_name
	var pNode = document.createElement('p')
	pNode.innerHTML = design_file;
	_ID('frame_body').getElementsByTagName('div')[0].getElementsByTagName('div')[0].appendChild(pNode);
}
else if (form_type != 'default'){
	_ID('frame_body').getElementsByTagName('a')[0].parentNode.removeChild(_ID('frame_body').getElementsByTagName('a')[0]);
}

/* 편집중 활성화 */
if (form_type == 'outline' && design_file == 'outline/_header.htm'){ // outline/_header.htm
	_ID('frame_outHeader').style.background = 'url("../img/map_header_bg_on.gif") no-repeat center bottom';
	_ID('frame_outHeader').getElementsByTagName('div')[0].style.color = '#FFFFFF';

	if (float_type == 'left'){
		_ID('frame_side').getElementsByTagName('div')[0].style.marginTop = '0';

		// Blue Box
		var d = document.createElement('div');
		d.style.position = 'relative';
		rdNode = _ID('codi_map').insertBefore(d, _ID('codi_map').firstChild);
		rdNode.innerHTML = '\
			<div style="position:absolute; width:100%; margin-top:25px;">\
				<div style="float:left; width:42%;">\
					<div style="border:solid 7px #6B86D5; border-width:7px 0 0 7px;"><div style="height:38px;"></div></div>\
				</div>\
				<div style="float:right; width:58%;">\
					<div style="border:solid 7px #6B86D5; border-width:7px 7px 7px 0;"><div style="height:38px;"></div></div>\
				</div>\
				<div style="clear:both; width:42%; position:relative;">\
					<div style="position:absolute; top:-7px; width:100%;"><div style="border:solid 7px #6B86D5; border-width:0 7px 7px 7px;"><div style="height:72px;"></div></div></div>\
				</div>\
			</div>\
			';
	}
	else {
		_ID('frame_header').getElementsByTagName('div')[0].className = 'editing';
	}
}
else if (form_type == 'outSection' && design_file.match(/outline\/header/) != null){ // 1. 상단디자인
	_ID('frame_header').getElementsByTagName('div')[0].style.background = '#6B86D7';
	_ID('frame_header').getElementsByTagName('div')[0].style.borderColor = '#6B86D7';
	_ID('frame_header').getElementsByTagName('div')[0].getElementsByTagName('img')[0].src = '../img/codi_main_01_ing.gif';
	_ID('frame_header').getElementsByTagName('div')[0].style.color = '#FFFFFF';
}
else if (form_type == 'outSection' && design_file.match(/outline\/side/) != null){ // 2. 측면디자인
	_ID('frame_side').getElementsByTagName('div')[0].style.background = '#6B86D7';
	_ID('frame_side').getElementsByTagName('div')[0].style.borderColor = '#6B86D7';
	_ID('frame_side').getElementsByTagName('div')[0].getElementsByTagName('div')[0].getElementsByTagName('img')[0].src = '../img/codi_main_02_ing.gif';
	_ID('frame_side').getElementsByTagName('div')[0].style.color = '#FFFFFF';
}
else if (form_type == 'file'){ // 3. 본문디자인
	_ID('frame_body').getElementsByTagName('div')[0].style.background = '#6B86D7';
	_ID('frame_body').getElementsByTagName('div')[0].style.borderColor = '#6B86D7';
	_ID('frame_body').getElementsByTagName('div')[0].getElementsByTagName('div')[0].getElementsByTagName('img')[0].src = '../img/codi_main_03_ing.gif';
	_ID('frame_body').getElementsByTagName('div')[0].style.color = '#FFFFFF';
}
else if (form_type == 'outSection' && design_file.match(/outline\/footer/) != null){ // 4. 하단디자인
	_ID('frame_footer').getElementsByTagName('div')[0].style.background = '#6B86D7';
	_ID('frame_footer').getElementsByTagName('div')[0].style.borderColor = '#6B86D7';
	_ID('frame_footer').getElementsByTagName('div')[0].getElementsByTagName('img')[0].src = '../img/codi_main_04_ing.gif';
	_ID('frame_footer').getElementsByTagName('div')[0].style.color = '#FFFFFF';
}
else if (form_type == 'outline' && design_file == 'outline/_footer.htm'){ // outline/_footer.htm
	_ID('frame_outFooter').style.background = 'url("../img/map_footer_bg_on.gif") no-repeat center bottom';
	_ID('frame_outFooter').getElementsByTagName('div')[0].style.color = '#FFFFFF';

	if (float_type == 'left'){
		// Blue Box
		var d = document.createElement('div');
		d.style.position = 'relative';
		rdNode = _ID('codi_map').insertBefore(d, _ID('codi_map').firstChild);
		rdNode.innerHTML = '\
			<div style="position:absolute; width:100%; margin-top:80px;">\
				<div style="float:left; width:100%;">\
					<div style="float:right; width:42%; height:75px; position:relative;">\
						<div style="position:absolute; top:0px; width:100%;"><div style="border:solid 7px #6B86D5; border-width:0 7px 0 0;"><div style="height:75px;"></div></div></div>\
					</div>\
					<div style="clear:both;"></div>\
					<div style="float:left; width:58%;">\
						<div style="border:solid 7px #6B86D5; border-width:7px 0 7px 7px;"><div style="height:38px;"></div></div>\
					</div>\
					<div style="float:right; width:42%;">\
						<div style="border:solid 7px #6B86D5; border-width:7px 0 7px 0;"><div style="height:38px;"></div></div>\
					</div>\
				</div>\
				<div style="float:right; width:12%;">\
					<div style="border:solid 7px #6B86D5; border-width:7px 7px 7px 0;"><div style="height:113px;"></div></div>\
				</div>\
			</div>\
			';
	}
	else {

		// Blue Box
		var d = document.createElement('div');
		d.style.position = 'relative';
		rdNode = _ID('codi_map').insertBefore(d, _ID('codi_map').firstChild);
		rdNode.innerHTML = '\
			<div style="position:absolute; width:100%; margin-top:83px;">\
				<div style="float:left; width:100%;">\
					<div style="float:right; width:42%; height:72px; position:relative;">\</div>\
					<div style="clear:both;"></div>\
					<div style="float:left; width:100%;">\
						<div style="border:solid 7px #6B86D5; border-width:7px 7px 7px 7px;"><div style="height:38px;"></div></div>\
					</div>\
				</div>\
			</div>\
			';
	}
}




</script>
