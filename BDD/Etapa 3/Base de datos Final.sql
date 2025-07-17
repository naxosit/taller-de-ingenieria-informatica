-- Tabla de regiones
CREATE TABLE Region (
	idRegion BIGINT PRIMARY KEY,
	NombreRegion VARCHAR(100)
);

-- Tabla de ciudades
CREATE TABLE Ciudad (
	idCiudad BIGINT PRIMARY KEY,
	idRegion BIGINT,
	NombreCiudad VARCHAR(100),
	FOREIGN KEY (idRegion) REFERENCES Region(idRegion)
);

-- Tabla de cines (actualizada con idCiudad)
CREATE TABLE Cine (
    idCine BIGINT GENERATED ALWAYS AS IDENTITY (START WITH 1) PRIMARY KEY,
    Nombre_cine VARCHAR(45) NOT NULL,
    correo_cine VARCHAR(45),
    telefono INT,
    ubicacion VARCHAR(150),
    idCiudad BIGINT,
    CONSTRAINT CHK_Correo_Cine CHECK (
        correo_cine ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.(cl|com)$'
    ),
    CONSTRAINT fk_idCiudad FOREIGN KEY (idCiudad) REFERENCES Ciudad(idCiudad)
);

-- Tabla de salas
CREATE TABLE Sala (
    idSala BIGINT GENERATED ALWAYS AS IDENTITY (START WITH 1) PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    Tipo_pantalla VARCHAR(45),
    Cine_idCine BIGINT NOT NULL,
    FOREIGN KEY (Cine_idCine) REFERENCES Cine(idCine)
);

-- Tabla de películas
CREATE TABLE Pelicula (
    idPelicula BIGINT GENERATED ALWAYS AS IDENTITY (START WITH 1) PRIMARY KEY,
    Nombre VARCHAR(45) NOT NULL,
    Duracion INT,
    Sinopsis VARCHAR(500),
    Director VARCHAR(45),
    Genero VARCHAR(50),
    Imagen VARCHAR
);

-- Tabla de funciones
CREATE TABLE Funcion (
    idFuncion BIGINT GENERATED ALWAYS AS IDENTITY (START WITH 1) PRIMARY KEY,
    Id_Pelicula BIGINT NOT NULL,
    Id_Sala BIGINT NOT NULL,
    FechaHora TIMESTAMP NOT NULL,
    FOREIGN KEY (Id_Pelicula) REFERENCES Pelicula(idPelicula),
    FOREIGN KEY (Id_Sala) REFERENCES Sala(idSala)
);

-- Tabla de butacas
CREATE TABLE Butaca (
    Id_Butaca BIGINT GENERATED ALWAYS AS IDENTITY (START WITH 1) PRIMARY KEY,
    Id_TipoButaca BIGINT NOT NULL,
    Id_Sala BIGINT NOT NULL,
    Fila VARCHAR(20),
    Columna INT,
    FOREIGN KEY (Id_Sala) REFERENCES Sala(idSala)
);

-- Tabla de perfiles
CREATE TABLE Perfil (
    Rut VARCHAR(12) PRIMARY KEY,
    Nombre VARCHAR(45),
    Apellido VARCHAR(45),
    Correo_Electronico VARCHAR(100),
    Rol VARCHAR(40) NOT NULL DEFAULT 'cliente',
    CONSTRAINT CHK_Rut_Formato CHECK (
        Rut ~ '^(\d{1,2}\.\d{3}\.\d{3}|\d{7,8})-[0-9Kk]$'
    ),
    CONSTRAINT CHK_Correo_Formato CHECK (
        Correo_Electronico ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.(cl|com)$'
    ),
    CONSTRAINT Chk_nombre_apellido_perfil CHECK (
        Nombre ~ '^[A-Za-zÁÉÍÓÚáéíóúÑñ]' AND
        Apellido ~ '^[A-Za-zÁÉÍÓÚáéíóúÑñ]'
    )
);

-- Tabla de contraseñas
CREATE TABLE Contraseña (
    Id_Contraseña BIGINT GENERATED ALWAYS AS IDENTITY (START WITH 1) PRIMARY KEY,
    ContraseñaUsuario VARCHAR(225) NOT NULL,
    Rut VARCHAR(12) NOT NULL,
    FOREIGN KEY (Rut) REFERENCES Perfil(Rut) ON DELETE CASCADE
);

-- Tabla de boletos
CREATE TABLE Boleto (
    Id_Boleto BIGINT GENERATED ALWAYS AS IDENTITY (START WITH 1) PRIMARY KEY,
    RUT VARCHAR(12),
    IdFuncion BIGINT,
    IdButaca BIGINT,
    Estado_Butaca VARCHAR(50),
    Fecha_inicio_boleto TIMESTAMP,
    Fecha_fin_boleto TIMESTAMP,
    Activo BOOLEAN,
    FOREIGN KEY (RUT) REFERENCES Perfil(Rut) ON DELETE CASCADE,
    FOREIGN KEY (IdFuncion) REFERENCES Funcion(idFuncion),
    FOREIGN KEY (IdButaca) REFERENCES Butaca(Id_Butaca) ON DELETE CASCADE
);

-- Tabla de pagos
CREATE TABLE Pago (
    Id_Pago BIGINT GENERATED ALWAYS AS IDENTITY (START WITH 1) PRIMARY KEY,
    IdBoleto BIGINT,
    Tipo VARCHAR(45),
    Marca VARCHAR(30),
    CuatroDig VARCHAR(4),
    Fecha_Transf TIMESTAMP,
    FOREIGN KEY (IdBoleto) REFERENCES Boleto(Id_Boleto) ON DELETE CASCADE,
    CONSTRAINT CHK_CuatroDig_Formato CHECK (
        CuatroDig ~ '^\d{4}$'
    )
);

-- Relación entre perfiles y cines
CREATE TABLE Conectarse (
    Rut VARCHAR(12) NOT NULL,
    Id_Cine BIGINT NOT NULL,
    PRIMARY KEY (Rut, Id_Cine),
    FOREIGN KEY (Rut) REFERENCES Perfil(Rut),
    FOREIGN KEY (Id_Cine) REFERENCES Cine(idCine)
);
