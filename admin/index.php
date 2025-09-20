<?php
    include_once __DIR__ . '/../config/db.php';

    session_start();

    // verifica auth na pág de login;
    if(isset($_SESSION['login'])){
        header("Location: dashboard.php");
        exit;
    }
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
    
    <!-- TELA DE LOGIN -->
    <?php include_once 'modulos-admin/login/index.php'; ?>
    <!-- TELA DE LOGIN -->

    
    

    <!--BOOTSTRAP JS-->
    <script src="<?= $base_url; ?>assets/utils/bootstrap/bootstrap.js"></script>
        
</body>
</html>