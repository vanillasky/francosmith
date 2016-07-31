<?
if (defined(_GD_COUPON_GENERATOR_)) return;
define(_GD_COUPON_GENERATOR_, true);

/**
	2011-01-25

 */
class couponGenerator {

	var $count;	// static

	var $max;
	var $length;
	var $prefix;
	var $glue;
	var $set;
	var $coupon;

	var $gen_method = 'ascii';


	function couponGenerator() {

		$this->max = 1;			// 쿠폰갯수
		$this->count = 0;			// 카운트
		$this->prefix = 0;			// prefix.
		$this->glue = '';			// 세트간 char
		$this->set = '';			// 세트수
		$this->length = 12;			// 자리수
		$this->coupon = array();	// 생성된 쿠폰쓰.

	}	//

	/**
		public functions / interface
	 */
		function make() {

			$this->coupon = array();

			while (sizeof($this->coupon) < $this->max) {

				$_gen = $this->_generate();

				// 중복 없으면 카운트 올림.
				if (! in_array($_gen, $this->coupon)) {
					$this->coupon[] = $_gen;
					$this->count++;
				}

			}

		}	//

		function pop() {

			return (sizeof($this->coupon) > 0) ? array_pop($this->coupon) : null;

		}


	/**
		private functions
	 */
		function _generate() {

			if ($this->gen_method == 'ascii')
				return $this->_generateFromCharAscii();
			else
				return $this->_generateFromTimeStamp();

		}	//


		function _generateFromCharAscii() {

			$_chars = array(
								'1','2','3','4','5','6','7','8','9','0',
								'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','T','S','U','V','W','X','Y','Z',
								'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
								'.',',','/','?','!','$','@','^','*','(',')','_','+','-','=',':',';','~','{','}'
							);

			shuffle($_chars);

			$_secret = array_slice($_chars,0,$this->length);

			$_coupon = ($this->prefix != '') ? $this->prefix : '';

			for ($i=0;$i<$this->length;$i++) {
				$_coupon .= is_numeric($_secret[$i]) ? $_secret[$i] : ord($_secret[$i]);
			}

			return substr($_coupon, 0, $this->length);

		}


		function _generateFromTimeStamp() {

			$_coupon = time().str_pad($this->count,3,0,STR_PAD_LEFT);

			return $_coupon;

		}

	// eof private functions



}	// eof class;
/*
$cp = new couponGenerator();

$cp->max = 1000;
//$cp->prefix = 'a';
$cp->length = 12;
$cp->make();

debug($cp->coupon);
*/
?>