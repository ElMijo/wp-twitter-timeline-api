<?php


/**
*
*/
class WTwitterTimelineAPIWidget extends WP_Widget
{
    public function __construct() {
        parent::__construct(
            'wtta_widget',
            __('Wtwitter Timeline',WTTAPI_DOMAIN),
            array( 'description' => __( 'A Foo Widget', 'text_domain' ))
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance )
    {
        include_once WTTAPI_DIR.'/inc/template/widget-frontend.phtml';
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        echo "form";
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        // processes widget options to be saved
    }
}
function register_foo_widget() {

}
add_action('widgets_init',function(){register_widget('WTwitterTimelineAPIWidget');});
?>