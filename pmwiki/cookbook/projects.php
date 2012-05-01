<?php if (!defined('PmWiki')) exit();
include_once("cookbook/properties.php");

/*$projectsMarkup = "/^@project\\s*([^\n]*\n\\s*
               (?: [^\n\\s] [^\n]*\n\\s*)*?)
               (<:vspace>|@project)
               /sexim";*/
               
$projectsMarkup = "/^@\\s*(.*?)\\s*$/emi";

function installProjectsMarkup()
{
    global $projectsMarkup;
    Markup('stripVspace', '<directives', '/<:vspace>/i', '');
    Markup('project', 'directives', $projectsMarkup, "projectMarkupCall(PSS('$1'))");
}

// TODO Obviously replace this with a much better representation
function projectHtml($project)
{
    $output = '<p>';
    $output .= @"Project: {$project[0]}<br/>";
    $output .= @"Description: {$project[1]['description']}";
    $output .= @"</p>\n";
    return $output;
}

$projectMarkupState = array();

function projectMarkupCall($arguments)
{
    global $projectMarkupState;
    $project = addProperty($projectMarkupState, $arguments, 'project');
    if ($project)
    {
        return projectHtml($project);
    }
    return '';
}

function parseProjects($fulltext)
{
    $projects = array();

    global $projectsMarkup;
    if (preg_match_all($projectsMarkup, $fulltext, $matches) > 0)
    {
        $state = array();
        
        //die($fulltext);
        //die(print_r($matches[1], true));
        
        foreach ($matches[1] as $match)
        {
            //array_push($projects, $match);
            $project = addProperty($state, $match, 'project');
            if ($project)
            {
                array_push($projects, $project);
            }
        }
    }
    return $projects;
}


