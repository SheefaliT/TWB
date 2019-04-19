</head>
    <body <?php body_class(); ?>><div id="wrapper">
            <div id="banner">
            <div id="logo">
<img src="<?php header_image(); ?>" height="<?php echo get_custom_header()->height; ?>" width="<?php echo get_custom_header()->width; ?>" alt="" />
</div>
            <div id="blogname"><?php bloginfo('name'); ?></div>
            <div id="tagline"><?php bloginfo('description'); ?></div>
<header id="top"><nav><?php wp_nav_menu( array( 'theme_location' => 'top-menu' ) ); ?></nav></header>
 