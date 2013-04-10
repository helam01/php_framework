<?php
	class User extends Controller
	{
		public function indexAction()
		{
			$this->view('user','index');			
		}

		public function addAction()
		{
			$this->view('user','add');			
		}

	}
?>