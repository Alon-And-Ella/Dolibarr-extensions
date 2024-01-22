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

        dol_syslog('A&E DoActions called! action: ' . $action . ' ctx: ' . $parameters['currentcontext'] . ' obj:' . $object->element);




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

}