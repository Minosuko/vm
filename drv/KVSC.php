<?php
# Key Verify Secure Crypto
# Based RSA but diff method
class KVSC{
	private function check_prime($num){
		if($num == 0 || $num == 1)
			return false;
		if($num <= 10)
			return false;
		for($i = 2; $i < $num; $i++)
			if ($num % $i == 0 && $i != $num)
				return false;
		return true;
	}
	private function generatePrime(){
		$p = 0;
		while(!$this->check_prime($p))
			$p = rand(11,100);
		return $p;
	}
	function generateKey(){
		$stt = "";
		for($i = 0; $i < 256; $i++)
			$stt .= chr(rand(12,25));
		while(true){
			$keygen = $this->generateNumkey($this->generatePrime(), $this->generatePrime());
			$pkpb = [$keygen['n'], $keygen['e']];
			$pkpv = [$keygen['n'], $keygen['d']];
			$crypt_ed = $this->encrypt($stt, $pkpb);
			$decrypt_ed = $this->decrypt($crypt_ed, $pkpv);
			$crypt_edl = strlen($crypt_ed);
			$decrypt_edl = strlen($decrypt_ed);
			if(
				(
					$crypt_edl == $decrypt_edl &&
					(
						$crypt_edl != 0 &&
						$decrypt_edl != 0
					)
				) &&
				(
					$crypt_ed == $decrypt_ed
				) && 
				(
					$pkpb[1] != $pkpv[1]
				)
			)
			return $this->MakeKey($keygen);
		}
	}
	private function generateValidTNUM($num){
		$t = 0;
		$s = '';
		while(true){
			$g = rand(1,100);
			$t += $g;
			$s .= chr($g);
			if($t > $num){
				$t = 0;
				$s = '';
			}
			if($t == $num)
				return $s;
		}
	}
	private function ParseTNUM($str){
		$tnum = 0;
		for($i = 0; $i < strlen($str); $i++)
			$tnum += ord($str[$i]);
		return $tnum;
	}
	private function MakeKey($keygen){
		$n = $this->generateValidTNUM($keygen['n']);
		$e = $this->generateValidTNUM($keygen['e']);
		$d = $this->generateValidTNUM($keygen['d']);
		$e_length = pack('i', strlen($e));
		$n_length = pack('i', strlen($n));
		$d_length = pack('i', strlen($d));
		$pub_b64 = base64_encode($n_length.$e_length.$n.$e);
		$pri_b64 = base64_encode($n_length.$d_length.$n.$d);
		$key = 
		[
			'public' => "-----BEGIN KVSC PUBLIC KEY-----\n$pub_b64\n-----END KVSC PUBLIC KEY-----",
			'private' => "-----BEGIN KVSC PRIVATE KEY-----\n$pri_b64\n-----END KVSC PRIVATE KEY-----",
			'e' => $keygen['e']
		];
		return $key;
	}
	public function parse_key($key){
		$key = str_replace("-----BEGIN KVSC PUBLIC KEY-----", "", $key);
		$key = str_replace("-----END KVSC PUBLIC KEY-----", "", $key);
		$key = str_replace("-----BEGIN KVSC PRIVATE KEY-----", "", $key);
		$key = str_replace("-----END KVSC PRIVATE KEY-----", "", $key);
		$key = str_replace("\r", "", $key);
		$key = str_replace("\n", "", $key);
		if(base64_encode(base64_decode($key)) != $key)
			return false;
		$key = base64_decode($key);
		$key_length_1 = unpack('i', substr($key,0,4));
		$key_length_2 = unpack('i', substr($key,4,4));
		if(!isset($key_length_1[1]) && !isset($key_length_2[1]))
			return false;
		$num1 = $this->ParseTNUM(substr($key, 8, $key_length_1[1]));
		$num2 = $this->ParseTNUM(substr($key, (8+$key_length_1[1]), $key_length_2[1]));
		return [$num1, $num2];
	}
	private function generateExponent($phi){
		while(true)
		{
			$e = rand(2,99999);
			$num1 = $e;
			$num2 = $phi;
			while($num2 > 0){
				$temp = $num1 % $num2;
				$num1 = $num2;
				$num2 = $temp;
			}
			if(
				($num1 == 1) &&
				($e > 1) &&
				($e < $phi)
			)
			return $e;
		}
	}
	private function generateNumkey($p, $q) {
		$n = $p * $q;
		$phi = ($p - 1) * ($q - 1);
		$e = $this->generateExponent($phi);
		$d = $this->modInverse($e, $phi);
		return 
		[
			'n' => $n,
			'e' => $e,
			'd' => $d
		];
	}
	private function modInverse($a, $m) {
		$m0 = $m;
		$y = 0;
		$x = 1;
		if ($m == 1)
			return 0;
		while ($a > 1) {
			$q = intval($a / $m);
			$t = $m;
			$m = $a % $m;
			$a = $t;
			$t = $y;
			$y = $x - $q * $y;
			$x = $t;
		}
		if ($x < 0)
			$x += $m0;
		return $x;
	}

	private function encrypt_char($message, $publicKey) {
		$n = $publicKey[0];
		$e = $publicKey[1];
		$m = $this->stringToNumber($message);
		$c = bcpowmod($m, $e, $n);
		return $c;
	}
	private function decrypt_char($ciphertext, $privateKey) {
		$n = $privateKey[0];
		$d = $privateKey[1];
		$m = bcpowmod($ciphertext, $d, $n);
		$message = $this->numberToString($m);
		return $message;
	}
	function encrypt($message, $publicKey) {
		if(!isset($publicKey[0]) && !isset($publicKey[1])) return '';
		if(!is_numeric($publicKey[0]) && !is_numeric($publicKey[1])) return '';
		if(strlen($message) == 0) return '';
		$x = '';
		for($i = 0; $i < strlen($message); $i++)
			$x .= $this->numberToString($this->encrypt_char($message[$i], $publicKey));
		return $x;
	}
	function decrypt($message, $privateKey) {
		if(!isset($privateKey[0]) && !isset($privateKey[1])) return '';
		if(!is_numeric($privateKey[0]) && !is_numeric($privateKey[1])) return '';
		if(strlen($message) == 0) return '';
		$x = '';
		for($i = 0; $i < strlen($message);$i++)
			$x .= $this->decrypt_char($this->stringToNumber($message[$i]), $privateKey);
		return $x;
	}
	  
	private function stringToNumber($string) {
		$result = 0;
		$len = strlen($string);
		for ($i = 0; $i < $len; $i++)
			$result = bcadd(bcmul($result, '256'), ord($string[$i]));
		return $result;
	}
	private function numberToString($number) {
		$result = '';
		while ($number > 0) {
			$result = chr(bcmod($number, '256')) . $result;
			$number = bcdiv($number, '256');
		}
		return $result;
	}
}
?>
