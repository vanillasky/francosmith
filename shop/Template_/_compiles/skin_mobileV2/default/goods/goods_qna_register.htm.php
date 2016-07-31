<?php /* Template_ 2.2.7 2013/12/23 10:26:06 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/goods/goods_qna_register.htm 000004577 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>

<?php  $TPL_VAR["page_title"] = "상품문의";?>
<?php $this->print_("sub_header",$TPL_SCP,1);?>


<style type="text/css">
section#nreviewregister {background:#FFFFFF; padding:12px;}
section#nreviewregister table{border:none; border-top:solid 1px #dbdbdb;width:100%;}
section#nreviewregister table td{padding:8px 0px 8px 10px; vertical-align:middle; border-bottom:solid 1px #dbdbdb;}
section#nreviewregister table th{text-align:center; background:#f5f5f5; width:70px; vertical-align:middle; border-bottom:solid 1px #dbdbdb; color:#353535; font-size:12px;}
section#nreviewregister table .img{padding:5px; width:60px;}
section#nreviewregister table .img img{border:solid 1px #d9d9d9;}
section#nreviewregister table td input[type=text], input[type=password], input[type=email], input[type=number], select{width:95%;height:21px;}
section#nreviewregister table td textarea{width:95%;height:116px;}
section#nreviewregister .btn_center {margin:auto; width:198px; height:34px; margin-top:20px; margin-bottom:20px;}
section#nreviewregister .btn_center .btn_save{border:none; background:url('/shop/data/skin_mobileV2/default/common/img/layer/btn_red01_off.png') no-repeat; color:#FFFFFF; font-size:14px; width:94px; height:34px; float:left; font-family:dotum;}
section#nreviewregister .btn_center .btn_prev{border:none; background:url('/shop/data/skin_mobileV2/default/common/img/layer/btn_black01_off.png') no-repeat; color:#FFFFFF; font-size:14px; width:94px; height:34px; float:right; font-family:dotum;}
section#nreviewregister .goods-nm{color:#353535; font-weight:bold; fonst-size:14px; margin-bottom:5px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
section#nreviewregister .goods-price{color:#f03c3c; font-size:12px;}
</style>

<form method=post action="indb.php" enctype="multipart/form-data" onSubmit="return chkForm(this)">
<input type=hidden name=mode value="<?php echo $GLOBALS["mode"]?>">
<input type=hidden name=goodsno value="<?php echo $GLOBALS["goodsno"]?>">
<input type=hidden name=sno value="<?php echo $GLOBALS["sno"]?>">
<input type=hidden name=referer value="<?php echo $GLOBALS["referer"]?>">

<section id="nreviewregister"  class="content">

<table>
	<tr>
		<th class="img"><?php echo goodsimgMobile($GLOBALS["goods"]["img_s"], 50)?></th>
		<td>
			<div class="goods-nm">
				<?php echo $GLOBALS["goods"]["goodsnm"]?>

			</div>
			<div class="goods-price">
				<?php echo number_format($GLOBALS["goods"]["price"])?>원
			</div>
		</td>
	</tr>
	</table>
	<div style="height:12px;"></div>
	<table>
	<tr>
		<th>작성자</th>
		<td>
			<input type="text" name="name" required label="작성자" value="<?php echo $GLOBALS["data"]["name"]?>" />
		</td>
	</tr>
<?php if(!$GLOBALS["sess"]&&empty($GLOBALS["data"]['m_no'])){?>
	<tr>
		<th>비밀번호</th>
		<td>
			<input type=password name=password style="width:100" required label="비밀번호">
		</td>
	</tr>
<?php }?>
	<tr>
		<th>이메일</th>
		<td>
			<input type="email" name="email"  label="이메일" value="<?php echo $GLOBALS["data"]["email"]?>" />
		</td>
	</tr>
	<tr>
		<th>휴대폰</th>
		<td>
			<input type="number" name="phone"  label="휴대폰번호" value="<?php echo $GLOBALS["data"]["phone"]?>" />
		</td>
	</tr>
<?php if($GLOBALS["cfg"]["qnaSecret"]=='secret'){?>
	<tr>
		<th>비밀글</th>
		<td>
			<label><input type="checkbox" name="secret" value="1" <?php echo $GLOBALS["data"]["chksecret"]?> /> 비밀글</label>
		</td>
	</tr>
<?php }?>
	<tr>
		<th>제목</th>
		<td>
			<input type="text" name="subject" required label="제목" value="<?php echo $GLOBALS["data"]["subject"]?>" />
		</td>
	</tr>
	<tr>
		<th>내용</th>
		<td>
			<textarea name="contents" required label="내용"  ><?php echo $GLOBALS["data"]["contents"]?></textarea>
		</td>
	</tr>
<?php if($GLOBALS["cfg"]["qnaSpamBoard"]& 2){?>
	<tr>
		<th>자동등록방지</th>
		<td class=cell_L><?php echo $this->define('tpl_include_file_1',"proc/_captcha.htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?></td>
	</tr>
<?php }?>

	</table>

	<div class="m_review">
		<div class="btn_center">
			<button type="submit" id="save-btn" class="btn_save">확 인</button>
			<button type="button" id="prev-btn" class="btn_prev"  onclick="history.back();">취 소</button>
		</div>
	</div>
</section>
</form>

<?php $this->print_("footer",$TPL_SCP,1);?>