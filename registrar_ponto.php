<?php

session_start(); //Iniciar a sessao

//Limpar o buffer
ob_start();

//Definir um fuso horario padrao
date_default_timezone_set('America/Sao_Paulo');

//Gerar com PHP o horario atual
$horario_atual = date("H:i:s");
//var_dump($horario_atual);

//Gerar a data com PHP no formato que deve ser salvo no BD
$data_entrada = date('Y/m/d');

//incluir a conexao com o BD
include_once "conexao.php";

//ID do usuario fixo para testar
$id_usuario = 1;

//Recuperar o ultimo ponto do usuario
$query_ponto = "SELECT id, saida_intervalo, retorno_intervalo, saida
                FROM ponto
                WHERE usuario_id = :usuario_id
                ORDER BY id DESC
                LIMIT 1";

//Preparar a Query
$result_ponto = $conn -> prepare($query_ponto);

//Substituir o link da query pelo valor
$result_ponto ->bindParam(':usuario_id', $id_usuario);

//Executar a Query
$result_ponto ->execute();

//verifica se encontrou algum registro no BD
if(($result_ponto) and ($result_ponto->rowCount() != 0)){
    //Realizar a leitura do registro
    $row_ponto = $result_ponto->fetch(PDO::FETCH_ASSOC);
    //var_dump($row_ponto);
    //Extrair para inserir através do nome da chave do array
    extract($row_ponto);
    
    //Verifica se o usuario bateu o ponto de saida para o intervalo
    if(($saida_intervalo == "") or ($saida_intervalo == null)){
        //Coluna que deve receber o valor
        $col_tipo_registro = "saida_intervalo";

        //tipo de registro
        $tipo_registro = "editar";

        //Texto parcial que deve ser apresentado ao usuario
        $text_tipo_registro = "saida intervalo";
    }elseif (($retorno_intervalo == "") or ($retorno_intervalo == null)) { // Verificar se o usuario bateu o ponto de retorno do intervalo
        // Coluna que deve receber o valor
        $col_tipo_registro = "retorno_intervalo";

        // Tipo de registro
        $tipo_registro = "editar";

        // Texto parcial que deve ser apresentado para o usuario
        $text_tipo_registro = "retorno do intervalo";
    } elseif (($saida == "") or ($saida == null)) { // Verificar se o usuario bateu o ponto de saida
        // Coluna que deve receber o valor
        $col_tipo_registro = "saida";

        // Tipo de registro
        $tipo_registro = "editar";

        // Texto parcial que deve ser apresentado para o usuario
        $text_tipo_registro = "saída";
    } else { // Criar novo registro no BD com o horrario de entrada
        // Tipo de registro
        $tipo_registro = "entrada";

        // Texto parcial que deve ser apresentado para o usuario
        $text_tipo_registro = "entrada";
    }
}else{
    //echo "Nenhuma ponto encontrado!<br>";
     // Tipo de registro
     $tipo_registro = "entrada";

     // Texto parcial que deve ser apresentado para o usuario
     $text_tipo_registro = "entrada";
}
//Verificar o tipo de registro, novo ponto ou editar registro existe
switch($tipo_registro){
    //Acessa os case qdo deve editar o registro
    case "editar":
        //Query para editar no BD
        $query_horario = "UPDATE ponto SET $col_tipo_registro = :horario_atual
                WHERE id = :id
                LIMIT 1";
        //Preparar a query
        $cad_horario = $conn ->prepare($query_horario);

        //substituir o link da Query pelo valor
        $cad_horario ->bindParam(':horario_atual',$horario_atual);
        $cad_horario ->bindParam(':id', $id);
        break;
        default:
            $query_horario = "INSERT INTO ponto (data_entrada, entrada, usuario_id) VALUES(:data_entrada, :entrada, :usuario_id)";
        
            //Preparar a Query
            $cad_horario = $conn ->prepare($query_horario);
            //Substituir o link da Query pelo valor
            $cad_horario ->bindParam(':data_entrada', $data_entrada);
            $cad_horario ->bindParam(':entrada', $horario_atual);
            $cad_horario ->bindParam(':usuario_id', $usuario_id);
        break;
    }
//Executar a Query
$cad_horario -> execute();

//Acessa o IF quando cadastrar com sucesso
if($cad_horario ->rowCount()){
    $_SESSION['msg'] = "<p style ='color: green;'>Horario de $text_tipo_registro cadastrado com sucesso!</p>";
    header("Location: index.php");
}else{
    $_SESSION['msg'] = "<p style ='color: #f00;'>Horario de $text_tipo_registro não cadastrado com sucesso!</p>";
    header("Location: index.php");
}
