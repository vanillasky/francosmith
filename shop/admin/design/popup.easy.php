<?

include "../_header.popup.php";
@include_once dirname(__FILE__) . "/webftp/webftp.class.php";
$webftp = new webftp;


### ��Ų �� �̹������� ����
$easySkin = "../../skin/easy";
$easyImg = $easySkin . $_GET[imgpath];


### ��� üũ
$errMsg = array();
if ( !file_exists( $easySkin ) ) $errMsg[] = 'easy ��Ų�� �������� �ʽ��ϴ�.';
if ( !file_exists( $easyImg ) || empty( $_GET[imgpath] ) ) $errMsg[] = basename( $_GET[imgpath] ) . ' ������ ��Ų���� �������� �ʽ��ϴ�.';


### ��������
if ( !count( $errMsg ) ){
	$path_parts = @pathinfo( $easyImg );
	$path_parts['extension'] = strtolower( $path_parts['extension'] );

	$f_time = date( 'y-m-d H:i:s', @filemtime( $easyImg ) ); # ��¥

	## ����ũ��
	$f_size = @filesize( $easyImg );

	if ( $f_size > 1024 ) $f_size = round( $f_size / 1024, 2 ) . ' Kb';	# KB
	else $f_size = $f_size . ' Byte';	# B

	## �׸�ũ��
	$p_size = $p_view = '';

	if ( $webftp->chkSheet( $easyImg, $webftp->img_ext_str ) == true ){
		$tmp = @getimagesize( $easyImg );
		$p_size = $tmp[0] . '�ȼ�'.' �� ' . $tmp[1] . '�ȼ�';
		$p_view = $webftp->ConfirmImage( $easyImg, $WSize="300", $HSize="200", $BorderSize=0, $IDName="", $vspace="0", $hspace="0" );
	}

	## ����
	$f_kind = $webftp->ext_name[ $f_type ];
	if ( $webftp->chkSheet( $easyImg, $webftp->app_ext_str ) == true ) $f_kind = $webftp->ext_name[ $path_parts['extension'] ];
}
?>

<div style="margin-bottom:10px;">


<div class="title title_top">�̹�����ü<span>�����Ͻ� �̹����� ��ü�մϴ�</span></div>

<? if ( count( $errMsg ) ){ ?>
	<div id=warning style="color:#FF0000; margin-bottom:10px;"><li><?=implode( "</li><li>", $errMsg )?></li></div><!-- ���޽��� -->
<? } ?>

<table cellpadding=0 cellspacing=1 border=0 bgcolor=EBEBEB>
<tr><td bgcolor=E8E8E8>
<table cellpadding=3 cellspacing=1 border=0 bgcolor=E8E8E8>
<col width=160><col width=400>
<tr>
  <td bgcolor=F6F6F6 align=center>���ϸ� �� ���</td>
  <td bgcolor=white><?=$_GET[imgpath]?></td>
</tr>
<tr>
  <td bgcolor=F6F6F6 align=center>��������</td>
  <td bgcolor=white>
    Ÿ�� : <?=$f_kind;?><br>
    �̹��������� : <?=$p_size;?><br>
    �뷮 : <?=$f_size;?><br>
    ������ : <?=$f_time;?>
  </td>
</tr>
<? if ( $p_view ){?>
<tr>
  <td bgcolor=F6F6F6 align=center>�̹�������</td>
  <td bgcolor=white><?=$p_view?></td>
</tr>
<? } ?>
</table>
</td></tr></table>

<div style="padding-top:20"></div>

<form name=frmMember method=post action="./popup.easy_indb.php" enctype="multipart/form-data" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="modify">
<input type=hidden name=imgpath value="<?=$_GET[imgpath]?>">

<table cellpadding=0 cellspacing=0 border=0 bgcolor=EBEBEB>
<tr><td bgcolor=E8E8E8>
<table cellpadding=2 cellspacing=1 border=0 bgcolor=E8E8E8>
<tr>
  <td bgcolor=F6F6F6 width=160 align=center><b>�̹�����ü</b></td>
  <td bgcolor=white width=400><input type=file name=userfile required label='�̹���'</td>
</tr>
</table>
</td></tr></table>

<div style="margin-bottom:10px;padding-top:10;" class=noline align=center>
<input type="image" src="../img/btn_confirm_s.gif">
</div>
</form>

<script>table_design_load();</script>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_tip>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� ��ü�� <font color=EA0095>�̹����� ���ϸ�</font>�� �ݵ�� <font color=EA0095>'���� �Ǵ� ����'</font>�� �Ǿ�� �մϴ�. (�ѱ����ϸ� �ȵ�)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ѹ� ����� �̹����� <font color=EA0095>�����̹����� �������� �ʽ��ϴ�.</font></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�����̹����� ������ <a href="http://enamoofreefix.godo.co.kr" target=blank><font color=EA0095><u>e���� ����200 �������Ʈ</u></font></a>���� �ش� �̹����� �ٿ�ε� �޾� �ٽ� �����ϼ���.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>
