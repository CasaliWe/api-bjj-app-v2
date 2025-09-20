<?php
    $base_url = $_ENV['BASE_URL'] . 'admin/';
    $title_site = $_ENV['NOME_SITE'] . " | Admin";
    $descri_site = $_ENV['DESCRICAO_SITE_SEO'];
?>


<!-- META TAGS -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta property="og:type" content="website">
<meta property="og:image" content="<?php echo $base_url ?>assets/imagens/favicon-admin/thumb-admin.png">
<meta property="og:image:width" content="310">
<meta property="og:image:height" content="310">
<meta property="og:title" content="<?php echo $title_site ?>">
<meta name="description" content="<?php echo $descri_site ?>">
<meta property="og:description" content="<?php echo $descri_site ?>">
<meta property="og:url" content="<?php echo $base_url ?>">

<!-- FAVICON -->
<link rel="icon" href="<?php echo $base_url ?>assets/imagens/favicon-admin/favicon-admin.png">

<!-- SWIPER -->
<link rel="icon" href="<?php echo $base_url ?>assets/utils/swiper/swiper.css">

<!--FONTAWSOME-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<!--BOOSTRAP-->
<link href="<?= $base_url; ?>assets/utils/bootstrap/bootstrap.css" rel="stylesheet">

<!-- GLOBAL CSS -->
<link rel="stylesheet" href="<?php echo $base_url ?>assets/css-global/style.css">

<!-- BASE URL JS -->
<script>
    var base_url = "<?= $_ENV['BASE_URL'] ?>admin";
</script>

<!-- GLOBAL JS -->
<script src="<?php echo $base_url ?>assets/js-global/app.js"></script>


<title><?= $_ENV['NOME_SITE'] ?> | <?= \Core\RoutesAdmin::getPageTitle(); ?></title>