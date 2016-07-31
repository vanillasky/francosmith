<?php
class mobile_board extends board {

	### 검색
	function getIndexSearch()
	{
		$this->search['name'] = 'on';
		$this->search['subject'] = 'on';
		$this->search['contents'] = 'on';

		$this->search['word'] = str_replace("%","\%",$this->search['word']);
		$div = explode(",",$this->search['word']);
		//echo $this->search['word'];
		if ($this->search['name'])		$tmp[] = "name like '%".$this->search['word'] ."%'";
		if ($this->search['subject'])	$tmp[] = "subject like '%".$this->search['word'] ."%'";
		if ($this->search['contents'])	$tmp[] = "contents like '%".$this->search['word'] ."%'";

		$tmp2[] = "(".implode(" or ",$tmp).")";
		
		$this->where[] = implode(" ",$tmp2);

		if($this->subSpeech)$this->where[] = "category = '".$this->subSpeech."'";

		if (!$this->var_['bdSearchMode']){
			$where = "where ".implode(" and ",$this->where);
			list($this->recode['total']) = $this->db->fetch("select count(*) from `".GD_BD_.$this->id."` ".$where);
		}
		$this->search['word']	= stripslashes($this->search['word']);

		$this->binary_patch();
	}

	function getList()
	{
		$this->miniList();
 
		if (!$this->search['word'] && !$this->subSpeech) $this->getIndex();
		else $this->where[]	= "idx like 'a%'";

		if ($this->search['word'] || $this->subSpeech) $this->getIndexSearch();
		if ($this->var_['bdUseSubSpeech']) $this->setSubSpeechBox();

		$this->exec();
		# 공지글 설정

		if($this->page['now']==1) {
			$this->getLoop('notice');
		}

		return $this->getLoop();
	}


	### Query 결과 Listing
	function getLoop($listType = "")
	{
		GLOBAL $bdPrnType,$bdUserDsp,$bdAdminDsp,$bdIpAsterisk,$bdTitleCChk,$bdTitleSChk,$bdTitleBChk,$bdListImgSizeW,$bdListImgSizeH,$bdAllowPluginTag,$bdAllowPluginDomain,$bdUseXss,$cfgMobileShop;

		# 공지글 인경우
		if($listType == "notice"){
			$this->query = "select * from `".GD_BD_.$this->id."` where notice = 'o' order by idx,main,hex(sub) ";
		# 일반글 인경우
		}else{
			$num = $this->recode['total'] - ($this->page['now']-1) * $this->page['num'] + 1;
			if (!$this->recode['total']) $num = ($this->page['now']-1)*$this->page['num'];

			$this->setQuery();
		}
		$res = $this->db->query($this->query);

		while ($data = $this->db->fetch($res)){

			if(class_exists('validation') && method_exists('validation','xssCleanArray')){
				$data = validation::xssCleanArray($data , 
					array(
						validation::DEFAULT_KEY=>'text',
						'contents' => array($bdUseXss,'ent_noquotes', $bdAllowPluginTag , $bdAllowPluginDomain),
						'subject'=>array($bdUseXss,'ent_noquotes', $bdAllowPluginTag , $bdAllowPluginDomain),
						'category'=>array($bdUseXss,'ent_noquotes', $bdAllowPluginTag , $bdAllowPluginDomain),
					));
			}

			if (!$this->recode['total']){
				$num++;
			}
			else{
				$num--;
			}

			$data['num'] = $num;
			$data['viewUrl'] = '';
			$data['viewUrl'] = "view.php?".$this->vars['no']."&no=".$data[no];

			$data['gapReply'] = '';
			if ($data['sub']) $data['gapReply'] = "<div style='width:".((strlen($data['sub'])-1)*15)."px;float:left;height:1px'></div>";
			# 이미지 설정
			$div = explode("|",$data['new_file']);

			if (@is_file("../..".$this->cfg['rootDir']."/data/board/".$this->id."/t/".$div[0])){
				$reSize	= ImgSizeSet("../data/board/".$this->id."/t/".$div[0],$bdListImgSizeW,$bdListImgSizeH);
				$data['imgnm']	= $div[0];
				$data['imgSizeW']	= $reSize[0];
				$data['imgSizeH']	= $reSize[1];
				$data['imgUrl'] = $this->cfg['rootDir']."/data/board/".$this->id."/t/".$div[0];
			}else{
				$data['imgSizeW']	= $bdListImgSizeW;
				$data['imgSizeH']	= $bdListImgSizeH;
				$data['imgUrl'] = $this->cfg['rootDir']."/data/skin_mobileV2/".$cfgMobileShop['tplSkinMobile']."/common/img/new/mobile_noimg.png"; //'../../common/img/new/mobile_noimg.png';
			}

			$data['new'] = '';
			$data['hot'] = '';
 
			//$data['subject'] = strcut($data['subject'],30);	//모바일은 리스트 제목길이 고정
			if ($this->var_['bdNew'] && (time()-strtotime($data['regdt']))/60/60 < $this->var_['bdNew']) $data['new'] = 'y';
			if ($this->var_['bdHot'] && $data['hit']>=$this->var_['bdHot']) $data['hot'] = 'y';

			list( $level,$data['m_id'],$data['nickname'] ) = $this->db->fetch("select level,m_id,nickname from ".GD_MEMBER." where m_no!='' and m_no='".$data[m_no]."'");

			# 작성자 표시 - [아이디 표시 : 아이디가 없는경우 이름 표시]
			if ($bdUserDsp == 1){
				if($data['m_id']) $data['name']	= $data['m_id'];

			# 작성자 표시 - [닉네임 표시 : 닉네임이 없는경우 이름 표시]
			}else if ($bdUserDsp == 2){
				if($data['nickname']) $data['name']	= $data['nickname'];
			}

			# 관리자 표시 - [이미지로 표시]
			if ($level == '100' && $this->adminicon){
				if (!$bdAdminDsp) {
					$data['name']	= "<img src=\"../../".$this->cfg['rootDir']."data/skin/".$this->cfg['tplSkin']."/".$this->adminicon."\" border=\"0\" />";
					$data['m_id']	= "<img src=\"../../".$this->cfg['rootdir']."data/skin/".$this->cfg['tplSkin']."/".$this->adminicon."\" border=\"0\" />";

					if(preg_match('/^'.str_replace('/', '', $this->cfg['mobileShopRootDir']).'/', $_SERVER['PHP_SELF'])){
						$data['name']	= "<img src=\"../..".$this->cfg['rootDir']."/data/skin/".$this->cfg['tplSkin']."/".$this->adminicon."\" border=\"0\" />";
						$data['m_id']	= "<img src=\"../..".$this->cfg['rootDir']."/data/skin/".$this->cfg['tplSkin']."/".$this->adminicon."\" border=\"0\" />";
					}
				}
			}

			# 리스트에서 IP 출력
			if ($bdIpAsterisk && $this->ici_admin === false){
				preg_match($pattern='/\.[0-9]*$/', $data['ip'], $matches);
				if (strlen($matches[0]) > 1) $data['ip'] = preg_replace($pattern, '.'.str_repeat("*", strlen($matches[0]) - 1), $data['ip']);
			}

			$this->loop[]	= $data;
		}

		return $this->loop;
	}

	### Query 실행
	function setQuery()
	{
		$recode['end'] = ($this->recode['total'] && $this->recode['total']<$this->page['now']*$this->page['num']) ? $this->recode['total']-($this->page['now']-1)*$this->page['num'] : $this->page['num'];
		if ($this->where) $where = "where ".implode(" and ",$this->where);
		$this->query = "select name, idx,main,hex(sub) as sub,subject,no,regdt,category,secret,notice,_member,m_no,new_file,old_file,password,comment,hit from `".GD_BD_.$this->id."` ".$where." and notice !='o' ".$this->orderby." limit ".$this->recode[start].",".$recode[end];
	}

	function _view()
	{
		GLOBAL $bdAdminDsp,$bdIpAsterisk,$bdUserDsp,$bdUseXss,$bdAllowPluginTag,$bdAllowPluginDomain;
		$this->vars['no']  = (!$_GET['search']['word']) ? getVars('no,sel,password,mode,search') : getVars('no,sel,mode,password');

		$query	= "select * from `".GD_BD_.$this->id."` where no='".$this->no."'";
		$this->data	= $this->db->fetch($query,1);
		
		if(class_exists('validation') && method_exists('validation','xssCleanArray')){
			$this->data = validation::xssCleanArray($this->data , 
				array(
					validation::DEFAULT_KEY=>'text',
					'contents'=>array($bdUseXss,'ent_noquotes', $bdAllowPluginTag , $bdAllowPluginDomain ),
					'subject'=>array($bdUseXss,'ent_noquotes', $bdUseXss , $bdAllowPluginDomain),
					'category'=>array($bdUseXss,'ent_noquotes', $bdAllowPluginTag , $bdAllowPluginDomain),
					'link'=>'disable',
					'password'=>'disable',
					'old_file'=>'disable',
					'new_file'=>'disable',
					'urlLink'=>'disable',
					'link'=>'disable',
					'titleStyle'=>'disable',
				));
			$_POST['titleStyle'] = validation::xssCleanArray($_POST['titleStyle'] , array(
				validation::DEFAULT_KEY=>'html',
			));
		}

		# 제목 스타일
		if ($this->data['secret']) $this->chkSecret();
		
		if ($this->relation) $this->relation();
		$this->data = array_merge($this->data,array(
					contents	=> ($this->setContents($this->data['contents'],$this->data['html'],1)),
					loopComment => $this->getComment(),
					'link'		=> array(
								'reply'=>"write.php?".$this->vars[no]."&no=".$this->no."&mode=reply",
								),
					));
		if ($this->privLink($this->data['m_no'])){
			$this->data['link']['modify']	= "write.php?".$this->vars[no]."&no=".$this->no."&mode=modify";
			$this->data['link']['delete']	= "delete.php?".$this->vars[no]."&no=".$this->no;
		}

		if ($this->data['old_file']) $this->data['uploadedFile'] = $this->getFileList();

		$this->setLink();
		if (!strpos(",,".$_COOKIE['mini_idno'],",".$this->id."_".$this->no.",")) $this->addHit();

		list( $level,$this->data['m_id'] ) = $this->db->fetch("select level,m_id from ".GD_MEMBER." where m_no!='' and m_no='".$this->data[m_no]."'");

		list( $this->data['nickname'] ) = $this->db->fetch("select nickname from ".GD_MEMBER." where m_no!='' and m_no='".$this->data[m_no]."'");

		# 작성자 표시 - [아이디 표시 : 아이디가 없는경우 이름 표시]
		if ($bdUserDsp == 1){
			if($this->data['m_id']) $this->data['name']	= $this->data['m_id'];

		# 작성자 표시 - [닉네임 표시 : 닉네임이 없는경우 이름 표시]
		}else if ($bdUserDsp == 2){
			if($this->data['nickname']) $this->data['name']	= $this->data['nickname'];
		}

		if ( $level == '100' && $this->adminicon && !$bdAdminDsp ){
			$this->data['name'] = "<img src=\"../..".$this->cfg['rootDir']."/data/skin/".$this->cfg['tplSkin']."/".$this->adminicon."\" border=\"0\" />";
			$this->data['m_id'] = "<img src=\"../..".$this->cfg['rootDir']."/data/skin/".$this->cfg['tplSkin']."/".$this->adminicon."\" border=\"0\" />";
		}

		if ($bdIpAsterisk && $this->ici_admin === false){
			preg_match($pattern='/\.[0-9]*$/', $this->data['ip'], $matches);
			if (strlen($matches[0]) > 1) $this->data['ip'] = preg_replace($pattern, '.'.str_repeat("*", strlen($matches[0]) - 1), $this->data['ip']);
		}
	}

	function getFileList()
	{
		$this->oDiv = explode("|",$this->data['old_file']);
		$this->nDiv = explode("|",$this->data['new_file']);

		for ($i=0;$i<count($this->oDiv);$i++){
			if($this->nDiv[$i] != '' && file_exists("../..".$this->cfg['rootDir']."/data/board/".$this->id."/".$this->nDiv[$i])){
				$file_size = filesize("../..".$this->cfg['rootDir']."/data/board/".$this->id."/".$this->nDiv[$i]);
				if ($file_size>=1048576) $file_size = ((int)($file_size/10485.76)/100)."MB";
				else if ($file_size>=1024) $file_size = ((int)($file_size/102.4)/10)."KB";
				else $file_size =  $file_size."Bytes";
				$uploadedFile[] = "<u><a href=\"download.php?id=".$this->id."&no=".$this->no."&div=".$i."\">".$this->oDiv[$i]."</a></u> (".$file_size.")";
			}
		}

		if($uploadedFile) return implode("<br/>",$uploadedFile);
	}

	function chkSecret()
	{
		if ($this->ici_admin) $priv_secret = 1;
		if ($this->data['m_no'] && $this->sess['m_no']){
			if ($this->sess['m_no']==$this->data['m_no'] || $this->sess['m_no']==$this->data['_member']) $priv_secret = 1;
		} else if ($_POST['password']){
			$query = "select * from `".GD_BD_.$this->id."` where no='".$this->no."' and (password='".md5($_POST['password'])."' or _pass='".md5($_POST['password'])."')";
			list ($chk) = $this->db->fetch($query);
			if ($chk) $priv_secret = 1;
		}
		if (!$priv_secret){
			if ($this->data['_pass']) $this->data['m_no'] = 0;
			$this->tpl->define('secret', "board/".$this->bdSkin."/secret.htm");
			$returnUrl = ($_POST['returnUrl']) ? $_POST['returnUrl'] : $_SERVER['HTTP_REFERER'];
			$this->tpl->assign(returnUrl,$returnUrl);
			$this->tpl->assign($this->data);
			$this->tpl->print_('secret');
			exit;
		}
	}

	function getComment()
	{
		GLOBAL $bdAdminDsp,$bdUserDsp;
		$res = $this->db->query("select * from ".GD_BOARD_MEMO." where id='".$this->id."' and no='".$this->no."' order by sno limit 500");
		while ($data = $this->db->fetch($res)){
			$data['comment'] = $this->setContents($data['memo'],2);
			if ($this->privLink($data['m_no'])) $data['link']['delete'] = "<a href=\"delete.php?".$this->vars['no']."&sno=".$data['sno']."&mode=comment\">";

			list( $level,$data['m_id'],$data['nickname'] ) = $this->db->fetch("select level,m_id,nickname from ".GD_MEMBER." where m_no!='' and m_no='".$data['m_no']."'");

			# 작성자 표시 - [아이디 표시 : 아이디가 없는경우 이름 표시]
			if ($bdUserDsp == 1){
				if($data['m_id']) $data['name']	= $data['m_id'];

			# 작성자 표시 - [닉네임 표시 : 닉네임이 없는경우 이름 표시]
			}else if ($bdUserDsp == 2){
				if($data['nickname']) $data['name']	= $data['nickname'];
			}

			if ( $level == '100' && $this->adminicon && !$bdAdminDsp ){
				$data['name'] = "<img src=\"../..".$this->cfg['rootDir']."/data/skin/".$this->cfg['tplSkin']."/".$this->adminicon."\" border=\"0\" />";
				$data['m_id'] = "<img src=\"../..".$this->cfg['rootDir']."/data/skin/".$this->cfg['tplSkin']."/".$this->adminicon."\" border=\"0\" />";
			}

			$arr[] = $data;
		}
		return $arr;
	}

	function setContents($contents,$html,$code=0)
	{
		if ($html%2==0){
			$contents	= htmlspecialchars($contents);
			$contents	= str_replace("  ","&nbsp; ",$contents);
			$contents	= str_replace("\t","&nbsp; &nbsp; ",$contents);
			if ($this->search['word'] && $this->search['contents']) $contents	= str_replace($this->search['word'],"<span style=\"background-color:yellow\">".$this->search[word]."</span>",$contents);

			$contents	= preg_replace("/(http|https|ftp|telnet|news|mms):\/\/([a-zA-Z0-9.-]+\.[a-zA-Z0-9.:&#=_?\/~+%@;-]+)/i","<a href=\"\\1://\\2\" target=\"_blink\">\\1://\\2</a>",$contents);
		} else {
			$contents	= preg_replace("/<(\/*)(style)/i","&lt\\1\\2",$contents);
		}
		
		// 이미지에 쇼핑몰의 상품상세/분류페이지 링크가 걸려있을 경우 유저몰로 링크되어 있는 url을 모바일샵으로 변경
		$urlMatchPattern = '@href=([\'"])(.*?)(?<!\x5c)\1@i';
		preg_match_all($urlMatchPattern,$contents,$urlMatch);
		if (is_array($urlMatch[2]) && count($urlMatch[2]>0)){
			$changelinkArray = array(
				"/shop/goods/goods_view.php"=>"/m/goods/view.php",
				"/shop/goods/goods_list.php"=>"/m/goods/list.php"
			);
			foreach($urlMatch[2] as $v => $val){
				$changeUrl = "";
				foreach($changelinkArray as $t => $tval){
					if (strstr($val,$t)){
						$changeUrl = str_replace($t,$tval,$val);
						$contents = str_replace($val,$changeUrl,$contents);
						break;
					}
				}
			}
		}

		$contents	= str_replace("&amp;#","&#",$contents);
		if ($html>1) $contents	= nl2br($contents);

		$imgContents = '';
		if ($code){
			$pattern="/\[\:이미지([0-9]+)\:\]/e";
			preg_match($pattern,$contents,$match);

			if(!$match[0]){
				$oDiv = explode("|",$this->data['old_file']);
				for($i=0; $i<count($oDiv); $i++) {
					if (!strstr($imgContents,"[:이미지".($i+1).":]") && $oDiv[$i] && preg_match("/(gif|jpg|bmp|png|png)$/is",$oDiv[$i])){
						$imgContents.= "<div><img src=\"download.php?id=".$this->id."&no=".$this->data['no']."&div=".$i."&mode=1\" border=\"0\" /></div><br>";
						break;
					}
				}
			}else{
				$contents = preg_replace("/\[\:이미지([0-9]+)\:\]/e","'<img src=\"download.php?id=".$this->id."&no=".$this->data['no']."&mode=1&div='.(\\1-1).'\">'",$contents);
			}
		}
		$contents = $imgContents.$contents;

		return $contents;
	}
}
?>