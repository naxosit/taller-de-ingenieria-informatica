-- Inserción de regiones
INSERT INTO Region (idRegion, NombreRegion) VALUES (10, 'Región de Los Lagos');
INSERT INTO Region (idRegion, NombreRegion) VALUES (14, 'Región de Los Ríos'), (13, 'Región Metropolitana de Santiago');

-- Inserción de ciudades
INSERT INTO Ciudad (idCiudad, idRegion, NombreCiudad) VALUES (10301, 10, 'Osorno');
INSERT INTO Ciudad (idCiudad, idRegion, NombreCiudad) VALUES (10101, 10, 'Puerto Montt');
INSERT INTO Ciudad (idCiudad, idRegion, NombreCiudad) VALUES (14101, 14, 'Valdivia');
INSERT INTO Ciudad (idCiudad, idRegion, NombreCiudad) VALUES (13101, 13, 'Santiago');

--Ahora asignamos los cines existentes a la ciudad de Osorno
UPDATE Cine SET idciudad = 10301 WHERE nombre_cine in ('Cine Meyer', 'Cine Chuyaca');