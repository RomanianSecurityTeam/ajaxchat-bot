<?php

function is_iterable($var) {
	set_error_handler(function ($errno, $errstr, $errfile, $errline, array $errcontext)
	{
		throw new \ErrorException($errstr, null, $errno, $errfile, $errline);
	});

	try {
		foreach ($var as $v) {
			break;
		}
	} catch (\ErrorException $e) {
		restore_error_handler();
		return false;
	}
	restore_error_handler();
	return true;
}

function many($closure, $num){
	$num = str_replace(['una', 'doua', 'trei', 'patru', 'cinci', 'sase', 'sapte', 'opt', 'noua', 'zece', 'o'], [1,2,3,4,5,6,7,8,9,10,1], $num);
	for($i=1; $i<=$num; $i++){
		$closure();
	}

	return $num;
}