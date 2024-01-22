<?php
require_once '../main.inc.php';

if (!$user->hasRight("fournisseur", "commande", "lire")) {
    dol_print_error('', $user->error);
	exit;
}


include './PHPReport.php';

$template = 'purchaseOrders_template.xlsx';
	
//set absolute path to directory with template files
$templateDir = __DIR__ . '/';
$config=array(
    'template'=>$template,
    'templateDir'=>$templateDir
);

// Get the purchase orders DATA
// ----------------------------
$sql = "SELECT
    po.ref AS ref,
    po.date_creation AS order_date,
    po.total_ht as amount,
    po.note_private,
    po.note_public,

    v.rowid AS vendor_id,
    v.nom AS vendor,
    CONCAT(u.lastname, ', ', u.firstname) AS approver1,
    CONCAT(u.lastname, ', ', u.firstname) AS approver2,
    pt.code AS payment_term
FROM
    llx_commande_fournisseur AS po
    INNER JOIN llx_societe AS v ON po.fk_soc = v.rowid
    LEFT OUTER JOIN llx_user AS u ON po.fk_user_approve = u.rowid
    LEFT OUTER JOIN llx_user AS u2 ON po.fk_user_approve2 = u2.rowid
    LEFT OUTER JOIN llx_c_payment_term AS pt ON po.fk_cond_reglement = pt.rowid
WHERE fk_statut = 2"; // -- Assuming '2' represents the status for approved purchase orders


// Execute the SQL query
$resql = $db->query($sql);
if ($resql) {
    $purchaseOrders = $resql->fetch_all(MYSQLI_ASSOC);


    if (count($purchaseOrders) == 0) {
        // orders
        llxHeader('', 'הזמנות רכש');
        exit( "<h1>אין הזמנות רכש מאושרות</h1>");
    }


    // Translate the payment terms
    $langs->loadLangs(array('bills'));

    $modifiedPurchaseOrders = array_map(function($order) use ($langs) {
        // Replace the 'vendor' field with a new value
        if (!empty($order['payment_term'])) {
            $order['payment_term'] = $langs->trans("PaymentCondition" . $order['payment_term']);
        }
    
        // Return the modified order
        return $order;
    }, $purchaseOrders);

    $R=new PHPReport($config);

    $R->load(
        array(
        array(
            'id'=>'header',
            'data'=>array('date'=>date('Y-m-d')),
            'format'=>array(
                    'date'=>array('datetime'=>'d/m/Y')
                )
            ),
            array(
                'id'=>'ord',
                'repeat'=>true,
                'data'=>$modifiedPurchaseOrders,
                'minRows'=>2,
                'format'=>array(
                        'amount'=>array('number'=>array('decimals'=>2, 'thousandsSep'=>'')),
                        'order_date'=>array('datetime'=>'d/m/Y')
                    )
                )
            )
        );



    echo $R->render('excel');
}