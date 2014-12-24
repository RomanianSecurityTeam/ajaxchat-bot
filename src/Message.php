<?php namespace Bot;

class Message {
	/**
	 * @var string
	 */
	public $user;

	/**
	 * @var string
	 */
	private $text;

	/**
	 * @var int
	 */
	private $id;

	public function __construct($messageXml){
		$this->user = $messageXml->username->__toString();
		$this->text = $messageXml->text->__toString();
		$this->id = $messageXml['id']->__toString();
	}

	public function match($regex, &$matches = null){
		return preg_match("/{$regex}/i", $this->getText(), $matches);
	}

	/**
	 * @return string
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * @return string
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function writtenBy($user){
		return $this->getUser() == $user;
	}
}