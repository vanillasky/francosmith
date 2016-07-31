<?

class Board Extends Page
{
	var $var_ = array();

	function miniList()
	{
		$this->vars['no']	= (!$_GET['search']['word']) ? getVars('no,sel,password,mode,search') : getVars('no,sel,password,mode');
		$this->page['url']	= "list.php";
	}

	function assign($arg)
	{
		if (is_array($arg)) $var = array_merge($var=&$this->var_, $arg);
		else $this->var_[$arg] = func_get_arg(1);
	}

	### MySQL v5.0 이상일때 binary 정렬문제로 인한 패치..
	function binary_patch()
	{
		$this->orderby = "order by idx,main,hex(sub)";
	}

	function getIndex()
	{
		$query	= "select * from ".GD_BOARD_INF." where id='".$this->id."' order by idx";
		$res = $this->db->query($query);

		while ($data = $this->db->fetch($res)){
			$total += $data['num'];
			$idx	= $data['idx'];
			if ($this->recode['start'] < $total){
				$this->recode['start'] -= ($total - $data['num']);
				break;
			}
		}

		if ($data['num'] < $this->recode['start'] + $this->page['num']){
			if ($idx!="a999") $_idx_[] = "idx='a".(substr($idx,1,3)+1)."'";
		}

		if ($idx) $_idx_[] = "idx='$idx'";
		$this->where[] = "(".implode(" or ",$_idx_).")";

		$data = $this->db->fetch("select count(*)+1 from `".GD_BD_.$this->id."` where notice!='o'");
		$this->recode['total'] = $data[0]-1;
		$this->binary_patch();
	}

	### 검색
	function getIndexSearch()
	{
		if (!$this->search['name'] && !$this->search['subject'] && !$this->search['contents']){
			$this->search['subject'] = $this->search['contents'] = "on";
		}
		if (!$this->search['code']) $search['code'] = "or";
		$this->search['word'] = str_replace("%","\%",$this->search['word']);
		$div = explode(",",$this->search['word']);
		for ($i=0;$i<count($div);$i++){
			if ($this->search['name'])		$tmp[] = "name like '%".$div[$i]."%'";
			if ($this->search['subject'])	$tmp[] = "subject like '%".$div[$i]."%'";
			if ($this->search['contents'])	$tmp[] = "contents like '%".$div[$i]."%'";
			if ($this->search['mode'])		$tmp[] = "(".$this->search['mode']." like '%".$div[$i]."%')";
			$tmp2[] = "(".implode(" or ",$tmp).")";
		}
		$this->where[] = implode(" ".$search[code]." ",$tmp2);

		if($this->subSpeech)$this->where[] = "category = '".$this->subSpeech."'";

		if (!$this->var_['bdSearchMode']){
			$where = "where ".implode(" and ",$this->where);
			list($this->recode['total']) = $this->db->fetch("select count(*) from `".GD_BD_.$this->id."` ".$where);
		}
		$this->search['word']	= stripslashes($this->search['word']);

		if ($this->search['name'])		$checked['search']['name']		= "checked";
		if ($this->search['subject'])	$checked['search']['subject']	= "checked";
		if ($this->search['contents'])	$checked['search']['contents']	= "checked";
		$this->tpl->assign(checked,$checked);
		$this->binary_patch();
	}

	### Query 실행
	function setQuery()
	{
		$recode['end'] = ($this->recode['total'] && $this->recode['total']<$this->page['now']*$this->page['num']) ? $this->recode['total']-($this->page['now']-1)*$this->page['num'] : $this->page['num'];
		if ($this->where) $where = "where ".implode(" and ",$this->where);
		$this->query = "select * from `".GD_BD_.$this->id."` ".$where." and notice !='o' ".$this->orderby." limit ".$this->recode[start].",".$recode[end];
	}

	### Query 결과 Listing
	function getLoop($listType = "")
	{
		GLOBAL $bdPrnType,$bdUserDsp,$bdAdminDsp,$bdIpAsterisk,$bdTitleCChk,$bdTitleSChk,$bdTitleBChk,$bdListImgSizeW,$bdListImgSizeH,$bdAllowPluginTag,$bdAllowPluginDomain,$bdUseXss;

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

			if (!$this->recode['total']) $num++;
			else $num--;

			$data['num'] = $num;
			if (!$this->var_['bdLvlR'] || $this->var_['bdLvlR'] <= $this->sess['level']) $data['link']['view'] = "<a href=\"view.php?".$this->vars['no']."&no=".$data[no]."\">";
			if ($data['sub']) $data['gapReply'] = "<span style='width:".((strlen($data['sub'])-1)*15)."'></span>";

			# 이미지 설정
			$div = explode("|",$data['new_file']);
			if (@is_file("../data/board/".$this->id."/t/".$div[0])){
				$reSize		= ImgSizeSet("../data/board/".$this->id."/t/".$div[0],$bdListImgSizeW,$bdListImgSizeH);
				$data['imgnm']		= $div[0];
				$data['imgSizeW']	= $reSize[0];
				$data['imgSizeH']	= $reSize[1];
				$data['img']		= "<img src=\"../data/board/".$this->id."/t/".$div[0]."\" onClick=\"popupImg('../data/board/".$this->id."/".$div[0]."')\" style=\"cursor:pointer;\" width=\"100\" />";
			}else{
				$data['imgSizeW']	= $bdListImgSizeW;
				$data['imgSizeH']	= $bdListImgSizeH;

				//내용에 이미지가 있다면 첫 이미지 표시
				
				preg_match('/<img [^>]*src=["|\']([^"|\']+)/i', $data['contents'], $matches);
				if ($matches[1] != '') {
					$data['imgContents'] = $matches[1];
					$data['img'] = "<img src=\"".$matches[1]."\" onClick=\"popupImg('".$matches[1]."')\" style=\"cursor:pointer;\" width=\"100\" />";
				}
			}

			

			if ($this->var_['bdStrlen']) $data['subject'] = strcut($data['subject'],$this->var_['bdStrlen']);
			if ($this->var_['bdNew'] && (time()-strtotime($data['regdt']))/60/60 < $this->var_['bdNew']) $data['new'] = true;
			if ($this->var_['bdHot'] && $data['hit']>=$this->var_['bdHot']) $data['hot'] = true;

			# 제목 스타일
			$data['subject']	= $this->titleStyle($data['titleStyle'],$data['subject']);

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
					$data['name']	= "<img src=\"../data/skin/".$this->cfg['tplSkin']."/".$this->adminicon."\" border=\"0\" />";
					$data['m_id']	= "<img src=\"../data/skin/".$this->cfg['tplSkin']."/".$this->adminicon."\" border=\"0\" />";
				}
			}

			# 리스트에서 IP 출력
			if ($bdIpAsterisk && $this->ici_admin === false){
				preg_match($pattern='/\.[0-9]*$/', $data['ip'], $matches);
				if (strlen($matches[0]) > 1) $data['ip'] = preg_replace($pattern, '.'.str_repeat("*", strlen($matches[0]) - 1), $data['ip']);
			}

			# 상세내역형식 리스트일 경우 (webzine 스킨)
			if($bdPrnType == 2){
				$this->no	= $data['no'];
				$this->data	= $data;

				$data['loopComment']	= $this->getComment();
				$data['uploadedFile']	= $this->getFileList();
				$data['contents']		= $this->setContents($data['contents'],$data['html'],1);

				if ($this->privLink($this->data['no'])){
					$data['link']['modify']	= "<a href=\"".url("board/write.php?".$this->vars['no']."&no=".$this->no."&mode=modify")."\" />";
					$data['link']['delete']	= "<a href=\"delete.php?".$this->vars['no']."&no=".$this->no."\" />";
				}
				if($_GET['no'] != $data['no'])	$this->loop[]	= $data;

			}else{
				$this->loop[]	= $data;
			}
		}

		$this->tpl->assign('list',$this->loop);
	}

	### 게시판 출력
	function assignToTpl()
	{
		$this->setLink();
		$this->tpl->assign(array(
			id			=> $this->id,
			search		=> $this->search,
			page		=> $this->page,
			recode		=> $this->recode,
			speechBox	=> $this->speechBox,
			));
	}

	function setLink()
	{
		$link	= array(
				'list'		=> "<a href=\"list.php?".$this->vars['no']."\">",
				'viewSel'	=> "<a href=\"javascript:frmSubmit('view.php')\" onclick=\"return isChked(document.getElementsByName('sel[]'),0);\">",
				'chk'		=> "<a href=\"javascript:chkBox(document.getElementsByName('sel[]'),'rev');\">",
				'end'		=> "</a>",
		);
		$link['write'] = "<a href=\"".url("board/write.php?".$this->vars['no'])."\">";
		if ($this->var_['bdSearchMode'] && $this->search['word']){
			if ($this->page['now']>1) $link['prev'] = "<a href=\"list.php?".$this->vars['page']."&page=".($this->page['now']-1)."\">";
			if (count($this->loop) && !(count($this->loop)%$this->page['num'])) $link['next'] = "<a href=\"list.php?".$this->vars['page']."&page=".($this->page[now]+1)."\">";
		}
		if ($this->ici_admin) $link['delete'] = "<a href=\"javascript:frmSubmit('delete_ok.php')\" onclick=\"return isChked(document.getElementsByName('sel[]'),'정말로 삭제하시겠습니까?')\">";
		$this->tpl->assign('link',$link);
	}

	### 말머리 박스
	function setSubSpeechBox()
	{
		$subSpeech	= explode("|",$this->var_['bdSubSpeech']);
		foreach ($subSpeech AS $sKey => $sVal){
			$strLenCnt	= strLen($sVal);
			if($strLenCnt >= $strLenChk){
				$strLenChk	= $strLenCnt;
			}
			$chk = ($this->subSpeech == $sVal) ? "selected" : "";
			$speechBox .= "<option value=\"".$sVal."\" ".$chk.">".$sVal."</option>";
		}
		if($this->var_['bdSubSpeechTitle']){
			$selectTitleStr	= $this->var_['bdSubSpeechTitle'];
		}else{
			$selectTitleStr	= str_repeat("-",$strLenChk);
		}
		$onChange	= " onChange=this.form.submit(); ";
		$this->speechBox = "<select name=\"subSpeech\" ".$onChange."><option value=\"\">".$selectTitleStr."</option>".$speechBox."</select>";
	}

	function _list()
	{
		GLOBAL $bdNoticeList;

		$this->miniList();

		# 공지글 설정
		if($bdNoticeList !=""){
			# 전체페이지에 나오는 경우
			$this->where[]	= "notice = '' ";
		}

		if (!$this->search['word'] && !$this->subSpeech) $this->getIndex();
		else $this->where[]	= "idx like 'a%'";

		if ($this->search['word'] || $this->subSpeech) $this->getIndexSearch();
		if ($this->var_['bdUseSubSpeech']) $this->setSubSpeechBox();

		$this->exec();
		# 공지글 설정
		if($bdNoticeList !=""){
			# 전체페이지에 나오는 경우
			$this->getLoop('notice');
		}
		else{
			if($this->page['now']==1) {
				$this->getLoop('notice');
			}
		}
		$this->getLoop();
		$this->assignToTpl();
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
				$data['name'] = "<img src=\"../data/skin/".$this->cfg['tplSkin']."/".$this->adminicon."\" border=\"0\" />";
				$data['m_id'] = "<img src=\"../data/skin/".$this->cfg['tplSkin']."/".$this->adminicon."\" border=\"0\" />";
			}

			$arr[] = $data;
		}
		return $arr;
	}

	function getFileList()
	{
		$this->oDiv = explode("|",$this->data['old_file']);
		$this->nDiv = explode("|",$this->data['new_file']);

		for ($i=0;$i<count($this->oDiv);$i++){
			if($this->nDiv[$i] != '' && file_exists("../data/board/".$this->id."/".$this->nDiv[$i])){
				$file_size = filesize("../data/board/".$this->id."/".$this->nDiv[$i]);
				if ($file_size>=1048576) $file_size = "<b>".((int)($file_size/10485.76)/100)."</b>MB";
				else if ($file_size>=1024) $file_size = "<b>".((int)($file_size/102.4)/10)."</b>KB";
				else $file_size = "<b>".$file_size."</b>Bytes";
				$uploadedFile[] = "<a href=\"download.php?id=".$this->id."&no=".$this->no."&div=".$i."\">".$this->oDiv[$i]."</a> (".$file_size.")";
			}
		}
		if($uploadedFile) return implode(" <font color=\"cccccc\">|</font> ",$uploadedFile);
	}

	function privLink($m_no)
	{
		if ($this->ici_admin) return true;
		if ($m_no){
			if ($m_no == $this->sess['m_no']) return true;
		} else {
			if (!$this->sess) return true;
		}
	}

	function addHit()
	{
		$this->db->query("update `".GD_BD_.$this->id."` set hit=hit+1 where no='".$this->no."'");
		$this->mini_idno .= $this->id."_".$this->no.",";
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

	function relation()
	{
		$no = $this->no;
		$pos = array("next","prev");
		if ($this->data['sub']){
			list($no) = $this->db->fetch("select no from `".GD_BD_.$this->id."` where idx='".$this->data['idx']."' and main='".$this->data[main]."' and sub=''");
		}

		if ($this->search['word']){
			$this->getIndexSearch();
			$where = $this->where;
			$pos = array("prev","next");
		}
		$where[] = "sub = ''";
		$where = "and ".implode(" and ",$where);

		### 다음글
		$query = "select * from `".GD_BD_.$this->id."` where no>'".$no."' ".$where." order by no limit 1";
		$data = $this->db->fetch($query,1);
		$data['link']['view'] = "<a href=\"view.php?".$this->vars['no']."&no=".$data['no']."\">";
		$this->data['relation'][$pos[0]] = $data;

		### 이전글
		$query = "select * from `".GD_BD_.$this->id."` where no<'".$no."' ".$where." order by no desc limit 1";
		$data = $this->db->fetch($query,1);
		$data['link']['view'] = "<a href=\"view.php?".$this->vars['no']."&no=".$data['no']."\">";
		$this->data['relation'][$pos[1]] = $data;

		### 답글
		$this->binary_patch();
		$query = "select * from `".GD_BD_.$this->id."` where idx='".$this->data[idx]."' and main=".$this->data[main]." ".$this->orderby;
		$res = $this->db->query($query);
		if ($this->db->count_($res)>1){
			while ($data=$this->db->fetch($res,1)){
				if ($data['sub']) $data['gapReply'] = "<span style=\"width:".((strlen($data['sub'])-1)*15)."\"></span>";
				$data['link']['view'] = "<a href=\"view.php?".$this->vars['no']."&no=".$data['no']."\">";
				$this->data['relation']['reply'][] = $data;
			}
		}
	}

	function _view()
	{
		GLOBAL $bdAdminDsp,$bdIpAsterisk,$bdUserDsp,$bdAllowPluginDomain, $bdAllowPluginTag, $bdUseXss;

		$this->vars['no']  = (!$_GET['search']['word']) ? getVars('no,sel,password,mode,search') : getVars('no,sel,mode,password');

		$query	= "select * from `".GD_BD_.$this->id."` where no='".$this->no."'";
		$this->data	= $this->db->fetch($query,1);

		if(class_exists('validation') && method_exists('validation','xssCleanArray')){
			$this->data = validation::xssCleanArray($this->data , 
				array(
					validation::DEFAULT_KEY=>'text',
					'contents'=>array($bdUseXss,'ent_noquotes', $bdAllowPluginTag , $bdAllowPluginDomain ),
					'subject'=>array($bdUseXss,'ent_noquotes', $bdAllowPluginTag , $bdAllowPluginDomain),
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
		$this->data['subject']	= $this->titleStyle($this->data['titleStyle'],$this->data['subject']);

		if ($this->data['secret']) $this->chkSecret();

		if ($this->relation) $this->relation();
		$this->data = array_merge($this->data,array(
					contents	=> $this->setContents($this->data['contents'],$this->data['html'],1),
					loopComment => $this->getComment(),
					'link'		=> array(
								reply	=> "<a href=\"".url("board/write.php?".$this->vars[no]."&no=".$this->no."&mode=reply")."\">",
								),
					));
		if ($this->privLink($this->data['m_no'])){
			$this->data['link']['modify']	= "<a href=\"".url("board/write.php?".$this->vars[no]."&no=".$this->no."&mode=modify")."\">";
			$this->data['link']['delete']	= "<a href=\"delete.php?".$this->vars[no]."&no=".$this->no."\">";
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
			$this->data['name'] = "<img src=\"../data/skin/".$this->cfg['tplSkin']."/".$this->adminicon."\" border=\"0\" />";
			$this->data['m_id'] = "<img src=\"../data/skin/".$this->cfg['tplSkin']."/".$this->adminicon."\" border=\"0\" />";
		}

		if ($bdIpAsterisk && $this->ici_admin === false){
			preg_match($pattern='/\.[0-9]*$/', $this->data['ip'], $matches);
			if (strlen($matches[0]) > 1) $this->data['ip'] = preg_replace($pattern, '.'.str_repeat("*", strlen($matches[0]) - 1), $this->data['ip']);
		}
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

		// 이미지에 쇼핑몰의 상품상세/분류페이지 링크가 걸려있을 경우 모바일샵으로 링크되어 있는 url을 유저몰로 변경
		$urlMatchPattern = '@href=([\'"])(.*?)(?<!\x5c)\1@i';
		preg_match_all($urlMatchPattern,$contents,$urlMatch);
		if (is_array($urlMatch[2]) && count($urlMatch[2]>0)){
			$changelinkArray = array(
				"/m/goods/view.php"=>"/shop/goods/goods_view.php",
				"/m2/goods/view.php"=>"/shop/goods/goods_view.php",
				"/m/goods/list.php"=>"/shop/goods/goods_list.php",
				"/m2/goods/list.php"=>"/shop/goods/goods_list.php"
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
						$imgContents.= "<div><img onLoad='miniSelfResize(contents_{$this->data[no]},this);' src=\"download.php?id=".$this->id."&no=".$this->data['no']."&div=".$i."&mode=1\" border=\"0\" /></div><br>";
						break;
					}
				}
			}else{
				$contents = preg_replace("/\[\:이미지([0-9]+)\:\]/e","'<img src=\"download.php?id=".$this->id."&no=".$this->data['no']."&mode=1&div='.(\\1-1).'\">'",$contents);
			}
			$contents = preg_replace( "/<img/i", "<img onLoad='miniSelfResize(contents_{$this->data[no]},this); if(this.parentNode.tagName==\"A\"){this.onclick = \"\";}'",$contents);
		}
		$contents = $imgContents.$contents;

		return $contents;
	}

	function titleStyle($titleStyle,$subject)
	{
		GLOBAL $bdTitleCChk,$bdTitleSChk,$bdTitleBChk;

		# 제목 스타일
		if( $bdTitleCChk || $bdTitleSChk || $bdTitleBChk ){
			$tmp_titleStyle	= explode("|",$titleStyle);
			foreach($tmp_titleStyle AS $sKey => $sVal){
				$tmp_title	= explode(":",$sVal);
				if( $bdTitleCChk && $tmp_title[0] == "^C"){
					$tmp_style[] = "color:" . $tmp_title[1];
				}
				if( $bdTitleSChk && $tmp_title[0] == "^S"){
					$tmp_style[] = "font-size:" . $tmp_title[1];
				}
				if( $bdTitleBChk && $tmp_title[0] == "^B"){
					$tmp_style[] = "font-weight:" . $tmp_title[1];
				}
			}
			if(is_array($tmp_style)){
				$subject = "<font style=\"" . implode(";",$tmp_style) . ";\">".$subject."</font>";
			}
			unset($tmp_style);
		}

		return $subject;
	}

}

?>