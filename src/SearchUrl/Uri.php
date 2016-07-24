<?php
namespace MetadataBrowse\SearchUrl;

class Uri
{
    public function searchUrl($controllerName, $propertyId, $url, $route, $target)
    {
        $searchTarget = $target->uri();
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
