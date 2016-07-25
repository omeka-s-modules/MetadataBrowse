<?php
namespace MetadataBrowse\SearchUrl;

interface SearchUrlInterface
{
    public function searchUrl($controllerName, $propertyId, $url, $route, $target, $html);
}
