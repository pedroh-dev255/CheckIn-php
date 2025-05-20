CREATE DATABASE checkin;
use checkin;


CREATE TABLE checkin.users(
    id int AUTO_INCREMENT,
    nome varchar(255) NOT NULL,
    email varchar(255) UNIQUE NOT NULL,
    senha varchar(64) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE checkin.nominal(
    id int AUTO_INCREMENT,
    id_usuario int NOT NULL,
    dia_semana ENUM('Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado') NOT NULL,
    hora1 time,
    hora2 time,
    hora3 time,
    hora4 time,
    hora5 time,
    hora6 time,
    
    PRIMARY KEY (id),
    FOREIGN KEY (id_usuario) REFERENCES checkin.users(id)
);


CREATE TABLE checkin.configs(
    id int AUTO_INCREMENT,
    id_usuario int NOT NULL,
    nome varchar(255) NOT NULL,
    valor varchar(255) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (id_usuario) REFERENCES checkin.users(id)
);

CREATE TABLE checkin.registros(
    id int AUTO_INCREMENT,
    id_usuario int NOT NULL,
    data date NOT NULL,
    registo1 time,
    registo2 time,
    registo3 time,
    registo4 time,
    registo5 time,
    registo6 time,
    obs text,
    mode ENUM('Folga', 'Feriado', 'Feriado Meio', 'Folga bonificada'),
    PRIMARY KEY (id),
    FOREIGN KEY (id_usuario) REFERENCES checkin.users(id)
);


CREATE TABLE checkin.extras(
    id int AUTO_INCREMENT,
    id_usuario int NOT NULL,
    ref date not null,
    hora050 time not null,
    hora100 time not null,
    obs text not null,
    PRIMARY KEY (id),
    FOREIGN KEY (id_usuario) REFERENCES checkin.users(id)

);
