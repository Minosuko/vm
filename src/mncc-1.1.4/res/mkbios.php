<?php
ini_set("memory_limit", "1024M");
class CSA{
	function __construct($debug = 0){
	    $this->debug = $debug;
	    $this->encrypt = base64_decode("EHMOTgxVQUQIKhxcWB1uRjVcVwI8B1pvSU0hEV4DKwhVWz4OVWRuAQ==");
	}
	 function private_key_to_public_key($private_key){
		if(!empty($private_key)){
			$key = $this->text2ascii($this->crypt_key(base64_decode($private_key)));
			$eak = $this->ekey($key);
			$keysize = count($key);
			$cipher = "";
			for($i = 0; $i < $keysize; $i++){
				$cipher .= chr($key[$i] ^ $eak);
			}
			if($this->debug == 1){
				echo "\nConverting key to public";
			}
			return base64_encode($this->crypt_key($cipher));
		}else{
			return false;
			$this->error('turn private key to public key', 'missing private key');
		}
	}
	  function decrypt($text, $private_key) {
		if(!empty($private_key)){
			$key = $this->text2ascii($this->crypt_key(base64_decode($private_key)));
			$eak = $this->ekey($key);
			$text = $this->text2ascii($text);
			$keysize = count($key);
			$text_size = count($text);
			$crypt = "";
			for ($i = 0; $i < $text_size; $i++){
				$crypt .= chr($text[$i] ^ ($key[$i % $keysize] ^ $eak));
			}
			if($this->debug == 1){
				echo "\nDecrypting";
			}
			return $crypt;
		}else{
			return false;
			$this->error('decrypt','missing private key');
		}
	}
	  function encrypt($text, $public_key){
		if(!empty($public_key)){
			$key = $this->text2ascii($this->crypt_key(base64_decode($public_key)));
			$eak = $this->ekey($key);
			$text = $this->text2ascii($text);
			$keysize = count($key);
			$text_size = count($text);
			$cipher = "";
			for($i = 0; $i < $text_size; $i++){
				$cipher .= chr($text[$i] ^ $key[$i % $keysize]);
			}
			if($this->debug == 1){
				echo "\nEncrypting";
			}
			return $cipher;
		}else{
			return false;
			$this->error('encrypt', 'missing public key');
		}
	 }
	  function create_private_key($bit = 2048){
		if(in_array($bit, array(512, 1024, 2048, 4096, 8192, 16384))){
			$key = '';
			$e = 0;
			for($x = 0; $x < $bit; $x++){
				$rand = rand(32, 126);
				$key .= chr($rand);
				$e = $e+$rand;
				if($this->debug == 1 && $x % 2 == 0){
					if($rand > 63){
						echo '+';
					}else{
						echo '.';
					}
				}
			}
			if($this->debug == 1){
				echo "\ngenerate key finissed, e is ".$e;
			}
			return base64_encode($this->crypt_key($key));
		}else{
			return false;
			$this->error('Create private key', 'invalid bit length');
		}
	}
	private function crypt_key($text){
		$key = $this->text2ascii($this->encrypt);
		$text = $this->text2ascii($text);
		$keysize = count($key);
		$text_size = count($text);
		$cipher = "";
		for($i = 0; $i < $text_size; $i++){
			$cipher .= chr($text[$i] ^ $key[$i % $keysize]);
		}
		if($this->debug == 1){
			echo "\nCrypting key";
		}
		return $cipher;
	}
	private function text2ascii($text){
		return array_map('ord', str_split($text));
	}
	private function ekey($ascii) {
		$text = 0;
		foreach($ascii as $char){
			$text = $text+$char;
		}
		return $text;
	}
	private function error($name, $reason){
		$err = "<br><b>CSA Error:</b> Could not ".$name.", ".$reason." in <b>".$_SERVER['DOCUMENT_ROOT'].substr($_SERVER['SCRIPT_NAME'],1)."</b>.";
		throw new Exception($err);
	}
}
$csa = new CSA();
if(isset($argv[1]) && isset($argv[2])){
	if(file_exists($argv[1]))
		file_put_contents($argv[2], $csa->encrypt(file_get_contents($argv[1]),'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'));
	else
		echo "File not exists.";
	echo "Done.\n";
}else{
	echo "Missing argument.";
}