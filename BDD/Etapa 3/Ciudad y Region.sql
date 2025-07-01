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

-- Modificar tabla Cine, primero con el nombre del atributo
ALTER TABLE Cine
RENAME COLUMN Ubicacion TO idCiudad

-- Los atributos previamente insertados los pasamos a NULL para que nos deje continuar
-- con el siguiente paso
UPDATE Cine 
SET idCiudad = NULL

-- Ahora cambiamos el tipo de atributo
ALTER TABLE Cine
ALTER COLUMN idCiudad TYPE BIGINT USING (idCiudad::BIGINT); 

-- Finalmente, añadimos la restricción de la clave foránea
ALTER TABLE Cine
ADD CONSTRAINT fk_idCiudad FOREIGN KEY (idCiudad) REFERENCES Ciudad (idCiudad);

