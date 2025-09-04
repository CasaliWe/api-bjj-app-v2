<nav class="d-flex flex-column w-100 mt-5 pt-5 mt-lg-0 pt-lg-0">
    <a href="dashboard.php" class="link-nav-desktop <?= \Core\RoutesAdmin::isActive('dashboard') ?>">Dashboard</a>
    <a class="link-nav-desktop"><?php include __DIR__ . "/../../btn-logout/index.php"; ?></a>
</nav>