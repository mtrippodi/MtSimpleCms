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

namespace MtSimpleCms;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Service factory for page cache service
 */
class PageCacheListenerService implements FactoryInterface
{
    /**
     * Create and return page cache listener
     *
     * @param  ServiceLocatorInterface $services
     * @return PageCacheListener
     * @throws Exception\ServiceNotCreatedException
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $cache    = $services->get('MtSimpleCms\PageCache');
        return new PageCacheListener($cache);
    }
}
