<?php if (!defined('PmWiki')) exit();
include_once("cookbook/projects.php");
include_once('cookbook/events.php');

/**
 * Fetch the quotes page and return a select quote.
 */
function loadQuote()
{
    $quotePage = @RetrieveAuthPage('Main.Quotes', 'read', false, READPAGE_CURRENT);
    if (!$quotePage || !is_array($quotePage) || !isset($quotePage['text']))
    {
        return '';
    }
    
    // Take one quote per line, ignoring empty lines
    $quotes = array_values(array_filter(explode("\n", $quotePage['text']), strlen));
    
    if ($quotes)
    {
        // Figure this one out for yourself
        $i = hexdec(substr(sha1(date("d-m-Y")), 0, 8)) % count($quotes);
        return $quotes[$i];
    }
    return '';
}

/**
 * Fetch the projects page and pass of the parsing to the projects lib.
 */
function loadProjects()
{
    $projectsPage = @RetrieveAuthPage('Projects.Featured', 'read', false, READPAGE_CURRENT);
    if (!$projectsPage || !is_array($projectsPage) || !isset($projectsPage['text']))
    {
        return "No projects";
    }
    return parseProjects($projectsPage['text']);
}

