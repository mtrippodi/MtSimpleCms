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

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\Application;
use Zend\Mvc\Exception;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\View\Model\ViewModel;

/**
 * Page controller
 *
 * Generic page controller for mapping route end points directly to the
 * template that provides the display.
 */
class PageController implements
    EventManagerAwareInterface,
    InjectApplicationEventInterface,
    DispatchableInterface
{
    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * Set the event manager instance
     *
     * Sets the event manager instance, after first setting the identifiers:
     *
     * - PhlySimplePage\PageController
     * - current class name
     * - Zend\Stdlib\DispatchableInterface
     *
     * It also registers the onDispatch method as the default handler for the
     * dispatch event.
     *
     * @param  EventManagerInterface $events
     * @return PageController
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
            __CLASS__,
            get_class($this),
            'Zend\Stdlib\DispatchableInterface',
        ));
        $events->attach('dispatch', array($this, 'onDispatch'));
        $this->events = $events;
        return $this;
    }

    /**
     * Retrieve the event manager instance
     *
     * Lazy-creates an instance if none registered.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (!$this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }

    /**
     * Set the current application event instance
     *
     * @param  EventInterface $event Typically an MvcEvent
     * @return PageController
     */
    public function setEvent(EventInterface $event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * Retrieve the current application event instance
     *
     * @return EventInterface|null
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Dispatch the current request
     *
     * @trigger dispatch
     * @param   RequestInterface $request
     * @param   ResponseInterface|null $response
     * @return  mixed
     */
    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
        $event = $this->getEvent();
        if (!$event) {
            $event = new MvcEvent();
        }

        $event->setRequest($request);
        if ($response) {
            $event->setResponse($response);
        }
        $event->setTarget($this);

        $results = $this->getEventManager()->trigger(__FUNCTION__, $event, function ($r) {
            return ($r instanceof ResponseInterface);
        });

        if ($results->stopped()) {
            return $results->last();
        }

        return $event->getResult();
    }

    /**
     * Handle the dispatch event
     *
     * If the event is not an MvcEvent, raises an exception.
     *
     * If the event does not have route matches, raises an exception.
     *
     * If the route matches do not contain a template, sets the event error to
     * Application::ERROR_CONTROLLER_INVALID, and, if the response is an HTTP
     * response type, sets the status code to 404.
     *
     * Otherwise, it creates a ViewModel instance, and sets the template to
     * the value in the route match, and sets the ViewModel as the event result.
     *
     * @param  EventInterface $e Usually an MvcEvent
     * @throws Exception\DomainException
     */
    public function onDispatch(EventInterface $e)
    {
        if (!$e instanceof MvcEvent) {
            throw new Exception\DomainException(sprintf(
                '%s requires an MvcEvent instance; received "%s"',
                __CLASS__,
                get_class($e)
            ));
        }
		
        $matches = $e->getRouteMatch();
        $params = $matches->getParams();
        $content_key = Module::contentKey($params, $e->getRequest()->getUriString());

        if (!$matches instanceof RouteMatch) {
            throw new Exception\DomainException(sprintf(
                'No RouteMatch instance provided to event passed to %s',
                __CLASS__
            ));
        }
		$sm = $e->getApplication()->getServiceManager();
		
		$pageModel = $sm->get('PagesModel');
		$page = $pageModel->getActivePage($content_key);
		
        if (!$page) {
            $e->setError(Application::ERROR_CONTROLLER_INVALID);
            $response = $e->getResponse();
            if ($response instanceof HttpResponse) {
                $response->setStatusCode(404);
            }
            return;
        }
		
		$metas = $pageModel->getHeadMeta($page->id);

		$metaNameDefaults = $matches->getParam('meta_names', FALSE);
		if($metaNameDefaults)
			$metaNames = $this->compileMetas($metas['name'], $metaNameDefaults);
		else
			$metaNames = $metas['name'];
		
		
		$metaNameProps = $matches->getParam('meta_props', FALSE);
		if($metaNameProps)
			$metaProps = $this->compileMetas($metas['property'], $metaNameProps);
		else
			$metaProps = $metas['property'];
		
		
		$metaHelper = $sm->get('viewhelpermanager')->get('HeadMeta');
		
		foreach ($metaNames as $key => $value) {
			$metaHelper->appendName($key, $value);
		}
		
		foreach ($metaProps as $key => $value) {
			$metaHelper->appendProperty($key, $value);
		}
		
        $layout = $e->getViewModel();
		$title = empty($page->title) ? '' : $page->title;
		$titleDefault = $matches->getParam('title', FALSE);
		if($titleDefault)
			$title = trim(sprintf($titleDefault, $title));
		$layout->title = $title;
		$layout->content = empty($page->content) ? '' : $page->content;

        $e->setResult(FALSE);
    }

    /**
     * Sets up all "name" or "property" meta-tags for the view of a given page
	 * while considering if default meta-tag are set
     *
     * @param  array The given meta-tags for a specific page
	 * @param  array Default values for meta-tags
     * @return array Meta-tags
     */
	protected function compileMetas($metas, $defaults)
	{
				
		foreach ($defaults as $key => $value) {
			if(is_array($value)){
				$template = $value[0];
				$force = (bool) (empty($value[1]) ? FALSE : $value[1]);
			}
			else {
				$template = $value;
				$force = FALSE;
			}
			
			$meta = FALSE;
			
			if(empty($metas[$key])){
				if($force == TRUE)
					$meta = trim(sprintf($template, ''));
			}
			else{
				$meta = trim(sprintf($template, $metas[$key]));
			}
			
			if($meta){
				if(substr($meta, -1) == ',')
					$meta = substr($meta, 0, -1);
			
				$metas[$key] = $meta;
			}
		}

		return $metas;
		
	}
}
