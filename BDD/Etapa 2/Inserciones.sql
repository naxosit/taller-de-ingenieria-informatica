INSERT INTO Pelicula (Nombre, Duracion, Sinopsis, Director, Genero, Imagen) VALUES
('Inception', 148, 'Un ladrón que roba secretos corporativos a través del uso de la tecnología de los sueños.', 'Christopher Nolan', 'Ciencia ficción', 'https://i.blogs.es/bfb0b4/inception-origen-nuevo-cartel/450_1000.jpg'),
('Cómo entrenar a tu dragón', 125, 'Un joven vikingo aspira a cazar dragones, pero se convierte inesperadamente en amigo de un joven dragón.', 'Dean DeBlois', 'Aventura, Fantasia', 'https://m.media-amazon.com/images/M/MV5BY2E5YjE2YTQtZGIxYi00YTU4LTk3YzItYmMyZTM3M2I5ZTM3XkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg'),
('LILO Y STICH', 110, 'LILO Y STITCH es la conmovedora y divertidísima historia de una niña hawaiana y el alienígena fugitivo que la ayuda a reconstruir su familia.', 'Dean Fleischer Camp', 'Familiar', 'https://static.cinepolis.com/img/peliculas/49850/1/1/49850.jpg'),
('Avengers: Endgame', 181, 'Los Vengadores se enfrentan a Thanos en una batalla épica.', 'Anthony y Joe Russo', 'Acción', 'https://hips.hearstapps.com/hmg-prod/images/poster-vengadores-endgame-1552567490.jpg'),
('Oppenheimer', 180, 'Durante la Segunda Guerra Mundial, el teniente general Leslie Groves designa al físico J. Robert Oppenheimer para un grupo de trabajo que está desarrollando el Proyecto Manhattan, cuyo objetivo consiste en fabricar la primera bomba atómica.', 'Christopher Nolan', 'Suspenso', 'https://pics.filmaffinity.com/Oppenheimer-828933592-large.jpg');

-- Inserciones de cines en la tabla Cine
INSERT INTO Cine (Nombre_cine, correo_cine, telefono, Ubicacion) VALUES
('Cine Meyer', 'contacto@cinemeyer.cl', 226789321, 'Calle Meyer 1234, Osorno'),
('Cine Chuyaca', 'info@chuyacacine.cl', 223456789, 'Av. Chuyaca 4321, Osorno');

INSERT INTO Sala (Nombre, Tipo_pantalla, Cine_idCine) VALUES
('Sala 1', '2D', 1),
('Sala 2', '3D', 1),
('Sala Premium', 'IMAX', 1),
('Sala 1', '2D', 2),
('Sala 2', '4DX', 2);

INSERT INTO Funcion (Id_Pelicula, Id_Sala, FechaHora) VALUES
(1, 1, '2025-06-22 18:00:00'),
(2, 2, '2025-06-22 20:30:00'),
(3, 3, '2025-06-23 17:00:00'),
(4, 4, '2025-06-24 21:00:00'),
(5, 5, '2025-06-25 16:00:00');

--Insertamos butacas a las salas
DO $$
DECLARE
    fila CHAR;
    columna INT;
    sala_id INT;
BEGIN
    FOR sala_id IN 1..5 LOOP
        FOREACH fila IN ARRAY ARRAY['A','B','C','D','E'] LOOP
            FOR columna IN 1..8 LOOP
                INSERT INTO Butaca (Id_TipoButaca, Id_Sala, Fila, Columna)
                VALUES (1, sala_id, fila, columna);
            END LOOP;
        END LOOP;
    END LOOP;
END $$;
