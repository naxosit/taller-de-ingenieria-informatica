CREATE TABLE Cine (
    idCine INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    Nombre_cine VARCHAR(45) NOT NULL,
    correo_cine VARCHAR(45),
    telefono INT,
    Ubicacion VARCHAR(45)
);

CREATE TABLE Sala (
    idSala INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    Capacidad INT,
    Tipo_pantalla VARCHAR(45),
    Cine_idCine INT NOT NULL,
    FOREIGN KEY (Cine_idCine) REFERENCES Cine(idCine)
);

CREATE TABLE Pelicula (
    idPelicula INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    Nombre VARCHAR(45) NOT NULL,
    Duracion INT,
    Sinopsis VARCHAR(500),
    Director VARCHAR(45),
    Genero VARCHAR(50)
);

CREATE TABLE Proyeccion (
    Id_Pelicula INT NOT NULL,
    Id_Cine INT NOT NULL,
    PRIMARY KEY (Id_Pelicula, Id_Cine),
    FOREIGN KEY (Id_Pelicula) REFERENCES Pelicula(idPelicula),
    FOREIGN KEY (Id_Cine) REFERENCES Cine(idCine)
);

CREATE TABLE Funcion (
    Id_Pelicula INT NOT NULL,
    Id_Sala INT NOT NULL,
    Fecha DATE NOT NULL,
    PRIMARY KEY (Id_Pelicula, Id_Sala, Fecha),
    FOREIGN KEY (Id_Pelicula) REFERENCES Pelicula(idPelicula),
    FOREIGN KEY (Id_Sala) REFERENCES Sala(idSala)
);

CREATE TABLE Tipo_Butaca (
    Id_TipoButaca INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    Fila VARCHAR(10),
    Columna VARCHAR(10)
);

CREATE TABLE Butaca (
    Id_Butaca INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    Id_TipoButaca INT NOT NULL,
    Id_Sala INT NOT NULL,
    FOREIGN KEY (Id_TipoButaca) REFERENCES Tipo_Butaca(Id_TipoButaca),
    FOREIGN KEY (Id_Sala) REFERENCES Sala(idSala)
);

CREATE TABLE Pago (
    Id_Pago INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    Tipo VARCHAR(50),
    Marca VARCHAR(50),
    CuatroDig VARCHAR(4),
    Fecha_Transf DATE
);

CREATE TABLE Boleto (
    Id_Pago INT NOT NULL,
    IdPelicula INT NOT NULL,
    IdButaca INT NOT NULL,
    Estado_Butaca VARCHAR(50),
    PRIMARY KEY (Id_Pago, IdPelicula, IdButaca),
    FOREIGN KEY (Id_Pago) REFERENCES Pago(Id_Pago),
    FOREIGN KEY (IdPelicula) REFERENCES Pelicula(idPelicula),
    FOREIGN KEY (IdButaca) REFERENCES Butaca(Id_Butaca)
);

CREATE TABLE Rol (
    idRol INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    Nombre VARCHAR(50)
);

CREATE TABLE Contraseña (
    Id_Contraseña INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    ContraseñaUsuario VARCHAR(225) NOT NULL
);

CREATE TABLE Perfil (
    Rut VARCHAR(12) PRIMARY KEY,
    Nombre VARCHAR(45),
    Apellido VARCHAR(45),
    Correo_Electronico VARCHAR(100),
    Rol_idRol INT NOT NULL,
    Id_Contraseña INT NOT NULL,
    FOREIGN KEY (Rol_idRol) REFERENCES Rol(idRol),
    FOREIGN KEY (Id_Contraseña) REFERENCES Contraseña(Id_Contraseña)
);

CREATE TABLE Conectarse (
    Rut VARCHAR(12) NOT NULL,
    Id_Cine INT NOT NULL,
    PRIMARY KEY (Rut, Id_Cine),
    FOREIGN KEY (Rut) REFERENCES Perfil(Rut),
    FOREIGN KEY (Id_Cine) REFERENCES Cine(idCine)
);

-- Vista que reemplaza 'Ubicacion' en la Butaca
CREATE VIEW VistaUbicacionButacas AS
SELECT 
    b.Id_Butaca AS ID_Butaca,
    b.Id_Sala AS ID_Sala,
    tb.Fila || '-' || tb.Columna AS Ubicacion
FROM 
    Butaca b
JOIN 
    Tipo_Butaca tb ON b.Id_TipoButaca = tb.Id_TipoButaca;