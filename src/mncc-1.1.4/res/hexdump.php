<?php
ini_set("memory_limit", "1024M");
function hexDUMP($input, $output){
	$output_d = '';
	$input = file_get_contents($input);
	$filter_text = str_split("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZŸ\\'\",./[]{}=-)|(*&^%$#@!~`_+:<>áàảãạấầẩẫậắằẳẵặéèẻẽẹếềểễệóòỏõọốồổỗộớờởỡợđíìỉĩịýỳỷỹỵúùứừửữựúùủũụª");
	$filter_text[] = ' ';
	$string = str_split(str_replace(str_split(str_replace($filter_text, '', $input)), '.', $input));
	$hex = str_split(strtoupper(bin2hex($input)), 2);
	$hc = count($hex);
	$t = ($hc - ($hc % 16)) / 16;
	$c = 0;
	$n = 0;
	for($i = 0; $i < $t; $i++){
		$d = '';
		for($x = 0; $x < 16; $x++){
			$d .= $hex[$c];
			$d .= " ";
			$c++;
		}
		$output_d .= substr($d,0, 48);
		$output_d .= "        ";
		$d = '';
		for($x = 0; $x < 16; $x++){
			$d .= $string[$n];
			$n++;
		}
		$output_d .= $d;
		$output_d .= "\n";
	}
	if($hc % 16 != 0){
		$d = '';
		for($x = 0; $x < ($hc % 16); $x++){
			$d .= $hex[$c];
			$d .= " ";
			$c++;
		}
		$d .= str_repeat('   ',(16 - $hc % 16));
		$d .= " ";
		$output_d .= substr($d, 0, (strlen($d)-1));
		$output_d .= "        ";
		$d = '';
		for($x = 0; $x < ($hc % 16); $x++){
			$d .= $string[$n];
			$n++;
		}
		$output_d .= $d;
	}
	file_put_contents($output, $output_d);
}
if(isset($argv[1]) && isset($argv[2])){
	if(file_exists($argv[1]))
		hexDUMP($argv[1], $argv[2]);
}else{
	echo "Missing argument.";
}
?>