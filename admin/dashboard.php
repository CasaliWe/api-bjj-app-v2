<?php
    include_once __DIR__ . '/../config/db.php';
   
   //verifica auth;
   include_once './helpers/verifica-auth.php';

    // //pegando dados do login
    // use Repositories\LoginRepository;
    // $login = LoginRepository::getLogin();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>

    <!-- HEADER -->
    <?php include_once 'modulos-admin/header/index.php'; ?>
    <!-- HEADER -->

</head>
<body>

    <!-- LOADING -->
    <?php include_once 'modulos-admin/loading/index.php'; ?>
    <!-- LOADING -->

    <!-- NAVEGAÇÃO -->
    <?php include_once 'modulos-admin/navegacao/index.php'; ?>
    <!-- NAVEGAÇÃO -->


    <!-- CONTENT -->
    <main id="content-pagina">
        <h5 id="titulo-content-pagina" class="fw-semibold"><?= \Core\RoutesAdmin::getPageTitle(); ?></h5>

        <!-- módulo content página -->
        <?php include_once 'modulos-admin/contents/dashboard/index.php';?>
        <!-- módulo content página -->
    </main>
    <!-- CONTENT -->

    
    <!-- MODAL AVISOS -->
     <?php include_once "modulos-admin/modal-aviso/index.php"; ?>
    <!-- MODAL AVISOS -->


    
    <!-- SWIPER JS -->
    <script src="<?php echo $base_url ?>assets/utils/swiper/swiper.js"></script>
    
    <!--BOOTSTRAP JS-->
    <script src="<?= $base_url; ?>assets/utils/bootstrap/bootstrap.js"></script>
        
</body>
</html>