<?php
require_once '../main.inc.php';

if (!$user->rights->produit->lire) {
    dol_print_error('', $user->error);
	exit;
}

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';

llxHeader('', 'Men Hours per Product Report');

$sql = "SELECT p.rowid as project_id, p.ref as project_ref, p.title as project_title, count(a.rowid) as total_participants, 
            TIMESTAMPDIFF(HOUR, p.date_start_event, p.date_end_event) AS event_hours,
            TIMESTAMPDIFF(HOUR, event.datep, event.datep2) AS event_hours2
        FROM ".MAIN_DB_PREFIX."projet as p
        LEFT JOIN ".MAIN_DB_PREFIX."actioncomm as event ON p.rowid = event.fk_project
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
    print '<td>Event (H)</td>';
    print '<td>Total Participants</td>';
    print '<td>Total Hours</td>';
    print '</tr>';

    // Fetch and display the results
    while ($obj = $db->fetch_object($resql)) {
        $hours = $obj->event_hours ? $obj->event_hours : $obj->event_hours2;
        print '<tr>';
        print '<td>' . $obj->project_ref . ' - ' . $obj->project_title . '</td>';
        print '<td>' . $hours . '</td>';
        print '<td>' . $obj->total_participants . '</td>';
        print '<td>' . $hours * $obj->total_participants . '</td>';
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
