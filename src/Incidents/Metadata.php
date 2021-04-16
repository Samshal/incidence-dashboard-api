<?php declare (strict_types=1);
/**
 * Controller Class.
 *
 * @license MIT
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 *
 */

namespace IncidenceDashboardApi\Incidents;

use EmmetBlue\Core\Factory\DatabaseConnectionFactory as DBConnectionFactory;
use EmmetBlue\Core\Factory\DatabaseQueryFactory as DBQueryFactory;
use EmmetBlue\Core\Builder\QueryBuilder\QueryBuilder as QB;


/**
 * class IncidenceDashboardApi\Incidents
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 04/16/2021 12:08
 */
class Metadata {
    public static function newValue(array $data){
        $field = $data["field"];
        $value = $data["value"] ?? "";

        $inputData = [
            "Field"=>QB::wrapString($field, "'"),
            "Value"=>QB::wrapString($value, "'")
        ];

        $result = DBQueryFactory::insert("Incidents_MetadataFieldValues", $inputData, false);

        if (!$result['lastInsertId']){
            //throw an exception, insert was unsuccessful
        }   
        
        return $result;
    }

    public static function viewValues(array $data){
        $field = $data["field"];
        $query = "SELECT * FROM Incidents_MetadataFieldValues WHERE Field = '$field'";
        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }
}