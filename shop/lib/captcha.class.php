<?

/**
 * Captcha class
 * 캡차 출력/검증 클래스
 */

class Captcha
{

	function Captcha($act='')
	{
		@session_start();
		if ($act && method_exists($this, $act)) $this->$act();
	}

	function rgb($color, $basic)
	{
		if ($color == '') $color = $basic;
		$color = str_replace("#", "", $color);
		sscanf($color, "%2x%2x%2x", $c[r], $c[g], $c[b]);
		return $c;
	}

	### 캡차 이미지 출력
	function output()
	{
		ob_start();
		@include dirname(__FILE__) . "/../conf/captcha.php";
		$bc_rgb = $this->rgb($captcha['bgcolor'], 'FFFFFF');
		$fc_rgb = $this->rgb($captcha['color'], '262626');

		## Defined
		$canvas = array('w' => 120, 'h' => 40);
		$strpit = array('x' => 7, 'y' => 7);

		## Get string of Captcha
		$captchaChars = "";
		for ($i = 0; $i < 5; $i++)
		{
			if (($t = rand(0, 1)) == 0) $captchaChars .= rand(1, 9);
			else if ($t == 1) $captchaChars .= chr(rand(0, 25) + 65);
		}
		$_SESSION['captchaGraph']	= $captchaGraph;
	    $captchaGraph = $_SESSION['captchaGraph'] = md5(md5($captchaChars)); // PHP 버전별 차이를 없애기 위한 방법
		
		## Create Canvas
		$im = @imageCreateTrueColor($canvas['w'], $canvas['h']);
		$trans_colour = imagecolorallocate($im, $bc_rgb[r], $bc_rgb[g], $bc_rgb[b]);
		imagefill($im, 0, 0, $trans_colour);

		## Paint Arc
		for ($i = 0; $i < 15; $i++)
		{
			$color = imageColorAllocate($im, rand(160, 250), rand(160, 250), rand(200, 250));
			$cx = rand(0, $canvas['w']);
			$cy = rand(0, $canvas['h']);
			$size = rand(5, 20);
			$half = $size / 2;

			# Reset coordinate
			if ($cx < $half) $cx = $half; // Left
			if ($cy < $half) $cy = $half; // Top
			if (($cx + $size) > $canvas['w']) $cx = $canvas['w'] - ($half + 1); // Right
			if (($cy + $size) > $canvas['h']) $cy = $canvas['h'] - ($half + 1); // Bottom

			imageFilledArc($im, $cx, $cy, $size, $size, 0, 360, $color, ($i%2 == 0 ? IMG_ARC_NOFILL : IMG_ARC_EDGED));
		}

		## Paint String
		$font = imageloadfont("../lib/captcha.dimurph.gdf");
		$text_color = imagecolorallocate($im, $fc_rgb[r], $fc_rgb[g], $fc_rgb[b]);
		for ($i = 0; $i < strlen($captchaChars); $i++)
		{
			imageChar($im, $font, $strpit['x'], $strpit['y'], $captchaChars[$i], $text_color);
			$strpit['x'] += imagefontwidth($font);
		}
		## Output Image
		ob_end_clean();
		
		header("Content-type: image/png");
		imagePng($im);
		imageDestroy($im);
		
	}

	### 캡차 검증
	function verify()
	{
		
		## Checked Exists
		if (session_is_registered('captchaGraph') === false) return array('code'=>'4001', 'msg'=>'Fail to verify CAPTCHA. (Not registered session)');
		if (empty($_POST['captcha_key']) === true) return array('code'=>'4002', 'msg'=>'Fail to verify CAPTCHA. (captcha_key is empty)');

		## Compared Key
		$captcha_key = md5(md5($_POST['captcha_key']));
		if (strcmp($_SESSION['captchaGraph'], $captcha_key)) return array('code'=>'4003', 'msg'=>'Fail to verify CAPTCHA. (Not equal)');

		return array('code'=>'0000', 'msg'=>'Succeed in verifing');
	}

}

?>