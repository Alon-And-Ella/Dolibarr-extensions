<?php


include_once DOL_DOCUMENT_ROOT.'/core/boxes/modules_boxes.php';


/**
 * Class to manage the box to show user birthdays
 */
class test_box extends ModeleBoxes
{
	public $boxcode = "test_box";
	public $boximg = "object_user";
	public $boxlabel = "BoxMyTest";
	public $depends = array(); //"adherent");
    //public $enabled = true;
    //public $version = array("development");

	/**
	 * @var DoliDB Database handler.
	 */
	public $db;

	public $enabled = 1;

	public $info_box_head = array();
	public $info_box_contents = array();


	/**
	 *  Constructor
	 *
	 *  @param  DoliDB	$db      	Database handler
	 *  @param	string	$param		More parameters
	 */
	public function __construct($db, $param = '')
	{
		global $user;

		$this->db = $db;

		$this->hidden = false; //!($user->hasRight("adherent", "lire") && empty($user->socid));
	}

	/**
	 *  Load data for box to show them later
	 *
	 *  @param	int		$max        Maximum number of records to load
	 *  @return	void
	 */
	public function loadBox($max = 20)
	{
		global $conf, $user, $langs;
        dol_syslog("A&E: Test_Box widget load");

		// include_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
		// include_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
		// $memberstatic = new Adherent($this->db);

		// $langs->load("boxes");

		// $this->max = $max;

		 $this->info_box_head = array('text' => 'ווידגט לדוגמא');

		// if ($user->hasRight('adherent', 'lire')) {
		// 	$data = array();

		// 	$tmparray = dol_getdate(dol_now(), true);

		// 	$sql = "SELECT u.rowid, u.firstname, u.lastname, u.societe, u.birth, date_format(u.birth, '%d') as daya, u.email, u.statut as status, u.datefin";
		// 	$sql .= " FROM ".MAIN_DB_PREFIX."adherent as u";
		// 	$sql .= " WHERE u.entity IN (".getEntity('adherent').")";
		// 	$sql .= " AND u.statut = ".Adherent::STATUS_VALIDATED;
		// 	$sql .= dolSqlDateFilter('u.birth', 0, $tmparray['mon'], 0);
		// 	$sql .= " ORDER BY daya ASC";	// We want to have date of the month sorted by the day without taking into consideration the year
		// 	$sql .= $this->db->plimit($max, 0);

		// 	dol_syslog(get_class($this)."::loadBox", LOG_DEBUG);
		// 	$resql = $this->db->query($sql);
		// 	if ($resql) {
		// 		$num = $this->db->num_rows($resql);

		// 		$line = 0;
		// 		while ($line < $num) {
		// 			$data[$line] = $this->db->fetch_object($resql);

		// 			$line++;
		// 		}

		// 		$this->db->free($resql);
		// 	}

		// 	if (!empty($data)) {
		// 		$j = 0;
		// 		while ($j < count($data)) {
		// 			$memberstatic->id = $data[$j]->rowid;
		// 			$memberstatic->firstname = $data[$j]->firstname;
		// 			$memberstatic->lastname = $data[$j]->lastname;
		// 			$memberstatic->company = $data[$j]->societe;
		// 			$memberstatic->email = $data[$j]->email;
		// 			$memberstatic->status = $data[$j]->status;
		// 			$memberstatic->statut = $data[$j]->status;
		// 			$memberstatic->datefin = $this->db->jdate($data[$j]->datefin);

		// 			$dateb = $this->db->jdate($data[$j]->birth);
		// 			$age = date('Y', dol_now()) - date('Y', $dateb);

		// 			$typea = '<i class="fas fa-birthday-cake inline-block"></i>';

		// 			$this->info_box_contents[$j][0] = array(
		// 				'td' => '',
		// 				'text' => $memberstatic->getNomUrl(1),
		// 				'asis' => 1,
		// 			);

		// 			$this->info_box_contents[$j][1] = array(
		// 				'td' => 'class="center nowraponall"',
		// 				'text' => dol_print_date($dateb, "day", 'tzserver').' - '.$age.' '.$langs->trans('DurationYears')
		// 			);

		// 			$this->info_box_contents[$j][2] = array(
		// 				'td' => 'class="right nowraponall"',
		// 				'text' => $typea,
		// 				'asis' => 1
		// 			);

		// 			/*$this->info_box_contents[$j][3] = array(
		// 			 'td' => 'class="right" width="18"',
		// 			 'text' => $memberstatic->LibStatut($objp->status, 3)
		// 			 );*/

		// 			$j++;
		// 		}
		// 	}
		// 	if (is_array($data) && count($data) == 0) {
		// 		$this->info_box_contents[0][0] = array(
		// 			'td' => 'class="center"',
		// 			'text' => '<span class="opacitymedium">'.$langs->trans("None").'</span>',
		// 		);
		// 	}
		// } else {
			$this->info_box_contents[0][0] = array(
								'td' => 'class="right nowraponall"',
								'text' => 'ariel'
								//'asis' => 1
							);
							$this->info_box_contents[0][1] = array(
								'td' => 'class="center"',
								'text' => 'some data'
								//'asis' => 1
							);
			// $this->info_box_contents[0][0] = array(
			// 	'td' => 'class="nohover left"',
			// 	'text' => '<span class="opacitymedium">'.'תוכן לדוגמא'.'</span>'
			// );
		// }
	}

	/**
	 *	Method to show box
	 *
	 *	@param	array	$head       Array with properties of box title
	 *	@param  array	$contents   Array with properties of box lines
	 *  @param	int		$nooutput	No print, only return string
	 *	@return	string
	 */
	public function showBox($head = null, $contents = null, $nooutput = 0)
	{
        dol_syslog("A&E: Test_Box widget showBox");

		return parent::showBox($this->info_box_head, $this->info_box_contents, $nooutput);
	}
}
