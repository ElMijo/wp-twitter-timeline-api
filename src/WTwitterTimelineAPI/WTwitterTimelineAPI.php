<?php
/**
 * Contiene la clase base del plugin
 *
 * @author Jerry Anselmi <jerry.anselmi@gmail.com>
 * @copyright 2015 Jerry Anselmi
 * @license MIT
 * @package WTwitterTimelineAPI
 * @version 1.0
 */

/**
*Clase base del plugin
*/
class WTwitterTimelineAPI   extends WTwitterTimelineAPISettingsFactory  implements WTwitterTimelineAPISettingsInterface
{
    /**
     * slug del plugin
     * @var string
     */
    protected $menu_slug = 'wtwitter-timeline-api';

    function __construct()
    {
        parent::__construct();
        add_action( 'admin_menu', array($this,'register_menu'));
    }

    final public function register_menu()
    {
        add_menu_page('Twitter Timeline API', 'WTwitter', 'manage_options', $this->menu_slug, array($this,'menu_page'), WTTAPI_URL.'/images/icon.png',60);
    }

    final public function menu_page()
    {
        $titulo = get_admin_page_title();

        include WTTAPI_DIR.'/inc/template/options.phtml';
    }
    /**
     * @see WTwitterTimelineAPISettingsInterface::get_settings
     */
    public function get_settings()
    {
        return array(
            'default' => array(
                array(
                    'id' => 'twitter-username',
                    'title' => 'Username',
                    'type' => 'text',
                    'help' => __('Nombre de usuario',WTTAPI_DOMAIN),
                    'attrs' => array(
                        'class' => 'regular-text'
                    )
                ),
                array(
                    'id' => 'twitter-number',
                    'title' => 'N° Tweets',
                    'type' => 'number',
                    'help' => __('Cantidad de tweets que se requieren por consulta',WTTAPI_DOMAIN),
                    'attrs' => array(
                        'class' => 'small-text'
                    )

                )
            ),
            'auth' => array(
                array(
                    'id' => 'twitter-consumer-key',
                    'title' => 'Consumer Key',
                    'type' => 'text',
                    'help' => __('Llave generada por el API de twitter para consumir los servicios',WTTAPI_DOMAIN),
                    'attrs' => array(
                        'class' => 'large-text'
                    )
                ),
                array(
                    'id' => 'twitter-consumer-secret',
                    'title' => 'Consumer Secret',
                    'type' => 'text',
                    'help' => __('Llave secreta generada por el API de twitter para consumir los servicios',WTTAPI_DOMAIN),
                    'attrs' => array(
                        'class' => 'large-text'
                    )
                ),
                array(
                    'id' => 'twitter-access-token',
                    'title' => 'Access Token',
                    'type' => 'text',
                    'help' => __('Token de acceso generada por el API de twitter',WTTAPI_DOMAIN),
                    'attrs' => array(
                        'class' => 'large-text'
                    )
                ),
                array(
                    'id' => 'twitter-access-token-secret',
                    'title' => 'Access Token Secret',
                    'type' => 'text',
                    'help' => __('Token secreto de acceso generada por el API de twitter',WTTAPI_DOMAIN),
                    'attrs' => array(
                        'class' => 'large-text'
                    )
                ),
            )
        );
    }
}
new WTwitterTimelineAPI();
?>