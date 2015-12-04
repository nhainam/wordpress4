<html>
<head>
    <title><?php echo the_title(); ?></title>
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
    <?php wp_head(); ?>
</head>
<body>
<div id="wrapper">
    <div id="header">
        <?php wp_nav_menu( array( 'menu' => 'Primary Menu' ) ); ?>
    </div>