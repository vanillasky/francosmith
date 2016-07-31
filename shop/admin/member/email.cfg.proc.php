<?
$skinType=$_GET[skinType];
$mail_mode=$_GET[mail_mode];
$mode=$_GET[mode];
$filename=$_GET[filename];
$dir = "../../log/email/";

if($mode=='skin_select') {
	$body=file_get_contents("../../conf/email/tpl_{$mail_mode}_type_{$skinType}.php");
	$body=str_replace("\r","",$body);
	$body=str_replace("\n","",$body);
	$body=addslashes($body);

	include "../../conf/email/subject_{$mail_mode}_type_{$skinType}.php";
	$subject=str_replace("\r","",$headers[Subject]);
	$subject=str_replace("\n","",$subject);
	$subject=addslashes($subject);

	echo "<script> 
		parent.document.getElementById(\"miniEditorIframe_body\").contentWindow.document.body.innerHTML='{$body}';
		parent.document.forms[0].subject.value='{$subject}';
		parent.document.forms[0].body.value='{$body}';
		parent.document.getElementById(\"miniEditorIframe_body\").style.height='860px';
	</script>
	";
}
else if($mode=='rollbackView'){
	$files=array();
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			$cnt=0;
			while (($file = readdir($dh)) !== false) {
				if ( ($file != "." && $file != "..") || filetype($dir . $file)=='file') {
					$files[filemtime($dir.$file)]=array("name"=>$file,"date1"=>date("Y-m-d",filemtime($dir.$file)),"date2"=>date("H:i:s",filemtime($dir.$file)));
				}
			}
			closedir($dh);
		}
	}
	arsort($files);

	$tbl="<div style='border:1px solid #737373;padding:10px;width:280px;background-color:#ffffff;letter-spacing:0px'>";
	$tbl.="<div style='text-align:right;width:100%'><a href='javascript:rollbackList_remove()'>X 닫기</a></div>";
	$tbl.="<table width='100%' class='tb' borderColor='#404040' style='width: 100%; border-collapse: collapse;' border='1' cellPadding='5'>";
	$tbl.="<tr><td width=150 align=center bgcolor='#1a88df'><b><font color=#ffffff>백업일시</font></b></td><td align=center bgcolor='#1a88df'><b><font color=#ffffff>복원하기</font></b></td></tr>";
	if($files){
		foreach($files as $key=>$val) {
			$tbl.="<tr onmouseover='this.style.backgroundColor=\"#b8e6fc\"' onmouseout='this.style.backgroundColor=\"#ffffff\"'><td align=center><a href=\"javascript:sel_file('".$val[name]."')\"><span style='color:#2e2e2e;'>".$val[date1]." [".$val[date2]."]</span></a></td><td align=center><a href=\"javascript:sel_file('".$val[name]."')\"><font color='#2e2e2e'>[복원하기]</font></a></td></tr>";
		}
	}
	else{
		$tbl.="<tr><td colspan=2 align=center><font color='#2e2e2e'>백업파일이 없습니다.</font></td></tr>";
	}
	$tbl.="</table></div>";

	$tbl=addslashes($tbl);
	echo "<script>
		parent.document.getElementById('rollbackList').innerHTML='{$tbl}';
	</script>";
	unset($files);
}
else if($mode=='rollbackForm'){
	$filepath=$dir.$filename;
	if(file_exists($filepath)){
		$body=addslashes(file_get_contents($filepath));
		$body=str_replace("\r","",$body);
		$body=str_replace("\n","",$body);

		echo "<script> 
				if(confirm('현재 디자인영역에 선택하신 디자인으로 대체되어 보여지므로\\n확인 후 저장하세요.\\n \\n선택하신 파일로 복원하시겠습니까?')){			
					parent.document.getElementById(\"miniEditorIframe_body\").contentWindow.document.body.innerHTML='{$body}';
					parent.document.forms[0].body.value='{$body}';
				}
			</script>
		";
	}
	else{
		
	}
}
?>