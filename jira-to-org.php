<?php


/**
 * @file
 * Retrieves all issues for favorite filters for a user and returns them in a project-based org-mode list
 * 
 * Usage: Update config.php and, from the command line, run:
 * $ php -f jira.php > my-file.org
 */


/**
 * Setup
 */
include_once('./jira-to-org.config.php');
$client = new SoapClient($wsdl);
$token = $client->login($username, $password);

// General server info
$server = $client->getServerInfo($token);

// Get statuses
$result = $client->getStatuses($token);
$statuses = array();
foreach ($result as $status) {
    $statuses[$status->id] = $status->name;
}

// Get projects
$projects = $client->getProjectsNoSchemes($token);
foreach ($projects as $project) {
    $projects[$project->key] = $project;
}

// Get favorite filters
$filters = $client->getFavouriteFilters($token);


/**
 * Construct the org file
 */
$out = array();

// Header

$out[] = "[[$base_url][JIRA $server->edition ($server->version)]] ($username)";
$out[] = '';

foreach ($filters as $filter) {
    // Get issues from a filter
    $out[] = "* Projects ($filter->name)";
    $issues = $client->getIssuesFromFilter($token, $filter->id);
    $projectIssues = array();
    foreach ($issues as $issue) {
        // rearrange issues by project
        $projectIssues[$issue->project][] = $issue;
    }
    // Generate the list of issues, grouped by project
    foreach (array_keys($projectIssues) as $projectKey) {
        $out[] = "** ". $projects[$projectKey]->name;
        foreach ($projectIssues[$projectKey] as $issue) {
            $out[] = "*** [[$base_url/browse/$issue->key][$issue->key]] $issue->summary";
            if ($issue->duedate) {
                $date = date('Y-m-d D', strtotime($issue->duedate));
                $out[] = "    DEADLINE: <$date>";
            }

            $out[] = "    :PROPERTIES:";
            $out[] = "    :status: ". $statuses[$issue->status];
            $out[] = "    :reporter: $issue->reporter";
            $out[] = "    :assignee: $issue->assignee";
            $out[] = "    :END:";
            if ($issue->description) {
                $out[] = '';
                $out[] = $issue->description;
            }
        }
    }
}

// Output
print implode("\n", $out);
print "\n";