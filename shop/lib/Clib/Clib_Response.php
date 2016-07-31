<?php
/**
 * Clib_Response
 * @author extacy @ godosoft development team.
 */
class Clib_Response
{

	public function display($var)
	{

		if ($var instanceof Clib_Exception) {
			msg($var->getMessage(), - 1);
			exit ;
		}

	}

	public function jsAlert($msg)
	{
		if ($msg instanceof Exception) {
			$msg = $msg->getMessage();
		}

		echo '
		<script type="text/javascript">
		alert("' . htmlspecialchars($msg) . '");
		</script>
		';

		return $this;
	}

	public function redirect($url)
	{
		if ( ! headers_sent()) {
			header('Location: ' . $url);
		}
		else {
			echo '
			<script type="text/javascript">
			top.window.location.replace("' . $url . '");
			</script>
			';
		}
		exit ;
	}

	public function historyBack($depth = 1)
	{
		$depth = abs($depth);

		if ($depth == 0) {
			$depth = 1;
		}

		$depth = $depth * - 1;

		echo '
		<script type="text/javascript">
		window.history.go(' . $depth . ');
		</script>
		';
		exit ;
	}

}
