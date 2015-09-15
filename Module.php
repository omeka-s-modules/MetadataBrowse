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
                'filterLiteralValue',
                array($this, 'filterLiteralValue')
                );
    }
    
    public function handleConfigForm(AbstractController $controller)
    {
        
    }
    
    public function getConfigForm(PhpRenderer $renderer)
    {
        $html = '';
        $form = new ConfigForm($this->getServiceLocator());
        $html .= "<div id='properties'>props</div>";
        $html .= "<div class='sidebar active'>sidebar stuff</div>";
        $html .= $renderer->formElements($form);
        
        return $html;
    }
    
    
    public function filterLiteralValue($event)
    {
        $params = $event->getParams();
        $value = $params['value'];
        return "$value filtered";
    }
}