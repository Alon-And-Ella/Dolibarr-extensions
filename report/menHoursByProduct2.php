<?php
require_once '../main.inc.php';

if (!$user->rights->produit->lire) {
    dol_print_error('', $user->error);
	exit;
}

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once 'PHPReport.php'; 

$report = new PHPReport($db);

// Set the template for the report (replace 'path_to_your_template' with the actual path to your PHPReport template file)
$report->load(dirname(__FILE__) . '/report_template.php');


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
$data[] = [];
while ($obj = $db->fetch_object($resql)) {
    $hours = $obj->event_hours ? $obj->event_hours : $obj->event_hours2;
    $row = [
        'name' => $obj->project_title,
        'total_hours' => $hours * $obj->total_participants,
    ];
    array_push($data, $row);
}

// Set the data array as a variable in the template
$report->set('data', $data);

$output = $report->generate();

// Output or save the generated report
print $output;

// End of page
llxFooter();
