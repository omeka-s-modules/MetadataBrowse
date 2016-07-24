<?php
return [
    'view_manager' => [
        'template_path_stack'      => [
            OMEKA_PATH . '/modules/MetadataBrowse/view',
        ],
    ],
    'metadata_browse_search_url_types' => [
         'resource' => 'MetadataBrowse\SearchUrl\Resource',
         'uri'      => 'MetadataBrowse\SearchUrl\Uri',
         'literal'  => 'MetadataBrowse\SearchUrl\Literal',
    ],
];
