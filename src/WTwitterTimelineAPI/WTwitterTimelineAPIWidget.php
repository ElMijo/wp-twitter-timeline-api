<?php
/**
 * Contiene la clase que registra y naneja el widget
 *
 * @author Jerry Anselmi <jerry.anselmi@gmail.com>
 * @copyright 2015 Jerry Anselmi
 * @license MIT
 * @package WTwitterTimelineAPI
 * @version 1.0
 */

/**
* Clase que maneja el widget
*/
class WTwitterTimelineAPIWidget extends WP_Widget
{
    public function __construct() {
        parent::__construct(
            'wtta_widget',
            __('Wtwitter Timeline',WTTAPI_DOMAIN),
            array( 'description' => __( 'Wtwitter Widget', WTTAPI_DOMAIN ))
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
    public function form( $instance )
    {

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
    public function update( $new_instance, $old_instance )
    {

    }
}
add_action('widgets_init',function(){register_widget('WTwitterTimelineAPIWidget');});

add_action('init',function(){
    if(!!is_active_widget(false,false,'wtta_widget')&&!is_admin())
    {
        wp_enqueue_style('wtwitter-frontend-timeline-css',WTTAPI_URL.'css/wtwitter-timeline-widget.css');
        wp_enqueue_script('wtwitter-frontend-timeline-js',WTTAPI_URL.'js/wtwitter-timeline-widget.js',array('wtwitter-core','jquery'));
    }
});
?>