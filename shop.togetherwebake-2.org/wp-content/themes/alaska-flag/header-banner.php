</head>
    <body <?php body_class(); ?>><div id="wrapper">
<div id="banner">
<header id="top"><nav><?php wp_nav_menu( array( 'theme_location' => 'top-menu' ) ); ?></nav></header>
 
<div id="header"><div id="blogname"><?php bloginfo('name'); ?></div>
<div id="tagline"><?php bloginfo('description'); ?></div></div>
<div id="logo"><img src="
https://shop.togetherwebake.org/wp-content/uploads/2018/04/TWB-LOGOx100.png" alt="Cooking utensils displayed in a circle"/></div>
<div id="bannertext"><?php $post_b = get_post( 1069 ); echo $post_b->post_content; ?></div>
</div>
 