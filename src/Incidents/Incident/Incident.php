<?php declare (strict_types=1);
/**
 * Controller Class.
 *
 * @license MIT
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 *
 */

namespace IncidenceDashboardApi\Incidents\Incident;

use EmmetBlue\Core\Factory\DatabaseConnectionFactory as DBConnectionFactory;
use EmmetBlue\Core\Factory\DatabaseQueryFactory as DBQueryFactory;
use EmmetBlue\Core\Builder\QueryBuilder\QueryBuilder as QB;


/**
 * class IncidenceDashboardApi\Incidents\Incident\Incident.
 *
 * Incident Controller
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 04/15/2021 11:59
 */
class Incident {
    public static function newIncident(array $data){
        $name = $data["name"];
        $type = $data["type"];
        $location = $data["location"];
        $data = $data["date"];
        $pointOfInterest = $data["pointOfInterest"] ?? NULL;
        $description = $data["description"] ?? "";

        $query = "INSERT INTO Incidents_Incidents(IncidentName, IncidentType, IncidentLocation, IncidentDate, IncidentPointOfInterest, IncidentDescription) VALUES ()"

        $result = DBQueryFactory::insert("Incidents_IncidentCategories", $inputData, false);

        if (!$result['lastInsertId']){
            //throw an exception, insert was unsuccessful
        }   
        
        return $result;
    }

	public static function newIncident(array $data){
        $name = $data["name"];
        $category = $data["category"];
        $description = $data["description"] ?? "";

        $inputData = [
            "IncidentName"=>QB::wrapString($name, "'"),
            "IncidentCategoryId"=>$category,
            "IncidentDescription"=>QB::wrapString($description, "'")
        ];

        $result = DBQueryFactory::insert("Incidents_Incidents", $inputData, false);

        if (!$result['lastInsertId']){
			//throw an exception, insert was unsuccessful
		}	
		
		return $result;
    }

    public static function viewCategories(){
        $query = "SELECT * FROM Incidents_IncidentCategories";
        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public static function viewIncidents(int $categoryId=0){
        $query = "SELECT * FROM Incidents_Incidents WHERE IncidentCategoryId = $categoryId";
        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }
}