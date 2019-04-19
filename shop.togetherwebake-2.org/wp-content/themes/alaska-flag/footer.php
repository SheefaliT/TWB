<div id="footer">
<?php wp_footer();?>
<p class="footertag">Together We Bake’s mission is to provide a comprehensive workforce training and personal development program to help women gain self-confidence, transferable workforce skills, and invaluable hands-on experience which will allow them to find sustainable employment and move toward self-sufficiency.</p>
    <nav><div class="menu">
        <?php if(has_nav_menu('bottom-menu')){
        wp_nav_menu( array( 'theme_location' => 'bottom-menu' ) ); }if(has_nav_menu('bottom2-menu')){
        wp_nav_menu( array( 'theme_location' => 'bottom2-menu' ) ); }if(has_nav_menu('social-menu')){
        wp_nav_menu( array( 'theme_location' => 'social-menu' ) ); }?></div></nav><br />
<div class="copyright">
<p>We use cookies and tracking analytics to improve the operation of the website. By continuing to browse the site you are agreeing to our use of cookies and tracking analytics.</p>
<p>Copyright &copy; <?php echo date("Y") ?> <?php bloginfo('name'); ?></p><br />
<p><a href="https://aurorawebsites.net/">Theme By: AuroraWebsites.net</a></p>
</div>