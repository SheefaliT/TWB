<html><head>
<?php get_header();?>
<?php get_header('banner');?>

<div id="content" role="main">
    <h1><p>Error 404 &#126; Page Not Found.</p></h1>
    <h2><p>Would you like one of the following pages?</p></h2>
        <nav><div id="footer"><div class="menu">
        <?php echo '<ul>';
            wp_list_pages('sort_order=ACS&title_li=');echo '<br />';
            wp_get_archives();echo '<br />';
            wp_list_categories('title_li=');
            echo '</ul>';
        ?><br /><br />
<p><a href="http://aurorawebsites.net/">Theme By: AuroraWebsites.net</a></p><br />
<p>Copyright &copy; <?php echo date("Y") ?> <?php bloginfo('name'); ?>, no reproduction of text or photos allowed without written permission.</p>
</div></div></nav><br />
</div>
<?php wp_footer();?>
</div></body></html>