<?php
namespace MetadataBrowse\SearchUrl;

use MetadataBrowse\SearchUrl\SearchUrlInterface;

class Literal implements SearchUrlInterface
{
    public function searchUrl($controllerName, $propertyId, $url, $route, $target, $html)
    {
        $searchTarget = $html;
        $searchUrl = $url($route,
              array('controller' => $controllerName, 'action' => 'browse'),
              array('query' => array('Search' => '',
                                     "property[$propertyId][eq][]" => $searchTarget
                               )
                    )
          );
        return $searchUrl;
    }
}
