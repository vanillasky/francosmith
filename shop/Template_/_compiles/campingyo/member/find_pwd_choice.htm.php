<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/member/find_pwd_choice.htm 000008418 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<script language="JavaScript">
var nsGodo_PasswordFinder = function() {
	return {
		raiseError : function(code) {
			switch (code) {
				case '0001':
					alert('����� ������ �������� �ʽ��ϴ�.');
					window.location.replace('../member/find_pwd.php');
					break;
				case '0002':
					alert('�߸��� �����Դϴ�. �ٽ� �õ��� �ּ���.');
					window.location.replace('../member/find_pwd.php');
					break;
				case '0003':
					alert('��ȿ�Ⱓ�� ����Ǿ����ϴ�. �ٽ� �õ��� �ּ���.');
					window.location.replace('../member/find_pwd.php');
					break;
				case '0004':
					alert('������ �Ұ����� �̸��� �ּ� �Դϴ�.');
					break;
				case '0005':
					alert('������ �Ұ����� �޴��� ��ȣ �Դϴ�.');
					break;
				case '0006':
					alert('������ȣ�� ��Ȯ�� �Է��� �ּ���.');
					break;
				case '0007':
					alert('��й�ȣ ������ ���������� �Ϸ���� �ʾҽ��ϴ�.\n\n�ٽ� �õ��� �ּ���.');
					break;
				case '0008':
					alert('�����ȣã�� �޴�����ȣ ���� �� ��߱� ���� ����� ��ְ� �߻��Ͽ����ϴ�.\n\n�ٸ� ���񽺷� �����ȣã�⸦ �����Ͽ� �ּ���.');
					break;
				case '0009':
					alert('����� �Ұ����� ��й�ȣ �Դϴ�.');
					break;
				default:
					alert('��Ÿ ����');
					break;

			}

			return false;
		},

		changePwd : function(pwd,m_id,token) {

			gd_ajax({
				url : '../member/indb.find_pwd.php',
				type : 'POST',
				param : '&mode=change&pwd='+encodeURIComponent(pwd)+'&m_id='+m_id+'&token='+token,
				success : function(rst) {
					if (rst == '0000') {
						alert('��й�ȣ ������ �Ϸ�Ǿ����ϴ�.');
						window.location.replace('../member/login.php');
					}
					else {
						return nsGodo_PasswordFinder.raiseError(rst);
					}

				}

			});
		},
		sendOTP : function(type,m_id,token,cb) {

			gd_ajax({
				url : '../member/indb.find_pwd.php?type='+type,
				type : 'POST',
				param : '&mode=send&m_id='+m_id+'&token='+token,
				success : function(rst) {

					if (rst == '0000') {
						cb();
					}
					else {
						return nsGodo_PasswordFinder.raiseError(rst);
					}

				}
			});
		},
		compareOTP : function(otp,m_id,token) {

			gd_ajax({
				url : '../member/indb.find_pwd.php',
				type : 'POST',
				param : '&mode=compare&otp='+otp+'&m_id='+m_id+'&token='+token,
				success : function(rst) {

					/*
					��� �ڵ�

					0000 : ��������
					0006 : ����Ű ����ġ
					*/
					if (rst == '0000') {

						// ��� ����â
						nsGodo_PasswordFinder.dialog.open('', 374, 460);

						document.certForm.action = '../member/change_pwd.php';
						document.certForm.target = _ID('certFrame').name;
						document.certForm.submit();

					}
					else {
						return nsGodo_PasswordFinder.raiseError(rst);
					}

				}
			});

		},
		dialog : {
			open : function(url, w_width, w_height) {

				this.close();

				var c_width = document.body.clientWidth;
				var c_height = document.body.clientHeight;

				var s_width = document.body.scrollLeft;
				var s_height = document.body.scrollTop;

				_ID('certPopLayer').style.width = _ID('certPopLayerBG').style.width = (c_width + s_width) + 'px';
				_ID('certPopLayer').style.height = _ID('certPopLayerBG').style.height = (c_height + s_height) + 'px';

				with(_ID('certFrameLayer').style) {
					width = w_width + 'px';
					height = w_height + 'px';
					left = ((c_width - w_width) / 2 + s_width) + 'px';
					top = ((c_height - w_height) / 2 + s_height) + 'px';
				}

				_ID('certPopLayer').style.display = "block";

				if (url) {
					_ID('certFrame').src = url;
				}

			},
			close : function() {
				_ID('certPopLayer').style.display = "none";
				_ID('certFrame').src = 'about:blank';
			}
		}


	}
}();



function closeAuthLayer() {
	nsGodo_PasswordFinder.dialog.close();
}

function openAuthLayer(type) {

	// ������ȣ �Է�â
	switch (parseInt(type)) {
		case 1:	// �̸��� ����
		case 2:	// �޴��� ����
		case 3:	// ������ �޴��� ����
		case 4:	// ������ ���� ���������� ����
			var url = '../member/find_pwd_auth.php?type='+type;
			break;
		default:
			return;
			break;
	}

	nsGodo_PasswordFinder.dialog.open('', 374, 260);

	document.certForm.action = url;
	document.certForm.target = _ID('certFrame').name;
	document.certForm.submit();
}

function selectOption(type, cb) {
	if (!cb) {
		cb = function(){
			switch (parseInt(type)) {
				case 1:	// �̸��� ����
					alert('������ȣ�� �������� �����ּҷ� ���� �Ǿ����ϴ�.');
					break;
				case 2:	// �޴��� ����
					alert('������ȣ�� �������� �ڵ��� ��ȣ�� ���� �Ǿ����ϴ�.');
					break;
			}
			openAuthLayer(type)
		};
	}
	nsGodo_PasswordFinder.sendOTP(type, '<?php echo $TPL_VAR["m_id"]?>', '<?php echo $TPL_VAR["token"]?>', cb);
}

function resend_certKey(type) {
	if(confirm("������ȣ�� ������ �Ͻðڽ��ϱ�?\n\n�� ������ȣ�� �������Ͻø�\n������ ���۵Ǿ��� ������ȣ�� ����Ͻ� �� �����ϴ�.")) {
		selectOption(type, function(){alert('������ȣ�� ������ �Ǿ����ϴ�.')});
	}
}
</script>

<style type="text/css">
	.method-wrap {display:inline-block;_display:inline;margin:0 0 5px 0;padding:0;width:325px;height:135px;z-index:999;}
	.method-wrap .method {background-position:center center;background-repeat:no-repeat;height:100%;width:100%;margin:0;position:relative;z-index:10;}
	.method-wrap .method .cont { color:#373737; font-family:dotum; font-size:12px;text-align:center; position:absolute;top:105px;left:0;width:100%;z-index:11;}

	#certPopLayer { display:none; position:absolute; left:0px; top:0px;  z-index:110; }
	#certPopLayer #certFrameLayer { position:absolute; z-index:110; }
	#certPopLayer #certFrameLayer #certFrame { width:100%; height:100%; }
	#certPopLayer #certPopLayerBG { background:#000; opacity:.70; filter:alpha(opacity=70); height:500px; left:0px; position:absolute; top:0px; width:500px; z-index:100; }
</style>

<!-- ���� ���̾� �˾� -->
<div id="certPopLayer"><div id="certFrameLayer"><iframe id="certFrame" name="certFrame" src="about:blank" scrolling="no" frameborder="0"></iframe></div><div id="certPopLayerBG"></div></div>

<!-- ����̹��� || ������ġ -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><img src="/shop/data/skin/campingyo/img/common/title_pwsearch.gif" border=0></td>
</tr>
<TR>
	<td class="path">HOME > �������� > <B>��й�ȣã��</B></td>
</TR>
</TABLE>

<style>

</style>
<div class="indiv"><!-- Start indiv -->

<form name="certForm" method="post" action="">
<input type="hidden" name="token" value="<?php echo $TPL_VAR["token"]?>" />
<input type="hidden" name="m_id" value="<?php echo $TPL_VAR["m_id"]?>" />
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr align="center" valign="middle" height="131">
	<td colspan="2"><div style="background:url(/shop/data/skin/campingyo/img/common/img_c_re_pass.gif); height:111px; width:401px;" /></div></td>
</tr>
<tr align="center" valign="middle">
	<td>

<?php if($TPL_VAR["temp_email"]){?>
		<div class="method-wrap">
			<div class="method" style="background-image:url(/shop/data/skin/campingyo/img/common/img_re_email.gif);">
				<div class="cont">
					<?php echo $TPL_VAR["temp_email"]?>

					<a href="javascript:void(0);" onClick="<?php if($TPL_VAR["temp_email"]){?>selectOption(1);<?php }else{?>alert('�̸��� �ּҰ� �ùٸ��� �ʾ�, ������ �� �����ϴ�.');<?php }?>"><img src="/shop/data/skin/campingyo/img/common/btn_get_authnum.gif" align="absmiddle" /></a>
				</div>
			</div>
		</div>
<?php }?>

<?php if($TPL_VAR["temp_mobile"]){?>
		<div class="method-wrap">
			<div class="method" style="background-image:url(/shop/data/skin/campingyo/img/common/img_re_phone.gif);">
				<div class="cont">
					<?php echo $TPL_VAR["temp_mobile"]?>

					<a href="javascript:void(0);" onClick="<?php if($TPL_VAR["temp_mobile"]){?>selectOption(2);<?php }else{?>alert('�޴��� ��ȣ�� �ùٸ��� �ʾ�, ������ �� �����ϴ�.');<?php }?>"><img src="/shop/data/skin/campingyo/img/common/btn_get_authnum.gif" align="absmiddle" /></a>
				</div>
			</div>
		</div>
<?php }?>

	</td>
</tr>
</table>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>