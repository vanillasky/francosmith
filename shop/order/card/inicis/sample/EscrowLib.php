<?

class Escrow
{
	
	var $EscrowType;    // ��û���� (��۵�� - dr, ��۾�����Ʈ - du,  ��ǰ��� - rr, ��ǰ������Ʈ - ru)
	var $mid;
	var $inipayhome;      // �̴����� ���ҽý����� ��ġ�Ǿ� �ִ� ���� ���
	var $hanatid;           // �ϳ����� �ŷ� TID
	var $sendMsg;         // ���� ��û �޼���
	var $EscrowMsg;     // ��û �޼���
	var $rEscrowMsg;    // ��� �޼���
	

	function startAction()
	{
	
		$this->EscrowMsg = 
		        	"inipayhome=" . $this->inipayhome . "^" .
				"EscrowType=" . $this->EscrowType . "^" .
				"msg=" .$this->sendMsg;
												
		$this->rEscrowMsg = exec($this->inipayhome . '/phpexec/INIescrow.phpexec \'' . $this->EscrowMsg . '\'');
							
		if(strlen($this->rEscrowMsg) <= 1)
			$this->rEscrowMsg = "libResultCode=01&libResultMsg=INVOKE ERR : " . $this->inipayhome . '/phpexec/INIescrow.phpexec';
					
		parse_str($this->rEscrowMsg);
		$this->resultCode = $libResultCode;
		$this->resultMsg = $libResultMsg;
		
	}
	
	
}

?>
