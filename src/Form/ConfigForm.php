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
                        'info' => "If checked, the properties selected below will be linked on the admin side, overriding all site-specific settings. Each site's own settings will be reflected on the public side. Otherwise, the admin side will reflect the aggregated settings for all sites; anything selected to be a link in any site will be a link on the admin side.", // @translate
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
