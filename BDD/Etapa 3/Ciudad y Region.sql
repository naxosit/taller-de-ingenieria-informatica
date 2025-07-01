-- Creación de la tabla Region
CREATE TABLE Region (
	idRegion BIGINT PRIMARY KEY,
	NombreRegion VARCHAR(100)
);



-- Creación de la tabla Ciudad
CREATE TABLE Ciudad (
	idCiudad BIGINT PRIMARY KEY,
	idRegion BIGINT,
	NombreCiudad VARCHAR(100),
	FOREIGN KEY (idRegion) REFERENCES Region(idRegion)
);


