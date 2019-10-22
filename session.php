<?php
session_start();
require_once "configBD.php";

if(isset($_SESSION['nomeDoUsuario'])){
    //logado
}else{
    //se nao esta logado, redirecionar para index.php
    header("location: index.php");
}
