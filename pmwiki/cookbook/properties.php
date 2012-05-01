<?php if (!defined('PmWiki')) exit();

/**
 * Given an array of lines, parse as properties and return an associative array.
 */
function parseProperties($lines)
{
    $continuation = null;
    $properties = array();
    foreach ($lines as $line)
    {
        $line = trim($line);
        if (strlen($line) > 0)
        {
            if ($continuation)
            {
                if (substr($line, -1) == '\\')
                {
                    $line = substr($line, 0, -1);
                    $properties[$continuation] .= $line;
                }
                else
                {
                    $properties[$continuation] .= $line;
                    $continuation = null;
                }
            }
            else
            {
                if (preg_match("/^([^:]+):\\s*(.*)$/", $line, $matches) > 0)
                {
                    if (substr($matches[2], -1) == '\\')
                    {
                        $matches[2] = substr($matches[2], 0, -1);
                        $continuation = $matches[1];
                    }
                    else
                    {
                        $continuation = null;
                    }
                    $properties[$matches[1]] = $matches[2];
                }
            }
        }
    }
    return $properties;
}

/**
 * Add a property to the internal state ($s), and when we have a whole object,
 * return it.
 */
function addProperty(&$s, $argument, $type)
{
    // Make sure our state is valid
    $output = null;
    $current = null;
    $properties = array();
    if (isset($s['cur']))
        $current = $s['cur'];
    if (isset($s['prop']))
        $properties = $s['prop'];
        
    if (preg_match("/^$type\\s*([^\\s:]*?)\\s*$/i", $argument, $matches) > 0)
    {
        if ($current)
        {
            $output = array($current, $properties);
        }
        $current = $matches[1];
        $properties = array();
    }
    else if (preg_match("/^end\\s*$/i", $argument) > 0)
    {
        if ($current)
        {
            $output = array($current, $properties);
        }
        $current = null;
        $properties = array();
    }
    else if (preg_match("/^\\s*([^\\s:]*):\\s*(.*?)\\s*$/i", $argument, $matches) > 0)
    {
        if ($current)
        {
            $properties[$matches[1]] = $matches[2];
        }
    }
    
    // Save the state
    $s['cur'] = $current;
    $s['prop'] = $properties;
    return $output;
}

