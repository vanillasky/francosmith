<?
require_once "../class/framework/Request.class.php";
require_once "../class/enamu/EnamuDAO.class.php";
require_once "../class/comment/CommentDAO.class.php";
require_once "../class/cody/CodyDAO.class.php";
require_once "../../Template_/Template_.class.php";
require_once "../class/lib/Mata.class.php";

class inDBComment{
	var $req;
	var $dao;
	var $cdao;

	function inDBComment(){
		$this->req = new Request('Request');
		$this->Edao = new EnamuDAO();
		$this->dao = new CommentDAO();
		$this->Cdao = new CodyDAO();
		$this->setGConfig = loadConfig('setGoodsConfig','setGoodsConfig.php');
	}

	function main(){
		$mode = $this->req->get('mode');
		switch($mode) {
			case "register"  : //등록
				$this->insertComment();
			break;
			case "modify" : //수정
				$this->updateComment();
			break;
			case "delete" : //삭제
				$this->deleteComment();
			break;
			default:
				L_mata::err('error!');
			break;
		}

	}

	function insertComment() { //등록

		if($this->setGConfig['memo'] == "Y") {

			$cody_idx	= $this->req->get('cody_idx');
			$m_no		= $this->Edao->getSession('m_no');
			$nickname	= $this->req->get('nickname');
			$password	= $this->req->get('password');
			$memo		= $this->req->get('memo');

			if ((!$this->Edao->getSession('sess')) && ($this->setGConfig['memo_permission'] == "user")) {

				L_mata::mata('/shop/member/login.php?returnUrl='.urlencode($_SERVER[HTTP_REFERER]),'회원전용 서비스 입니다. \r\n로그인/회원가입 페이지로 이동합니다.');

			} else {

				$cobj= $this->Cdao->find('idx='.$cody_idx,'idx desc');

				if($this->Edao->getSession('sess') && $password == '') { //회원일때 비밀번호
					$password = uniqid(time());
				}

				$obj = new Comment();
				$obj->set('cody_idx', $cody_idx);
				$obj->set('m_no', $m_no);
				$obj->set('nickname', $nickname);
				$obj->set('password', md5($password));
				$obj->set('memo', $memo);
				$obj->set('regdate', 'now()');

				$this->dao->setObject($obj);
				$this->dao->add(array('cody_idx','m_no','nickname','password','memo','regdate'));
				$comment_cnt_add = $cobj->get('recody_cnt')+1;

				//$this->Cdao->isDebug = true;
				$Cobj = new Cody();
				$Cobj->set('recody_cnt', $comment_cnt_add);
				$this->Cdao->setObject($Cobj);
				$this->Cdao->modify(array('recody_cnt'), 'idx='.$cody_idx);

				L_mata::replace('../content.php?idx='.$cody_idx.'#comment','댓글이 등록되었습니다.');
			}
		} else {
			L_mata::mata('../content.php?idx='.$cody_idx.'&#comment','댓글을 등록 할 수 없습니다.');
		}
	} 

	function updateComment() { //수정

		$idx = $this->req->get('idx');
		$cody_idx = $this->req->get('cody_idx');
		$memo = $this->req->get('memo');
		$password = $this->req->get('password');
		$no_member = $this->req->get('no_member');

		$select_objs = $this->dao->find("idx = '".$idx."'");
		$select_obj= $this->dao->jarArrayConverter($select_objs,'F');

		$obj = new Comment();
		$obj->set('memo', $memo);
		$this->dao->setObject($obj);

		if ($no_member == '1') { //비회원이 쓴 글 일때
			if ($select_obj['0']['password'] == md5($password)) { //비밀번호가 맞았을때
					$this->dao->modify(array('memo'),"idx = '".$idx."'");
					L_mata::replace('../content.php?idx='.$cody_idx.'&#comment','댓글이 수정되었습니다.');
			} else {
					L_mata::mata('../content.php?idx='.$cody_idx.'&#comment','비밀번호가 맞지 않습니다.');
			}
		} else {
			if ($this->Edao->getSession('level') >= 80) {
				$this->dao->modify(array('memo'),"idx = '".$idx."'");
			} else {
				$this->dao->modify(array('memo'),"idx = '".$idx."' and m_no = '".$this->Edao->getSession('m_no')."'");
			}
			L_mata::replace('../content.php?idx='.$cody_idx.'&#comment','댓글이 수정되었습니다.');
		}
	}

	function deleteComment() { //삭제

		$cody_idx = $this->req->get('cody_idx');

		$idx = $this->req->get('idx');
		$password = $this->req->get('password');
		$no_member = $this->req->get('no_member');

		$objs = $this->dao->find("idx = '".$idx."'");
		$obj= $this->dao->jarArrayConverter($objs,'F');

		$cobj= $this->Cdao->find('idx='.$cody_idx,'idx desc');

		$comment_cnt_add = $cobj->get('recody_cnt')-1;

		$Cobj = new Cody();
		$Cobj->set('recody_cnt', $comment_cnt_add);
		$this->Cdao->setObject($Cobj);



		$isDeletable = false;

		// 관리자
		if ((int)$this->Edao->getSession('level') >= 80) {
			$isDeletable = true;
		}
		// 그 외
		else {

			// 회원 번호 체크
			if ((int)$obj['0'] > 0 && (int)$this->Edao->getSession('m_no') == (int)$obj['0']['m_no']) {
				$isDeletable = true;
			}
			// 비밀번호 체크
			elseif ((int)$obj['0'] == 0 && $obj['0']['password'] == md5($password)) {
				$isDeletable = true;
			}

		}

		// 삭제 처리 (삭제 할 댓글이 있고, 권한 있을때)
		if ((int)$obj['0']['idx'] > 0 && $isDeletable) {

			if ($this->dao->delete("idx = '".$idx."'")) {
				$this->Cdao->modify(array('recody_cnt'), 'idx='.$cody_idx);
				L_mata::mata('../content.php?idx='.$cody_idx.'&#comment','댓글이 삭제되었습니다.');
				exit;
			}

		}
		else {
			L_mata::mata('../content.php?idx='.$cody_idx.'&#comment','삭제 할 수 없습니다.');
		}

	}
}
$indbcomment = new inDBComment();

$indbcomment->main();
?>