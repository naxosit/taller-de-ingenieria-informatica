-- Entidades

CREATE TABLE CONTRASENA (
	IdContrasena INT PRIMARY KEY,
	ContrasenaUsuario VARCHAR(255)
);

CREATE TABLE PERFIL (
	Rut VARCHAR(12) PRIMARY KEY,
	Nobre VARCHAR(100),
	Apellido VARCHAR(100),
	Correo VARCHAR(200),
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
	Nombre VARCHAR(15),
	Duracion INT, -- En minutos (Modificable a tipo TIME, y luego programar para convertir hora a minutos)
	Sinopsis VARCHAR(200),
	Director VARCHAR(200),
	Genero VARCHAR (100),
	Id_Cine INT NOT NULL,
	FOREIGN KEY (Id_Cine) REFERENCES CINE(Id_Cine)
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
	Fila CHAR(1)
	Columna CHAR(1)
);

CREATE TABLE BUTACA (
	Id_Butaca INT PRIMARY KEY,
	Id_TipoButaca INT NOT NULL,
	Id_Sala INT NOT NULL,
	Ubicación VARCHAR(50),
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


