<?php
//Teste se existe a ação
require_once 'configBD.php';

function verificar_entrada($entrada){
    //filtrando a entrada
    $saida = htmlspecialchars($entrada);
    $saida = stripslashes($saida);
    $saida = trim($saida);
    return $saida; //retorna a saida limpa
}


if (isset($_POST['action'])) {
    //Teste se ação é igual a cadastro
    if ($_POST['action'] == 'cadastro'){
        //Teste se ação é igual a cadastro
        # echo "\n<p>cadastro</p>";
        # echo "\n<pre>";
        # print_r($_POST);
        # echo "\n</pre";
        $nomeCompleto = verificar_entrada ($_POST['nomeCompleto']);
        $nomeDoUsuario = verificar_entrada ($_POST['nomeDoUsuario']);
        $emailUsuario = verificar_entrada ($_POST['emailUsuario']);
        $senhaDoUsuario = verificar_entrada ($_POST['senhaDoUsuario']);
        $senhaUsuarioConfirmar = verificar_entrada ($_POST['senhaUsuarioConfirmar']);

        $dataCriado = date("Y-m-d"); //data atual no formato banco de dados

        //codificando as senhas
        $senhaCodificada = sha1($senhaDoUsuario);
        $senhaConfirmarCod = sha1($senhaUsuarioConfirmar);
        //teste de captura de dados
        // echo "<p>Nome Completo: $nomeCompleto </p>";
        // echo "<p>Nome de Usuário: $nomeDoUsuario </p>";
        // echo "<p> E-mail : $emailUsuario </p>";
        // echo "<p>Senha : $senhaCodificada </p>";
        // echo "<p>Data de Criação : $dataCriado </p>";
        if($senhaCodificada != $senhaConfirmarCod){
            echo "<p class='text-danger' >Senhas não conferem.</p>";
            exit;
        }else{
            //as senhas conferem, verifica se o usuario ja existe no banco de dados
            $sql = $connect->prepare("SELECT nomeDoUsuario, emailUsuario 
            FROM usuario WHERE nomeDoUsuario = ? OR emailUsuario = ?");
            $sql->bind_param("ss", $nomeDoUsuario, $emailUsuario);
            $slq->execute();
            $resultado = $sql->get_result();
            $linha = $resultado->fetch_array(MYSQL_ASSOC);
        }


    } else if ($_POST['action'] == 'login') {
        //Senão, teste se ação é login
        echo "\n<p>login</p>";
        echo "\n<pre>";
        print_r($_POST);
        echo "\n</pre";

    } else if ($_POST['action'] == 'senha') {
        //Senão,teste se ação é recuperar senha
        echo "\n<p>senha</p>";
        echo "\n<pre>";
        print_r($_POST);
        echo "\n</pre";

    } else {
        header("location:index.php");
    }
    
} else {
    //redirecionando para index.php, negando o acesso
    //a esse arquivo diretamente 
    header("location:index.php");
}
