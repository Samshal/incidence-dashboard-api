<?php declare (strict_types=1);
/**
 * Controller Class.
 *
 * This file is part of RFHubAPI, please read the documentation
 * available in the root level of this project
 *
 * @license MIT
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 *
 */


namespace IncidenceDashboardApi\SpatialEntity\Entity;

use EmmetBlue\Core\Factory\DatabaseConnectionFactory as DBConnectionFactory;
use EmmetBlue\Core\Factory\DatabaseQueryFactory as DBQueryFactory;
use EmmetBlue\Core\Builder\QueryBuilder\QueryBuilder as QB;


/**
 * class IncidenceDashboardApi\SpatialEntity\Entity\Entity.
 *
 * Entity Controller
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 27/01/2021 09:15
 */
class Entity {
	public static function newEntity(array $data){
        $name = $data["entityName"];
        $type = $data["entityType"];
        $parentId = $data["entityParentId"] ?? null;
        $geometry = $data["entityGeometry"] ?? null;
        $description = $data["description"] ?? "";

        $inputData = [
            "EntityName"=>QB::wrapString($name, "'"),
            "EntityType"=>$type,
            "EntityDescription"=>QB::wrapString($description, "'")
        ];

        if (!is_null($parentId) && $parentId != 0){
            $inputData["EntityParent"] = $parentId;
        }

        if (!is_null($geometry)){
            $inputData["EntityGeometry"] = "'$geometry')";
        }

        $result = DBQueryFactory::insert("spatialentities_entities", $inputData, false);

        if (!$result['lastInsertId']){
			//throw an exception, insert was unsuccessful
		}	
		
		return $result;
    }

    public static function viewEntitiesByType(array $data){
        $type = $data["entityType"];
        $query = "SELECT a.EntityId, a.EntityName, a.EntityType, a.EntityGeometry as EntityGeometry, a.EntityDescription, a.DateCreated, a.LastModified, b.EntityName as EntityParent FROM spatialentities_entities a LEFT OUTER JOIN spatialentities_entities b ON a.EntityParent = b.EntityId WHERE a.EntityType = $type";
        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public static function viewEntityChildren(array $data){
        $entity = $data["entityId"];
        $query = "SELECT EntityId, EntityName, EntityType, EntityGeometry as EntityGeometry, EntityDescription, DateCreated, LastModified FROM spatialentities_entities WHERE EntityParent = $entity";
        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public static function viewEntityTypes(){
        $query = "SELECT * FROM spatialentities_entitytypes;";
        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }
}