<?php
    class Model extends Application
    {
    	public $db;
    	private $host;
    	private $user;
    	private $pass;
    	private $base;

        public $table;


        /**
        * Define os dados de acesso a base de dados
        */
        public function __construct()
        {
            if(ENVIRONMENT == "dev"){
                $this->host = "host";
                $this->user = "user";
                $this->pass = "pass";
                $this->base = "database";
            }
            elseif (ENVIRONMENT == "prod") {
                $this->host = "host";
                $this->user = "user";
                $this->pass = "pass";
                $this->base = "database";
            }

            /**
            * Inicia uma conexão a base com o PDO
            */
            $this->db= new PDO("mysql:host={$this->host}; dbname={$this->base}", $this->user, $this->pass);
            return $this->db;
        }

        /**
        * Replace para evitar SQL Injection
        */
        public function anti_injection($str)
        {
            $_return = preg_replace("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/","",$str);
            $_return = trim($_return);
            $_return = strip_tags($_return);
            $_return = addslashes($_return);
            //$_return = mysql_real_escape_string($_return);
            return $_return;
        }    	


        /**
        * Insert
        * @param array $data Array que contem os campos e valores a serem inseridos
        */
        public function insert($table, Array $data)
        {
            foreach ($data as $item) {
                $fields[] = $item['field']; // Campo
                $values[] = $item['value']; // Valor
                $key[] = $item['key'];      // Campo key para o Prepare PDO
            }

            /**
            * Converte o array dos campos e campos keys para string
            */
            $fields_str = implode(", ", $fields);
            $keys_str = implode(", ", $key);

            /**
            * monta a query SQL, usando os campos keys para PDO
            */
            $sql = "INSERT INTO `{$table}` ({$fields_str}) VALUES({$keys_str})";

            $sql_prepare = $this->db->prepare($sql);

            /**
            * Monta o array com os campos keys e seus valores
            */
            $security_vars = array();
            foreach ($data as $item) {
                $security_vars[$item['key']] = $item['value'];
            } 

            if ($sql_prepare->execute($security_vars)) {
                $_return = array(
                    'status' => true,
                    'message' => 'Cadastro criado com sucesso.',
                );
            }
            else {
                $_return = array(
                    'status' => false,
                    'message' => 'Falha ao criar cadastro.',
                );
            }

            return $_return;            
        }


        /**
        * Select. Retorna um array com os campos selecinados
        * @param string $table Tabela
        * @param string $alias Alias para tabela
        * @param string $join Tabela de join
        * @param array $fields Campos que serão selecionados
        * @param array $_where Condições para o where
        * @param string $param Parametros extras: Limit, Order, etc
        * @return array Resultado da consulta
        */
        public function select(
            $table = null,
            $alias=null,
            $join=null,
            Array $fields=null,
            Array $_where=null,
            $param=null
        )
        {
            /**
            * Verifica se foi definido os campos da tabela.
            * Se não foi, irá return todos os campos.
            */
            if (!empty($fields)) {
               $fields = implode(",", $fields);
            } else{
                $fields = "*";
            }


            /**
            * Verifica se existe condição WHERE
            */
            if (!empty($_where)) {
                $where = "WHERE "
                . $_where[0]['field']
                . $_where[0]['operation']
                . $_where[0]['security_var'];

                /**
                * Verifica se existem mais de uma condição para WHERE
                */
                if (count($_where) > 1) {                   
                   foreach ($_where as $key => $value) {
                       if ($key > 0) {
                           $where .= " AND "
                           . $_where[$key]['field']
                           . " "
                           . $_where[$key]['operation']
                           . $_where[$key]['security_var'];
                       }
                   }
                }  

            } else{
                $where = null;
            }        
            

            $sql = "SELECT {$fields} FROM {$table} {$join} {$where} {$param}";
            $sql_prepare = $this->db->prepare($sql);

            if (!empty($_where)) {
                foreach ($_where as $key => $value) {
                    $sql_prepare->bindParam(
                            ":" . $value['security_var'],
                            $value['value'][0],
                            $value['value'][1] 
                    );
                }
            }
            $sql_prepare->execute();
            $sql_prepare->setFetchMode(PDO::FETCH_ASSOC);
            $result = $sql_prepare->fetchAll();
            return $result; 
        }


        /**
        * Update
        * @param String $table Tabela
        * @param array $data Array que contem os campos e valores a serem inseridos
        * @param array $_where Condições para o where
        * @return array Com dois parametros. ['status'] => Boolean. ['message']=> String
        */
        public function update($table, Array $data, array $_where = null)
        {
            foreach ($data as $item) {
                $fields[] = $item['field']; // Campo
                $values[] = $item['value']; // Valor
                $key[] = $item['key'];      // Campo key para o Prepare PDO
            }

            /**
            * Junta os fields com os keys para formar o SET
            */
            $set = "";
            for ($x=0; $x < count($fields); $x++) {
                if ($x < count($fields)-1)
                    $set .= $fields[$x] . " = " . $key[$x] . ", ";
                else
                    $set .= $fields[$x] . " = " . $key[$x]; 
            }
            
            /**
            * Verifica se existe condição WHERE
            */
            if (!empty($_where)) {
                $where = "WHERE "
                . $_where[0]['field']
                . $_where[0]['operation']
                . $_where[0]['security_var'];

                /**
                * Verifica se existem mais de uma condição para WHERE
                */
                if (count($_where) > 1) {                   
                   foreach ($_where as $key => $value) {
                       if ($key > 0) {
                           $where .= " AND "
                           . $_where[$key]['field']
                           . " "
                           . $_where[$key]['operation']
                           . $_where[$key]['security_var'];
                       }
                   }
                }  

            } else{
                $where = null;
            }

            /**
            * monta a query SQL, usando os campos keys para PDO
            */
            $sql = "UPDATE {$table} SET {$set} {$where}";
            $sql_prepare = $this->db->prepare($sql);

            /**
            * Monta o array com as variaveis de segurança para o SET
            */
            $security_vars = array();
            foreach ($data as $item) {
                $security_vars[$item['key']] = $item['value'];
            }

            /**
            * Montao o array com as variaveis de segurança para o WHERE
            */
            foreach ($_where as $item) {
                $security_vars[":".$item['security_var']] = $item['value'][0];
            }
            
            try {
                $sql_prepare->execute($security_vars);
                $_return = array(
                    'status' => true,
                    'message' => 'Dados alterados com sucesso.',
                );
            } catch (Exception $e) {
                print $e;
                $_return = array(
                    'status' => false,
                    'message' => 'Falha ao alterar dados.',
                );
            }
            return $_return;            
        }


        /**
        * Delete       
        * @param Array $_where Contem o campo para condição WHERE
        * @return array Com dois parametros. ['status'] => Boolean. ['message']=> String
        */
        public function delete($_where)
        {
            /**
            * Verifica se existe condição WHERE
            */
            if (!empty($_where)) {
                $where = "WHERE "
                . $_where[0]['field']
                . $_where[0]['operation']
                . $_where[0]['security_var'];

                /**
                * Verifica se existem mais de uma condição para WHERE
                */
                if (count($_where) > 1) {                   
                   foreach ($_where as $key => $value) {
                       if ($key > 0) {
                           $where .= " AND "
                           . $_where[$key]['field']
                           . " "
                           . $_where[$key]['operation']
                           . $_where[$key]['security_var'];
                       }
                   }
                }  

            } else{
                $where = null;
            }

            /**
            * monta a query SQL, usando os campos keys para PDO
            */
            $sql = "DELETE FROM {$this->table} {$where}";
            $sql_prepare = $this->db->prepare($sql);

            /**
            * Montao o array com as variaveis de segurança para o WHERE
            */
            foreach ($_where as $item) {
                $security_vars[":".$item['security_var']] = $item['value'][0];
            }

            try {
                $sql_prepare->execute($security_vars);
                $_return = array(
                    'status' => true,
                    'message' => 'Registro deletado com sucesso.',
                );
            } catch (Exception $e) {
                print $e;
                $_return = array(
                    'status' => false,
                    'message' => 'Falha ao deletar Registro.',
                );
            }
            return $_return;
        }


    }