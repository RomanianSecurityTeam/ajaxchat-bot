<?php namespace Bot;

class State {
	public $messages;
	public $users;
	public $response;
	public $channel = 'RSTech';
	public $lastMessageTo = 'undefined';
	public $lastCaller;
	public $sleep = false;

	public function userIsOnline($who){
		foreach($this->users as $user){
			if($user->__toString() == $who){
				return true;
			}
		}

		return false;
	}

	public function lastResponseBy(){
		foreach($this->messages as $message){}

		return $message->username->__toString();
	}

	public function setState($response){
		$this->response = $response;
		$this->messages = $this->response->messages->message;
		$this->users    = $this->response->users->user;
	}
} 