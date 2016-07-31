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
			case "register"  : //���
				$this->insertComment();
			break;
			case "modify" : //����
				$this->updateComment();
			break;
			case "delete" : //����
				$this->deleteComment();
			break;
			default:
				L_mata::err('error!');
			break;
		}

	}

	function insertComment() { //���

		if($this->setGConfig['memo'] == "Y") {

			$cody_idx	= $this->req->get('cody_idx');
			$m_no		= $this->Edao->getSession('m_no');
			$nickname	= $this->req->get('nickname');
			$password	= $this->req->get('password');
			$memo		= $this->req->get('memo');

			if ((!$this->Edao->getSession('sess')) && ($this->setGConfig['memo_permission'] == "user")) {

				L_mata::mata('/shop/member/login.php?returnUrl='.urlencode($_SERVER[HTTP_REFERER]),'ȸ������ ���� �Դϴ�. \r\n�α���/ȸ������ �������� �̵��մϴ�.');

			} else {

				$cobj= $this->Cdao->find('idx='.$cody_idx,'idx desc');

				if($this->Edao->getSession('sess') && $password == '') { //ȸ���϶� ��й�ȣ
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

				L_mata::replace('../content.php?idx='.$cody_idx.'#comment','����� ��ϵǾ����ϴ�.');
			}
		} else {
			L_mata::mata('../content.php?idx='.$cody_idx.'&#comment','����� ��� �� �� �����ϴ�.');
		}
	} 

	function updateComment() { //����

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

		if ($no_member == '1') { //��ȸ���� �� �� �϶�
			if ($select_obj['0']['password'] == md5($password)) { //��й�ȣ�� �¾�����
					$this->dao->modify(array('memo'),"idx = '".$idx."'");
					L_mata::replace('../content.php?idx='.$cody_idx.'&#comment','����� �����Ǿ����ϴ�.');
			} else {
					L_mata::mata('../content.php?idx='.$cody_idx.'&#comment','��й�ȣ�� ���� �ʽ��ϴ�.');
			}
		} else {
			if ($this->Edao->getSession('level') >= 80) {
				$this->dao->modify(array('memo'),"idx = '".$idx."'");
			} else {
				$this->dao->modify(array('memo'),"idx = '".$idx."' and m_no = '".$this->Edao->getSession('m_no')."'");
			}
			L_mata::replace('../content.php?idx='.$cody_idx.'&#comment','����� �����Ǿ����ϴ�.');
		}
	}

	function deleteComment() { //����

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

		// ������
		if ((int)$this->Edao->getSession('level') >= 80) {
			$isDeletable = true;
		}
		// �� ��
		else {

			// ȸ�� ��ȣ üũ
			if ((int)$obj['0'] > 0 && (int)$this->Edao->getSession('m_no') == (int)$obj['0']['m_no']) {
				$isDeletable = true;
			}
			// ��й�ȣ üũ
			elseif ((int)$obj['0'] == 0 && $obj['0']['password'] == md5($password)) {
				$isDeletable = true;
			}

		}

		// ���� ó�� (���� �� ����� �ְ�, ���� ������)
		if ((int)$obj['0']['idx'] > 0 && $isDeletable) {

			if ($this->dao->delete("idx = '".$idx."'")) {
				$this->Cdao->modify(array('recody_cnt'), 'idx='.$cody_idx);
				L_mata::mata('../content.php?idx='.$cody_idx.'&#comment','����� �����Ǿ����ϴ�.');
				exit;
			}

		}
		else {
			L_mata::mata('../content.php?idx='.$cody_idx.'&#comment','���� �� �� �����ϴ�.');
		}

	}
}
$indbcomment = new inDBComment();

$indbcomment->main();
?>