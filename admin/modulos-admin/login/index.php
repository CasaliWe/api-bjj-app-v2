<style>
    #container-login{
        height: 100vh;
        width: 100%;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #container-login #container-content{
        background-color: rgba(0, 0, 0, 0.374);
        width: 35%;
        border-radius: 15px;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        padding: 50px 50px;
        color: white;
    }

    #logo-login{
        width: 70%;
        margin-bottom: 25px;
    }



    @media(min-width:1500px){
        #container-login #container-content{
            width: 25%;
            padding: 50px 50px;
        }

        #logo-login{
            width: 70%;
            margin-bottom: 25px;
        }
    }
    @media(max-width:992px){
        #container-login #container-content{
            width: 90%;
            padding: 40px 20px;
        }
    }
</style>



<main id="container-login" style="background-image: url(<?php echo $base_url ?>assets/imagens/site-admin/banner-login.webp);">
    <form onsubmit="loading()" action="<?= $base_url; ?>modulos-admin/login/php/logar.php" method="post" id="container-content" class="border-login">
        <img src="<?php echo $base_url ?>assets/imagens/site-admin/logo.png" id="logo-login">
        <p class="mb-4">PAINEL ADMIN</p>
        <input type="text" autofocus required class="w-75 mb-2 form-control text-center" placeholder="Login" name="login">
        <input type="password" required class="w-75 mb-2 form-control text-center" placeholder="Senha" name="senha">
        <button type="submit" class="btn btn-principal text-dark">ENTRAR</button>

        <?php if(isset($_SESSION['erro-login'])){ ?>
            <h6 class="mt-3 fw-semibold text-danger">Verifique os dados!</h6>
        <?php } ?>
    </form>
</main>



<?php 
    // removendo sessão
    unset($_SESSION['erro-login']);  
    exit;
?>

    