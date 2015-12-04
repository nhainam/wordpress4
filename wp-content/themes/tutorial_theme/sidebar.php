<div id="sidebar">
    <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
        <div id="widget-area-1" class="widget-area" role="complementary">
            <?php dynamic_sidebar( 'sidebar-1' ); ?>
        </div><!-- .widget-area -->
    <?php endif; ?>
    <?php if ( is_active_sidebar( 'sidebar-2' ) ) : ?>
        <div id="widget-area-2" class="widget-area" role="complementary">
            <?php dynamic_sidebar( 'sidebar-2' ); ?>
        </div><!-- .widget-area -->
    <?php endif; ?>
</div>