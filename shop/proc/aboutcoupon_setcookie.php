<?php
include "../_header.php";

if ($about_coupon->use) {
	setcookie("about_cp","1",0,'/');
}
?>
<script>

var about_coupon = parent.document.getElementById("aboutcoupon_popup");
if (about_coupon) {
	about_coupon.style.display = "none";
	parent.location.reload();
}
</script>