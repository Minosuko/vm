<?php
ini_set("memory_limit", "1024M");
function hexDUMP($input){
	$output_d = '';
	$input = file_get_contents($input);
	$filter_text = str_split(str_replace(str_split("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZŸ\\'\",./[]{}=-)|(*&^%$#@!~`_+:<>áàảãạấầẩẫậắằẳẵặéèẻẽẹếềểễệóòỏõọốồổỗộớờởỡợđíìỉĩịýỳỷỹỵúùứừửữựúùủũụª"), '', $input));;
	$filter_text[] = ' ';
	$string = str_split(str_replace($filter_text,'.',$input));
	$hex = str_split(strtoupper(bin2hex($input)), 2);
	$hc = count($hex);
	$t = ($hc - ($hc % 16)) / 16;
	$c = 0;
	$n = 0;
	for($i = 0; $i < $t; $i++){
		$k = dechex($i);
		$output_d .= "\033[1;33m";
		$output_d .= "0x";
		if(strlen($k) < 6)
			$output_d .= str_repeat('0', (6-strlen($k)));
		$output_d .= strtoupper($k);
		$output_d .= "0\033[0m | ";
		$d = '';
		for($x = 0; $x < 16; $x++){
			$d .= $hex[$c];
			$d .= " ";
			$c++;
		}
		$output_d .= "\033[0;35m";
		$output_d .= substr($d,0, 48);
		$output_d .= "\033[0m";
		$d = '';
		$output_d .= "   |    ";
		for($x = 0; $x < 16; $x++){
			$d .= $string[$n];
			$n++;
		}
		$output_d .= $d;
		$output_d .= "\n";
	}
	if($hc % 16 != 0){
		$k = dechex($t+1);
		$output_d .= "\033[1;33m";
		$output_d .= "0x";
		if(strlen($k) < 6)
			$output_d .= str_repeat('0', (6-strlen($k)));
		$output_d .= strtoupper($k);
		$output_d .= "0\033[0m | ";
		$d = '';
		for($x = 0; $x < ($hc % 16); $x++){
			$d .= $hex[$c];
			$d .= " ";
			$c++;
		}
		$d .= str_repeat('   ',(16 - $hc % 16));
		$d .= " ";
		$output_d .= "\033[0;35m";
		$output_d .= substr($d, 0, (strlen($d)-1));
		$output_d .= "\033[0m";
		$output_d .= "   |    ";
		$d = '';
		for($x = 0; $x < ($hc % 16); $x++){
			$d .= $string[$n];
			$n++;
		}
		$output_d .= $d;
	}
	return $output_d;
}
if(isset($argv[1])){
	if(file_exists($argv[1])){
		echo "\033[1;30m=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\033[0m\n";
		echo "\033[1;33m Address\033[0m  |                           \033[0;35mHEX\033[0m                      |          ANSII      \n";
		echo "\033[1;30m=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\033[0m\n";
		echo hexDUMP($argv[1]);
		echo "\n";
		echo "\033[1;30m=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\033[0m\n";
	}
}else{
	echo "Missing argument.";
}
?>