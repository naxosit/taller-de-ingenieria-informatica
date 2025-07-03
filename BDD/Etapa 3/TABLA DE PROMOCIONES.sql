-- Tabla de promociones básicas que se emplearán en todos los cines

CREATE TABLE PromocionesDiarias (
	idPromocion BIGINT PRIMARY KEY,
	NombrePromocion VARCHAR(100),
	DescripcionPromocion VARCHAR(350),
	DiaPromocion VARCHAR(10)
);

-- Inserción de valores para ejemplificación
INSERT INTO PromocionesDiarias (idPromocion, NombrePromocion, DescripcionPromocion, DiaPromocion)
VALUES (1, 'DosPorUnoBebestibles', '¡Solo los días lunes lleve 2x1 en cualquier bebestible de nuestros locales', 'Lunes');

UPDATE PromocionesDiarias
SET NombrePromocion = 'Dos x 1 Bebestibles'
WHERE idPromocion = 1;

INSERT INTO PromocionesDiarias (idPromocion, NombrePromocion, DescripcionPromocion, DiaPromocion)
VALUES (4, 'Jueves de Amor', 'Ven acompañado de tu novio/a y tendrás 15% de descuento en confitería <3', 'Jueves');