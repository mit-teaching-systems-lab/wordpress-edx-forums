<?php

$current = isset($_GET['tab']) ? $_GET['tab'] : 'widgets';

if ($current != 'toolbox') {
    $this->upgrade_notice();
}

$tabs = array(
    'widgets' => __("Settings", "gd-bbpress-widgets"), 
    'faq' => __("FAQ", "gd-bbpress-widgets"), 
    'toolbox' => __("Toolbox", "gd-bbpress-widgets"), 
    'd4p' => __("Dev4Press", "gd-bbpress-widgets"), 
    'about' => __("About", "gd-bbpress-widgets")
);

?>
<div class="wrap">
    <h2>GD bbPress Widgets</h2>
    <div id="icon-themes" class="icon32"><br></div>
    <h2 class="nav-tab-wrapper">
    <?php

    foreach($tabs as $tab => $name){
        $class = ($tab == $current) ? ' nav-tab-active' : '';
        echo '<a class="nav-tab'.$class.'" href="edit.php?post_type=forum&page=gdbbpress_widgets&tab='.$tab.'">'.$name.'</a>';
    }

    ?>
    </h2>
    <div id="d4p-panel" class="d4p-panel-<?php echo $current; ?>">
        <?php include(GDBBPRESSWIDGETS_PATH.'forms/tabs/'.$current.'.php'); ?>
    </div>
</div>