-- Creada con identidad correcta
CREATE TABLE direccionmaps (
    iddireccion BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    idcine      BIGINT,
    idciudad    BIGINT,
    url         TEXT,
    FOREIGN KEY (idciudad) REFERENCES ciudad(idciudad),
    FOREIGN KEY (idcine) REFERENCES cine(idcine)
);
-- Inserción de cine Meyer y cine Chuyaca
INSERT INTO DireccionMaps (idCine, idCiudad, URL) VALUES (2, 10301, 'https://www.google.cl/maps/place/Universidad+de+Los+Lagos/@-40.5851249,-73.0930591,15.5z/data=!4m6!3m5!1s0x961636074299eb0b:0x9666c6a322034b6b!8m2!3d-40.586833!4d-73.0894195!16zL20vMDRwMDNw?hl=es&entry=ttu&g_ep=EgoyMDI1MDYyOS4wIKXMDSoASAFQAw%3D%3D');
INSERT INTO DireccionMaps (idCine, idCiudad, URL) VALUES (1, 10301, 'https://www.google.cl/maps/place/Universidad+De+Los+Lagos+(MEYER)/@-40.6038904,-73.1121674,14z/data=!4m6!3m5!1s0x961635ef5d0df291:0x80a0581c66373a2d!8m2!3d-40.5977313!4d-73.1040146!16s%2Fg%2F11b7q1sqlr?hl=es&entry=ttu&g_ep=EgoyMDI1MDYyOS4wIKXMDSoASAFQAw%3D%3D');
