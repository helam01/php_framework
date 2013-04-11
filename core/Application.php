<?php
    class Application
    {
    	protected $auth = null;

    	function __construct()
    	{    		
    		
    	}

    	protected function redir($url)
    	{
    		header('Location:' . $url);
    		die();
    	}
    }