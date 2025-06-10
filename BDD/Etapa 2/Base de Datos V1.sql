CREATE TABLE Cine (
    idCine BIGINT GENERATED ALWAYS AS IDENTITY (START WITH 1) PRIMARY KEY, -- Identificador único del cine
    Nombre_cine VARCHAR(45) NOT NULL,                    -- Nombre del cine
    correo_cine VARCHAR(45),                             -- Correo electrónico del cine
    telefono INT,                                        -- Número de teléfono
    Ubicacion VARCHAR(100)                               -- Dirección o ubicación del cine
);

-- Tabla que almacena las salas de cada cine
CREATE TABLE Sala (
    idSala BIGINT GENERATED ALWAYS AS IDENTITY (START WITH 1) PRIMARY KEY, -- Identificador único de la sala
    Nombre VARCHAR(100) NOT NULL,                        -- Nombre de la sala
    Tipo_pantalla VARCHAR(45),                           -- Tipo de pantalla (ej: 2D, 3D, IMAX)
    Cine_idCine BIGINT NOT NULL,                            -- Cine al que pertenece
    FOREIGN KEY (Cine_idCine) REFERENCES Cine(idCine)
);

-- Tabla con la información de cada película
CREATE TABLE Pelicula (
    idPelicula BIGINT GENERATED ALWAYS AS IDENTITY (START WITH 1) PRIMARY KEY, -- Identificador único de la película
    Nombre VARCHAR(45) NOT NULL,                             -- Título
    Duracion INT,                                            -- Duración en minutos
    Sinopsis VARCHAR(500),                                   -- Descripción corta
    Director VARCHAR(45),                                    -- Nombre del director
    Genero VARCHAR(50),                                       -- Género (acción, drama, etc.)
    Imagen VARCHAR(225)										 -- Portada de la pelicula
);

-- Tabla que indica funciones (proyecciones) específicas de una película en una sala y fecha determinada
CREATE TABLE Funcion (
    idFuncion BIGINT GENERATED ALWAYS AS IDENTITY (START WITH 1) PRIMARY KEY,
    Id_Pelicula BIGINT NOT NULL,
    Id_Sala BIGINT NOT NULL,
    FechaHora TIMESTAMP NOT NULL,                                     -- Fecha y hora de la función
    FOREIGN KEY (Id_Pelicula) REFERENCES Pelicula(idPelicula),
    FOREIGN KEY (Id_Sala) REFERENCES Sala(idSala)
);

-- Butacas físicas dentro de una sala, asociadas a un tipo de butaca
CREATE TABLE Butaca (
    Id_Butaca BIGINT GENERATED ALWAYS AS IDENTITY (START WITH 1) PRIMARY KEY,
    Id_TipoButaca BIGINT NOT NULL,                              -- Fila y columna de la butaca
    Id_Sala BIGINT NOT NULL,
    Fila VARCHAR(20),
    Columna INT,                                    -- Sala donde se encuentra,
    FOREIGN KEY (Id_Sala) REFERENCES Sala(idSala)
);


-- Tabla que almacena las contraseñas cifradas de los perfiles
CREATE TABLE Contraseña (
    Id_Contraseña BIGINT GENERATED ALWAYS AS IDENTITY (START WITH 1) PRIMARY KEY,
    ContraseñaUsuario VARCHAR(225) NOT NULL                  -- Contraseña del usuario
);


-- Perfiles de usuarios que pueden acceder al sistema (clientes o personal)
CREATE TABLE Perfil (
    Rut VARCHAR(12) PRIMARY KEY,                             -- Identificador chileno único
    Nombre VARCHAR(45),                                      -- Nombre del usuario
    Apellido VARCHAR(45),                                    -- Apellido del usuario
    Correo_Electronico VARCHAR(100),                         -- Correo del usuario
    Rol VARCHAR(40) NOT NULL,                                -- Rol (cliente, administrador, etc.)
    Id_Contraseña BIGINT NOT NULL,                              -- FK a la contraseña asociada
    FOREIGN KEY (Id_Contraseña) REFERENCES Contraseña(Id_Contraseña)
);

-- Boleto generado para una película, butaca y pago específico
CREATE TABLE Boleto (
    Id_Boleto BIGINT GENERATED ALWAYS AS IDENTITY (START WITH 1) PRIMARY KEY,
    RUT VARCHAR(12),
    IdPelicula BIGINT,                                          -- Película asociada
    IdButaca BIGINT,                                            -- Butaca reservada
    Estado_Butaca VARCHAR(50),                                  -- Estado (reservada, disponible, ocupada)
    Fecha_inicio_boleto TIMESTAMP,                              --Fecha a la que inicia la funcion
    Fecha_fin_boleto TIMESTAMP,                                 -- Fecha a la que termina la funcion
    Activo BOOLEAN,                                             --Marca si esta activo el boleto o no.
    FOREIGN KEY (RUT) REFERENCES Perfil(Rut),
    FOREIGN KEY (IdPelicula) REFERENCES Pelicula(idPelicula),
    FOREIGN KEY (IdButaca) REFERENCES Butaca(Id_Butaca)
);

-- Información sobre los pagos realizados por boletos
CREATE TABLE Pago (
    Id_Pago BIGINT GENERATED ALWAYS AS IDENTITY (START WITH 1) PRIMARY KEY,                -- Identificador del pago
    IdBoleto BIGINT,                                         -- Id del boleto asociado al pago
    Tipo VARCHAR(45),                                        -- Tipo de tarjeta 
    Marca VARCHAR(30),                                       -- Marca de la tarjeta (Visa, Mastercard)
    CuatroDig VARCHAR(4),                                    -- Últimos 4 dígitos de la tarjeta
    Fecha_Transf TIMESTAMP,                                      -- Fecha del pago o transacción	                      
    FOREIGN KEY (IdBoleto) REFERENCES Boleto(Id_Boleto)
);

-- Relación que indica qué perfil accede a qué cine
CREATE TABLE Conectarse (
    Rut VARCHAR(12) NOT NULL,
    Id_Cine BIGINT NOT NULL,
    PRIMARY KEY (Rut, Id_Cine),
    FOREIGN KEY (Rut) REFERENCES Perfil(Rut),
    FOREIGN KEY (Id_Cine) REFERENCES Cine(idCine)
);


-- Restricción que valida el formato del RUT chileno (con o sin puntos y con K/k final)
ALTER TABLE Perfil
ADD CONSTRAINT CHK_Rut_Formato
CHECK (
    Rut ~ '^(\d{1,2}\.\d{3}\.\d{3}|\d{7,8})-[0-9Kk]$'
);

-- Restricción que valida el formato del correo
ALTER TABLE Perfil
ADD CONSTRAINT CHK_Correo_Formato
CHECK (
    Correo_Electronico ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.(cl|com)$'
);  

-- Restricción que verifica que el campo CuatroDig tenga exactamente 4 dígitos numéricos
ALTER TABLE Pago
ADD CONSTRAINT CHK_CuatroDig_Formato
CHECK (
    CuatroDig ~ '^\d{4}$'
);


-- Cambios 01-06-25

ALTER TABLE Perfil
ADD CONSTRAINT Chk_nombre_apellido_perfil
CHECK (
	Nombre ~ '^[A-Za-zÁÉÍÓÚáéíóúÑñ]' and --Solo permite Letras mayusculas y minusculas, y todos las letras con acentos
	Apellido ~ '^[A-Za-zÁÉÍÓÚáéíóúÑñ]'   -- ademas de la Ñ
);

ALTER TABLE Perfil
ALTER COLUMN Rol SET DEFAULT 'cliente'; --Al momento de crear un perfil automaticamente tendra el rol de Cliente

ALTER TABLE Cine
ADD CONSTRAINT CHK_Correo_Cine
CHECK (
    correo_cine ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.(cl|com)$'  --Al momento de ingresar correo del cine sea del formato correcto
);  


-- Cambios boleto, fecha_boleto cambie de Date a TIMESTAMP el atributo para registrar la hora

-- Cambios todos los id Etc pasaron de INT a BIGINT
