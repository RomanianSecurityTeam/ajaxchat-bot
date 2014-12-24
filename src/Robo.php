<?php namespace Bot;

use Ionut\Curl;

class Robo {
	/**
	 * @var array
	 */
	public $credentials = ['user', 'pass'];

	/**
	 * @var Curl
	 */
	protected $http;

	protected $loop = true;

	protected $loops = 0;

	protected $owner = 'eusimplu';


	function __construct($credentials)
	{
		$this->credentials = $credentials;

		$this->http  = new Curl\Manager;
		$this->http->setPrefix('https://rstforums.com/');
//		$this->http->setLog(__DIR__.'/../curl.log');

		$this->state = new State;
	}

	public function loop_messages(){
		$lastID = 0;
		while($this->loop){
			$response = $this->http->get('chat', ['ajax' => true, 'channelName' => $this->state->channel])->xml();
			if(!is_object($response) || !is_iterable($response->messages->message)){
				$this->login();
				sleep(2);
				continue;
			}

			$this->users = $response->users->user;
			$this->state->setState($response);
			foreach($response->messages->message as $message){
				if($message['id'] <= $lastID) continue;

				$message = new Message($message);

				if($this->loops > 0){
					if( ! $message->writtenBy($this->getUser())){
						$this->messageReceived($message);
					}
				}

				$lastID = $message->getId();
			}

			$this->loops++;
			usleep(500000);
		}
	}

	public function messageReceived(Message $message){

		if($this->state->lastResponseBy() == $this->getUser() && $message->getUser() != $this->owner){
			echo "Bulangiul de $message->user instiga la spam.".PHP_EOL;
			return;
		}

		if(preg_match('#/privmsg#', $message->getText())){
			return $this->write("Bulangiul de $message->user da mesaje private.", $message->getUser());
		}

		if($this->state->sleep && $message->writtenBy($this->owner)){
			echo 'Scuze, dorm.';
			return;
		}

		if($message->match('hacker')){
			return $this->write("E greu sa fii hacher.");
		}

		$called = $message->match('robo');
		if($called) {
			$this->state->lastCaller = $message->getUser();

			if ($message->match('saluta gagica')){
				return $this->write('buna gagica!');
			}

			if($message->match('omoara tiganu')){
				for($i=0; $i<10; $i++){
					$this->write('/msg CM3D TE OMOR TIGANE');
				}
				return;
			}

			if ($message->match('plangi')){
				return $this->write(':((((((');
			}

			if ($message->match('ruleta')){
				return $this->ruleta();
			}

			if($message->match('\\/') && $message->writtenBy($this->owner)){
				return $this->write("E greu sa fii hacher.");
			}

			if($message->writtenBy($this->owner)){
				if($message->match('who is your daddy')){
					return $this->write('eusimplu is my daddy');
				}

				if($message->match('culcarea')){
					$this->logout();
					exit;
				}

				if($message->match('fa ([a-z]+) injuratur', $m)){
					return many(function(){ $this->write($this->genereazaInjuratura()); }, $m[1]);
				}

				if($message->match('pauza')){
					$this->state->sleep = true;
					return $this->write("Astept sa fiu chemat inapoi.");
				}

				if($message->match('trezirea')){
					$this->state->sleep = false;
					return $this->write("Am fost chemat inpoi.");
				}

				if($message->match('spune (.+)', $m)){
					return $this->write($m[1]);
				}

				if($message->match('taci')){
					$this->write("Iau pauza 30 de secunde. Imi pare rau $message->getUser().", $message->getUser());
					sleep(30);
					return;
				}

				if($message->match('pleaca')){
					$this->write("https://www.youtube.com/watch?v=0RLH6m89DLs");
					$this->switchChannel();
					sleep(30);
					$this->switchChannel();
					$this->write('Nu scapi ma de mine!');
					return;
				}

				if($message->match('love you')){
					return $this->write('me too');
				}

			}

			if($message->match('cere scuze')){
				return $this->write("Pare rau ".$this->state->lastMessageTo);
			}


			if($message->match('(injura-l pe|injura) (.+)', $m)){
				return $this->injuratura($m[2]);
			}

			if($message->match('muieste(?:\-l|)(?: pe|) (.+)', $m)){
				return $this->write($m[1].' '.$this->genereazaInjuratura(), $m[1]);
			}


			if($message->match('(suge|sugi|coaie|pula|muie|pizda)')) {
				$this->write("/votekick $message->getUser()");

				return $this->write("Asa sa vorbesti cu ma-ta $message->getUser()", $message->getUser());
			}
		}

		if($message->writtenBy('RST')){
			if(preg_match('/channelLeave eusimplu/', $message->getText())){
				$this->write('Unde pleci ma fara mine?');
				$this->switchChannel();
			}
		}

		if($message->match('reckon')){
			return $this->write("Reckon e un havijar");
		}

		if($message->match('scoala')){
			return $this->write("Sa se duca ma-ta la scoala.");
		}

		if($message->match($this->owner) && !$message->writtenBy('RST')){
			if( ! $this->state->userIsOnline($this->owner)){
				return $this->write("De ce il vorbiti pe $this->owner pe la spate ma golanilor?");
			}
		}


		if($called){
			return $this->write("Nu inteleg, {$message->getUser()}", $message->getUser());
		}
	}

	public function ruleta(){
		$users = [];
		foreach($this->state->users as $user){
			if(!in_array($user->__toString(), [$this->owner, $this->getUser()])){
				$users[] = $user->__toString();
			}
		}
		$user = $users[array_rand($users)];
		return $this->write($user.' '.$this->genereazaInjuratura(), $user);
	}

	public function injuratura($user){
		static $injuraturi;
		if(!$injuraturi){
			$injuraturi = file(__DIR__.'/data/injuraturi.txt');
		}

		$injuratura = $injuraturi[array_rand($injuraturi)];
		return $this->write($user.' '.strtolower($injuratura), $user);
	}

	public function genereazaInjuratura(){
		$words = [
			'sa-ti' => [
				'bagi', 'bag', 'fut', 'futi', 'distrug', 'distrugi', 'arunci', 'rupi', 'arunc', 'rup',
				'indesi', 'indes', 'faci o laba cu'
			],
			'' => [
				'pula', 'destu', 'mana', 'piciorul', 'sora', 'nevasta', 'burta', 'paru din nas', 'laptopu',
				'codu binar', 'limba', 'procesorul', 'calculatorul', 'sticla', 'pula', 'pula', 'havij-ul'
			],
			'in' => [
				'cap', 'cur', 'pula', 'pizda', 'nas', 'urche', 'mana', 'picior', 'sora', 'nevasta', 'burta', 'paru din nas', 'gura',
				'gaoaza', 'limba', 'tate', 'calculator', 'priza', 'gura'
			]
		];

		$str = '';
		foreach($words as $k => $v){
			$str .= $k.' '.$words[$k][array_rand($v)].' ';
		}
		return $str;
	}

	public function switchChannel(){
		$this->loops = 0;
		$this->state->channel = $this->state->channel == 'RST' ? 'RSTech' : 'RST';
		return;
	}

	public function write($text, $user = null){
		$this->state->lastMessageTo = $user;
		echo $text.PHP_EOL;
		return $this->http->post('chat/?ajax=true', ['text' => $text])->exec();
	}

	public function login()
	{
		$data = [
				's' => '',
				'cookieuser' => 1,
				'do' => 'login',
				'url' => 'https://rstforums.com/chat/?channelName='.$this->state->channel,
				'vb_login_username' => $this->getUser(),
				'vb_login_password' => $this->credentials[1],
				'channelName' => $this->state->channel
			];
		file_put_contents('test', $this->http->post('forum/login.php?do=login', $data)->exec());
		$this->http->get('chat/?channelName='.$this->state->channel)->exec();
	}

	public function logout()
	{
		$this->write('/quit');
		@unlink('cookie');
	}

	public function getUser(){
		return $this->credentials[0];
	}
}
