<?php

if ($argc < 3) {
    die("Uso: php scripts/create_admin_modulo.php nome-da-pagina 'Título do Módulo'\n");
}

$nomePagina = $argv[1]; // Exemplo: dashboard
$tituloModulo = $argv[2]; // Exemplo: "Dashboard"

$caminhoArquivoAdmin = __DIR__ . "/../admin/$nomePagina.php";
$caminhoConteudo = __DIR__ . "/../admin/modulos-admin/contents/$nomePagina/index.php";

// Criar o arquivo PHP na pasta admin
$conteudoAdmin = "<?php
   require 'config/config.php';
   
   //verifica auth;
   include_once './helpers/verifica-auth.php';

    // *** pegando endereco
    // use Repositories\EnderecoRepository;
    // \$endereco = EnderecoRepository::getEndereco();
?>

<!DOCTYPE html>
<html lang=\"pt-br\">
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
    <main id=\"content-pagina\">
        <h5 id=\"titulo-content-pagina\" class=\"fw-semibold\"><?= \Core\RoutesAdmin::getPageTitle(); ?></h5>

        <!-- módulo content página -->
        <?php include_once 'modulos-admin/contents/$nomePagina/index.php';?>
        <!-- módulo content página -->
    </main>
    <!-- CONTENT -->

    <!-- MODAL AVISOS -->
     <?php include_once \"modulos-admin/modal-aviso/index.php\"; ?>
    <!-- MODAL AVISOS -->

    <!-- SWIPER JS -->
    <script src=\"<?php echo \$base_url ?>assets/utils/swiper/swiper.js\"></script>

    <!--BOOTSTRAP JS-->
    <script src=\"<?= \$base_url; ?>assets/utils/bootstrap/bootstrap.js\"></script>

</body>
</html>";

// Criar o arquivo
file_put_contents($caminhoArquivoAdmin, $conteudoAdmin);
echo "✅ Arquivo '$nomePagina.php' criado dentro de 'admin' com sucesso!\n";

// Criar a estrutura dentro de modulos-admin/contents
$caminhoPastaConteudo = __DIR__ . "/../admin/modulos-admin/contents/$nomePagina";
mkdir($caminhoPastaConteudo, 0777, true);

// Criar o arquivo index.php dentro de modulos-admin/contents
$conteudoConteudo = "<link rel=\"stylesheet\" href=\"<?= \$base_url; ?>modulos-admin/contents/$nomePagina/css/style.css\">

<section>
    <p class=\"mb-5 small\">Sessão destinada para <strong>$tituloModulo</strong></p>
</section>

<script src=\"<?= \$base_url; ?>modulos-admin/contents/$nomePagina/js/app.js\"></script>";

// Criar o arquivo index.php
file_put_contents($caminhoConteudo, $conteudoConteudo);
echo "✅ Arquivo 'index.php' criado dentro de 'modulos-admin/contents/$nomePagina' com sucesso!\n";

// Criar as pastas auxiliares (css, js, modais, php)
mkdir(__DIR__ . "/../admin/modulos-admin/contents/$nomePagina/css", 0777, true);
mkdir(__DIR__ . "/../admin/modulos-admin/contents/$nomePagina/js", 0777, true);
mkdir(__DIR__ . "/../admin/modulos-admin/contents/$nomePagina/modais", 0777, true);
mkdir(__DIR__ . "/../admin/modulos-admin/contents/$nomePagina/php", 0777, true);

echo "✅ Pastas dentro do modulo criado com sucesso!\n";


// Criar arquivos de exemplo dentro das pastas css e js
file_put_contents(__DIR__ . "/../admin/modulos-admin/contents/$nomePagina/css/style.css", "/* Estilo para o módulo $tituloModulo */");
file_put_contents(__DIR__ . "/../admin/modulos-admin/contents/$nomePagina/js/app.js", "// Funções JavaScript para o módulo $tituloModulo");

echo "✅ Por fim, atualize o arquivo core/RoutesAdmin.php e admin/modulos-admin/navegacao/nav/index.php!\n";
