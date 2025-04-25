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
    nome varchar(255) UNIQUE NOT NULL,
    valor varchar(255) NOT NULL,
    PRIMARY KEY (id)
);
//-- Configurações iniciais
INSERT INTO checkin.configs (nome, valor) VALUES
('toleranciaPonto', '5'),
('toleranciaGeral', '10');

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




select * from checkin.users;

select * from checkin.registros;

select * from checkin.configs;


drop table checkin.registros;
drop table checkin.users;



INSERT INTO checkin.nominal (id_usuario, dia_semana, hora1, hora2, hora3, hora4, hora5, hora6) VALUES
(1, 'Domingo',  NULL,       NULL,       NULL,       NULL,      NULL, NULL),
(1, 'Segunda', '08:00:00', '11:00:00', '13:00:00', '18:00:00', null, null),
(1, 'Terça',   '08:00:00', '11:00:00', '13:00:00', '18:00:00', null, null),
(1, 'Quarta',  '08:00:00', '11:00:00', '13:00:00', '18:00:00', null, null),
(1, 'Quinta',  '08:00:00', '11:00:00', '13:00:00', '18:00:00', null, null),
(1, 'Sexta',   '08:00:00', '11:00:00', '13:00:00', '18:00:00', null, null),
(1, 'Sábado',  '08:00:00', '12:00:00', null,        null,      null, null);