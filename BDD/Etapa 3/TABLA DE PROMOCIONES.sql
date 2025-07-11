-- Tabla de promociones básicas que se emplearán en todos los cines

CREATE TABLE PromocionesDiarias (
	idPromocion BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
	NombrePromocion VARCHAR(100),
	DescripcionPromocion VARCHAR(350),
	DiaPromocion VARCHAR(10)
);

-- Inserción de valores para ejemplificación
INSERT INTO PromocionesDiarias (NombrePromocion, DescripcionPromocion, DiaPromocion)
VALUES ('Dos x 1 Bebestibles', '¡Solo los días lunes lleve 2x1 en cualquier bebestible de nuestros locales', 'Lunes');


INSERT INTO PromocionesDiarias (NombrePromocion, DescripcionPromocion, DiaPromocion)
VALUES ('Jueves de Amor', 'Ven acompañado de tu novio/a y tendrás 15% de descuento en confitería <3', 'Jueves');