USE IncidenceDashboard;

CREATE TABLE Incidents_IncidentCategories (
	IncidentCategoryId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	IncidentCategoryName VARCHAR(50) NOT NULL UNIQUE,
	IncidentCategoryDescription VARCHAR(500)
);

CREATE TABLE Incidents_IncidentTypes (
	IncidentTypeId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	IncidentCategoryId INT,
	IncidentTypeName VARCHAR(50) NOT NULL,
	IncidentTypeDescription VARCHAR(500),
	
	CONSTRAINT u_Incident_Type_Category
		UNIQUE(IncidentCategoryId, IncidentTypeName),
	CONSTRAINT fk_IncidentCategoryId_IncidentCategories_CategoryId
		FOREIGN KEY (IncidentCategoryId) REFERENCES Incidents_IncidentCategories (IncidentCategoryId) ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE Incidents_MetadataFieldTypes (
	TypeName VARCHAR(50) NOT NULL PRIMARY KEY,
	TypeDescription VARCHAR(500)
);

CREATE TABLE Incidents_MetadataFields (
	FieldId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	FieldType VARCHAR(50) NOT NULL,
	FieldName VARCHAR(50) NOT NULL UNIQUE,
	FieldDescription VARCHAR(500),

	CONSTRAINT fk_MetadataFields_Incidents_MetadataFieldTypes
		FOREIGN KEY (FieldType) REFERENCES Incidents_MetadataFieldTypes (TypeName) ON UPDATE CASCADE ON DELETE NO ACTION
);

CREATE TABLE Incidents_Incidents (
	IncidentId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	IncidentName VARCHAR(256),
	IncidentType INT NOT NULL,
	IncidentLocation INT NOT NULL,
	IncidentDate DATETIME NOT NULL,
	IncidentPointOfInterest POINT, 
	IncidentDescription TEXT,
	DateCreated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	LastModified DATETIME,

	CONSTRAINT fk_Incidents_IncidentTypes_IncidentTypeId
		FOREIGN KEY (IncidentType) REFERENCES Incidents_IncidentTypes (IncidentTypeId) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT fk_Entities_Incidents_Entities_Incidents_IncidentId
		FOREIGN KEY (IncidentLocation) REFERENCES SpatialEntities_Entities (EntityId) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE Incidents_IncidentMetadata (
	MetadataId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	IncidentId INT NOT NULL,
	FieldId INT NOT NULL,
	FieldValue VARCHAR(256),
	DateCreated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	LastModified DATETIME,

	CONSTRAINT fk_IncidentMetadata_Incidents_Entities_IncidentId
		FOREIGN KEY (IncidentId) REFERENCES Incidents_Incidents (IncidentId) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT fk_IncidentMetadata_Incidents_MetadataFields_FieldId
		FOREIGN KEY (FieldId) REFERENCES Incidents_MetadataFields (FieldId) ON UPDATE CASCADE ON DELETE CASCADE 
);