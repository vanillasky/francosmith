<?php
/**
 * 멀티 팝업 이미지 등록 페이지
 * @author cjb3333 , artherot @ godosoft development team.
 */

// 팝업 상단 해더
include "../_header.popup.php";
?>
<script src="/shop/lib/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="/shop/lib/js/jquery.banner.js"></script>

<form name="frm" action="./indb.multipopup.php" method="post" enctype="multipart/form-data"  target="iframe_upload">
<input type="hidden" name="indexKey" id="indexKey" value="<?php echo $_GET['indexKey'];?>" />
<input type="hidden" name="mode" value="upload">

<table width="100%" class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>이미지 등록방식</td>
	<td>
		<input type="radio" name="image_attach_method" value="file" checked="checked" />직접 업로드
		<input type="radio" name="image_attach_method" value="url" />이미지호스팅 URL 입력
	</td>
</tr>
</table>
<div style="margin:5px 0px 5px 0px;">
	<div class="url" style="border:1px solid #cccccc; background:#f6f6f6; color:#006594; padding:5px;">
		이미지 호스팅에 등록된 이미지의 웹 주소를 복사하여 붙여 넣기 하시면 상품 이미지가 등록됩니다.<br />
		ex) http://godohosting.com/img/img.jpg
	</div>
</div>
<table width="100%" class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>작은이미지1<br>(기본)</td>
	<td>
		<div class="ver8 blue">※ 기본 노출되는 이미지입니다.</div>
		<div class="file noline"><input type="file" name="mouseOutImg" style="width:95%" /><input type="hidden" name="mouseOutImg" /></div>
		<div class="url"><input type="text" name="url_mouseOutImg" style="width:95%" class="line" /></div>
	</td>
</tr>
<tr>
	<td>작은이미지2<br>(효과)</td>
	<td>
		<div class="ver8 blue">※ 마우스커서를 올렸을 때 보이는 이미지입니다.</div>
		<div class="file noline"><input type="file" name="mouseOnImg" style="width:95%" /><input type="hidden" name="mouseOnImg" /></div>
		<div class="url"><input type="text" name="url_mouseOnImg" style="width:95%" class="line" /></div>
	</td>
</tr>
<tr>
	<td>큰이미지</td>
	<td>
		<div class="ver8 blue">※ 작은이미지 선택 시 중앙에 보이는 이미지입니다.</div>
		<div class="file noline"><input type="file" name="mainBannerImg" style="width:95%" /><input type="hidden" name="mainBannerImg" /></div>
		<div class="url"><input type="text" name="url_mainBannerImg" style="width:95%" class="line" /></div>
	</td>
</tr>
<tr>
	<td>클릭시 이동할 URL</td>
	<td><input type="text" name="linkUrl" style="width:95%" class="line" />
	</td>
</tr>
<tr>
	<td>클릭시 이동할 창</td>
	<td>
		<input type="radio" name="linkTarget" value="blank" checked="checked" />새로운창
		<input type="radio" name="linkTarget" value="self" />본창
	</td>
</tr>
</table>

<div style="padding-top:20px" align="center" class="noline">
	<input type="image" src="../img/btn_save.gif" id="btn_submit" alt="저장" />
	<input type="image" src="../img/btn_cancel.gif" id="btn_cancel" alt="저장" />
</div>
</form>
<script>
function fileUpload()
{
    var target_name = 'iframe_upload';

    // iframe 생성
    var iframe = $('<iframe src="../../blank.txt" name="'+target_name+'" style="display:none"></iframe>');
    $('body').append(iframe);

    // iframe 내용에 따라 처리
    iframe.load(function(){
        var doc		= this.contentWindow ? this.contentWindow.document : (this.contentDocument ? this.contentDocument : this.document);
        var root	= doc.documentElement ? doc.documentElement : doc.body;
        var result	= root.textContent ? root.textContent : root.innerText;

		iframe.remove();

		if(result == 'fileError'){
			alert('이미지 파일이 잘못되었거나, 작성된 정보가 잘못되었습니다.');
			return;
		} else {
			uploadSuccess(result);
		}
    });

    // 이미지 업로드
	//document.frm.submit();
}

/**
 * 이미지 등록후 부모창에 정보 전달
 */
function uploadSuccess(rst)
{
	var attach_method	= $('input[name=image_attach_method]:checked').val();			// 이미지 등록방식
	var mouseOnImg		= '';															// 작은 이미지1
	var mouseOutImg		= '';															// 작은 이미지2
	var mainBannerImg	= '';															// 큰 이미지
	var linkTarget		= $('input[name=linkTarget]:checked').val();					// 링크 타겟
	var linkUrl			= $('input[name=linkUrl]').val();								// 링크 URL
	var indexKey		= $('input[name=indexKey]').val();								// 멉티 팝업의 해당 위치 값 (인덱스)
	var contentTable	= $(parent.parent.ifrmCodi.document).find("#contentTable");

	// 이미지호스팅 URL 입력인 경우
	if(attach_method == 'url'){
		mouseOnImg		= $('input[name=url_mouseOnImg]').val();
		mouseOutImg		= $('input[name=url_mouseOutImg]').val();
		mainBannerImg	= $('input[name=url_mainBannerImg]').val();
	}
	// 직접 업로드인 경우
	else {
		var obj			= eval('('+rst+')');
		mouseOnImg		= obj.mouseOnImg;
		mouseOutImg		= obj.mouseOutImg;
		mainBannerImg	= obj.mainBannerImg;
	}

	contentTable.find("#image_attach_method"+indexKey).val(attach_method);
	contentTable.find("#mouseOnImg"+indexKey).val(mouseOnImg);
	contentTable.find("#mouseOutImg"+indexKey).val(mouseOutImg);
	contentTable.find("#mainBannerImg"+indexKey).val(mainBannerImg);
	contentTable.find("#linkUrl"+indexKey).val(linkUrl);
	contentTable.find("#linkTarget"+indexKey).val(linkTarget);

	parent.parent.ifrmCodi.imgTableView($(parent.parent.ifrmCodi.document).find(".mimgView"),mainBannerImg,300);
	parent.parent.ifrmCodi.imgTableView(contentTable.find(".simgView_"+indexKey),mouseOutImg,80);

	parent.parent.ifrmCodi.setImgDataTemp();
	parent.setHeight_ifrmCodi();
	parent.closeLayer();
}

function preview(obj)
{
	var dir;
	if(obj.val().indexOf("tmp_") > -1) dir = 'tmp_skinCopy';
	else if(obj.val().indexOf("ori_") > -1) dir = 'multipopup';

	target = obj.next();
	objSrc = "../../../data/" + dir + "/" + obj.val();

	if(/^http(s)?:\/\//.test(obj.val())){
		src = obj.val();
	}else{
		src = objSrc;
	}

	target.html(" <img src='" + src + "' width=20 onload='if(this.height>this.width){this.height=20}' style='border:1 solid #cccccc' onclick=popupImg(this.src,'../../') class=hand>");
}

/**
 * 이미지 등록 방식
 */
function fnSetImageAttachForm()
{
	if($('input[name=image_attach_method]:checked').val() == 'file'){
		$('.file').show();
		$('.url').hide();
	}else{
		$('.file').hide();
		$('.url').show();
	}
}

/**
 * 부모창에 있는 값을 레이어 팝업창에 표시
 */
function setLoad()
{
	// 선택 인덱스
	indexKey		= $('input[name=indexKey]').val();

	//부모창 테이블
	contentTable	= $(parent.parent.ifrmCodi.document).find("#contentTable");

	//부모창 정보
	par_image_attach_method		= contentTable.find("#image_attach_method"+indexKey).val();
	par_mouseOnImg				= contentTable.find("#mouseOnImg"+indexKey).val();
	par_mouseOutImg				= contentTable.find("#mouseOutImg"+indexKey).val();
	par_mainBannerImg			= contentTable.find("#mainBannerImg"+indexKey).val();
	par_linkUrl					= contentTable.find("#linkUrl"+indexKey).val();
	par_linkTarget				= contentTable.find("#linkTarget"+indexKey).val();

	//레이어창 정보
	self_image_attach_method	= $('input[name=image_attach_method]');
	self_mouseOnImg				= $('input:hidden[name=mouseOnImg]');
	self_mouseOutImg			= $('input:hidden[name=mouseOutImg]');
	self_mainBannerImg			= $('input:hidden[name=mainBannerImg]');
	self_linkUrl				= $('input[name=linkUrl]');
	self_linkTarget				= $('input[name=linkTarget]');

	// 레이어창에 기존값 대입
	self_mouseOnImg.val(par_mouseOnImg);
	self_mouseOutImg.val(par_mouseOutImg);
	self_mainBannerImg.val(par_mainBannerImg);
	self_linkUrl.val(par_linkUrl);

	for (var i = 0; i < self_image_attach_method.length; i++) {
		if (self_image_attach_method[i].value == par_image_attach_method) self_image_attach_method[i].checked = true;
	}
	for (var i = 0; i < self_linkTarget.length; i++){
		if (self_linkTarget[i].value == par_linkTarget) self_linkTarget[i].checked = true;
	}

	if($('input[name=image_attach_method]:checked').val() == 'url'){

		self_mouseOnImg.val('');
		self_mouseOutImg.val('');
		self_mainBannerImg.val('');

		url_mouseOnImg = $('input[name=url_mouseOnImg]');
		url_mouseOutImg = $('input[name=url_mouseOutImg]');
		url_mainBannerImg = $('input[name=url_mainBannerImg]');

		url_mouseOnImg.val(par_mouseOnImg);
		url_mouseOutImg.val(par_mouseOutImg);
		url_mainBannerImg.val(par_mainBannerImg);

		if(par_mouseOnImg) preview(url_mouseOnImg);
		if(par_mouseOutImg)	preview(url_mouseOutImg);
		if(par_mainBannerImg) preview(url_mainBannerImg);

	}else{

		if(par_mouseOnImg) preview(self_mouseOnImg);
		if(par_mouseOutImg)	preview(self_mouseOutImg);
		if(par_mainBannerImg) preview(self_mainBannerImg);
	}
}

$(document).ready(function()
{
    // 등록 버튼 클릭시
    $('#btn_submit').click(function(){
		if($('input[name=image_attach_method]:checked').val() == 'url'){
			uploadSuccess();
			return;
		}
        fileUpload();
    });

	// 이미지 등록 방식 선택시 "이미지 등록 방식" 리셋
	$('input[name=image_attach_method]').click(function(){
		fnSetImageAttachForm();
    });

    // 취소 버튼 클릭시
    $('#btn_cancel').click(function(){
        parent.closeLayer();
    });

	// 부모창에 있는 값을 레이어 팝업창에 표시
	setLoad();

	// "이미지 등록 방식" 리셋
	fnSetImageAttachForm();
});
</script>
</body>
</html>