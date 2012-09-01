<?php if (!defined('PmWiki')) exit();

/*
 * Seriously, this is really fucking confusing. Templates are not very flexible
 * in PmWiki, and I had to wrangle everything into submission. If you're the one
 * tasked with maintaining this or you think you know a better way of doing it,
 * feel free to contact me.
 *
 * Note that there are many places where the URL of the wiki is assumed to be /
 *
 *  - Ben Moench
 */


loadTemplate($pagename);

function shouldDisplayMain($page)
{
	if ($page == 'Main.HomePage' && $GLOBALS['action'] == 'browse')
	{
		return true;
	}
	return false;
}

function loadTemplate($page)
{
	global $SkinDir;
	if (shouldDisplayMain($page))
	{
		// No need to include this stuff for every page load; include it here
		include_once('cookbook/mainpage.php');

		// Load and generate the HTML code for upcoming UPL events
		$GLOBALS['QuoteHtml'] = ''; // loadQuote(); // Temporarily disable the quote
		$GLOBALS['ProjectsHtml'] = generateProjectsHtml(loadProjects());
		$GLOBALS['EventsHtml'] = generateEventsHtml(loadEvents());
		
		$GLOBALS['PageContents'] = getIncludeContents("$SkinDir/mainpage.tmpl.php");
	}
	else
	{
		$GLOBALS['PageContents'] = '</div>';
	}
	
	LoadPageTemplate($page, "$SkinDir/upl.tmpl");
}

function includeTitle($page, $titleSpaced)
{
	if (shouldDisplayMain($page))
	{
	}
	else
	{
		// Some boilerplate HTML that goes before a normal page
		$before = <<<EOT
<div id="wrappertop">
	<div class="padding5">
		<div class="bigheading">
EOT;

		$after = <<<EOT
		</div>
		<div class="verticalspacer15"></div>
	</div>
</div>
<div class="verticalspacer30"></div>

</div>

<div id="content">
EOT;

		echo $before;
		
		// Determine whether or not to show the group
		$names = explode('.', $page);
		if (strtolower($names[0]) === strtolower($names[1]))
		{
			echo $titleSpaced;
		}
		else
		{
			echo "<a href=\"/${names[0]}\">${names[0]}</a> . $titleSpaced";
		}
		
		echo $after;
	}
}

function afterPageText($page, $titleSpaced)
{
	if (!shouldDisplayMain($page))
	{
		echo '<br/>';
	}
}

function generateProjectsHtml($projects)
{
    $total = 8;
    if (count($projects) < $total)
    {
        for ($i = count($projects); $i < $total; $i++)
        {
            array_push($projects, array('', array()));
        }
    }
    else if (count($projects) < $total)
    {
        $count = $total - count($projects);
        array_splice($projects, -$count, $count);
    }
    
    // Map a project into its HTML
    function formatProject($project)
    {
        return
            '<div class = "box"><a href="' . @$project[1]['url'] . '" target="_blank" class="boxlabel">' .
            $project[0] . '<br/>' . 
            '<span class="projectshorttext">' . @$project[1]['short'] . '</span></a></div>';
    }
    
    return implode("\n", array_map(formatProject, $projects));
}

function generateEventsHtml($events)
{
    if (count($events) < 1)
    {
        // TODO This should be improved
        return '<div class="mediumheading"></div><article><em>No upcoming events.</em></article>';
    }
    
    // Map an event into its HTML
    function formatEvent($event)
    {
        if (strlen($event['description']) < 1)
        {
            // HACK There's probably a better way of doing this
            $event['description'] = '<em>No description.</em>';
        }
        
        return
            '<div class = "mediumheading">' . $event['summary'] .
            ": " . formatEventDate($event['start'], $event['end']) .
            "</div>\n" .
            "<article>\n" .
            $event['description'] .
            "</article>\n";
    }
    
    return implode("\n", array_map(formatEvent, $events));
}

function getIncludeContents($filename)
{
    if (is_file($filename))
    {
        ob_start();
        include $filename;
        return ob_get_clean();
    }
    return '';
}

