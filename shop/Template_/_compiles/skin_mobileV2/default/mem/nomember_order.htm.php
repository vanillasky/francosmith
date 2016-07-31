<?php /* Template_ 2.2.7 2012/10/26 22:40:10 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/mem/nomember_order.htm 000001978 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>

<?php  $TPL_VAR["page_title"] = "비회원 주문확인";?>
<?php $this->print_("sub_header",$TPL_SCP,1);?>

<section class="content" id="login">
	<form name="login_form" action="login_ok.php" method="post" onSubmit="return chkForm(this);">
	<input type="hidden" name="mode" value="guest" />
		<fieldset>
			<legend class="hidden">주문확인 폼</legend>
			<div class="login_center">
			<div class="login_b">
				<div class="login_title">비회원 주문/주문확인</div>
				<label class="input_id">
					<span class="hidden">주문자명</span>
					<input type="text" name="ord_name" value="" maxlength="12" title="주문자명" required="required" msgR="주문자명을 입력하세요." placeholder="주문자명" tabindex="1" />
				</label>
				<label class="input_pw">
					<span class="hidden">비밀번호</span>
					<input type="text" name="ordno" maxlength="34" title="주문번호" required="required" msgR="주문번호를 입력하세요." placeholder="주문번호" tabindex="2" />
				</label>
				<div class="check_btn"><button id="check-btn" type="submit" tabindex="5" >주문확인</button></div>
				<div class="member_btn"><button id="member-btn" type="submit" tabindex="5" onclick="javascript:location.replace('./login.php');return false;">회원 로그인</button></div>
				<div class="joinmember2_btn"><button id="joinmember2-btn" type="submit" tabindex="5" onclick="javascript:location.replace('./join.php'); return false;">회원가입</button></div>
			</div>			
			</div>
		</fieldset>
	</form>
</section>
<script language="javascript">var shop_key = "<?php echo $_SERVER['HTTP_HOST'];?>";</script>
<script src="/shop/data/skin_mobileV2/default/common/js/base64.js"></script>
<script src="/shop/data/skin_mobileV2/default/common/js/login.js"></script>

<?php $this->print_("footer",$TPL_SCP,1);?>