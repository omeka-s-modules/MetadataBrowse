<?php
namespace MetadataBrowse;

use Omeka\Module\AbstractModule;
use Omeka\Entity\Job;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Controller\AbstractController;
use Zend\View\Renderer\PhpRenderer;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Mvc\MvcEvent;
use MetadataBrowse\Form\ConfigForm;

class Module extends AbstractModule
{
    protected $settings;

    public function onBootstrap(MvcEvent $event)
    {
        parent::onBootstrap($event);
        $this->settings = $this->getServiceLocator()->get('Omeka\Settings');
        
    }
    public function getConfig()
    {
        return array();
        //return include __DIR__ . '/config/module.config.php';
    }
    
    
    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        $sharedEventManager->attach(
                'Omeka\Api\Representation\ValueRepresentation',
                'filterValue',
                array($this, 'filterValue')
                );
    }
    
    public function handleConfigForm(AbstractController $controller)
    {
        $params = $controller->params()->fromPost();
        $propertyIds = json_encode($params['propertyIds']);
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
        $form = new ConfigForm($this->getServiceLocator());
        //$html .= $renderer->partial(__DIR__ . '/view/metadata-browse/property-template.phtml');
        $html .= "<div id='properties'><p>" . $escape($translator->translate("Choose properties from the sidebar to be searchable.")) . "</p></div>";
        $html .= '
<fieldset class="resource-values field template">
    <input type="hidden" disabled="disabled" name="propertyIds[]" class="property-ids"></input>

    <div class="field-meta">
        <div class="input-header">
            <span class="restore-property">' . $translator->translate("Property to be removed") .'</span>
            <ul class="actions">
                <li>
                    <a href="#" 
                    class="o-icon-delete remove-property" 
                    title="' . $translator->translate("Remove property") .'" 
                    aria-label="' . $escape($translator->translate("Remove property")) .'"></a>
                </li>
                <li>
                    <a href="#" 
                    class="o-icon-undo restore-property" 
                    title="' . $escape($translator->translate("Undo remove property")) .'" 
                    aria-label="' . $translator->translate("Undo remove property") . '"></a>
                </li>
            </ul>
        </div>
    
        <legend class="field-label"></legend>
        <a href="#" class="expand o-icon-right" aria-label="' . $escape($translator->translate("Expand")) .'"></a>
        <div class="collapsible">
            <div class="field-description"></div>
            <div class="field-term" title="' . $escape($translator->translate("Property term for development use")) .'"></div>
        </div>
    </div>
</fieldset>
        ';
        $renderer->headScript()->appendFile($renderer->assetUrl('js/metadata-browse.js', 'MetadataBrowse'));
        $renderer->headLink()->appendStylesheet($renderer->assetUrl('css/metadata-browse.css', 'MetadataBrowse'));
        $selectorHtml = $renderer->propertySelector('Select properties to be searchable');
        $html .= "<div class='sidebar active'>$selectorHtml</div>";
        $html .= $renderer->formElements($form);
        return $html;
    }

    public function filterValue($event)
    {
        $filteredPropertyIds = json_decode($this->settings->get('metadata_browse_properties'), true);
        $target = $event->getTarget();
        $propertyId = $target->property()->id();
        if (in_array($propertyId, $filteredPropertyIds)) {
            $params = $event->getParams();
            $html = $params['html'];
            $url = $this->getServiceLocator()->get('ViewHelperManager')->get('Url');
            $searchUrl = $url('admin/default',
                              array('controller' => 'item', 'action' => 'browse'),
                              array('query' => array('Search' => '',
                                                     "property[$propertyId][eq][]" => $html
                                               )
                                    )
                          );
            $translator = $this->getServiceLocator()->get('MvcTranslator');
            $text = $translator->translate('See all items with this value');
            $link = "<a href='$searchUrl'>$text</a>";
            $event->setParam('html', "$html $link");
        }

    }
}