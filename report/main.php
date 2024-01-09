<?php
require_once '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';

if (!$user->rights->produit->lire) {
    dol_print_error('', $user->error);
	exit;
}


// Create a new Form instance
$form = new Form($db);

// Set the page title
llxHeader('', 'Products Report');

// Start output
print '<div class="div-table-responsive">';
print '<table class="border" width="100%">';
print '<tr class="liste_titre">';
print '<td>' . $langs->trans('Ref') . '</td>';
print '<td>' . $langs->trans('Label') . '</td>';
print '<td>' . $langs->trans('Price') . '</td>';
print '</tr>';

// Retrieve list of products from the database
$sql = "SELECT p.ref, p.label, p.price FROM ".MAIN_DB_PREFIX."product as p";
$resql = $db->query($sql);
if ($resql) {
    while ($obj = $db->fetch_object($resql)) {
        print '<tr>';
        print '<td>' . $obj->ref . '</td>';
        print '<td>' . $obj->label . '</td>';
        print '<td>' . $obj->price . '</td>';
        print '</tr>';
    }
    $db->free($resql);
}

// End table
print '</table>';
print '</div>';

// End of page
llxFooter();
