<?php
/**
 * LibEmailDeny
 * 이메일 수신거부 클래스
 * @author pr @ godosoft development team.
 */
class LibEmailDeny
{
	protected $cryptKey = 'dusqhdtkdtmd';

	/**
	 * 수신거부 링크 리턴
	 * @param string $id 회원아이디
	 * @return string
	 */
	public function getDenyLink($id)
	{
		global $cfg;
		if (!$cfg) include dirname(__FILE__).'/../conf/config.php';

		$r_url = explode(':',$_SERVER['HTTP_HOST']);
		$r_dns = str_replace(array('https://','http://','www.'),'',$r_url[0]).$cfg['rootDir'];
		$enTric = urlencode($this->_encrypt(date('Ymi')));
		$enId = urlencode($this->_encrypt(trim($id)));
		$denyLink = 'http://'.$r_dns. '/proc/emailDeny.php?k='.$enTric.'&id=' . $enId;
		return $denyLink;
	}

	/**
	 * 수신거부 처리
	 * @param string $id 회원아이디
	 * @return string
	 */
	public function setDeny($k, $id)
	{
		global $db, $cfg;
		if (!$cfg) include dirname(__FILE__).'/../conf/config.php';

		try {
			$dormant = Core::loader('dormant');

			// 인자 유효성 체크
			$k = trim($k);
			$id = trim($id);
			if ($k == '') throw new Exception('k 값이 비어 있음.');
			if ($id == '') throw new Exception('id 값이 비어 있음.');

			// id 디코딩
			$m_id = $this->_decrypt($id);
			if ($m_id == '') throw new Exception('id 값이 디코딩 되지 않음.');

			$dormantMember = false;
			$dormantMember = $dormant->checkDormantMember(array('m_id'=>$m_id), 'm_id');

			// 회원 존재여부 체크
			if($dormantMember === true){
				list($data['m_no'], $data['email']) = $dormant->getDormantInfo('emailDeny', array('m_id'=>$m_id));
			}
			else {
				$data = $db->fetch("select m_no, email from ".GD_MEMBER." where m_id='$m_id'");
			}
			if ($data['m_no'] == '') throw new Exception($m_id.'은 존재하지 않음.');

			// 수신거부 처리
			if($dormantMember === true){
				$res = $dormant->updateMailling($m_id);
			}
			else {
				$res = $db->query("update ".GD_MEMBER." set mailling='n' where m_id='$m_id'");
			}
			if ($res == false) throw new Exception('수신거부 처리 실패');

			// 수신거부완료 안내메일
			if ($data['email'] && $cfg['mailyn_30'] == 'y') {
				$modeMail = 30;
				$automail = Core::loader('automail');
				$automail->_set($modeMail,$data['email'],$cfg);
				$automail->_assign('mail',$data['email']);
				$automail->_assign('agreeDate_year',date('Y'));
				$automail->_assign('agreeDate_month',date('m'));
				$automail->_assign('agreeDate_day',date('d'));
				$automail->_send();
			}

			return array('result' => true, 'msg' => '정상적으로 수신거부가 되었습니다.('.$m_id.')');
		} catch(Exception $e) {
			return array('result' => false, 'msg' => $e->getMessage());
		}
	}

	/**
	 * 디코딩 리턴
	 * @param string $data 디코딩 데이터
	 * @return string
	 */
	protected function _decrypt($data)
	{
		$xxtea = Core::loader('xxtea');
		$xxtea->setKey($this->cryptKey);
		return @unserialize($xxtea->decrypt(base64_decode($data)));
	}

	/**
	 * 인코딩 리턴
	 * @param string $data 인코딩 데이터
	 * @return string
	 */
	protected function _encrypt($data)
	{
		$xxtea = Core::loader('xxtea');
		$xxtea->setKey($this->cryptKey);
		return base64_encode($xxtea->encrypt(serialize($data)));
	}
}
?>