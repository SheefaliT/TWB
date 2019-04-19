
    <?php if ( is_sidebar_active('left_widget_area') ) : ?>
        <div id="leftsidebar" class="ie">
        <div id="leftwidgettop"></div>
        <div id="leftwidget"><?php dynamic_sidebar('left_widget_area'); ?></div>
        <div id="leftwidgetbottom"></div>
        </div>
<?php endif; ?>       
         
<?php if ( is_sidebar_active('right_widget_area') ) : ?>
        <div id="rightsidebar">
        <div id="rightwidgettop"></div>
        <div id="rightwidget"><?php dynamic_sidebar('right_widget_area'); ?></div>
        <div id="rightwidgetbottom"></div>
        </div>
<?php endif; ?>