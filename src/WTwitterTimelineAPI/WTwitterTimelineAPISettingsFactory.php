<?php
/**
 * Contiene la clase base para crear opciones en wordpress
 *
 * @author Jerry Anselmi <jerry.anselmi@gmail.com>
 * @copyright 2015 Jerry Anselmi
 * @license MIT
 * @package WTwitterTimelineAPI
 * @version 1.0
 */

/**
 * Clase base para crear opciones en wordpress
 */
class WTwitterTimelineAPISettingsFactory
{
    /**
     * Cadena de texto con el grupo de los ajustes
     * @var string
     */
    protected $setting_group = 'wordpress_twitter_timeline_api';

    /**
     * Cadena de texto con el nombre de las opciones
     * @var string
     */
    protected $setting_name = 'wordpress_twitter_timeline_api_settings';

    /**
     * Arreglo con las secciones predefinidas
     * @var array
     */
    private $setting_sections = array(
        "default" => array(
            "id"    => 'wordpress_twitter_timeline_api_settings_general',
            "title" => 'General'
        ),
        "auth" => array(
            "id"    => 'wordpress_twitter_timeline_api_settings_autenticacion',
            "title" => 'Datos de Autenticación'
        )
    );

    /**
     * Arreglo con la configuracion de cada ajuste
     * @var array
     */
    private $setting_fields = array();

    /**
     * cadena de texto con el formato de un input text, password, etc...
     * @var string
     */
    protected $format_input_text = '<input type="%s" id="%s" name="%s" value="%s" %s /><p><em>%s</em></p>';

    /**
     * Cadena de texto con el formato para manejar una imagen base64
     * @var string
     */
    protected $format_input_imgb64 = '<div id="imgbase64-uploader"><div class="box-img-upload"><p>Arrastre y suelte su imagen aqui <br>o haga click en esta área</p></div><div class="box-img-uploaded"><img src="%s" /></div></div><input type="hidden" id="%s" name="%s" value="%s" %s /><p><em>%s</em></p>';

    /**
     * Cadena de texto con el formato para un select
     * @var string
     */
    protected $format_select = '<select id="%s" name="%s" %s >%s</select><p><em>%s</em></p>';

    /**
     * Cadena de texto con el formato para un option de un select
     * @var string
     */
    protected $format_select_option = '<option value="%s" %s >%s</option>';

    public function __construct()
    {
        add_action('admin_init', array($this,'init_settings'));
    }

    /**
     * Metodo que permite inicializar los ajustes
     * @return void
     */
    final public function init_settings()
    {
        //var_dump($this->get_settings());
        register_setting($this->setting_group,$this->setting_name,array($this,'sanitize'));

        foreach ($this->setting_sections as $section)
        {
            add_settings_section($section["id"],$section["title"],NULL,$this->menu_slug);
        }

        $settings = $this->get_settings();

        foreach ($settings as $section_name => $section_fields)
        {
            foreach ($section_fields as $field)
            {
                $this->setting_fields[$field['id']] = array(
                    'type'    => isset($field['type'])?$field['type']:'text',
                    'id'      => $field['id'],
                    'setting' => $this->setting_name,
                    'name'    => $this->setting_name.'['.$field['id'].']',
                    'help'    => isset($field['help'])?$field['help']:'',
                    'attrs'   => isset($field['attrs'])?$field['attrs']:array(),
                    'list'    => isset($field['list'])?$field['list']:array(),
                    'wysiwyg_settings' => isset($field['wysiwyg_settings'])?$field['wysiwyg_settings']:array()
                );

                add_settings_field(
                    $field['id'],
                    $field['title'],
                    array($this,'field'),
                    $this->menu_slug,
                    $this->get_section_by_name($section_name),
                    $this->setting_fields[$field['id']]
                );
            }
        }
    }

    /**
     * Metodo que permite sanitizar los ajustes
     * @param  array $inputs Arreglo con los valores a sanitizar
     * @return array
     */
    final public function sanitize($inputs)
    {
        foreach ($inputs as $input_id => $input_value)
        {
            $field = $this->setting_fields[$input_id];
            $func_sanitize = $this->get_sanitize_func($field);
            $inputs[$input_id] = call_user_func($func_sanitize,$input_value);
        }
        return $inputs;
    }

    /**
     * Metodo que permite imprimir el elemento del formulario
     * @param  array $arguments Arreglo con los argumentos necesarios para imprimir el elemento
     * @return void
     */
    final public function field($arguments)
    {
        extract($arguments);

        $input     = '';
        $settings  = get_option($setting);
        $value     = isset($settings[$id])?esc_attr($settings[$id]):'';
        $attr_text = isset($attrs)?$this->attr_to_text($attrs):'';
        $options_text = is_array($list)?$this->options_to_text($list,$value):'';

        switch ($type)
        {
            case 'select':
                $input = sprintf($this->format_select,$id,$name,$attr_text,$options_text,$help);
                break;
            case 'imgbase64':
                $input = sprintf($this->format_input_imgb64,$value,$id,$name,$value, $attr_text,$help);
                break;
            case 'wysiwyg':
                $wysiwyg_settings['textarea_name'] = $name;
                wp_editor($value,$id,$wysiwyg_settings);
                break;
            default:
                $input = sprintf($this->format_input_text,$type,$id,$name,$value, $attr_text,$help);
                break;
        }

        echo $input;
    }

    /**
     * Metodo que permite obtener el id de una sección por su nombre
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    protected function get_section_by_name($name)
    {
        return isset($this->setting_sections[$name])?$this->setting_sections[$name]['id']:NULL;
    }

    /**
     * Metodo que permite serializar un arreglo en una vadena de texto de attributos de elementos de formulario.
     * @param  array  $attrs Arreglo con la lista de los atributos
     * @return string
     */
    private function attr_to_text($attrs = array())
    {
        $attr_text = '';

        foreach ($attrs as $k => $v)
        {
            if(!in_array($k, ['name','value','id','type']))
            {
                $attr_text.= sprintf('%s="%s"',$k,$v);
            }
        }

        return $attr_text;
    }

    /**
     * Metodo que permite serializar un arreglo en una vadena de texto de option de elemento select.
     * @param  array  $options        [description]
     * @param  [type] $value_selected [description]
     * @return [type]                 [description]
     */
    private function options_to_text($options = array(),$value_selected = NULL)
    {
        $options_text = '';

        foreach ($options as $k => $v)
        {
            $options_text.= sprintf($this->format_select_option,$k,selected($k,$value_selected,false),$v);
        }

        return $options_text;
    }

    /**
     * Metodo que permite definir la funcion o metodo de sanitización aplicable a un option_field
     * @param  array $field arreglo con los parametros de registro de un option_field
     * @return function
     */
    private function get_sanitize_func($field)
    {
        if (isset($field['sanitize']))
        {
            $func_sanitize = $field['sanitize'];
        }
        else
        {
            switch ($field['type'])
            {
                case 'urlpath':
                    $func_sanitize = 'esc_js';
                    break;
                case 'wysiwyg':
                    $func_sanitize = 'esc_textarea';
                    break;

                case 'number':
                    $func_sanitize = 'intval';
                    break;

                case 'email':
                    $func_sanitize = 'sanitize_email';
                    break;
                case 'color':
                    $func_sanitize = 'sanitize_hex_color';

                default:
                    $func_sanitize = 'sanitize_text_field';
                    break;
            }
        }
        return $func_sanitize;
    }
}