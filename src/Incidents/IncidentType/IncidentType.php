<?php declare (strict_types=1);
/**
 * Controller Class.
 *
 * @license MIT
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 *
 */

namespace IncidenceDashboardApi\Incidents\IncidentType;

use EmmetBlue\Core\Factory\DatabaseConnectionFactory as DBConnectionFactory;
use EmmetBlue\Core\Factory\DatabaseQueryFactory as DBQueryFactory;
use EmmetBlue\Core\Builder\QueryBuilder\QueryBuilder as QB;


/**
 * class IncidenceDashboardApi\Incidents\IncidentType\IncidentType.
 *
 * IncidentType Controller
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 04/15/2021 09:25
 */
class IncidentType {
    public static function newIncidentCategory(array $data){
        $name = $data["name"];
        $description = $data["description"] ?? "";

        $inputData = [
            "IncidentCategoryName"=>QB::wrapString($name, "'"),
            "IncidentCategoryDescription"=>QB::wrapString($description, "'")
        ];

        $result = DBQueryFactory::insert("Incidents_IncidentCategories", $inputData, false);

        if (!$result['lastInsertId']){
            //throw an exception, insert was unsuccessful
        }   
        
        return $result;
    }

	public static function newIncidentType(array $data){
        $name = $data["name"];
        $category = $data["category"];
        $description = $data["description"] ?? "";

        $inputData = [
            "IncidentTypeName"=>QB::wrapString($name, "'"),
            "IncidentCategoryId"=>$category,
            "IncidentTypeDescription"=>QB::wrapString($description, "'")
        ];

        $result = DBQueryFactory::insert("Incidents_IncidentTypes", $inputData, false);

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

    public static function viewIncidentTypes(int $categoryId=0){
        $query = "SELECT * FROM Incidents_IncidentTypes a INNER JOIN Incidents_IncidentCategories b ON a.IncidentCategoryId = b.IncidentCategoryId";

        if ($categoryId != 0){
            $query .= " WHERE a.IncidentCategoryId = $categoryId";
        }

        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }
}