<?php
if (isModEnabled('project')) {
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
}


class ActionsAlonAndElla
{
	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs, $db;

		$error = 0; // Error counter

        dol_syslog('A&E doActions called! action: ' . $action . ' ctx: ' . $parameters['currentcontext'] . ' obj:' . $object->element);




		//if (in_array($parameters['currentcontext'], array('printFieldPreListTitle'))) {	   
        if ($object->element === 'product') {
            if ($action == 'create') {

            } else {
                if (!empty($parameters['arrayfields'])) {
                    foreach ($parameters['arrayfields'] as $key => &$field) {
                        if ($key === 'p.minbuyprice' || $key === 'p.sellprice') {
                            dol_syslog('A&E field is being hidden: ' . $key );
                            $field['checked'] = 0;
                        }
                    }
                }
            }
        }

		if (!$error) {
			return 0; 
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}

	public function formConfirm($parameters, &$object, &$action, $hookmanager) {
		global $user;

		//dol_syslog('A&E formConfirm called! action: ' . $action . ' ctx: ' . $parameters['currentcontext'] . ' obj:' . $object->element);

		// if ($object->element === "commande") {
		// 	dol_syslog('fk_project'.$object->fk_project);
		// 	if (!empty($object->fk_project)) {
		// 		$res = $object->fetch_project();
		// 		if ($res > 0) {
		// 			dol_syslog('event.max attandees:'.$object->project->max_attendees);
		// 		}
		// 	}
		// }

		if ($action === "confirm_validate" && $object->element === "commande") {
			// When an order is validated, and it is connected to a project we import the 
			// max-attendees of the service into the project
			if (!empty($object->fk_project)) {
				$res = $object->fetch_project();
				if ($res > 0) {
					if (!empty($object->lines)) {
						foreach ($object->lines as $line) {
							$res = $line->fetch_product();
							if ($res  > 0 ) {
								//dol_syslog("line" . $line->product->label." max_vol:". $line->product->array_options['options_max_volenteers']);
								if ($line->product->array_options['options_max_volenteers'] > 0) {
									$object->project->max_attendees = $line->product->array_options['options_max_volenteers'];
									$object->project->update($user, 1);
								}
								// only uses the first line - TODO what if more than one?
								break;
							}
						}
					}
				}
			}
		} 
		// else if ($action === "import_tasks" && $object->element === 'project') {
		// 	print '<div>test</div>';
		// }

	}

	public function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager) {
		dol_syslog('A&E addMoreActionsButtons called! action: ' . $action . ' ctx: ' . $parameters['currentcontext'] . ' obj:' . $object->element);
		global $conf, $user, $langs, $db;
		if ($object->element === 'project') {
			if ($object->statut != Project::STATUS_CLOSED) {
				if ($action === "import_tasks") {
					$formproject = new FormProjets($db);
					$langs->load("projects");
					print '<form method="post" action="'.$_SERVER["PHP_SELF"].'?action=do_import_tasks&token='.newToken().'&id='.$object->id.'">';

					print '<tr>';
					print '<td>בחר פרוייקט ליבוא משימותיו</td><td>';
					print img_picto('', 'project', 'class="pictofixedwidth"');
					print $formproject->select_projects( -1, 
						(GETPOSTISSET('from_projectid')?GETPOST('from_projectid'):-1), 
						'from_projectid', 0, 0, 1,
						 1, 0, 0, 0, 
						 'תבנית', //$filterkey - a filter to the label of projects
						 1, 0, 
						'maxwidth500 widthcentpercentminusxx', ''
					);
					print '</td>';
					print '</tr>';

					print '<input type="submit" class="button valignmiddle" value="'.'בצע יבוא'.'">';
					print dolGetButtonAction('', 'בטל', 'default', $_SERVER["PHP_SELF"].'?token='.newToken().'&id='.$object->id, '');

					print '</form>';
					return 1; //no other buttons
				} else if ($action === "do_import_tasks") {
					$fromProjectId = GETPOST('from_projectid', 'int');
					$toId = $object->id;

					dol_syslog('A&E import tasks called! from: ' . $fromProjectId . ' into: ' . $toId);

					// Actually clone tasks from one project to the other:

					$this->cloneProjectTasks($db, $user, $fromProjectId, $toId);
					print '<div>משימות הועתקו בהצלחה</div>';
				} else if ($action === "") {
					print dolGetButtonAction('', 'יבא משימות', 'default', $_SERVER["PHP_SELF"].'?action=import_tasks&token='.newToken().'&id='.$object->id.'&mode=init', '');
				}
			}
		}
	}

	public function addOpenElementsDashboardGroup($parameters, &$object, &$action, $hookmanager) {
		$dashboardgroup = $parameters['dashboardgroup'];
		if (!empty($dashboardgroup)){
			$hookmanager->resArray = array();
			$projectGroup = $dashboardgroup['project'];
			if (!empty($projectGroup)) {
				array_unshift($projectGroup['stats'], 'draft_projects');

				$hookmanager->resArray['project'] = $projectGroup;
			}

			$orderGroup = $dashboardgroup['order_supplier'];
			if (!empty($orderGroup)) {
				array_unshift($orderGroup['stats'], 'order_supplier_await_approval');

				$hookmanager->resArray['order_supplier'] = $orderGroup;
			}

			return 0;
		}
		return -1;
	}


	public function addOpenElementsDashboardLine($parameters, &$object, &$action, $hookmanager) {
		global $conf, $user, $langs, $db;

		$draftProjectWB = new WorkboardResponse();
		$draftProjectWB->id = "1";
		$draftProjectWB->warning_delay = 'טיוטות מתעכבות';
		$draftProjectWB->label = "טיוטא";
		$draftProjectWB->labelShort = "טיוטא";
		$draftProjectWB->url = DOL_URL_ROOT."/projet/list.php?search_status=0";
		$draftProjectWB->img = img_object('', "project");
		$draftProjectWB->nbtodo = 0;
		$draftProjectWB->nbtodolate = 0;
		$draftProjectWB->warning_delay = 7;
		
		$now = dol_now('gmt');
		// Draft projects
		$sql = "SELECT 
					p.rowid AS project_id, 
					p.datec AS creation_date
				FROM ".MAIN_DB_PREFIX."projet AS p
				WHERE p.fk_statut = 0";

		$resql = $db->query($sql);
		if ($resql) {
			while ($obj = $db->fetch_object($resql)) {
				$draftProjectWB->nbtodo++;

				$tdate_create = $db->jdate($obj->creation_date);
				// Calculate the difference in seconds
				$diffInSeconds = $now - $tdate_create;

				// Calculate the number of days
				$daysDifference = floor($diffInSeconds / (60 * 60 * 24));
				if ($daysDifference > 7) {
					$draftProjectWB->nbtodolate++;
				}
			}
			$db->free($resql);
			$hookmanager->resArray['draft_projects'] = $draftProjectWB;
			
		}

		$awatingApproval = new WorkboardResponse();
		$awatingApproval->id = "1";
		$awatingApproval->warning_delay = 'ממתינות לאישור';
		$awatingApproval->label = "ממתינות לאישור";
		$awatingApproval->labelShort = "ממתינות לאישור";
		$awatingApproval->url = DOL_URL_ROOT."/fourn/commande/list.php?search_status=1";
		$awatingApproval->img = img_object('', "order");
		$awatingApproval->nbtodo = 2;
		$awatingApproval->nbtodolate = 1;
		$awatingApproval->warning_delay = 4;

		// Open Orders
		$sql = "SELECT rowid , date_valid FROM ".MAIN_DB_PREFIX."commande_fournisseur WHERE fk_statut=1";
		
		$resql = $db->query($sql);
		if ($resql) {
			while ($obj = $db->fetch_object($resql)) {
				$awatingApproval->nbtodo++;

				$tdate_validated = $db->jdate($obj->datev);
				// Calculate the difference in seconds
				$diffInSeconds = $now - $tdate_validated;

				// Calculate the number of days
				$daysDifference = floor($diffInSeconds / (60 * 60 * 24));
				if ($daysDifference > 4) {
					$awatingApproval->nbtodolate++;
				}
			}
			$db->free($resql);
			$hookmanager->resArray['order_supplier_await_approval'] = $awatingApproval;
			
		}

		$hookmanager->resArray['order_supplier_await_approval'] = $awatingApproval;
		return 0;
	}


	function cloneProjectTasks($db, $user, $fromid, $targetId) {

		// Copied the task cloning logic from the project cloning in project.class.php

		require_once DOL_DOCUMENT_ROOT.'/projet/class/task.class.php';

		$taskstatic = new Task($db);

		// Security check
		$socid = 0;
		if ($user->socid > 0) {
			$socid = $user->socid;
		}

		$tasksarray = $taskstatic->getTasksArray(0, 0, $fromid, $socid, 0);

		$tab_conv_child_parent = array();

		// Loop on each task, to clone it
		foreach ($tasksarray as $tasktoclone) {
			$result_clone = $taskstatic->createFromClone($user, $tasktoclone->id, $targetId, $tasktoclone->fk_task_parent, $move_date, true, false, $clone_task_file, true, false);
			if ($result_clone <= 0) {
				$this->error .= $taskstatic->error;
				$error++;
			} else {
				$new_task_id = $result_clone;
				$taskstatic->fetch($tasktoclone->id);

				//manage new parent clone task id
				// if the current task has child we store the original task id and the equivalent clone task id
				if (($taskstatic->hasChildren()) && !array_key_exists($tasktoclone->id, $tab_conv_child_parent)) {
					$tab_conv_child_parent[$tasktoclone->id] = $new_task_id;
				}
			}
		}

		//Parse all clone node to be sure to update new parent
		$tasksarray = $taskstatic->getTasksArray(0, 0, $targetId, $socid, 0);
		foreach ($tasksarray as $task_cloned) {
			$taskstatic->fetch($task_cloned->id);
			if ($taskstatic->fk_task_parent != 0) {
				$taskstatic->fk_task_parent = $tab_conv_child_parent[$taskstatic->fk_task_parent];
			}
			$res = $taskstatic->update($user, $notrigger);
			if ($result_clone <= 0) {
				$this->error .= $taskstatic->error;
				$error++;
			}
		}
	}

}
