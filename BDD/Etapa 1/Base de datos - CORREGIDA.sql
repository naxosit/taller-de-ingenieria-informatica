-- Entidades

CREATE TABLE CONTRASENA (
	Id_Contrasena INT PRIMARY KEY,
	ContrasenaUsuario VARCHAR(225)
);

CREATE TABLE PERFIL (
	Rut VARCHAR(12) PRIMARY KEY,
	Nombre VARCHAR(45),
	Apellido VARCHAR(45),
	Correo VARCHAR(45),
	IdContrasena INT NOT NULL,
	FOREIGN KEY (IdContrasena) REFERENCES CONTRASENA(IdContrasena)
);

CREATE TABLE ROL (
	IdRol INT PRIMARY KEY
);

CREATE TABLE CINE (
	Id_Cine INT PRIMARY KEY,
	Nombre VARCHAR(200),
	Correo VARCHAR(200),
	Telefono VARCHAR(20),
	Ubicacion VARCHAR(200)
);

CREATE TABLE PELICULA (
	Id_Pelicula INT PRIMARY KEY,
	Nombre VARCHAR(200),
	Duracion INT, -- En minutos (Modificable a tipo TIME, y luego programar para convertir hora a minutos)
	Sinopsis VARCHAR(500),
	Director VARCHAR(200),
	Genero VARCHAR(100)
);

CREATE TABLE SALA (
	Id_Sala INT PRIMARY KEY,
	Nombre VARCHAR(100),
	Capacidad INT,
	Tipo_Pantalla VARCHAR(100),
	Id_Cine INT NOT NULL,
	FOREIGN KEY (Id_Cine) REFERENCES CINE(Id_Cine)
);

CREATE TABLE TIPO_BUTACA (
	Id_TipoButaca INT PRIMARY KEY,
	Fila CHAR(1),
	Columna CHAR(1)
);

CREATE TABLE BUTACA (
	Id_Butaca INT PRIMARY KEY,
	Id_TipoButaca INT NOT NULL,
	Id_Sala INT NOT NULL,
	Ubicacion VARCHAR(50),
	FOREIGN KEY (Id_TipoButaca) REFERENCES TIPO_BUTACA(Id_TipoButaca),
	FOREIGN KEY (Id_Sala) REFERENCES SALA (Id_Sala)
);

CREATE TABLE PAGO (
	Id_Pago INT PRIMARY KEY,
	Tipo VARCHAR(50),
	Marca VARCHAR(50),
	CuatroDig CHAR(4),
	Fecha_Transf DATE -- Pk en el diagrama, pero se cree que por error de tipeo
);


-- RELACIONES N-N (Tablas intermedias)

-- Un perfil puede tener muchos roles, un rol pertenece a muchos perfiles
CREATE TABLE PERFIL_ROL (
	Rut VARCHAR(12) NOT NULL,
	IdRol INT NOT NULL, 
	PRIMARY KEY (Rut, IdRol),
	FOREIGN KEY (Rut) REFERENCES PERFIL(Rut),
	FOREIGN KEY (IdRol) REFERENCES ROL (IdRol)
);

-- Un perfil "se conecta" a muchos cines y viceversa
CREATE TABLE CONECTARSE (
	Rut VARCHAR(12) NOT NULL,
	Id_Cine INT NOT NULL,
	PRIMARY KEY (Rut, Id_Cine),
	FOREIGN KEY (Rut) REFERENCES PERFIL(Rut),
	FOREIGN KEY (Id_Cine) REFERENCES CINE (Id_Cine)
);

-- Un pago puede incluir varios boletos, una butaca puede venderse en varios pagos (a distintas funciones)
CREATE TABLE BOLETO (
	Id_Pago INT NOT NULL,
	Id_Butaca INT NOT NULL,
	Estado_Butaca VARCHAR(50), -- atributo "Estado_Butaca"
	PRIMARY KEY (Id_Pago, Id_Butaca),
	FOREIGN KEY (Id_Pago) REFERENCES PAGO (Id_Pago),
	FOREIGN KEY (Id_Butaca) REFERENCES BUTACA (Id_Butaca)
);

-- Tabla función/programación: qué película se exhibe en qué sala y cuándo
CREATE TABLE FUNCION (
	Id_Sala INT NOT NULL,
	Id_Pelicula INT NOT NULL,
	Fecha DATE NOT NULL, -- atributo "Fecha"
	PRIMARY KEY (Id_Sala, Id_Pelicula, Fecha),
	FOREIGN KEY (Id_Sala) REFERENCES SALA (Id_Sala),
	FOREIGN KEY (Id_Pelicula) REFERENCES PELICULA (Id_Pelicula)
);

