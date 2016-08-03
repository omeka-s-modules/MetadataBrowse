<?php

namespace MetadataBrowse\Service\Form;

use MetadataBrowse\Form\ConfigForm;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConfigFormFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $elements)
    {
        $form = new ConfigForm();
        $globalSettings = $elements->getServiceLocator()->get('Omeka\Settings');
        $form->setGlobalSettings($globalSettings);

        return $form;
    }
}
