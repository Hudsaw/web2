create database curriculo;
use curriculo;

CREATE TABLE curriculo (
    id int PRIMARY KEY AUTO_INCREMENT,
    nome varchar(50) NOT NULL,
    cpf varchar(11) NOT NULL UNIQUE,
    email varchar(250) NOT NULL UNIQUE,
    telefone varchar(11) NOT NULL,
    cep varchar(8) NOT NULL,
    complemento varchar(50),
    senha varchar(255) NOT NULL,
    area_atuacao_id INT REFERENCES area_atuacao(id),
    resumo text NOT NULL,
    experiencias text,
    escolaridade enum('fundamental', 'medio', 'superior') NOT NULL,
    linkedin varchar(250),
    github varchar(250)
 );

 CREATE TABLE area_atuacao (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO area_atuacao (nome) VALUES 
('TI'), 
('Engenharia'), 
('Administração'), 
('Marketing'), 
('Design');
