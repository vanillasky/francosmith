<?
### 옵션이미지 업로드
function upload_optimg(){
	global $db,$goodsno,$file,$data,$tmp_opt1img,$tmp_opt1icon,$tmp_opt2icon;
	$query = "
	SELECT
		opt1,opt1img,opt1icon,opt2icon,opt2
	FROM ".GD_GOODS_OPTION."
	WHERE
		goodsno = '$goodsno'
	AND (
		(optno AND optno IN (".implode(',',(array)array_notnull($_POST['option']['optno']))."))
		OR
		(optno = '')
	)
	and go_is_deleted <> '1'
	ORDER BY sno ASC
	";
	$res = $db->query($query);
	while($tmp = $db->fetch($res,1)){
		if(!$fopt1) $fopt1 = $tmp[opt1];
		if($opt1 != $tmp[opt1]){
			$tmp_opt1icon[] = $tmp[opt1icon];
			$tmp_opt1img[] = $tmp[opt1img];
			$opt1 = $tmp[opt1];
		}

		if($fopt1 == $tmp[opt1]){
			$tmp_opt2icon[] = $tmp[opt2icon];
			$opt2 = $tmp[opt2];
		}
	}

	$data[opt1img] = @implode('|',$tmp_opt1img);
	multiUpload("opt1img");

	if($_POST[opt1kind] == "img"){
		$data[opticon_a] = @implode('|',$tmp_opt1icon);
		multiUpload("opticon_a");
	}
	if($_POST[opt2kind] == "img"){
		$data[opticon_b] = @implode('|',$tmp_opt2icon);
		multiUpload("opticon_b");
	}

	return array($data['opt1img'],$data['opticon_a'],$data['opticon_b']);
}

function deloptimg(){
	global $db,$tmp_opt1img,$tmp_opt1icon,$tmp_opt2icon,$goodsno,$file;
	### 옵션아이콘 및 옵션상품이미지 삭제(폼이없어졌을경우)
	list($o_opt1kind,$o_opt2kind) = $db->fetch("select opt1kind,opt2kind from ".GD_GOODS." where goodsno='$goodsno' limit 1");
	if($tmp_opt1icon)foreach($tmp_opt1icon as $k => $v){
		$delimg = false;
		if( !isset($_FILES['opticon_a']['name'][$k]) ) $delimg = true;
		if($o_opt1kind == 'img' && $_POST['opt1kind'] == "color") $delimg = true;
		if($delimg ){
			unset($file[opticon_a][name][$k]);
			@unlink("../../data/goods/".$v);
		}
	}
	if($tmp_opt1img)foreach($tmp_opt1img as $k => $v){
		$delimg = false;
		if( !isset($_FILES['opt1img']['name'][$k]) ) $delimg = true;
		if($o_opt1kind == 'img' && $_POST['opt1kind'] == "color") $delimg = true;
		if($delimg ){
			unset($file[opt1img][name][$k]);
			@unlink("../../data/goods/".$v);
			@unlink("../../data/goods/t/".$v);
		}
	}
	if($tmp_opt2icon)foreach($tmp_opt2icon as $k => $v){
		$delimg = false;
		if( !isset($_FILES['opticon_b']['name'][$k]) ) $delimg = true;
		if($o_opt2kind == 'img' && $_POST['opt2kind'] == "color") $delimg = true;
		if($delimg ){
			unset($file['opticon_b']['name'][$k]);
			@unlink("../../data/goods/".$v);
		}
	}
}
?>