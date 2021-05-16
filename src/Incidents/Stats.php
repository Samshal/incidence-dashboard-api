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
 * @since v0.0.1 16/05/2021 14:04
 */
class Stats {	
	public static function byRegion(array $data = []){
		return Stats\IncidentStats::loadByRegion($data);
	}

	public static function byState(array $data = []){
		return Stats\IncidentStats::loadByState($data);
	}

	public static function loadTypesByRegion(array $data = []){
		return Stats\IncidentStats::loadTypesByRegion($data);
	}

	public static function loadTrendsByRegion(array $data = []){
		return Stats\IncidentStats::loadTrendsByRegion($data);
	}

	public static function loadIncidencesByRegion(array $data = []){
		return [
			"types"=>self::loadTypesByRegion($data),
			"trends"=>self::loadTrendsByRegion($data)
		];
	}
}