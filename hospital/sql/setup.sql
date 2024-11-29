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

CREATE TABLE dispositivos (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    status BOOLEAN DEFAULT TRUE
);

CREATE TABLE perguntas (
    id SERIAL PRIMARY KEY,
    texto TEXT NOT NULL,
    status BOOLEAN DEFAULT TRUE
);

CREATE TABLE usuarios_administrativos (
    id SERIAL PRIMARY KEY,
    login VARCHAR(50) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL
);

ALTER TABLE dispositivos ADD COLUMN id_setor INT NOT NULL;

SELECT 
    setores.nome AS setor,
    perguntas.texto AS pergunta,
    avaliacoes.resposta AS nota,
    avaliacoes.feedback_textual AS comentario,
    avaliacoes.data_hora
FROM avaliacoes
JOIN dispositivos ON avaliacoes.id_dispositivo = dispositivos.id
JOIN perguntas ON avaliacoes.id_pergunta = perguntas.id
JOIN setores ON dispositivos.id_setor = setores.id
WHERE dispositivos.id_setor = 1 
ORDER BY avaliacoes.data_hora DESC;


CREATE TABLE setores (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);
