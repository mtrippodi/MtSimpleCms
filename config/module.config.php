<?php
/**
 * ORIGINAL AUTHOR:
 * @link      https://github.com/weierophinney/PhlySimplePage for the canonical source repository
 * @copyright Copyright (c) 2012 Matthew Weier O'Phinney (http://mwop.net)
 * @license   https://github.com/weierophinney/PhlySimplePage/blob/master/LICENSE.md New BSD License
 * 
 * AUTHOR:
 * @copyright Copyright (c) 2014 Michael Trippodi <mail@trippodi.com>
 * @license   New BSD License
 */

return array(
    'controllers' => array(
        'invokables' => array(
            'MtSimpleCms\Controller\Page' => 'MtSimpleCms\PageController',
        ),
        'factories' => array(
            'MtSimpleCms\Controller\Cache' => 'MtSimpleCms\CacheControllerService',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'MtSimpleCms\PageCacheListener' => 'MtSimpleCms\PageCacheListenerService',
        ),
    ),
    'console' => array('router' => array('routes' => array(
        'phly-simple-page-clearall' => array('options' => array(
            'route' => 'phlysimplepage cache clear all',
            'defaults' => array(
                'controller' => 'MtSimpleCms\Controller\Cache',
                'action' => 'clearAll',
            ),
        )),
        'phly-simple-page-clearone' => array('options' => array(
            'route' => 'phlysimplepage cache clear --page=',
            'defaults' => array(
                'controller' => 'MtSimpleCms\Controller\Cache',
                'action' => 'clearOne',
            ),
        )),
    )))
);