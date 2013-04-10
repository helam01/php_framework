<?php
	class Index extends Controller
	{
		public function indexAction()
		{
			$this->view('index','index');			
		}

		public function addAction()
		{
			$this->view('index','add');			
		}

	}
?>