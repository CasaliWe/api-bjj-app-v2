<style>
    #navegacao-desktop{
        width: 20%;
    }

    #footer-nav{
        position: absolute;
        bottom: 7%;
        padding-left: 11%;
    }
    #footer-nav h6{
        font-size: 10px;
    }
    #footer-nav img{
        width: 28%;
    }
    @media(min-width:1500px){
        #footer-nav{
            position: absolute;
            bottom: 6%;
            padding-left: 12%;
        }
        #footer-nav h6{
            font-size: 14px;
        }
        #footer-nav img{
            width: 35%;
        }
    }
    @media(max-width:992px){
        #footer-nav{
            position: absolute;
            bottom: 6%;
            padding-left: 18%;
            width: 100%;
        }
        #footer-nav h6{
            font-size: 12px;
            margin-bottom: 12px;
        }
        #footer-nav img{
            width: 17%;
        }
    }

    .link-nav-desktop{
        width: 100%;
        padding-left: 12%;
        padding-top: 10px;
        padding-bottom: 10px;
        font-size: 10px;
        color: rgba(0, 0, 0, 0.767);
        font-weight: 600;
    }
    .link-nav-desktop:hover{
        background-color: rgba(128, 128, 128, 0.443);
        color: black;
    }
    @media(max-width:992px){
        .link-nav-desktop{
            padding-top: 12px;
            padding-bottom: 12px;
            font-size: 12px;
        }
    }

    .active-link-desktop{
        background-color: gray !important;
        color: white !important;
    }


    @media(min-width:1500px){
        .link-nav-desktop{
            padding-top: 15px;
            padding-bottom: 15px;
            font-size: 15px;
        }
    }



    #header-navegacao-mobile{
        display: none !important;
    }
    #content-navegacao-mobile{
        display: none !important;
    }


    @media(max-width:992px){
        #navegacao-desktop{
            display: none !important;
        }

        #header-navegacao-mobile{
            display: flex !important;
            justify-content: space-between;
            align-items: center;
            padding: 25px 40px;
        }
        #header-navegacao-mobile #logo-nav-mobile{
            height: 60px;
        }


        #content-navegacao-mobile{
            position: fixed;
            height: 0%;
            width: 100%;
            z-index: 1000 !important;
            background-color: #DFDFDF;
            display: flex !important;
            justify-content: center;
            align-items: start;
            overflow: hidden !important;
        }
        #content-navegacao-mobile #btn-close{
            position: fixed;
            top: 4%;
            right: 9%;
            background-color: transparent;
            border: none; 
            cursor: pointer;
            display: none;
        }
    }
</style>




<!-- MOBILE -->
<div id="content-navegacao-mobile" class="px-5">
    <button id="btn-close" onclick="abrirNavMobile()"> <i class="fas fa-close fs-1 text-danger"></i> </button>

    <!-- NAVEGAÇÃO -->
    <?php include "modulos-admin/navegacao/nav/index.php";?>
    <!-- NAVEGAÇÃO -->

    <div id="footer-nav">
        <h6 class="fw-semibold">PAINEL ADMINISTRATIVO</h6>
    </div>
</div>

<section id="header-navegacao-mobile" class="shadow-lg bg-logo-nav">
    <img id="logo-nav-mobile" src="<?php echo $base_url ?>assets/imagens/site-admin/logo.png" alt="Logo">

    <button onclick="abrirNavMobile()" style="background-color: transparent; border: none; cursor: pointer;"> <i class="fas fa-bars fs-1 color-toggler"></i> </button>
</section>
<!-- MOBILE -->



<!-- DESKTOP -->
<aside id="navegacao-desktop" class="position-fixed left-0 vh-100 d-flex flex-column bg-secondary bg-opacity-25 shadow-lg">
    <div class="bg-logo-nav py-4 w-100 px-3 d-flex justify-content-center align-items-center"><img class="w-50" src="<?php echo $base_url ?>assets/imagens/site-admin/logo.png" alt="Logo"></div>

    <!-- NAVEGAÇÃO -->
    <?php include "modulos-admin/navegacao/nav/index.php";?>
    <!-- NAVEGAÇÃO -->


    <div id="footer-nav">
        <h6 class="fw-semibold">PAINEL ADMINISTRATIVO</h6>
    </div>
</aside>
<!-- DESKTOP -->



<script>
    // abrir nav mobile
    var controleMenu = false
    function abrirNavMobile(){
        if(controleMenu){
            //fechar
            controleMenu = false
            document.getElementById("content-navegacao-mobile").style.cssText = `height:0%; transition: 350ms;`
            document.getElementById("btn-close").style.cssText = `display: none !important;`
        }else{
            //abrir
            controleMenu = true
            document.getElementById("content-navegacao-mobile").style.cssText = `height:100%; transition: 350ms;`
            document.getElementById("btn-close").style.cssText = `display: block !important;`
        }
    }
</script>