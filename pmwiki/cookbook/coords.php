<?php if (!defined('PmWiki')) exit();
include_once("cookbook/properties.php");

/*$coordsMarkup = "/^@coord\\s*([^\n]*\n\\s*
               (?: [^\n\\s] [^\n]*\n\\s*)*?)
               (<:vspace>|@coord)
               /sexim";*/
               
$coordsMarkup = "/^@\\s*(.*?)\\s*$/emi";

function installCoordsMarkup()
{
    global $coordsMarkup;
    Markup('stripVspace', '<directives', '/<:vspace>/i', '');
    Markup('coord', 'directives', $coordsMarkup, "coordMarkupCall(PSS('$1'))");
}

// TODO Obviously replace this with a much better representation
function coordHtml($coord)
{
    //$output = '<p>';
    if (isset($coord[1]['photo']))
    {
        $output .= @"%width=50px% {$coord[1]['photo']}"; // TODO This is a bit screwy
    }
    //$output .= @"{$coord[1]['name']} &lt;{$coord[1]['email']}&gt;";
    $output .= @"||{$coord[1]['name']} ||<em>{$coord[0]}</em> ||&lt;{$coord[1]['email']}&gt; ||";
    //$output .= @"</p>\n";
    return $output;
}

$coordMarkupState = array();

function coordMarkupCall($arguments)
{
    global $coordMarkupState;
    $coord = addProperty($coordMarkupState, $arguments, 'coord');
    if ($coord)
    {
        return coordHtml($coord);
    }
    return '';
}

function parseCoords($fulltext)
{
    return '';
}


