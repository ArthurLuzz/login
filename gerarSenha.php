<?php
    if(isset($_GET['token']) && isset($_GET['email'])){
        require_once 'configBD.php';
        $email = $_GET['email'];
        $token = $_GET['token'];
        //$msg = "$email : $token";
        //gerarSenha.php?token=123456&email=aaa@bbb
        
        $sql = $connect->prepare("SELECT * FROM usuario WHERE emailUsuario=? AND token=? AND tempoDeVida > NOW()");
        $sql->bind_param("ss", $email, $token);
        $sql->execute();

        $resultado = $sql->get_result();
        if($resultado->num_rows > 0){
            if(isset($_POST['gerar'])){
                $nova_senha = sha1($_POST['senha']);
                $confirmar_senha = sha1($_POST['csenha']);
                if($nova_senha == $confirmar_senha){
                    $sql = $connect->prepare("UPDATE usuario SET senhaDoUsuario=?, token='' WHERE emailUsuario=?");
                    $sql->bind_param("ss", $nova_senha, $email);
                    $sql->execute();
                    $msg = "senha alterada com sucesso";
                }else{
                    $msg = "Senhas não conferem.";
                }
            }

        }else{
            header("location: index.php");
            exit();
        }
    }
?>

<!doctype html>
<html lang="pt-br">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title> Página de criação de Nova Senha</title>
</head>

<body>
    <main class="container">
        <section class="row justify-content-center">
            <div class="col-lg-5 mt-5">
                <h3 class="text-center bg-dark text-light p-2 rounded">
                    Digite a sua nova Senha
                </h3>
                <h4 class="text-success text-center">
                    <?= @$msg?>
                </h4> 

                <form action="" method="post">
                    <div class="form-group">
                        <label for="senha">Nova Senha</label>
                        <input type="password" name="senha" id="senha"
                        class="form-control" placeholder="Nova Senha" required>
                    </div>

                    <div class="form-group">
                        <label for="csenha">Confirmar a Nova Senha</label>
                        <input type="password" name="csenha" id="csenha" class="form-control" placeholder="Confirmar Nova Senha" required>

                    </div>

                    <div class="form-group">
                        
                        <input type="submit" value="Criar uma nova senha "  name="gerar" 
                        class="btn btn-block btn-primary">

                    </div>

                </form>

            </div>
            
        </section>
    </main>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>