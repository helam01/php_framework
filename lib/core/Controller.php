<?php
	class Controller
	{
		protected function view($controller, $view)
		{
			return require('app/view/'.$controller.'/'.$view.'.phtml');
		}
	}