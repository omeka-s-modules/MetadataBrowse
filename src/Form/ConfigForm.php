<?php

namespace MetadataBrowse\Form;

use Zend\Form\Form;

class ConfigForm extends Form
{
    protected $globalSettings;

    public function init()
    {
        $this->add(array(
            'type' => 'checkbox',
            'name' => 'metadata_browse_use_globals',
            'options' => array(
                        'label' => 'Use global configuration on admin side', // @translate
                        'info' => 'If checked, the properties set below will be made links on the admin side. Otherwise, all properties made links in all sites will be links on the admin side.', // @translate
                    ),
            'attributes' => array(
                        'checked' => $this->globalSettings->get('metadata_browse_use_globals') ? 'checked' : '',
                    ),
        ));
    }

    public function setGlobalSettings($globalSettings)
    {
        $this->globalSettings = $globalSettings;
    }
}
