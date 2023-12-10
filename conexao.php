<?php

    //inicio da conexão com o BD utilizando PDO
    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "registrar";
    //$port = 3306; //não é necessario

    try {
        //conexão com a porta
        //$conn = new PDO("mysql:host=$host;port=$port;dbname, $user, $pass);

        //Conexão sem a porta
        $conn = new PDO("mysql:host=$host;dbname=" . $dbname, $user, $pass);
        //echo "Conexão com o BD realizado com sucesso.";
    } catch (PDOException $err) {
        echo "Erro: Conexão com o BD não realizado com sucesso. Erro gerado" . $err->getMessage();
    }
    //Fim da conexao com o BD utilizando PDO
