<?

class L_mata {
	
	function alert($msg) {
		echo "<script>";
		echo "alert('".$msg."');";
		//echo "alert('".iconv('utf-8','euc-kr',$msg)."');";
		echo "</script>";
	}

	function mata($url = '', $msg = ''){
		if ( $msg != '' ) L_mata::alert($msg);
		if ( $url != '') echo "<meta http-equiv='Refresh' content='0;url=$url'>";
		else echo "<script>history.back(-1);</script>";
		exit;
	}

	function back($msg = '') {
		$this->go('', $msg);
	}

	function go($url = '', $msg = '') {
		if ( $msg != '' ) L_mata::alert($msg);
		if ( $url != '') echo "<script>location.href='$url';</script>";
		else echo "<script>history.back(-1);</script>";
		exit;
	}

	function replace($url = '', $msg = '') {
		if ( $msg != '' ) L_mata::alert($msg);
		if ( $url != '') echo "<script>location.replace('$url');</script>";
		else echo "<script>history.back(-1);</script>";
		exit;
	}

	function reload($msg = '') {
		
		if ( $msg != '' ) L_mata::alert($msg);
		echo "<script>";
		echo "location.reload();";
		echo "</script>";
		exit;
	}

	function close($msg = ''){
		L_mata::alert($msg);
		echo "<script>";
		echo "opener.location.reload();";
		echo "self.close(); ";
		echo "</script>";
		exit;
	}

	function err($msg = '') {
		L_mata::alert($msg);		
	}

	function redirect($url) {
		echo "<script>";
		echo "location.reload('$url');";
		echo "</script>";
		exit;	
	}
}
?>