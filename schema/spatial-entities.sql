USE RFHApiDB;

-- This schema stores Geographic/Geospatial Features.
-- A geographic feature is anything in the world that has a location. A feature can be:
-- An entity. For example, a farm.
-- A space. For example, a city, country, etc.

CREATE TABLE SpatialEntities_EntityTypes (
	SpatialEntityTypeId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	SpatialEntityTypeName VARCHAR(50) NOT NULL UNIQUE,
	AdminLevel SMALLINT NOT NULL DEFAULT 256,
	SpatialEntityTypeDescription VARCHAR(500)
);

CREATE TABLE SpatialEntities_MetadataFieldTypes (
	TypeName VARCHAR(50) NOT NULL PRIMARY KEY,
	TypeDescription VARCHAR(500)
);

CREATE TABLE SpatialEntities_MetadataFields (
	FieldId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	FieldType VARCHAR(50) NOT NULL,
	FieldName VARCHAR(50) NOT NULL UNIQUE,
	FieldDescription VARCHAR(500),

	CONSTRAINT fk_MetadataFields_SpatialEntities_MetadataFieldTypes
		FOREIGN KEY (FieldType) REFERENCES SpatialEntities_MetadataFieldTypes (TypeName) ON UPDATE CASCADE ON DELETE NO ACTION
);

CREATE TABLE SpatialEntities_Entities (
	EntityId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	EntityName VARCHAR(256),
	EntityType INT NOT NULL,
	EntityParent INT,
	EntityGeometry GEOMETRY SRID 0,
	EntityDescription VARCHAR(500),
	DateCreated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	LastModified DATETIME,

	CONSTRAINT u_Entity_EntityName_EntityType
		UNIQUE (EntityName, EntityType),
	CONSTRAINT fk_Entities_SpatialEntities_EntityTypes_SpatialEntityTypeId
		FOREIGN KEY (EntityType) REFERENCES SpatialEntities_EntityTypes (SpatialEntityTypeId) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT fk_Entities_SpatialEntities_Entities_SpatialEntities_EntityId
		FOREIGN KEY (EntityParent) REFERENCES SpatialEntities_Entities (EntityId) ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE SpatialEntities_EntityMetadata (
	MetadataId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	EntityId INT NOT NULL,
	FieldId INT NOT NULL,
	FieldValue VARCHAR(256),
	DateCreated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	LastModified DATETIME,
	
	CONSTRAINT u_EntityMetadata_EntityId_FieldId
		UNIQUE(EntityId, FieldId),
	CONSTRAINT fk_EntityMetadata_SpatialEntities_Entities_EntityId
		FOREIGN KEY (EntityId) REFERENCES SpatialEntities_Entities (EntityId) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT fk_EntityMetadata_SpatialEntities_MetadataFields_FieldId
		FOREIGN KEY (FieldId) REFERENCES SpatialEntities_MetadataFields (FieldId) ON UPDATE CASCADE ON DELETE CASCADE 
);