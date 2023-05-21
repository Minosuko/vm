<?php
$a = '';
$r = str_repeat('N', 128);
for($i = 0; $i < 10485; $i++){
	$a .= "GLOBALS_SET rb_$i = \"$r\"\n";
}
file_put_contents("ram_bomb.mnc", $a);
exec('php C:\Users\Minosuko\Desktop\vm\vm\-dev\mncc-1.1.4\res\mkbios.php C:\Users\Minosuko\Desktop\vm\vm\-dev\MNC\ram_bomb.mnc ./RB');
$a = base64_encode(file_get_contents("RB"));
$c = "SET EXEC = \"$a\"\nB64D EXEC\nFILEWRITE \"BIN::RB\" CONTENTS \"VAR:EXEC:\"";
unlink('RB');
file_put_contents("ram_bomb.mnc", $c);
exec('php C:\Users\Minosuko\Desktop\vm\vm\-dev\mncc-1.1.4\res\mkbios.php C:\Users\Minosuko\Desktop\vm\vm\-dev\MNC\ram_bomb.mnc ../../EXEC');
?>