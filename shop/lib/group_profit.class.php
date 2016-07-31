<?php
class group_profit{
	var $db;
	var $groupsno;

	//ÇýÅÃÁ¤º¸
	var $name;
	var $grpnm;
	var $dc_type;
	var $dc_std_amt;
	var $dc;
	var $add_emoney_type;
	var $add_emoney_std_amt;
	var $add_emoney;
	var $free_deliveryfee;
	var $free_deliveryfee_std_amt;

	var $profits = array();


	function group_profit(){
		$this->db		= & $GLOBALS['db'];
		$this->groupsno = $_SESSION['sess']['groupsno'];
	}


	function getGroupProfit($groupsno = null) {

		if ($groupsno !== null)
			$this->groupsno = $groupsno;

		if (! isset($this->profits[ $this->groupsno ])) {

			$query = " SELECT grpnm, dc_type, dc_std_amt, dc, add_emoney_type, add_emoney_std_amt, add_emoney, free_deliveryfee, free_deliveryfee_std_amt";
			$query .= "  FROM ".GD_MEMBER_GRP." WHERE sno=[i]";
			$query = $this->db->_query_print($query, $this->groupsno);

			$ret = $this->db->_select($query);

			if( $ret ){
				$ret = $ret[0];
				$ret['name'] = $this->name = $_SESSION['member']['name'];
				$this->grpnm = $ret['grpnm'];
				$this->dc_type = $ret['dc_type'];
				$this->dc_std_amt = $ret['dc_std_amt'];
				$this->dc = $ret['dc'];
				$this->add_emoney_type = $ret['add_emoney_type'];
				$this->add_emoney_std_amt = $ret['add_emoney_std_amt'];
				$this->add_emoney = $ret['add_emoney'];
				$this->free_deliveryfee = $ret['free_deliveryfee'];
				$this->free_deliveryfee_std_amt = $ret['free_deliveryfee_std_amt'];

				$this->profits[ $this->groupsno ] = $ret;
			}
			else $this->profits[ $this->groupsno ] = false;

		}

		return $this->profits[ $this->groupsno ];

	}
}
?>
