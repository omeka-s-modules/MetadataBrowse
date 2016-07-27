<?php
namespace MetadataBrowse\Controller\Admin;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Form;


class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $site = $this->currentSite();
        $siteSettings = $this->siteSettings();
        $view = new ViewModel;
        $form = $this->getForm(Form::class);
        
        $view->setVariable('form', $form);
        return $view;
    }
}
