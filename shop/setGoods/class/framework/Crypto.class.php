<?php
require_once dirname(__FILE__) . "/../setGoods/class/_common.php";

class Crypto{

	var $key;	

	function Crypto(){
		$this->key = KEYVAL;		
	}

	
	function enc($input){
	    
		 $size = mcrypt_get_block_size('des', 'ecb');
		 $input = $this->pkcs5_pad($input, $size);
		 $td = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_ECB, '');	
		 $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);	
		 mcrypt_generic_init($td, $this->key, $iv);	
		 $encrypted_data = mcrypt_generic($td, $input);	
		 mcrypt_generic_deinit($td);	
		 mcrypt_module_close($td);	
		 $encrypted_data = base64_encode($encrypted_data);	
		 $encrypted_data = str_replace("+","!",$encrypted_data);

		 return $encrypted_data;
	}
	
	function dec($encrypted){
		$encrypted = str_replace("!","+",$encrypted);
		$encrypted = base64_decode($encrypted);
		$td = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_ECB, '');
		$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		mcrypt_generic_init($td, $this->key, $iv);
		$decrypted = mdecrypt_generic($td, $encrypted); 
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		$decrypted = $this->pkcs5_unpad($decrypted);
		
		return $decrypted;
	}

	
	function pkcs5_pad($text, $blocksize){

   		$pad = $blocksize - (strlen($text) % $blocksize);
   		
		return $text . str_repeat(chr($pad), $pad);
	}

	
	function pkcs5_unpad($text){
   		$pad = ord($text{strlen($text)-1});
   		if ($pad > strlen($text)) 
			return false;
   		if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) 
			return false;
   		return substr($text, 0, -1 * $pad);
	}

}

?>
