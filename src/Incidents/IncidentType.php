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
 * @since v0.0.1 11/04/2021 09:25
 */
class IncidentType {
	public static function newIncidentCategory(array $data){
		$return_result = IncidentType\IncidentType::newIncidentCategory($data);

		return $return_result;
	}

	public static function newIncidentType(array $data){
		$return_result = IncidentType\IncidentType::newIncidentType($data);

		return $return_result;
	}
	
	public static function viewCategories(){
		return IncidentType\IncidentType::viewCategories();
	}
	
	public static function viewIncidentTypes(int $categoryId){
		return IncidentType\IncidentType::viewIncidentTypes($categoryId);
	}
}