<?php if (!defined('PmWiki')) exit();
include_once('cookbook/secret.php');
include_once('cookbook/google-api-php-client/src/apiClient.php');
include_once('cookbook/google-api-php-client/src/contrib/apiCalendarService.php');


/**
 * Load the list of upcoming events in the UPL and return an array containing
 * an array of each event's information.
 */
function loadEvents()
{
    if (!class_exists('apiClient'))
    {
        return array();
    }
    
    // Load up the Google API client
    $client = new apiClient();
    $client->setApplicationName('UPL Website');
    $client->setUseObjects(true);
    apiKey($client);
    $cal = new apiCalendarService($client);
    
    // List all events in the UPL calendar
    $calendarId = '42jcv5rucaf2h6ehk89gbbog5g@group.calendar.google.com';
    $args = array(
        'singleEvents' => true,
        'orderBy' => 'startTime',
        'maxResults' => 5,
        'timeMin' => date(DateTime::RFC3339)
        // TODO Consider a maximum time
    );
    $events = $cal->events->listEvents($calendarId, $args);
    
    // Make sure that we at least have some events
    if (!$events)
    {
        return array();
    }
    $events = $events->getItems();
    if (!is_array($events))
    {
        return array();
    }
    
    // Just a little helper to convert dates
    function makeDate($evtDate)
    {
        return DateTime::createFromFormat(DateTime::RFC3339, $evtDate->getDateTime());
    }
    
    function mapEvent($event)
    {
        return array(
            'summary' => $event->getSummary(),
            'start' => makeDate($event->getStart()),
            'end' => makeDate($event->getEnd()),
            'description' => $event->getDescription(),
            'link' => $event->getHtmlLink()
        );
    }

    // Retrieve all our events
    return array_map(mapEvent, $events);
}

/**
 * Install a declaration that will let us insert a list of upcoming events into
 * the page.
 */
function installEventsMarkup()
{
    Markup('events', 'directives', '/\\(:events:\\)/e', "eventsMarkupCall()");
}

function eventsMarkupCall()
{
    $events = loadEvents();
    
    if (count($events) < 1)
    {
        return '<em>No upcoming events.</em>';
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
            '<h3>' . $event['summary'] .
            ": " . formatEventDate($event['start'], $event['end']) .
            "</h3>\n" .
            "<article>\n" .
            $event['description'] .
            "</article>\n";
    }
    
    return implode("\n", array_map(formatEvent, $events));
}

function formatEventDate($start, $end)
{
    return $start->format("l, F j") . " at " . $start->format("g:i A");
}

