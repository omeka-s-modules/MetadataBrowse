<?php

namespace MetadataBrowse\Form;

use Zend\Form\Form;

class ConfigForm extends Form
{
    protected $globalSettings;

    public function init()
    {
        $this->add([
            'type' => 'checkbox',
            'name' => 'metadata_browse_use_globals',
            'options' => [
                        'label' => 'Use global configuration on admin side', // @translate
                    ],
            'attributes' => [
                'checked' => $this->globalSettings->get('metadata_browse_use_globals') ? 'checked' : '',
                'id' => 'metadata-browse-use-globals',
            ],
        ]);
    }

    public function setGlobalSettings($globalSettings)
    {
        $this->globalSettings = $globalSettings;
    }
}
