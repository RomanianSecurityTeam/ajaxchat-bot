<?php

$users = explode(',', 'user1:pass,user2:pass');
for($i=0; $i<count($users); $i++){

	echo $users[$i].PHP_EOL;
	$pid = pcntl_fork();
	if(!$pid){
		$robo = new Robo(new Curl());
		$robo->credentials = explode(':', $users[$i]);
		$robo->login();
		for($i=0; $i<10; $i++){
			$robo->write('Eu sunt reckon si sunt un [b]havijar[/b]');
		}
		$robo->logout();
		exit($i);
	}
}

while (pcntl_waitpid(0, $status) != -1) {
	$status = pcntl_wexitstatus($status);
	echo "Child $status completed\n";
}