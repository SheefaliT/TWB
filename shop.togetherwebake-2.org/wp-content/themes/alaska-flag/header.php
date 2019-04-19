<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php wp_head();?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php bloginfo('name'); ?><?php wp_title( '-' );?></title>

<link href="https://fonts.googleapis.com/css?family=Lato|Shadows+Into+Light+Two" rel="stylesheet">

<link rel="stylesheet" id="style-css" href="<?php echo home_url(); ?>/wp-content/themes/alaska-flag/alaska_styles.css" type="text/css" media="screen">
<![if IE]>
<link rel="stylesheet" id="style-css" href="<?php echo home_url(); ?>/wp-content/themes/alaska-flag/alaska_custom_styles.css" type="text/css" media="screen">
<![endif]>
	<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-93926777-2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-93926777-2');
</script>
<meta name="viewport" content="width=device-width, initial-scale=1" />
