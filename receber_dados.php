<?php
//iniciaondo a sessao
session_start();

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

        $urlImagem = verificar_entrada($_POST['urlImagem']);

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
            $sql->execute();
            $resultado = $sql->get_result();
            $linha = $resultado->fetch_array(MYSQLI_ASSOC);

            
            if($linha['nomeDoUsuario'] == $nomeDoUsuario){
                echo "<p class='text-danger'>Usuario indisponivel </p>";

            }elseif ($linha['emailUsuario'] == $emailUsuario){
                echo "<p class='text-danger'>e-mail indisponivel </p>";
        }else{
            
            $sql = $connect->prepare("INSERT into usuario (nomeDoUsuario,
            nomeCompleto, emailUsuario, urlImagem, senhaDoUsuario, dataCriado)
            values(?, ?, ?, ?, ?, ?)");
            $sql->bind_param("ssssss", $nomeDoUsuario, $nomeCompleto, 
            $emailUsuario, $urlImagem, $senhaCodificada, $dataCriado);
            if($sql->execute()){
                echo"<p class='text-success'> Usuario Cadastrado</p>";
            }else{
                echo"<p class='text-danger'> Usuario nao cadastrado </p>";
                echo"<p class='text-danger'> Algo deu Muito errado </p>";
                }
            }
        }

    } else if ($_POST['action'] == 'login') {
        
        $nomeUsuario = verificar_entrada($_POST['nomeUsuario']);
        $senhaUsuario = verificar_entrada($_POST['senhaUsuario']);
        $senha = sha1($senhaUsuario); //senha codificada
        $sql = $connect->prepare("SELECT * FROM usuario WHERE senhaDoUsuario = ?
        AND nomeDoUsuario = ?");
        $sql->bind_param("ss", $senha, $nomeUsuario);
        $sql ->execute();
        $busca = $sql->fetch();

        if($busca != null ){
            $_SESSION['nomeDoUsuario']= $nomeUsuario;
            
            if(!empty($_POST['lembrar'])){
                
                setcookie("nomeDoUsuario",$nomeUsuario, time()+(60*60*24*30));
                setcookie("senhaDoUsuario", $senhaUsuario, time()+(60*60*24*30));
            }else {
                
                setcookie("nomeDoUsuario", "");
                setcookie("senhaDoUsuario", "");
                
            }
            
            echo"ok";
            
        }else {
            echo"<p class='text-danger'>";
            echo "Falhou a entrada no Sistema. Nome de Usuáruo 
            ou Senha Invalidos </p>";
            exit();

        }


    } else if ($_POST['action'] == 'senha') {
        $email = verificar_entrada($_POST['emailGerarSenha']);
        $sql = $connect->prepare("SELECT idUsuario FROM usuario WHERE emailUsuario = ?");
        $sql->bind_param("s", $email);
        $sql->execute();
        $resposta = $sql->get_result();
        if($resposta->num_rows > 0 ){
            $frase = "novasentaoamigosdobrausuledomundobatatadoceeamigosnochurrascocomchimarraoeamigosolohaaanovasentaofamilia";
            $palavra_secreta = str_shuffle($frase);
            $token = substr($palavra_secreta,0,10);
            
            $sql = $connect->prepare("UPDATE usuario SET token=?, SET token=?,
            tempoDeVida=DATE_ADD(NOW(),INTERVAL 1 MINUTE) WHERE emailUsuario = ?");
            $sql->bind_param("ss", $token, $email);
            $sql->execute();
            echo "Token no banco de dados";

        }else{
            echo "E-mail nao encontrado";
        }
        
    } else {
        header("location:index.php");
    }
    
} else {
    //redirecionando para index.php, negando o acesso
    //a esse arquivo diretamente 
    header("location:index.php");
}