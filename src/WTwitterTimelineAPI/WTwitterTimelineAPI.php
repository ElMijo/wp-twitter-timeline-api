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

    /**
     * Formato de texto del query del timeline
     * @var string
     */
    private $getfield_text_format = "?screen_name=%s&count=%s";

    function __construct()
    {
        parent::__construct();
        add_action('admin_menu', array($this,'register_menu'));
        add_action('wp_ajax_get_timeline', array($this,'ajax_get_timeline'));
        add_action('wp_ajax_nopriv_get_timeline', array($this,'ajax_get_timeline'));
        add_action('wp_enqueue_scripts',array($this,'include_css_js'));

    }

    /**
     * Permite registrar el menu de opciones
     * @return void
     */
    final public function register_menu()
    {
        add_menu_page('Twitter Timeline API', 'WTwitter', 'manage_options', $this->menu_slug, array($this,'menu_page'), WTTAPI_URL.'/images/icon.png',61);
    }

    /**
     * Permite mostrar la pagina del menu de opciones
     * @return void
     */
    final public function menu_page()
    {
        $titulo = get_admin_page_title();

        include WTTAPI_DIR.'/inc/template/options.phtml';
    }

    /**
     * Este metodo recibe la petición ajax para obtener el timeline
     * @return void
     */
    final public function ajax_get_timeline()
    {
        wp_send_json($this->get_twitter_timeline());
    }

    /**
     * Permite incluir los archivo javascript y css del plugin
     * @return void
     */
    final public function include_css_js()
    {
        wp_enqueue_script('wtwitter-core',WTTAPI_URL.'js/wtwitter-timeline-api.js', array('jquery'), 'v1.0.0');
        wp_localize_script('wtwitter-core','wtta',array('ajaxurl' => admin_url('admin-ajax.php')));

    }

    /**
     * Permite conectarse al API de Twitter para obtener el timeline
     * @return array     Devuelve un arreglo que se debe utilizar como respuesta a una petición ajax
     */
    private function get_twitter_timeline()
    {
        include_once WTTAPI_DIR.'/src/twitter-api-php/TwitterAPIExchange.php';

        extract($this->get_twitter_settings());

        $twitter_instance = new TwitterAPIExchange($settings);

        $query = $twitter_instance
            ->setGetfield( $getfield )
            ->buildOauth( $url, $request_method )
            ->performRequest()
        ;

        return $this->parse_twitter_response($query);
    }

    /**
     * Permite obtener la configuración del twitter
     * @return array
     */
    private function get_twitter_settings()
    {
        $data = get_option($this->setting_name,true);

        return array(
            'url' => 'https://api.twitter.com/1.1/statuses/user_timeline.json',
            'request_method' => 'GET',
            'getfield' => sprintf($this->getfield_text_format,$data['twitter-username'],$data['twitter-number']),
            'settings' => array(
                'oauth_access_token'        => $data['twitter-access-token'],
                'oauth_access_token_secret' => $data['twitter-access-token-secret'],
                'consumer_key'              => $data['twitter-consumer-key'],
                'consumer_secret'           => $data['twitter-consumer-secret']
            )
        );
    }

    /**
     * Permite procesar la respuesta y devolver un arreglo que pueda ser manejado como una respuesta
     * @param  array $response Arreglo con la respuesta cruda de la consulta al API de Twitter
     * @return array
     */
    private function parse_twitter_response($response)
    {
        $array_response = array('error'=>true,'data'=>array(),'message'=>'');
        if(!!$response)
        {
            $objct_response = json_decode($response);

            if(gettype($objct_response)=='array')
            {
                $array_response['error'] = false;
                $array_response['data'] = $objct_response;

            }
            else
            {
                $array_response['message'] = $objct_response['errors'];
            }
        }
        return $array_response;
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
$WTTAPI=new WTwitterTimelineAPI();
?>