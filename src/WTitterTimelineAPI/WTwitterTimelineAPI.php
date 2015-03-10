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
    function __construct()
    {

    }

    /**
     * @see WTwitterTimelineAPISettingsInterface::get_settings
     */
    public function get_settings()
    {
        return array(
            // 'estilo' => array(
            //     array(
            //         'id'    => 'color-base',
            //         'title' => __('Color Base',WPACE_DOMAIN),
            //         'type'  => 'color'
            //     ),
            //     array(
            //         'id'    => 'tema-pace',
            //         'title' => __('Tema',WPACE_DOMAIN),
            //         'type'  => 'select',
            //         'list'  => array(
            //             'Minimal',
            //             'Flash',
            //             'Barber Shop',
            //             'Mac OSX',
            //             'Fill Left',
            //             'Flat Top',
            //             'Big Counter',
            //             'Corner Indicator',
            //             'Bounce',
            //             'Loading Bar',
            //             'Center Circle',
            //             'Center Atom',
            //             'Center Radar',
            //             'Center Simple'
            //         )
            //     ),
            // )
        );
    }
}
?>