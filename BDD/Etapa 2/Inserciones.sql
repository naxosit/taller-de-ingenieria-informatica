INSERT INTO Pelicula (Nombre, Duracion, Sinopsis, Director, Genero, Imagen) VALUES
('Inception', 148, 'Un ladrón que roba secretos corporativos a través del uso de la tecnología de los sueños.', 'Christopher Nolan', 'Ciencia ficción', 'https://i.blogs.es/bfb0b4/inception-origen-nuevo-cartel/450_1000.jpg'),
('Titanic', 195, 'Una historia de amor durante el fatídico viaje del Titanic.', 'James Cameron', 'Drama/Romance', 'https://pics.filmaffinity.com/Titanic-321994924-large.jpg'),
('Avatar', 162, 'Un ex-marine en un mundo alienígena se ve atrapado entre dos mundos.', 'James Cameron', 'Ciencia ficción', 'https://www.mubis.es/media/covers/1516/2039/avatar-portada-original.jpg'),
('Avengers: Endgame', 181, 'Los Vengadores se enfrentan a Thanos en una batalla épica.', 'Anthony y Joe Russo', 'Acción', 'https://hips.hearstapps.com/hmg-prod/images/poster-vengadores-endgame-1552567490.jpg');

-- Inserciones de cines en la tabla Cine
INSERT INTO Cine (Nombre_cine, correo_cine, telefono, Ubicacion) VALUES
('CinePlanet Santiago Centro', 'contacto@santiago-cineplanet.cl', 226789321, 'Av. Libertador Bernardo O’Higgins 3470, Santiago'),
('Cinemark Alto Las Condes', 'info@cinemark.cl', 223456789, 'Av. Kennedy 9001, Las Condes, Santiago'),
('CineHoyts La Reina', 'servicio@hoyts.cl', 227654321, 'Av. Ossa 655, La Reina, Santiago'),
('Cinemark Plaza Oeste', 'contacto@cinemarkplaza.cl', 225678901, 'Av. Américo Vespucio 1501, Cerrillos, Santiago'),
('Cine Arte Alameda', 'info@cineartealameda.cl', 229876543, 'Av. Libertador Bernardo O’Higgins 139, Santiago Centro');

INSERT INTO Sala (Nombre, Tipo_pantalla, Cine_idCine) VALUES
('Sala 1', '2D', 1),
('Sala 2', '3D', 1),
('Sala Premium', 'IMAX', 2),
('Sala Junior', '2D', 3),
('Sala VIP', '4DX', 4);

INSERT INTO Funcion (Id_Pelicula, Id_Sala, FechaHora) VALUES
(1, 1, '2025-05-22 18:00:00'),
(2, 2, '2025-05-22 20:30:00'),
(3, 3, '2025-05-23 17:00:00'),
(4, 4, '2025-05-24 21:00:00'),
(5, 5, '2025-05-25 16:00:00');