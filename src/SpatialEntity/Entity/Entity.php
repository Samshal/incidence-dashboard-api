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


namespace RFHApi\SpatialEntity\Entity;

use EmmetBlue\Core\Factory\DatabaseConnectionFactory as DBConnectionFactory;
use EmmetBlue\Core\Factory\DatabaseQueryFactory as DBQueryFactory;
use EmmetBlue\Core\Builder\QueryBuilder\QueryBuilder as QB;


/**
 * class RFHApi\SpatialEntity\Entity\Entity.
 *
 * Entity Controller
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 27/01/2021 09:15
 */
class Entity {
	public function newEntity(array $data){
        $name = $data["entityName"];
        $type = $data["entityType"];
        $parentId = $data["entityParentId"] ?? null;
        $geometry = $data["entityGeometry"] ?? null;
        $description = $data["description"] ?? "";

        $inputData = [
            "EntityName"=>QB::wrapString($name, "'"),
            "EntityType"=>QB::wrapString($type, "'"),
            "EntityDescription"=>QB::wrapString($description, "'")
        ];

        if (!is_null($parentId)){
            $inputData["EntityParent"] = $parentId;
        }

        if (!is_null($geometry)){
            $inputData["EntityGeometry"] = "ST_GeomFromText('$geometry')";
        }

        $result = DBQueryFactory::insert("SpatialEntities_Entities", $inputData, false);

        if (!$result['lastInsertId']){
			//throw an exception, insert was unsuccessful
		}	
		
		return $result;
    }

    public function viewEntitiesByType(array $data){
        $type = $data["entityType"];
        $query = "SELECT EntityId, EntityName, EntityType, ST_AsText(EntityGeometry) as EntityGeometry, EntityDescription, DateCreated, LastModified FROM SpatialEntities_Entities WHERE EntityType = $type";
        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public function viewEntityChildren(array $data){
        $entity = $data["entityId"];
        $query = "SELECT EntityId, EntityName, EntityType, ST_AsText(EntityGeometry) as EntityGeometry, EntityDescription, DateCreated, LastModified FROM SpatialEntities_Entities WHERE EntityParent = $entity";
        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public function viewEntityTypes(){
        $query = "SELECT * FROM SpatialEntities_EntityTypes;";
        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }
}