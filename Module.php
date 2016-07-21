<?php
namespace MetadataBrowse;

use Omeka\Module\AbstractModule;
use Omeka\Entity\Job;
use Omeka\Entity\Value;
use Omeka\Event\Event;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractController;
use Zend\View\Renderer\PhpRenderer;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Mvc\MvcEvent;

class Module extends AbstractModule
{
    protected $settings;

    public function onBootstrap(MvcEvent $event)
    {
        parent::onBootstrap($event);
        $this->settings = $this->getServiceLocator()->get('Omeka\Settings');
        
    }
    
    public function install(ServiceLocatorInterface $serviceLocator)
    {
        $this->settings = $serviceLocator->get('Omeka\Settings');
        $propertyIds = json_encode(array());
        $this->settings->set('metadata_browse_properties', $propertyIds);
    }
    
    public function uninstall(ServiceLocatorInterface $serviceLocator)
    {
        //possible redundant double-checking that the settings service is available
        $this->settings = $serviceLocator->get('Omeka\Settings');
        $this->settings->delete('metadata_browse_properties');
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    
    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        $sharedEventManager->attach(
                'Omeka\Api\Representation\ValueRepresentation',
                Event::REP_VALUE_HTML,
                array($this, 'repValueHtml' )
                );
    }
    
    public function handleConfigForm(AbstractController $controller)
    {
        $params = $controller->params()->fromPost();
        if (isset($params['propertyIds'])) {
            $propertyIds = json_encode($params['propertyIds']);
        } else {
            $propertyIds = json_encode(array());
        }
        $this->settings->set('metadata_browse_properties', $propertyIds);
    }
    
    public function getConfigForm(PhpRenderer $renderer)
    {
        $filteredPropertyIds = $this->settings->get('metadata_browse_properties');
        $escape = $renderer->plugin('escapeHtml');
        $translator = $this->getServiceLocator()->get('MvcTranslator');
        $html = '';
        $html .= "<script type='text/javascript'>
        var filteredPropertyIds = $filteredPropertyIds;
        </script>
        ";
        $formElementManager = $this->getServiceLocator()->get('formElementManager');
        $form = $formElementManager->get(Form::class, array());
        $html .= "<div id='properties'><p>" . $escape($translator->translate("Choose properties from the sidebar to be searchable.")) . "</p></div>";
        $html .= $renderer->partial('metadata-browse/property-template', array('escape' => $escape, 'translator' => $translator));
        $renderer->headScript()->appendFile($renderer->assetUrl('js/metadata-browse.js', 'MetadataBrowse'));
        $renderer->headLink()->appendStylesheet($renderer->assetUrl('css/metadata-browse.css', 'MetadataBrowse'));
        $renderer->htmlElement('body')->appendAttribute('class', 'sidebar-open');
        $selectorHtml = $renderer->propertySelector('Select properties to be searchable');
        $html .= "<div class='sidebar active'>$selectorHtml</div>";
        $html .= $renderer->formCollection($form, false);
        return $html;
    }

    public function repValueHtml($event)
    {
        $filteredPropertyIds = json_decode($this->settings->get('metadata_browse_properties'), true);
        $target = $event->getTarget();
        $propertyId = $target->property()->id();
        $routeMatch = $this->getServiceLocator()->get('Application')
                        ->getMvcEvent()->getRouteMatch();
        if ($routeMatch->getParam('__ADMIN__')) {
            $route = 'admin/default';
        } else {
            $route = 'default';
        }
        if (in_array($propertyId, $filteredPropertyIds)) {
            $controllerName = $target->resource()->getControllerName();
            $url = $this->getServiceLocator()->get('ViewHelperManager')->get('Url');
            $translator = $this->getServiceLocator()->get('MvcTranslator');
            $params = $event->getParams();
            $html = $params['html'];
            switch ($target->type()) {
                case 'resource':
                    $searchTarget = $params['targetId'];
                    $searchUrl = $url($route,
                          array('controller' => $controllerName, 'action' => 'browse'),
                          array('query' => array('Search' => '',
                                                 "property[$propertyId][res][]" => $searchTarget
                                           )
                                )
                      );
                    break;
                case 'uri':
                    $searchTarget = $params['targetUrl'];
                    $searchUrl = $url($route,
                          array('controller' => $controllerName, 'action' => 'browse'),
                          array('query' => array('Search' => '',
                                                 "property[$propertyId][eq][]" => $searchTarget
                                           )
                                )
                      );
                    break;
                case 'literal':
                default:
                    $searchTarget = $html;
                    $searchUrl = $url($route,
                          array('controller' => $controllerName, 'action' => 'browse'),
                          array('query' => array('Search' => '',
                                                 "property[$propertyId][eq][]" => $searchTarget
                                           )
                                )
                      );
            }
            $text = $translator->translate('See all items with this value');
            $link = "<a href='$searchUrl'>$text</a>";
            $event->setParam('html', "$html $link");
        }
    }
}