-- Tabla Rol
CREATE TABLE Rol (
	idRol INT PRIMARY KEY,
	Nombre_Rol VARCHAR(45)
);

-- Tabla Perfil
CREATE TABLE Perfil (
	idPerfil INT PRIMARY KEY,
	Nombre VARCHAR(45),
	Apellido VARCHAR(45),
	Correo_electronico VARCHAR(45),
	Rol_idRol INT,
	FOREIGN KEY (Rol_idRol) REFERENCES Rol(idRol)
);

-- Tabla Cine
CREATE TABLE Cine (
	idCine INT PRIMARY KEY,
	nombre_cine VARCHAR(45),
	correo_cine VARCHAR(45),
	telefono INT
);


-- Tabla Login
CREATE TABLE Login (
	idLogin INT PRIMARY KEY,
	Correo_electronico VARCHAR(45),
	Contraseña VARCHAR(45),
	Perfil_idPerfil INT,
	Cine_idCine INT,
	FOREIGN KEY (Perfil_idPerfil) REFERENCES Perfil(idPerfil),
	FOREIGN KEY (Cine_idCine) REFERENCES Cine(idCine)
);

-- Tabla Genero
CREATE TABLE Genero (
	idGenero INT PRIMARY KEY,
	nombre_genero VARCHAR(45)
);

-- Tabla Estado Butaca
CREATE TABLE Estado_butaca (
	idEstado_butaca INT PRIMARY KEY,
	Nombre_estado_butaca VARCHAR(45)
);

-- Tabla Tipo Butaca
CREATE TABLE Tipo_butaca (
	idTipo_butaca INT PRIMARY KEY,
	Nombre_tipo_butaca VARCHAR(45)
);

-- Tabla Sala
CREATE TABLE Sala (
	idSala INT PRIMARY KEY,
	Capacidad_total_butaca INT,
	Tipo_pantalla VARCHAR(45),
	Cine_idCine INT,
	FOREIGN KEY (Cine_idCine) REFERENCES Cine(idCine)
);

-- Tabla Butaca
CREATE TABLE Butaca (
	idButaca INT PRIMARY KEY,
	Lugar_en_la_sala VARCHAR (45),
	Sala_idSala INT,
	Tipo_butaca_idTipo_butaca INT,
	Estado_butaca_idEstado_butaca INT,
	FOREIGN KEY (Sala_idSala) REFERENCES Sala(idSala),
	FOREIGN KEY (Tipo_butaca_idTipo_butaca) REFERENCES Tipo_butaca(idTipo_butaca),
	FOREIGN KEY (Estado_butaca_idEstado_butaca) REFERENCES Estado_butaca(idEstado_butaca)
);

-- Tabla Pelicula
CREATE TABLE Pelicula (
	idPelicula INT PRIMARY KEY,
	Nombre VARCHAR(45),
	Duracion INT,
	Sinopsis VARCHAR(45),
	Director VARCHAR(45),
	Genero_idGenero INT,
	FOREIGN KEY (Genero_idGenero) REFERENCES Genero(idGenero)
);


-- Tabla Funcion (modificada)
CREATE TABLE Funcion (
    idFuncion INT PRIMARY KEY,
    Hora_inicio TIME, -- Usar TIME para la hora
    Fecha DATE,
    Formato_produccion VARCHAR(45),
    Pelicula_idPelicula INT,
    Sala_idSala INT, -- Relacionamos la función con la sala
    FOREIGN KEY (Pelicula_idPelicula) REFERENCES Pelicula(idPelicula),
    FOREIGN KEY (Sala_idSala) REFERENCES Sala(idSala)
);

-- Tabla Cartelera
CREATE TABLE Cartelera (
	idCartelera INT PRIMARY KEY,
	Peliculas_estreno VARCHAR(45),
	Pelicula_idPelicula INT,
	Cine_idCine INT,
	FOREIGN KEY (Pelicula_idPelicula) REFERENCES Pelicula(idPelicula),
	FOREIGN KEY (Cine_idCine) REFERENCES Cine(idCine)
);

-- Tabla Factura (modificada para relacionarse con Comprar_boleto)
CREATE TABLE Factura (
    idFactura INT PRIMARY KEY,
    Nombre VARCHAR(45),
    NIT INT,
    Correo_electronico VARCHAR(45),
    Direccion VARCHAR(45),
    Fecha_emision TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Añadimos la fecha y hora de la factura
    Monto_total INT -- Podría calcularse en la aplicación o con triggers
);

-- Tabla Comprar Boleto (modificada con FK a Factura)
CREATE TABLE Comprar_boleto (
    idComprar_boleto INT PRIMARY KEY,
    Precio_boleto INT,
    Funcion_idFuncion INT,
    Butaca_idButaca INT,
    Factura_idFactura INT, -- Clave foránea para agrupar la compra
    FOREIGN KEY (Funcion_idFuncion) REFERENCES Funcion(idFuncion),
    FOREIGN KEY (Butaca_idButaca) REFERENCES Butaca(idButaca),
    FOREIGN KEY (Factura_idFactura) REFERENCES Factura(idFactura)
);

-- Tabla Transaccion
CREATE TABLE Transaccion (
	idTransaccion INT PRIMARY KEY,
	Nombre_tarjeta VARCHAR(45),
	Numero_tarjeta INT,
	fecha_vencimiento DATE,
	CVV INT,
	Monto_total INT,
	Factura_idFactura INT,
	FOREIGN KEY (Factura_idFactura) REFERENCES Factura(idFactura)
);