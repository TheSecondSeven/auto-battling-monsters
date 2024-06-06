<?php
/**
 * @var \Cake\View\View $this
 */
use Cake\Core\Configure;

$this->Html->css('BootstrapUI.dashboard', ['block' => true]);
$this->prepend(
    'tb_body_attrs',
    ' class="' .
        implode(' ', [h($this->request->getParam('controller')), h($this->request->getParam('action'))]) .
        '" '
);
$this->start('tb_body_start');
?>
<body <?= $this->fetch('tb_body_attrs') ?>>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <?= $this->Html->link(
            'DYMTAB',
            '/',
            ['class' => 'navbar-brand col-md-3 col-lg-2 me-0 px-3']
        ) ?>
        <button
            class="navbar-toggler position-absolute d-md-none collapsed" type="button"
            data-bs-toggle="collapse" data-bs-target="#sidebarMenu"
            aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>
        <ul class="list-group list-group-horizontal">
            <li class="list-group-item">Active Monsters: <?= count($user->monsters).'/'.$user->active_monster_limit ?></li>
            <li class="list-group-item">Gold: <?= $user->gold ?></li>
            <li class="list-group-item">Rune Shards: <?= $user->rune_shards ?></li>
        </ul>
        <ul class="navbar-nav px-3">
            <li class="nav-item text-nowrap">
                <?= $this->Html->link(
                    'Sign Out',
                    '/logout',
                    ['class' => 'nav-link']
                ) ?>
            </li>
        </ul>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light" style="">
                <div class="position-sticky pt-3">
                    <?= $this->element('side_nav'); ?>
                </div>
            </nav>

            <main role="main" class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
<?php
/**
 * Default `flash` block.
 */
if (!$this->fetch('tb_flash')) {
    $this->start('tb_flash');
    if (isset($this->Flash)) {
        echo $this->Flash->render();
    }
    $this->end();
}
$this->end();

$this->start('tb_body_end');
?>
            </main>
        </div>
    </div>
</body>
<?php
$this->end();

echo $this->fetch('content');
?>
<script type="text/javascript">
    $('document').ready(function(){
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
