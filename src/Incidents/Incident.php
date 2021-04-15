<?php declare (strict_types=1);
/**
 * Visitor Class.
 *
 * This file is part of IncidenceDashboardAPI, please read the documentation
 * available in the root level of this project
 *
 * @license MIT
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 *
 */

namespace IncidenceDashboardApi\Incidents;

/**
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 15/04/2021 12:04
 */
class Incident {
	public static function newIncident(array $data){
		$return_result = Incident\Incident::newIncident($data);

		return $return_result;
	}

	public static function viewIncidents(){
		return Incident\Incident::viewIncidents();
	}
	
	public static function viewIncident(int $incidentId){
		return Incident\Incident::viewIncident($incidentId);
	}
}