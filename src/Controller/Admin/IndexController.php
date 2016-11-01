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
        $view = new ViewModel();
        $form = $this->getForm(Form::class);
        if ($this->getRequest()->isPost()) {
            $params = $this->params()->fromPost();
            if (isset($params['propertyIds'])) {
                $propertyIds = $params['propertyIds'];
            } else {
                $propertyIds = [];
            }
            $siteSettings->set('metadata_browse_properties', $propertyIds);
        }

        $filteredPropertyIds = json_encode($siteSettings->get('metadata_browse_properties'));
        $view->setVariable('form', $form);
        $view->setVariable('filteredPropertyIds', $filteredPropertyIds);

        return $view;
    }
}
