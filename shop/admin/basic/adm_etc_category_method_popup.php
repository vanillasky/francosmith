<?php
include "../_header.popup.php";

// ��ǰ�з� ������ ��ȯ ��� ��ǰ �� ��
list ($totalCount) = $db->fetch("SELECT COUNT(0) FROM ".GD_GOODS);
?>
<style>
body,div,h1,h2,h3,h4,form,fieldset,p,button{margin:0;padding:0}
a{color:#7c7c7c;text-decoration:none}
a:hover{color:#7c7c7c;text-decoration:none}
a.ir:hover{text-decoration:none}

#category_method { width:490px; text-align:center; border:5px solid #FFFFFF; background-color:#D9D9D9;}
#category_method_box { width:100%; border:1px solid #000000;}
#category_method_subtitle { padding-top:20px; width:100%; height:12px; font-size:12px; color:#000; font-weight:bold; text-align:center; line-height:12px; }
#category_method_text { padding:10px 0px 10px 0px; width:100%; height:12px; font-size:12px; color:#FF0000; text-align:center; line-height:12px; letter-spacing:-1px; }
#informationText { float:left; padding-left:20px; color:#007FC4; }
#informationPercent { float:right; padding-right:20px; text-align:right; color:#007FC4; font-weight:bold; }
#progress { clear:both; margin:15px 15px 0 15px; width:460px; height:20px; background-color:#ebebeb; text-align:center; border:1px solid #c9c9c9; }
#progressBar { height:20px; background-color:#007FC4;width:0px; }
</style>
<div id="category_method">
	<div id="category_method_box">
		<div id="category_method_subtitle">��ǰ�з� ������ ��ȯ ������..</div>
		<div id="informationText">������...</div>
		<div id="informationPercent">0%</div>
		<div id="progress">
			<div id="progressBar"></div>
		</div>
		<div id="category_method_text">�� ��ǰ���� ���� ��� �۾��ð��� ���� �ɸ� �� �����Ƿ� â�� ���� ���� ��ٷ� �ֽʽÿ�.</div>
	</div>
</div>
<iframe name="ifrmCmHidden" src="./adm_etc_category_method.indb.php?totalCount=<?php echo $totalCount;?>&mode=<?php echo $_GET['mode'];?>" style="display:none;width:90%;height:100px;"></iframe>