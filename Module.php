<?php
namespace MetadataBrowse;

use Omeka\Module\AbstractModule;
use Omeka\Entity\Job;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Controller\AbstractController;
use Zend\View\Renderer\PhpRenderer;
use Zend\EventManager\SharedEventManagerInterface;
use MetadataBrowse\Form\ConfigForm;

class Module extends AbstractModule
{
    
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
        $settings = $this->getServiceLocator()->get('Omeka\Settings');
        $settings->set('metadata_browse_properties', $propertyIds);
    }
    
    public function getConfigForm(PhpRenderer $renderer)
    {
        $escape = $renderer->plugin('escapeHtml');
        $translator = $this->getServiceLocator()->get('MvcTranslator');
        $html = '';
        $form = new ConfigForm($this->getServiceLocator());
        $html .= "<div id='properties'><p>" . $escape($translator->translate("Choose properties to be searchable from the sidebar.")) . "</p></div>";
        $html .= '
<fieldset class="resource-values field template">
    <input type="hidden" name="propertyIds[]" class="property-ids"></input>
    <div class="field-meta">
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
        $selectorHtml = $renderer->propertySelector('Select properties to be searchable');
        $html .= "<div class='sidebar active'>$selectorHtml</div>";
        $html .= $renderer->formElements($form);
        
        return $html;
    }

    public function filterValue($event)
    {
        $target = $event->getTarget();
        $propertyId = $target->property()->id();
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