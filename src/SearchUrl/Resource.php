<?php
namespace MetadataBrowse\SearchUrl;

use MetadataBrowse\SearchUrl\SearchUrlInterface;

class Resource implements SearchUrlInterface
{
    public function searchUrl($controllerName, $propertyId, $url, $route, $target, $html)
    {
        $searchTarget = $target->valueResource()->id();
        $searchUrl = $url($route,
              array('controller' => $controllerName, 'action' => 'browse'),
              array('query' => array('Search' => '',
                                     "property[$propertyId][res][]" => $searchTarget
                               )
                    )
          );
        return $searchUrl;
    }
}
