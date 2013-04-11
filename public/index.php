<?php

    /**
    * Arquivo Index.
    * Este é o arquivo principal, onde são incluidos os outros arquivos
    * de configuração para o funcionamento do framework.
    * Onde tambem são definedas as constantes para os caminhos das pastas
    */

    
    //Inicia a session principal
    session_start("app");

    
    /**
    * Define as principais constantes
    */
    //Define o caminho para Application
    define('APPLICATION_PATH', $_SERVER{'DOCUMENT_ROOT'} . "/application");

    //Define o caminho para Application
    define('CORE_PATH', $_SERVER{'DOCUMENT_ROOT'} . "/core");

    //Define o caminho para Lib
    define('LIB_PATH', $_SERVER{'DOCUMENT_ROOT'} . "/lib");

    //Define the stantard path to controllers
    define('CONTROLLER', APPLICATION_PATH . '/controller/');
    
    //Define the stantard path to models
    define('MODEL', APPLICATION_PATH . '/model/');

    //Define the stantard path to models
    define('LAYOUT', APPLICATION_PATH . '/layout/');

    //Define the stantard path to models
    define('HELPER', LIB_PATH . '/helpers/');

    //Define o ambiente
    $server = explode(".", $_SERVER['SERVER_NAME']);
    if(end($server) == "local") {
        define('ENVIRONMENT', 'dev');
    } else {
        define('ENVIRONMENT', 'prod');
    }

    //Requere os arquivos
    require CORE_PATH . "/Application.php";
    require CORE_PATH . "/System.php";
    require CORE_PATH . "/Controller.php";
    require CORE_PATH . "/Model.php";
    require CORE_PATH . "/Layout.php";
    require CORE_PATH . "/Helper.php";

    /**
    * Função de auto load para requerir os models
    */
    function __autoload($file)
    {
        if(file_exists(MODEL . $file . ".php")){
            require MODEL . $file . ".php";
        } elseif (file_exists(HELPER . $file . ".php")) {
            require HELPER . $file . ".php";
        }
    }

    /**
    * Inicia a classe System, a aplicação
    */
    $bootstrap = new System();
    $bootstrap->run();
?>
