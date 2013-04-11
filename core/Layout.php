<?php

class Layout extends System
{

	private $application;
	private $action;

	public function __construct($application=null, $action=null)
	{
		$this->application = $application;
		$this->action= $action;
		parent:: __construct();
	}

	public function displayLayout()
	{
		$application = $this->application;
		$action = $this->action;

		if (!System::$layout) {
			return $application->$action();
		}
		else{
			require LAYOUT . "layout.phtml";
			//$application->$action();
		}
	}

	private function displayContent()
	{
		$application = $this->application;
		$action = $this->action;
		return $application->$action();
	}
}