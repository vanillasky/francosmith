<?php /* Template_ 2.2.7 2016/04/12 09:37:09 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/member/hack.htm 000004265 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>

<!-- ����̹��� || ������ġ -->
<div class="page_title_div">
	<div class="page_title">ȸ��Ż��</div>
	<div class="page_path"><a href="/shop/">HOME</a> &gt; <a href="/shop/mypage/mypage.php?&">����������</a> &gt; <span class='bold'>ȸ��Ż��</span></div>
</div>
<div class="page_title_line"></div>

<div style="width:100%; padding-top:20px;"></div>


<div class="indiv"><!-- Start indiv -->

<form method=post action="" onsubmit="return false;" id="hack_form">
<input type="hidden" name="act" value="Y">


<div style="border:1px solid #DEDEDE;" class="hundred">
	<table width=100% cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td style="border:5px solid #F3F3F3;">
		<table width="100%" border="0" cellspacing="0" cellpadding="13">
		<tr>
			<td width=150 valign=top bgcolor="#F3F3F3" align=right><img src="/shop/data/skin/freemart/img/common/memberout_01.gif"></td>
			<td>
				<div style="text-align:left"  ><?php echo $TPL_VAR["guideSecede"]?></div>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
</div>

<div style="height:10; font-size:0"></div>

<div style="border:1px solid #DEDEDE;" class="hundred" style="text-align:left;">
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td style="border:5px solid #F3F3F3;text-align:left;">
	<table width="100%" border="0" cellspacing="0" cellpadding="13">
	<tr>
		<td width=150 valign=top bgcolor="#F3F3F3" align=right><img src="/shop/data/skin/freemart/img/common/memberout_02.gif"></td>
		<td>
		<div><img src="/shop/data/skin/freemart/img/common/icon_arrow.gif" align=absmiddle><strong>��й�ȣ�� ��� �Ǽ���?</strong> <input type="password" name="password" size="20"></div>

		<div style="clear:both;margin-top:10px;"><img src="/shop/data/skin/freemart/img/common/icon_arrow.gif" align=absmiddle><strong>������ �����ϼ̳���?</strong></div>
		<div style="float:left;padding-right:10px;" class=noline>
<?php if((is_array($TPL_R1=codeitem('hack'))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {$TPL_S1=count($TPL_R1);$TPL_I1=-1;foreach($TPL_R1 as $TPL_K1=>$TPL_V1){$TPL_I1++;?>
<?php if($TPL_I1%ceil($TPL_S1/ 2)== 0&&$TPL_I1!= 0){?>
		</div><div style="float:left;padding-right:10px;" class=noline>
<?php }?>
		<div><input type="checkbox" name="outComplain[]" value="<?php echo $TPL_K1?>">&nbsp;<?php echo $TPL_V1?></div>
<?php }}?>
		</div>

		<div style="clear:both;padding-top:10px;">
			<img src="/shop/data/skin/freemart/img/common/icon_arrow.gif" align=absmiddle><strong>�������� ���ɾ ��� ��Ź�帳�ϴ�.</strong>
		</div>
		<div><textarea name="outComplain_text" cols="70" rows="8" class="box"></textarea></div>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</div>



<div style="width:100%; text-align:center; padding-top:15px; padding-bottom:20" class=noline>
	<button class="button-big button-red" onClick="chk_hackfm();return false;">Ż���ϱ�</button>
	<button class="button-big button-dark" onClick="javascript:history.back();">��������</button>
	
</div>

</form>



<SCRIPT type="text/javascript">
<!--
/*-------------------------------------
 Ż���û üũ
-------------------------------------*/
function chk_hackfm(  ){
	var fobj = document.getElementById("hack_form");
	
	if ( fobj.password.value == '' ){
		alert('��й�ȣ�� �Է��Ͽ� �ֽʽÿ�.');
		fobj.password.focus();
		return false;
	}

	var outComp = fobj["outComplain[]"].length;
	var cont = fobj.outComplain_text;
	var ckS = 0;

	for ( i = 0; i < outComp; i++ ){

		if ( fobj["outComplain[]"][i].checked == true ) break;
		else ckS++;
	}

	if ( ckS == outComp ){

		alert('���񽺺��������� 1���̻� üũ�Ͽ� �ֽʽÿ�. \n\n �ش������ �������׿� �ݿ��Ǿ����ϴ�.');
		return false;
	}

	if ( !confirm ( 'ȸ��Ż�� �Ͻø� ȸ������ ��� ����Ÿ( ��������, ����Ʈ �� )�� ���� �Ǿ����ϴ�. \n �׷��� ȸ���� Ż���Ͻðڽ��ϱ�?' ) ){
		return false;
	}

	return true;
}
//-->
</SCRIPT>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>