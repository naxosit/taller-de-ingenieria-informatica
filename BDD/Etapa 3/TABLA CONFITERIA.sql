--Creacion tabla confiteria
create table confiteria (
    id_producto serial primary key,
    nombre varchar(100) not null,
    descripcion text,
    categoria varchar(100) not null,
    precio int not null,
    imagen varchar(255) not null
);