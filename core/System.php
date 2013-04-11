<?php
	/**
	*
	*/
	class System
	{
		/**
		* Declara os principaid atributos
		*/
		private $_url;
		private $_separetor;
		public $_controller;
		public $_action;
		public $_params;
		protected $auth = null;
		public static $layout = true;
		private $server_info;

		/**
		* Executa os metodos quando o classe for instanciada
		*/
		public function __construct()
		{
			$this->setUrl();			
			$this->setSeparator();
			$this->setController();
			$this->setAction();			
			$this->setParams();	

			$this->server_info = array('PHP_SELF', 
				'argv', 
				'argc', 
				'GATEWAY_INTERFACE', 
				'SERVER_ADDR', 
				'SERVER_NAME', 
				'SERVER_SOFTWARE', 
				'SERVER_PROTOCOL', 
				'REQUEST_METHOD', 
				'REQUEST_TIME', 
				'REQUEST_TIME_FLOAT', 
				'QUERY_STRING', 
				'DOCUMENT_ROOT', 
				'HTTP_ACCEPT', 
				'HTTP_ACCEPT_CHARSET', 
				'HTTP_ACCEPT_ENCODING', 
				'HTTP_ACCEPT_LANGUAGE', 
				'HTTP_CONNECTION', 
				'HTTP_HOST', 
				'HTTP_REFERER', 
				'HTTP_USER_AGENT', 
				'HTTPS', 
				'REMOTE_ADDR', 
				'REMOTE_HOST', 
				'REMOTE_PORT', 
				'REMOTE_USER', 
				'REDIRECT_REMOTE_USER', 
				'SCRIPT_FILENAME', 
				'SERVER_ADMIN', 
				'SERVER_PORT', 
				'SERVER_SIGNATURE', 
				'PATH_TRANSLATED', 
				'SCRIPT_NAME', 
				'REQUEST_URI', 
				'PHP_AUTH_DIGEST', 
				'PHP_AUTH_USER', 
				'PHP_AUTH_PW', 
				'AUTH_TYPE', 
				'PATH_INFO', 
				'ORIG_PATH_INFO');
		}


		/**
		* Pega o URI e, verifica se possui conteudo,
		* caso não, define o padrão index/index 
		*/
		private function setUrl()
		{
    		$this->_url = ($_SERVER['REQUEST_URI'] != "/" ? $_SERVER['REQUEST_URI'] : "index/index");
		}


		/**
		* Separa a URI por "/", para serem usados como parametros
		*/
		private function setSeparator()
		{
			$separetor = explode('/', $this->_url);
			$separetor[2] = (isset($separetor[2]) && $separetor[2] != null ? $separetor[2] . 'Action' : 'indexAction');
			unset($separetor[0]);
			$this->_separetor = $separetor;
		}


		/**
		* Define o controller pegando o primeiro valor da $this->_separetor
		*/
		private function setController()
		{
			$this->_controller = $this->_separetor[1];
		}


		/**
		* Define a action pegando o segundo valor da $this->_separetor
		*/
		private function setAction()
		{
			$this->_action = ($this->_separetor[2]);
		}


		/**
		* Define os parametros, pegando os demais valores da $this->_separetor
		*/
		private function setParams()
		{
			/**
			* Remove do $this->_separetor os dois primeiros valores
			* referentes ao controller e action, deixando os demais valores
			* que serão usados para formarem os parametros, indices e valores
			*/
			unset($this->_separetor[1], $this->_separetor[2]);
			
			/**
			* Caso o ultimo item do $this->_separetor seja vazio
			* o mesmo é removido
			*/
			if ( end($this->_separetor) == null ) {
				array_pop($this->_separetor);
			}

			
			/**
			* Se a $this->_separetor estivar vazia,
			* então os parametros serão definidos como vazios
			*/
			if ( !empty($this->_separetor) ) {
				/**
				* Percorre o array $this->_separetor, verificando os indices
				* se for impar, então seu valor será o indice do parametro.
				* Se for par, seu valor será o valor do paremetro
				*/
				foreach ($this->_separetor as $key => $value) {
					if ($key % 2 == 0) {
						$param_value[] = $value;
					} else {
						$param_indice[] = $value;
					}				
				}
			} else {
				$param_value = array();
				$param_indice = array();
			}


			/**
			* Verifica se os indices e valores dos parametros
			* não são vazios e possuem a mesma quantidade
			* Então vaz um "array_combine" para juntar os dois arrays
			* formando um indice->valor para o parametro
			*/
			if ( !empty($param_indice) 
				&& !empty($param_value)
				&& count($param_indice)
				== count($param_value)
			) {
				$this->_params = array_combine($param_indice, $param_value);
			} else {
				$this->_params = array();
			}
		}

		/**
		* Função para ser usada nos Controllers
		* retorna o array de parametros
		* @return array
		*/
		public function getParam($param=null)
		{			
			$return = ($param != null) ? $this->_params[$param] : $this->_params ;			
			return $return;
		}

		/**
		* Faz redirecionamento para alguma URL
		*/
		protected function redir($url)
    	{
    		header('Location:' . $url);
    		die();
    	}



		/**
		* Função para iniciar a aplicação.
		* Verifica se os arquivos do controller e action requeridos existes
		*/
		public function run()
		{
			$controller_path = CONTROLLER . $this->_controller . "Controller.php";

			if (!file_exists($controller_path)) {
				die("ERRO 0: Página não encontrada.");
			}

			require $controller_path;

			$controller = ucfirst($this->_controller);
			$action = $this->_action;
			
			/**
			* instancia um objeto do controller
			*/
			$application = new $controller();

			if (!method_exists($application, $action)) {
				die("No Ation");
			}
			
			$layout = new Layout($application, $action);
			$layout->displayLayout();
			/*
			if (self::$layout) {
				$layout = new Layout($application, $action);
				$layout->displayLayout();
			} else{
				$application->$action();
			}
			*/
		}
		

		protected function setAuth($value)
        {
            $this->auth = $value;
        }

        protected function unsetAuth()
        {
            $this->auth = null;
        }


        public function getServer_info(int $index = null)
        {
        	return $this->server_info[$index];
        }

        public function showAllServer_info()
        {
        	foreach ($this->server_info as $arg) {
        		echo $arg . ": " . $_SERVER[$arg] . "\n\r<br>";
        	}
        }
	}