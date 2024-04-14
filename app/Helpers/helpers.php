<?php 



function purify_html($html){
    return($html);
}

function active_menu($url)
{
    $path_url = str_replace(request()->root().'/', '', request()->fullUrl());
    return $url == $path_url ? 'active' : '';
    // return $url == request()->path() ? 'active' : '';
}