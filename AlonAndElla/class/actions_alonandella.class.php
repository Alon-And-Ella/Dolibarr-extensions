<?php
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
		global $conf, $user, $langs;

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

	}


}
