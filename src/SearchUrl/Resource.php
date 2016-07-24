<?php
namespace MetadataBrowse\SearchUrl;

class Resource
{
    public function searchUrl($controllerName, $propertyId, $url, $route, $target)
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
