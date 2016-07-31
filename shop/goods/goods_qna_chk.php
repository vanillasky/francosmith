<?php
include "../lib/library.php";
include "../conf/config.php";
@include "../lib/tplSkinView.php";
include "../lib/goods_qna.lib.php";

$query = "select * from ".GD_GOODS_QNA." where sno = '".$_GET['sno']."' limit 1";
$data = $db->fetch($query);
// @qnibus 2015-06 XSS 취약으로 인해 필터링 구문 추가
if(class_exists('validation') && method_exists('validation','xssCleanArray')){
	$data = validation::xssCleanArray($data, array(
		validation::DEFAULT_KEY => 'text',
		'subject' => 'html',
		'contents' => 'html',
	));
}
### 원글 체크
list($data['parent_m_no'],$data['secret'],$data['type']) = goods_qna_answer($data['sno'],$data['parent'],$data['secret']);
list($data['authmodify'],$data['authdelete'],$data['authview']) = goods_qna_chkAuth($data);
$tmp = $data['m_no'];
list($pcnt) = $db->fetch("select count(sno) from ".GD_GOODS_QNA." where sno='{$data['parent']}'");

if($data['m_no'] && !$data['parent_m_no'] && $data['type'] == 'A' && $pcnt > 0)$tmp = 0;
if($data['authview'] == 'N' && $tmp == 0){
?>
	popup_pass(<?php echo $data['parent'];?>);
<?php
}else {
	include_once "../Template_/Template_.class.php";
	$tpl = new Template_;
	$tpl->prefilter		= "adjustPath|include_file|capture_print";
	$tpl->template_dir = "../data/skin/".$cfg['tplSkin']."/goods/";
	$tpl->compile_dir = "../Template_/_compiles/".$cfg['tplSkin']."/goods/";
	$tpl->define('contents',"goods_qna_contents.htm");
	$tpl->assign($data);
	$tags = str_replace('"','\"',preg_replace('/[\n\r\t]/','',$tpl->fetch('contents')));
	$tags = str_replace('\\\"','\\"',$tags);// @qnibus 2015-06 InnerHTML에서 "로 닫는 부분 방지
?>
	document.getElementById('content_id_<?php echo $_GET['sno'];?>').innerHTML = "<?php echo $tags;?>";
	document.getElementById('content_id_<?php echo $_GET['sno'];?>').style.display = "block";
<?php
	if($_GET[mode] == 'view'){
?>
	try {
	resizeFrame();
	} catch (e) {}
<?php
	}
}
?>