CREATE TABLE curriculo (
    id int PRIMARY KEY,
    nome varchar(50) NOT NULL,
    cpf varchar(11) NOT NULL,
    email varchar(250) NOT NULL,
    telefone varchar(11) NOT NULL,
    cep varchar(8) NOT NULL,
    complemento varchar(50),
    senha varchar(255) NOT NULL,
    resumo text NOT NULL,
    experiencias text NOT NULL,
    escolaridade enum('fundamental', 'medio', 'superior') NOT NULL,
    linkedin varchar(250),
    github varchar(250)
 );
