<?php
    class Controller extends System
    {
    	/**
    	*  $vars Array que passará as variáveis para o view
    	*/
    	protected $vars = Array();
    	
    	/**
    	* $controller Para definir o controller em execução
    	*/
    	protected $controller = null;

    	/**
    	* Executa o __construct da classe System para setar as variaves
    	* de parametros
    	*/
    	public function __construct()
    	{
    		parent:: __construct();
    	}


        /**
        * Função para ser executada antes de chamar o view.
        * Metodo comum para todos os views.
        */
        public function init(){}


    	/**
    	* Função que faz a requizição o arquivo .phtml do view
    	* @return string Retorna o conteúdo do view .phtml
    	*/
    	protected function view($view)
    	{    		
    		return require(APPLICATION_PATH . '/view/' . $this->controller . '/' . $view . '.phtml');
    	}
    }