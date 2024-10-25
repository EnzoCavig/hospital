-- Criação da tabela de avaliações
CREATE TABLE avaliacoes (
    id SERIAL PRIMARY KEY,
    id_setor INT NOT NULL,
    id_pergunta INT NOT NULL,
    id_dispositivo INT NOT NULL,
    resposta INT CHECK (resposta BETWEEN 0 AND 10) NOT NULL,
    feedback_textual TEXT,
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    FOREIGN KEY (id_pergunta) REFERENCES perguntas(id),
    FOREIGN KEY (id_dispositivo) REFERENCES dispositivos(id)
);

-- Criação da tabela de dispositivos
CREATE TABLE dispositivos (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    status BOOLEAN DEFAULT TRUE
);

-- Criação da tabela de perguntas
CREATE TABLE perguntas (
    id SERIAL PRIMARY KEY,
    texto TEXT NOT NULL,
    status BOOLEAN DEFAULT TRUE
);

-- Criação da tabela de usuários administrativos
CREATE TABLE usuarios_administrativos (
    id SERIAL PRIMARY KEY,
    login VARCHAR(50) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL
);
