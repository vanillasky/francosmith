<?php
/**
 * ��Ƽ �˾� �̹��� ��� ������
 * @author cjb3333 , artherot @ godosoft development team.
 */

// �˾� ��� �ش�
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
	<td>�̹��� ��Ϲ��</td>
	<td>
		<input type="radio" name="image_attach_method" value="file" checked="checked" />���� ���ε�
		<input type="radio" name="image_attach_method" value="url" />�̹���ȣ���� URL �Է�
	</td>
</tr>
</table>
<div style="margin:5px 0px 5px 0px;">
	<div class="url" style="border:1px solid #cccccc; background:#f6f6f6; color:#006594; padding:5px;">
		�̹��� ȣ���ÿ� ��ϵ� �̹����� �� �ּҸ� �����Ͽ� �ٿ� �ֱ� �Ͻø� ��ǰ �̹����� ��ϵ˴ϴ�.<br />
		ex) http://godohosting.com/img/img.jpg
	</div>
</div>
<table width="100%" class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�����̹���1<br>(�⺻)</td>
	<td>
		<div class="ver8 blue">�� �⺻ ����Ǵ� �̹����Դϴ�.</div>
		<div class="file noline"><input type="file" name="mouseOutImg" style="width:95%" /><input type="hidden" name="mouseOutImg" /></div>
		<div class="url"><input type="text" name="url_mouseOutImg" style="width:95%" class="line" /></div>
	</td>
</tr>
<tr>
	<td>�����̹���2<br>(ȿ��)</td>
	<td>
		<div class="ver8 blue">�� ���콺Ŀ���� �÷��� �� ���̴� �̹����Դϴ�.</div>
		<div class="file noline"><input type="file" name="mouseOnImg" style="width:95%" /><input type="hidden" name="mouseOnImg" /></div>
		<div class="url"><input type="text" name="url_mouseOnImg" style="width:95%" class="line" /></div>
	</td>
</tr>
<tr>
	<td>ū�̹���</td>
	<td>
		<div class="ver8 blue">�� �����̹��� ���� �� �߾ӿ� ���̴� �̹����Դϴ�.</div>
		<div class="file noline"><input type="file" name="mainBannerImg" style="width:95%" /><input type="hidden" name="mainBannerImg" /></div>
		<div class="url"><input type="text" name="url_mainBannerImg" style="width:95%" class="line" /></div>
	</td>
</tr>
<tr>
	<td>Ŭ���� �̵��� URL</td>
	<td><input type="text" name="linkUrl" style="width:95%" class="line" />
	</td>
</tr>
<tr>
	<td>Ŭ���� �̵��� â</td>
	<td>
		<input type="radio" name="linkTarget" value="blank" checked="checked" />���ο�â
		<input type="radio" name="linkTarget" value="self" />��â
	</td>
</tr>
</table>

<div style="padding-top:20px" align="center" class="noline">
	<input type="image" src="../img/btn_save.gif" id="btn_submit" alt="����" />
	<input type="image" src="../img/btn_cancel.gif" id="btn_cancel" alt="����" />
</div>
</form>
<script>
function fileUpload()
{
    var target_name = 'iframe_upload';

    // iframe ����
    var iframe = $('<iframe src="../../blank.txt" name="'+target_name+'" style="display:none"></iframe>');
    $('body').append(iframe);

    // iframe ���뿡 ���� ó��
    iframe.load(function(){
        var doc		= this.contentWindow ? this.contentWindow.document : (this.contentDocument ? this.contentDocument : this.document);
        var root	= doc.documentElement ? doc.documentElement : doc.body;
        var result	= root.textContent ? root.textContent : root.innerText;

		iframe.remove();

		if(result == 'fileError'){
			alert('�̹��� ������ �߸��Ǿ��ų�, �ۼ��� ������ �߸��Ǿ����ϴ�.');
			return;
		} else {
			uploadSuccess(result);
		}
    });

    // �̹��� ���ε�
	//document.frm.submit();
}

/**
 * �̹��� ����� �θ�â�� ���� ����
 */
function uploadSuccess(rst)
{
	var attach_method	= $('input[name=image_attach_method]:checked').val();			// �̹��� ��Ϲ��
	var mouseOnImg		= '';															// ���� �̹���1
	var mouseOutImg		= '';															// ���� �̹���2
	var mainBannerImg	= '';															// ū �̹���
	var linkTarget		= $('input[name=linkTarget]:checked').val();					// ��ũ Ÿ��
	var linkUrl			= $('input[name=linkUrl]').val();								// ��ũ URL
	var indexKey		= $('input[name=indexKey]').val();								// ��Ƽ �˾��� �ش� ��ġ �� (�ε���)
	var contentTable	= $(parent.parent.ifrmCodi.document).find("#contentTable");

	// �̹���ȣ���� URL �Է��� ���
	if(attach_method == 'url'){
		mouseOnImg		= $('input[name=url_mouseOnImg]').val();
		mouseOutImg		= $('input[name=url_mouseOutImg]').val();
		mainBannerImg	= $('input[name=url_mainBannerImg]').val();
	}
	// ���� ���ε��� ���
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
 * �̹��� ��� ���
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
 * �θ�â�� �ִ� ���� ���̾� �˾�â�� ǥ��
 */
function setLoad()
{
	// ���� �ε���
	indexKey		= $('input[name=indexKey]').val();

	//�θ�â ���̺�
	contentTable	= $(parent.parent.ifrmCodi.document).find("#contentTable");

	//�θ�â ����
	par_image_attach_method		= contentTable.find("#image_attach_method"+indexKey).val();
	par_mouseOnImg				= contentTable.find("#mouseOnImg"+indexKey).val();
	par_mouseOutImg				= contentTable.find("#mouseOutImg"+indexKey).val();
	par_mainBannerImg			= contentTable.find("#mainBannerImg"+indexKey).val();
	par_linkUrl					= contentTable.find("#linkUrl"+indexKey).val();
	par_linkTarget				= contentTable.find("#linkTarget"+indexKey).val();

	//���̾�â ����
	self_image_attach_method	= $('input[name=image_attach_method]');
	self_mouseOnImg				= $('input:hidden[name=mouseOnImg]');
	self_mouseOutImg			= $('input:hidden[name=mouseOutImg]');
	self_mainBannerImg			= $('input:hidden[name=mainBannerImg]');
	self_linkUrl				= $('input[name=linkUrl]');
	self_linkTarget				= $('input[name=linkTarget]');

	// ���̾�â�� ������ ����
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
    // ��� ��ư Ŭ����
    $('#btn_submit').click(function(){
		if($('input[name=image_attach_method]:checked').val() == 'url'){
			uploadSuccess();
			return;
		}
        fileUpload();
    });

	// �̹��� ��� ��� ���ý� "�̹��� ��� ���" ����
	$('input[name=image_attach_method]').click(function(){
		fnSetImageAttachForm();
    });

    // ��� ��ư Ŭ����
    $('#btn_cancel').click(function(){
        parent.closeLayer();
    });

	// �θ�â�� �ִ� ���� ���̾� �˾�â�� ǥ��
	setLoad();

	// "�̹��� ��� ���" ����
	fnSetImageAttachForm();
});
</script>
</body>
</html>