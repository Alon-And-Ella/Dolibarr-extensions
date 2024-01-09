<?php
require_once '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';

// Load user and its permissions
// $user = new User($db);
// $user->fetch($_SESSION["dol_login"]);
// $user->getrights();

// // Check permissions
// if (!$user->rights->project->read) {
//     accessforbidden();
// }

// Define the SQL query to retrieve the data

//SUM(a.nbhours) as total_hours 
$sql = "SELECT p.rowid as project_id, p.ref as project_ref, p.title as project_title, count(a.rowid) as total_participants
        FROM ".MAIN_DB_PREFIX."projet as p
        LEFT JOIN ".MAIN_DB_PREFIX."eventorganization_conferenceorboothattendee as a ON p.rowid = a.fk_project
        WHERE p.entity = ".$conf->entity."
        GROUP BY p.rowid";

// Execute the SQL query
$resql = $db->query($sql);
if ($resql) {
    // Start output
    print '<div class="div-table-responsive">';
    print '<table class="border" width="100%">';
    print '<tr class="liste_titre">';
    print '<td>Project</td>';
    print '<td>Event</td>';
    print '<td>Total Participants</td>';
    print '</tr>';

    // Fetch and display the results
    while ($obj = $db->fetch_object($resql)) {
        print '<tr>';
        print '<td>' . $obj->project_ref . ' - ' . $obj->project_title . '</td>';
        print '<td>' . $obj->event_ref . ' - ' . $obj->event_label . '</td>';
        print '<td>' . $obj->total_participants . '</td>';
        print '</tr>';
    }

    // End table
    print '</table>';
    print '</div>';

    // Free the result set
    $db->free($resql);
} else {
    // Handle error
    echo 'Error executing query: ' . $db->lasterror();
}

// End of page
llxFooter();
