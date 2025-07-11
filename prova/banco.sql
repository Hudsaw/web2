create database quizads;
use quizads;

-- Tabela de cadastro
CREATE TABLE usuarios (
    id int PRIMARY KEY AUTO_INCREMENT,
    tipo enum('admin', 'candidato') NOT NULL DEFAULT 'candidato',
    nome varchar(50) NOT NULL,
    cpf varchar(11) NOT NULL UNIQUE,
    email varchar(250) NOT NULL UNIQUE,
    telefone varchar(11) NOT NULL,
    cep varchar(8) NOT NULL,
    complemento varchar(50),
    senha varchar(255) NOT NULL,
    avaliacao int NULL DEFAULT 0,
    total_perguntas INT NULL DEFAULT 0,
    area_atuacao_id INT REFERENCES area_atuacao(id),
    resumo text NOT NULL,
    experiencias text,
    escolaridade enum('fundamental', 'medio', 'superior') NOT NULL,
    linkedin varchar(250),
    github varchar(250)
 );

 INSERT INTO usuarios (tipo, nome, email, senha) VALUES
 ('admin', 'Hudsaw', 'hudsawfs@gmail.com', 123456);

-- Tabela de áreas de atuação
CREATE TABLE area_atuacao (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL UNIQUE
);

-- Tabela de níveis de dificuldade
CREATE TABLE nivel (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome ENUM('Facil', 'Medio', 'Dificil') NOT NULL
);

-- Tabela de perguntas
CREATE TABLE perguntas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pergunta TEXT NOT NULL,
    resposta_correta TEXT NOT NULL,
    alternativa1 TEXT NOT NULL,
    alternativa2 TEXT NOT NULL,
    alternativa3 TEXT NOT NULL,
    area_atuacao_id INT,
    nivel_id INT,
    ativa TINYINT DEFAULT 0,
    FOREIGN KEY (area_atuacao_id) REFERENCES area_atuacao(id),
    FOREIGN KEY (nivel_id) REFERENCES nivel(id)
);


-- Inserção de áreas de atuação
INSERT INTO area_atuacao (nome) VALUES 
('BackEnd'), 
('CienciaDeDados'),
('Design'),
('Engenharia'), 
('FrontEnd'), 
('FullStack'),
('Programacao'), 
('SecurancaInformacao');

-- Inserindo níveis de dificuldade
INSERT INTO nivel (nome) VALUES ('Fácil'), ('Médio'), ('Difícil');

INSERT INTO perguntas (pergunta, resposta_correta, alternativa1, alternativa2, alternativa3, area_atuacao_id, nivel_id, ativa) VALUES
('Qual protocolo é usado em requisições HTTP?', 'HTTP', 'FTP', 'SMTP', 'TCP', 1, 1, 1),
('O que significa API?', 'Interface de Programação de Aplicações', 'Protocolo Avançado de Internet', 'Aplicativo Interligado', 'Arquivo de Processamento Interno', 1, 1, 1),
('Qual linguagem é comum no BackEnd?', 'Java', 'HTML', 'CSS', 'JavaScript', 1, 1, 1),
('O que é JSON?', 'Formato leve de dados', 'Linguagem de marcação', 'Protocolo de rede', 'Sistema operacional', 1, 1, 1),
('Qual banco é mais usado no BackEnd?', 'MySQL', 'Photoshop', 'Excel', 'Word', 1, 1, 1),
('Qual método HTTP envia dados ao servidor?', 'POST', 'GET', 'SELECT', 'UPDATE', 1, 1, 1),
('O que é REST?', 'Estilo arquitetural de APIs', 'Um tipo de linguagem', 'Uma ferramenta de design', 'Um sistema operacional', 1, 1, 1),
('Qual framework backend é baseado em Node.js?', 'Express', 'React', 'Angular', 'Vue', 1, 1, 1),
('O que é um endpoint?', 'Ponto de acesso da API', 'Tipo de variável', 'Função interna do JS', 'Comando de loop', 1, 1, 1),
('O que significa CRUD?', 'Criar, Ler, Atualizar, Deletar', 'Codificar, Rodar, Usar, Desenvolver', 'Conectar, Requerer, Usar, Disponibilizar', 'Criptografar, Rodar, Usar, Deletar', 1, 1, 1),
('O que é OAuth?', 'Protocolo de autorização', 'Linguagem de programação', 'Banco de dados relacional', 'Editor de texto', 1, 2, 1),
('O que é uma lambda function?', 'Função sem nome', 'Função com classe', 'Função dentro de outra função', 'Função com retorno múltiplo', 1, 2, 1),
('O que é middleware?', 'Software intermediário entre aplicações', 'Dispositivo físico', 'Biblioteca gráfica', 'Protocolo de segurança', 1, 2, 1),
('O que é serialização de dados?', 'Converter objeto para formato armazenável', 'Compactar arquivos', 'Criptografar dados', 'Enviar dados via rede', 1, 2, 1),
('O que é ORM?', 'Mapeamento Objeto-Relacional', 'Operador Relacional Matemático', 'Objeto de Resposta Múltipla', 'Organizador de Redes Móveis', 1, 2, 1),
('O que é JWT?', 'Token de autenticação', 'Protocolo de rede', 'Linguagem de marcação', 'Servidor web', 1, 2, 1),
('O que é um Webhook?', 'URL que recebe eventos', 'Sistema de backup', 'Rede privada virtual', 'Ferramenta de design', 1, 2, 1),
('O que é cache em sistemas web?', 'Armazenamento temporário de dados', 'Banco de dados permanente', 'Linguagem de marcação', 'Protocolo de rede', 1, 2, 1),
('O que é eventual consistency?', 'Base de dados eventualmente sincronizada', 'Sistema de segurança', 'Protocolo de rede', 'Linguagem de marcação', 1, 3, 1),
('O que é CQRS?', 'Pattern de separação de leitura e escrita', 'Linguagem de programação', 'Ferramenta de design', 'Editor de texto', 1, 3, 1),
('O que é Event Sourcing?', 'Gravar mudanças como eventos', 'Criptografia avançada', 'Backup automático', 'Monitoramento remoto', 1, 3, 1),
('O que é Saga Pattern?', 'Gerenciar transações distribuídas', 'Modelo de interface', 'Padrão de design', 'Sistema operacional', 1, 3, 1),
('Qual biblioteca Python é usada para visualização de dados?', 'Matplotlib', 'Photoshop', 'Word', 'PowerPoint', 2, 1, 1),
('Qual é o objetivo do machine learning?', 'Prever resultados a partir de dados', 'Editar imagens', 'Criar interfaces', 'Desenvolver jogos', 2, 1, 1),
('O que é big data?', 'Grandes volumes de dados', 'Dados pequenos', 'Dados de baixa qualidade', 'Dados estruturados', 2, 1, 1),
('O que é um DataFrame?', 'Estrutura de dados bidimensional', 'Documento Word', 'Planilha Excel simples', 'Tabela de banco de dados relacional', 2, 1, 1),
('Qual é o principal uso do Pandas?', 'Manipulação de dados', 'Edição de imagens', 'Desenvolvimento web', 'Análise financeira', 2, 1, 1),
('O que é regressão linear?', 'Previsão de valores numéricos', 'Classificação de dados', 'Agrupamento de informações', 'Detecção de anomalias', 2, 1, 1),
('O que é um outlier?', 'Valor fora do padrão esperado', 'Valor médio', 'Valor mais comum', 'Valor mínimo', 2, 1, 1),
('O que é aprendizado supervisionado?', 'Algoritmos com dados rotulados', 'Sem rótulos', 'Apenas classificação', 'Apenas regressão', 2, 1, 1),
('O que é aprendizado não supervisionado?', 'Algoritmos sem dados rotulados', 'Com dados rotulados', 'Somente regressão', 'Somente classificação', 2, 1, 1),
('O que é um modelo preditivo?', 'Prevê eventos futuros', 'Mostra estatísticas passadas', 'Visualiza dados', 'Edita documentos', 2, 1, 1),
('O que é K-means?', 'Algoritmo de agrupamento', 'Linguagem de programação', 'Protocolo de rede', 'Biblioteca de visualização', 2, 2, 1),
('O que é overfitting?', 'Modelo ajustado demais aos dados de treino', 'Modelo subajustado', 'Erro aleatório', 'Falha na coleta de dados', 2, 2, 1),
('O que é validação cruzada?', 'Técnica para avaliar modelos', 'Forma de limpar dados', 'Processo de normalização', 'Método de visualização', 2, 2, 1),
('O que é PCA?', 'Análise de componentes principais', 'Programa de controle de acesso', 'Plano de carreira anual', 'Princípio de codificação avançada', 2, 2, 1),
('O que é A/B testing?', 'Comparar versões de um modelo ou produto', 'Teste de segurança', 'Benchmark de hardware', 'Análise de tráfego', 2, 2, 1),
('O que é deep learning?', 'Redes neurais complexas', 'Algoritmo simples', 'Técnica de compressão', 'Método de análise financeira', 2, 2, 1),
('O que é NLP?', 'Processamento de linguagem natural', 'Nome lógico de processo', 'Navegação lenta por página', 'Nova linguagem de programação', 2, 2, 1),
('O que é bias em ML?', 'Viés nos dados ou modelo', 'Erro de software', 'Falta de memória', 'Variável oculta', 2, 2, 1),
('O que é gradient boosting?', 'Técnica de ensemble learning', 'Algoritmo de criptografia', 'Método de visualização', 'Linguagem de marcação', 2, 3, 1),
('O que é GAN?', 'Rede adversarial generativa', 'Gestão avançada de redes', 'Gráfico animado neural', 'Gerenciamento automatizado de nuvem', 2, 3, 1),
('O que é cross-validation stratified?', 'Divisão proporcional de classes', 'Validação com dados desbalanceados', 'Validação cruzada com erro fixo', 'Divisão aleatória de dados', 2, 3, 1),
('O que é feature engineering?', 'Criar variáveis relevantes para o modelo', 'Limpar dados', 'Visualizar dados', 'Escrever documentação', 2, 3, 1),
('Qual ferramenta é usada para design gráfico?', 'Adobe Photoshop', 'MySQL', 'Node.js', 'Python', 3, 1, 1),
('O que significa UI?', 'Interface do Usuário', 'Experiência do Usuário', 'Unidade de Interface', 'Interação Universal', 3, 1, 1),
('O que NÃO é uma cor primária?', 'Verde', 'Vermelho', 'Azul', 'Amarelo', 3, 1, 1),
('O que é tipografia?', 'Estudo das fontes', 'Edição de imagens', 'Criação de vídeos', 'Animação 3D', 3, 1, 1),
('O que é paleta de cores?', 'Conjunto de cores usadas em design', 'Pintura digital', 'Modelo de interface', 'Plano de projeto', 3, 1, 1),
('O que é UX Design?', 'Experiência do usuário', 'Interface do usuário', 'Unidade de Xerox', 'Interface Visual', 3, 1, 1),
('O que é wireframe?', 'Esboço visual de uma interface', 'Documento técnico', 'Diagrama de banco de dados', 'Layout finalizado', 3, 1, 1),
('O que é contraste em design?', 'Diferença entre elementos visuais', 'Tamanho de fonte', 'Alinhamento de texto', 'Espaçamento entre linhas', 3, 1, 1),
('O que é alinhamento em design?', 'Organização dos elementos na página', 'Tamanho de imagem', 'Cor de fundo', 'Escala de cores', 3, 1, 1),
('O que é hierarquia visual?', 'Ordem de importância dos elementos', 'Tamanho do layout', 'Número de cores', 'Quantidade de texto', 3, 1, 1),
('O que é grid system?', 'Sistema de alinhamento visual', 'Formato de imagem', 'Tipo de fonte', 'Protocolo de rede', 3, 2, 1),
('O que é kerning?', 'Espaçamento entre letras', 'Espaçamento entre palavras', 'Altura da linha', 'Largura do parágrafo', 3, 2, 1),
('O que é leading em tipografia?', 'Espaçamento vertical entre linhas', 'Espaçamento horizontal entre letras', 'Tamanho da fonte', 'Estilo da letra', 3, 2, 1),
('O que é responsive design?', 'Design adaptável a diferentes telas', 'Design estático', 'Design apenas para desktop', 'Design apenas para mobile', 3, 2, 1),
('O que é branding?', 'Identidade visual de uma marca', 'Desenvolvimento web', 'Gestão de projetos', 'Redes sociais', 3, 2, 1),
('O que é mockup?', 'Visualização realista de um design', 'Protótipo funcional', 'Wireframe interativo', 'Documento de requisitos', 3, 2, 1),
('O que é CMYK?', 'Modelo de cor usado na impressão', 'Modelo de cor digital', 'Tipo de fonte', 'Formato de imagem', 3, 2, 1),
('O que é RGB?', 'Modelo de cor usado na tela', 'Modelo de cor impresso', 'Tipo de fonte', 'Formato de imagem', 3, 2, 1),
('O que é teoria das cores?', 'Estudo sobre combinações de cor', 'Técnica de animação', 'Método de programação', 'Princípio de segurança', 3, 3, 1),
('O que é golden ratio?', 'Proporção áurea estética', 'Taxa de juros', 'Resolução de tela', 'Velocidade de carregamento', 3, 3, 1),
('O que é whitespace?', 'Espaço vazio em um design', 'Fundo branco', 'Margem de texto', 'Borda de imagem', 3, 3, 1),
('O que é sistema de design?', 'Coleção de componentes reutilizáveis', 'Paleta de cores', 'Fonte padrão', 'Template de apresentação', 3, 3, 1),
('O que estuda a Engenharia de Software?', 'Desenvolvimento de sistemas', 'Construção civil', 'Hardware de computadores', 'Redes elétricas', 4, 1, 1),
('Qual norma é usada para gestão de projetos?', 'PMBOK', 'ISO 9001', 'IEEE 802.11', 'ANSI C', 4, 1, 1),
('O que é MVP em desenvolvimento de software?', 'Produto Mínimo Viável', 'Modelo Visual Padrão', 'Método de Validação Paralela', 'Máquina Virtual Protegida', 4, 1, 1),
('O que é engenharia reversa?', 'Analisar código existente para entender seu funcionamento', 'Reconstruir hardware', 'Refatorar código', 'Desenvolver interfaces', 4, 1, 1),
('O que é UML?', 'Linguagem de modelagem unificada', 'Linguagem de programação', 'Protocolo de rede', 'Sistema operacional', 4, 1, 1),
('O que é arquitetura de software?', 'Estrutura geral do sistema', 'Layout do frontend', 'Configuração de servidores', 'Documentação técnica', 4, 1, 1),
('O que é diagrama de classes?', 'Representa estrutura de objetos', 'Diagrama de fluxo', 'Mapa de rede', 'Modelo de processo', 4, 1, 1),
('O que é ciclo de vida do software?', 'Fases do desenvolvimento de software', 'Processo de fabricação', 'Testes de hardware', 'Atualizações de drivers', 4, 1, 1),
('O que é qualidade de software?', 'Característica de funcionamento correto', 'Velocidade de execução', 'Aparência visual', 'Facilidade de instalação', 4, 1, 1),
('O que é manutenção de software?', 'Correção e atualização de sistemas', 'Limpeza física', 'Backup automático', 'Instalação remota', 4, 1, 1),
('O que é metodologia ágil?', 'Abordagem iterativa e colaborativa', 'Modelo linear de desenvolvimento', 'Método de análise financeira', 'Processo de documentação', 4, 2, 1),
('O que é Scrum?', 'Framework ágil de gestão de projetos', 'Linguagem de programação', 'Protocolo de rede', 'Sistema de segurança', 4, 2, 1),
('O que é backlog?', 'Lista de tarefas pendentes', 'Relatório financeiro', 'Base de dados temporária', 'Registro de erros', 4, 2, 1),
('O que é sprint?', 'Ciclo curto de desenvolvimento', 'Versão beta do produto', 'Teste de segurança', 'Simulação de rede', 4, 2, 1),
('O que é refatoração?', 'Melhorar o código sem mudar sua funcionalidade', 'Criar novas funcionalidades', 'Remover bugs', 'Testar performance', 4, 2, 1),
('O que é arquitetura cliente-servidor?', 'Divisão de processamento entre cliente e servidor', 'Arquitetura local', 'Modelo offline', 'Sistema embarcado', 4, 2, 1),
('O que é arquitetura MVC?', 'Modelo-Vista-Controlador', 'Modelo de camadas', 'Arquitetura de rede', 'Padrão de segurança', 4, 2, 1),
('O que é DevOps?', 'Combinação de desenvolvimento e operações', 'Desenvolvimento web', 'Gestão de projetos', 'Marketing digital', 4, 2, 1),
('O que é arquitetura orientada a serviços?', 'Comunicação entre microserviços', 'Arquitetura monolítica', 'Modelo de banco de dados', 'Sistema de segurança', 4, 3, 1),
('O que é entrega contínua?', 'Implantação automática de atualizações', 'Entrega manual de software', 'Teste de segurança', 'Backup de dados', 4, 3, 1),
('O que é pipeline CI/CD?', 'Automação de build, teste e deploy', 'Fluxo de dados', 'Processo de compilação', 'Controle de acesso', 4, 3, 1),
('O que é TDD?', 'Desenvolvimento guiado por testes', 'Teste de usabilidade', 'Treinamento de desenvolvedores', 'Teoria de design', 4, 3, 1),
('Qual tecnologia estrutura páginas web?', 'HTML', 'CSS', 'JavaScript', 'PHP', 5, 1, 1),
('Qual framework JavaScript é popular no FrontEnd?', 'React', 'Node.js', 'Express', 'MongoDB', 5, 1, 1),
('O que define o estilo visual de uma página web?', 'CSS', 'JSON', 'XML', 'SQL', 5, 1, 1),
('O que é DOM?', 'Modelo de Objeto de Documento', 'Documento Oficial do Mundo', 'Diretório Online de Módulos', 'Definição de Objetos Múltiplos', 5, 1, 1),
('O que é SPA?', 'Single Page Application', 'Sistema de Paginação Automática', 'Site Público Aberto', 'Serviço de Processamento Avançado', 5, 1, 1),
('O que é JSX?', 'Extensão do JavaScript para React', 'Linguagem de marcação', 'Protocolo de rede', 'Formato de imagem', 5, 1, 1),
('O que é Bootstrap?', 'Framework CSS', 'Banco de dados', 'Servidor web', 'Sistema operacional', 5, 1, 1),
('O que é Flexbox?', 'Método de layout CSS', 'Técnica de animação', 'Tipo de linguagem', 'Protocolo de rede', 5, 1, 1),
('O que é evento em JavaScript?', 'Resposta a ações do usuário', 'Função matemática', 'Variável global', 'Comando condicional', 5, 1, 1),
('O que é AJAX?', 'Técnica de carregar dados sem recarregar a página', 'Linguagem de marcação', 'Protocolo de rede', 'Sistema operacional', 5, 1, 1),
('O que é SSR?', 'Server Side Rendering', 'Single Sign On', 'Secure Socket Request', 'System Service Runtime', 5, 2, 1),
('O que é CSR?', 'Client Side Rendering', 'Central System Request', 'Cloud Storage Resource', 'Custom Script Render', 5, 2, 1),
('O que é Webpack?', 'Empacotador de módulos JS', 'Editor de texto', 'Banco de dados', 'Servidor web', 5, 2, 1),
('O que é Babel?', 'Compilador de JavaScript moderno', 'Linguagem de marcação', 'Protocolo de rede', 'Sistema operacional', 5, 2, 1),
('O que é Virtual DOM?', 'Cópia leve do DOM real', 'DOM otimizado', 'DOM remoto', 'DOM local', 5, 2, 1),
('O que é escopo em JavaScript?', 'Visibilidade de variáveis', 'Tamanho do documento', 'Velocidade de execução', 'Quantidade de memória', 5, 2, 1),
('O que é hoisting?', 'Elevação de declarações', 'Declaração de classes', 'Escopo de função', 'Regra de estilo', 5, 2, 1),
('O que é callback?', 'Função passada como parâmetro', 'Função recursiva', 'Função anônima', 'Função assíncrona', 5, 2, 1),
('O que é currying?', 'Transformar função com múltiplos argumentos em sequenciais', 'Função recursiva', 'Função pura', 'Função impura', 5, 3, 1),
('O que é closure?', 'Função que mantém acesso a variáveis externas', 'Função privada', 'Função pública', 'Função estática', 5, 3, 1),
('O que é shadow DOM?', 'DOM isolado usado em web components', 'DOM oculto', 'DOM temporário', 'DOM compartilhado', 5, 3, 1),
('O que é Tree Shaking?', 'Remover código não utilizado', 'Compactar arquivos', 'Criptografar dados', 'Enviar dados via rede', 5, 3, 1),
('O que faz um desenvolvedor FullStack?', 'Trabalha tanto no front quanto no back', 'Apenas cria designs', 'Gerencia servidores', 'Faz análises de dados', 6, 1, 1),
('Qual tecnologia permite rodar JavaScript no servidor?', 'Node.js', 'React', 'Angular', 'Vue.js', 6, 1, 1),
('Qual base de dados é frequentemente usada com Node.js?', 'MongoDB', 'Oracle', 'PostgreSQL', 'MySQL', 6, 1, 1),
('O que é API RESTful?', 'API baseada em padrões REST', 'API com autenticação OAuth', 'API com token JWT', 'API local', 6, 1, 1),
('O que é SSR?', 'Server Side Rendering', 'Single Sign On', 'Secure Socket Request', 'System Service Runtime', 6, 1, 1),
('O que é CSR?', 'Client Side Rendering', 'Central System Request', 'Cloud Storage Resource', 'Custom Script Render', 6, 1, 1),
('O que é SPA?', 'Single Page Application', 'Sistema de Paginação Automática', 'Site Público Aberto', 'Serviço de Processamento Avançado', 6, 1, 1),
('O que é middleware?', 'Software intermediário entre aplicações', 'Dispositivo físico', 'Biblioteca gráfica', 'Protocolo de segurança', 6, 1, 1),
('O que é cache em sistemas web?', 'Armazenamento temporário de dados', 'Banco de dados permanente', 'Linguagem de marcação', 'Protocolo de rede', 6, 1, 1),
('O que significa CRUD?', 'Criar, Ler, Atualizar, Deletar', 'Codificar, Rodar, Usar, Desenvolver', 'Conectar, Requerer, Usar, Disponibilizar', 'Criptografar, Rodar, Usar, Deletar', 6, 1, 1),
('O que é GraphQL?', 'Linguagem de consulta de APIs', 'Linguagem de marcação', 'Protocolo de rede', 'Sistema operacional', 6, 2, 1),
('O que é JWT?', 'Token de autenticação', 'Protocolo de rede', 'Linguagem de marcação', 'Servidor web', 6, 2, 1),
('O que é Docker?', 'Plataforma de containers', 'Editor de texto', 'Banco de dados', 'Servidor web', 6, 2, 1),
('O que é CI/CD?', 'Integração e entrega contínuas', 'Controle de versão', 'Desenvolvimento guiado por testes', 'Gestão de projetos', 6, 2, 1),
('O que é ORM?', 'Mapeamento Objeto-Relacional', 'Operador Relacional Matemático', 'Objeto de Resposta Múltipla', 'Organizador de Redes Móveis', 6, 2, 1),
('O que é microserviço?', 'Arquitetura modular de serviços independentes', 'Aplicação monolítica', 'Base de dados relacional', 'Protocolo de rede', 6, 2, 1),
('O que é arquitetura cliente-servidor?', 'Divisão de processamento entre cliente e servidor', 'Arquitetura local', 'Modelo offline', 'Sistema embarcado', 6, 2, 1),
('O que é DevOps?', 'Combinação de desenvolvimento e operações', 'Desenvolvimento web', 'Gestão de projetos', 'Marketing digital', 6, 2, 1),
('O que é eventual consistency?', 'Base de dados eventualmente sincronizada', 'Sistema de segurança', 'Protocolo de rede', 'Linguagem de marcação', 6, 3, 1),
('O que é CQRS?', 'Pattern de separação de leitura e escrita', 'Linguagem de programação', 'Ferramenta de design', 'Editor de texto', 6, 3, 1),
('O que é Saga Pattern?', 'Gerenciar transações distribuídas', 'Modelo de interface', 'Padrão de design', 'Sistema operacional', 6, 3, 1),
('O que é pipeline CI/CD?', 'Automação de build, teste e deploy', 'Fluxo de dados', 'Processo de compilação', 'Controle de acesso', 6, 3, 1),
('Qual é uma linguagem orientada a objetos?', 'Java', 'HTML', 'CSS', 'SQL', 7, 1, 1),
('O que é um loop em programação?', 'Estrutura de repetição', 'Função matemática', 'Variável global', 'Comando condicional', 7, 1, 1),
('Qual comando finaliza um programa em C?', 'return 0;', 'exit(, 1)', 'end', 'stop', 7, 1, 1),
('O que é uma variável?', 'Espaço na memória para armazenar dados', 'Função', 'Classe', 'Método', 7, 1, 1),
('O que é uma função?', 'Bloco de código reutilizável', 'Loop infinito', 'Comando condicional', 'Variável global', 7, 1, 1),
('O que é recursão?', 'Função que chama a si mesma', 'Variável global', 'Loop infinito', 'Condicional múltipla', 7, 1, 1),
('O que é depuração?', 'Processo de encontrar erros no código', 'Compilar código', 'Executar testes automatizados', 'Converter código', 7, 1, 1),
('O que é compilação?', 'Converter código-fonte em máquina', 'Rodar programas', 'Escrever código', 'Testar software', 7, 1, 1),
('O que é sintaxe?', 'Regras de escrita de uma linguagem', 'Estilo de codificação', 'Boas práticas', 'Performance do código', 7, 1, 1),
('O que é tipo de dado?', 'Classificação do valor armazenado', 'Nome da variável', 'Valor numérico', 'Texto', 7, 1, 1),
('O que é paradigma de programação?', 'Estilo ou abordagem de desenvolvimento', 'Linguagem de marcação', 'Protocolo de rede', 'Sistema operacional', 7, 2, 1),
('O que é polimorfismo?', 'Capacidade de assumir várias formas', 'Herança de classe', 'Encapsulamento de dados', 'Abstração de objeto', 7, 2, 1),
('O que é herança em POO?', 'Capacidade de uma classe herdar propriedades', 'Classe abstrata', 'Interface', 'Método estático', 7, 2, 1),
('O que é pilha de chamadas?', 'Lista de funções em execução', 'Memória heap', 'Cache do sistema', 'Pilha de threads', 7, 2, 1),
('O que é exceção?', 'Erro durante a execução do programa', 'Variável não declarada', 'Função inexistente', 'Comando inválido', 7, 2, 1),
('O que é lambda function?', 'Função sem nome', 'Função com classe', 'Função dentro de outra função', 'Função com retorno múltiplo', 7, 2, 1),
('O que é garbage collection?', 'Liberação automática de memória', 'Limpeza de disco', 'Exclusão de arquivos', 'Backup automático', 7, 2, 1),
('O que é sobrecarga de método?', 'Vários métodos com mesmo nome mas diferentes parâmetros', 'Método privado', 'Método público', 'Método estático', 7, 2, 1),
('O que é curry?', 'Transformar função com múltiplos argumentos em sequenciais', 'Função recursiva', 'Função pura', 'Função impura', 7, 3, 1),
('O que é memoização?', 'Armazenar resultados de chamadas anteriores', 'Recursão', 'Iteração', 'Otimização de rede', 7, 3, 1),
('O que é tail recursion?', 'Recursão que ocorre no final da função', 'Recursão múltipla', 'Recursão aninhada', 'Recursão não otimizada', 7, 3, 1),
('O que é concorrência?', 'Execução simultânea de tarefas', 'Execução sequencial', 'Programação funcional', 'Programação estruturada', 7, 3, 1),
('O que é phishing?', 'Tentativa de enganar usuários para roubar informações', 'Ataque físico a servidores', 'Vírus de computador', 'Código malicioso', 8, 1, 1),
('O que é criptografia?', 'Codificação de dados para proteger informações', 'Backup automático', 'Análise de tráfego', 'Monitoramento remoto', 8, 1, 1),
('O que é firewall?', 'Sistema de segurança que filtra tráfego de rede', 'Software antivírus', 'Dispositivo de armazenamento', 'Servidor DNS', 8, 1, 1),
('O que é malware?', 'Software malicioso', 'Firewall avançado', 'Sistema operacional', 'Dispositivo de rede', 8, 1, 1),
('O que é ransomware?', 'Malware que criptografa arquivos', 'Software de backup', 'Antivírus', 'Ferramenta de edição', 8, 1, 1),
('O que é zero-day?', 'Vulnerabilidade desconhecida', 'Atualização de segurança', 'Senha forte', 'Certificado digital', 8, 1, 1),
('O que é autenticação?', 'Verificação de identidade do usuário', 'Autorização de acesso', 'Criptografia de dados', 'Backup remoto', 8, 1, 1),
('O que é autorização?', 'Permissão concedida após autenticação', 'Verificação de identidade', 'Codificação de dados', 'Proteção física', 8, 1, 1),
('O que é hash?', 'Função que transforma dados em valor fixo', 'Chave simétrica', 'Chave assimétrica', 'Algoritmo de compressão', 8, 1, 1),
('O que é spoofing?', 'Falsificação de identidade em redes', 'Rastreamento de IP', 'Backup de dados', 'Monitoramento de rede', 8, 1, 1),
('O que é XSS?', 'Injeção de scripts entre sites', 'Ataque de força bruta', 'Ataque de negação de serviço', 'Vírus de macro', 8, 2, 1),
('O que é SQL injection?', 'Inserção de código SQL malicioso', 'Ataque de phishing', 'Vírus de script', 'Ataque de buffer overflow', 8, 2, 1),
('O que é DDoS?', 'Ataque de negação de serviço distribuído', 'Invasão de servidor', 'Phishing em larga escala', 'Trojan Horse', 8, 2, 1),
('O que é IDS?', 'Sistema de detecção de intrusão', 'Firewall avançado', 'Sistema de backup', 'Software antivírus', 8, 2, 1),
('O que é IPS?', 'Sistema de prevenção de intrusão', 'Sistema de backup', 'Firewall básico', 'Antivírus', 8, 2, 1),
('O que é MITM?', 'Ataque de homem no meio', 'Ataque de phishing', 'Ataque de força bruta', 'Ataque de spoofing', 8, 2, 1),
('O que é honeypot?', 'Sistema falso para atrair invasores', 'Sistema de backup', 'Sistema de monitoramento', 'Sistema de firewall', 8, 2, 1),
('O que é pentest?', 'Teste de penetração ético', 'Teste de desempenho', 'Teste de hardware', 'Teste de usabilidade', 8, 2, 1),
('O que é pivoting?', 'Movimentação lateral em rede comprometida', 'Ataque inicial', 'Exploração de vulnerabilidade', 'Ataque de brute force', 8, 3, 1),
('O que é steganography?', 'Ocultar dados dentro de outros dados', 'Criptografia avançada', 'Backup seguro', 'Monitoramento remoto', 8, 3, 1),
('O que é buffer overflow?', 'Exploração de falha de memória', 'Ataque de phishing', 'Ataque de força bruta', 'Ataque de spoofing', 8, 3, 1),
('O que é rootkit?', 'Ferramenta que esconde acessos maliciosos', 'Software antivírus', 'Sistema de backup', 'Firewall avançado', 8, 3, 1);

INSERT INTO perguntas (pergunta, resposta_correta, alternativa1, alternativa2, alternativa3, area_atuacao_id, nivel_id, ativa) VALUES 
('O que representa um "ator" em um Diagrama de Casos de Uso?', 'Uma entidade externa que interage com o sistema', 'Um método da classe', 'Um estado do objeto', 'Um evento do sistema', 4, 1, 1),
('Como são representados os casos de uso?', 'Com elipses', 'Com retângulos', 'Com triângulos', 'Com círculos', 4, 1, 1),
('Qual símbolo representa a relação entre um ator e um caso de uso?', 'Linha simples', 'Linha tracejada', 'Seta dupla', 'Círculo', 4, 1, 1),
('O que indica a relação "include" entre casos de uso?', 'Que um caso de uso é sempre chamado por outro', 'Que o caso de uso é opcional', 'Que o comportamento se repete', 'Que o caso é condicional', 4, 1, 1),
('O que indica a relação "extend"?', 'Que um caso de uso pode adicionar comportamento opcionalmente', 'Que o caso de uso é obrigatório', 'Que há uma decisão no fluxo', 'Que o caso é repetido', 4, 1, 1),
('Como se chama a notação usada para agrupar casos de uso?', 'Sistema', 'Pacote', 'Diagrama', 'Classe', 4, 1, 1),
('O que NÃO é representado em um Diagrama de Casos de Uso?', 'Métodos e atributos', 'Casos de uso', 'Relações entre atores', 'Atividades do sistema', 4, 1, 1),
('Para qual público o Diagrama de Casos de Uso é mais útil?', 'Stakeholders e usuários', 'Desenvolvedores', 'Testadores', 'Analistas de dados', 4, 1, 1),
('Qual tipo de relacionamento permite reusar partes comuns de diferentes casos de uso?', 'Include', 'Extend', 'Generalização', 'Associação', 4, 1, 1),
('O que representa uma associação entre dois atores?', 'Interação entre eles', 'Herança', 'Dependência', 'Comunicação direta com o sistema', 4, 1, 1),
('Como se chama o elemento que encapsula todos os casos de uso?', 'Sistema', 'Pacote', 'Objeto', 'Classe', 4, 1, 1),
('Qual ferramenta UML usa esse diagrama principalmente?', 'Análise de requisitos', 'Projeto de banco de dados', 'Implementação de código', 'Documentação técnica', 4, 1, 1),
('O que NÃO pode ser modelado com um Diagrama de Casos de Uso?', 'Fluxo interno de uma operação', 'Interação com o usuário', 'Funcionalidades do sistema', 'Regras de negócio', 4, 1, 1),
('Como se chamam os elementos que podem iniciar um caso de uso?', 'Atores primários', 'Objetos', 'Classes', 'Estados', 4, 1, 1),
('Qual é o foco desse diagrama?', 'Funções do sistema vistas pelo usuário', 'Estrutura interna do sistema', 'Comportamento sequencial', 'Estados de um objeto', 4, 1, 1),
('Qual é o principal elemento de um Diagrama de Classes?', 'Classe', 'Atributo', 'Método', 'Objeto', 4, 1, 1),
('Como é representada uma classe?', 'Retângulo com três compartimentos', 'Elipse', 'Losango', 'Triângulo', 4, 1, 1),
('O que significa a multiplicidade "1..*"?', 'Um ou mais', 'Zero ou um', 'Exatamente um', 'Zero ou mais', 4, 1, 1),
('O que é uma associação?', 'Relação entre duas classes', 'Tipo de classe abstrata', 'Operação da classe', 'Atributo privado', 4, 1, 1),
('Qual símbolo representa a agregação?', 'Losango vazio', 'Losango preenchido', 'Seta simples', 'Seta dupla', 4, 1, 1),
('Qual símbolo representa a composição?', 'Losango preenchido', 'Losango vazio', 'Seta tracejada', 'Elipse', 4, 1, 1),
('O que é uma classe abstrata?', 'Não pode ser instanciada', 'Tem apenas métodos públicos', 'Tem apenas atributos', 'É uma interface', 4, 1, 1),
('Como se chama a relação onde uma classe é especialização de outra?', 'Generalização', 'Agregação', 'Associação', 'Dependência', 4, 1, 1),
('O que é uma interface?', 'Classe sem implementação', 'Classe concreta', 'Objeto estático', 'Enumeração', 4, 1, 1),
('Como se indica visibilidade pública em UML?', '+', '-', '#', '~', 4, 1, 1),
('Como se indica visibilidade privada em UML?', '-', '+', '#', '~', 4, 1, 1),
('O que é uma associação bidirecional?', 'As duas classes conhecem uma à outra', 'Uma classe herda da outra', 'Uma classe contém a outra', 'Uma classe usa a outra', 4, 1, 1),
('O que é uma associação unidirecional?', 'Apenas uma classe conhece a outra', 'Ambas classes têm métodos iguais', 'Uma classe herda da outra', 'Uma classe contém a outra', 4, 1, 1),
('O que é uma classe associativa?', 'Tem atributos e métodos além da ligação', 'É uma classe abstrata', 'É uma interface', 'É uma classe estática', 4, 1, 1),
('Como se chama o número de classes envolvidas em uma associação?', 'Aridade', 'Cardinalidade', 'Multiplicidade', 'Navegabilidade', 4, 1, 1),
('O que representa um nó de início em um Diagrama de Atividades?', 'Círculo preenchido', 'Quadrado', 'Losango', 'Elipse', 4, 1, 1),
('O que simboliza um nó de término?', 'Círculo com borda dupla', 'Círculo preenchido', 'Losango', 'Quadrado', 4, 1, 1),
('Qual forma representa uma decisão?', 'Losango', 'Círculo', 'Quadrado', 'Elipse', 4, 1, 1),
('Como se chama o fluxo paralelo de atividades?', 'Barras de sincronização', 'Transições', 'Decisões', 'Finais', 4, 1, 1),
('Qual é a finalidade deste diagrama?', 'Mostrar o fluxo de atividades', 'Mostrar a interação entre objetos', 'Mostrar estados', 'Mostrar relações entre classes', 4, 1, 1),
('Como se chama o elemento que divide o fluxo em caminhos distintos?', 'Decisão', 'Ação', 'Objeto', 'Estado', 4, 1, 1),
('Como se chama o elemento que junta caminhos distintos?', 'Junção', 'Decisão', 'Ação', 'Objeto', 4, 1, 1),
('Como se chama o elemento que inicia várias ações simultaneamente?', 'Barras de sincronização', 'Decisão', 'Junção', 'Ação', 4, 1, 1),
('O que é um fluxo de controle?', 'Sequência de ações executadas', 'Relação entre classes', 'Conexão entre objetos', 'Hierarquia de estados', 4, 1, 1),
('Como se chama a transição entre ações?', 'Fluxo de controle', 'Fluxo de objeto', 'Fluxo de dados', 'Fluxo de mensagens', 4, 1, 1),
('Qual elemento representa uma atividade específica?', 'Retângulo com cantos arredondados', 'Círculo', 'Losango', 'Triângulo', 4, 1, 1),
('Como se chama a saída de um fluxo condicional?', 'Fluxo de decisão', 'Fluxo de controle', 'Fluxo de objeto', 'Fluxo de mensagem', 4, 1, 1),
('Como se chama a entrada de um fluxo condicional?', 'Fluxo de decisão', 'Fluxo de controle', 'Fluxo de objeto', 'Fluxo de mensagem', 4, 1, 1),
('O que é um fluxo de objeto?', 'Movimento de dados entre ações', 'Sequência de ações', 'Relação entre classes', 'Conexão entre objetos', 4, 1, 1),
('Como se chama o fluxo que representa dados sendo passados entre ações?', 'Fluxo de objeto', 'Fluxo de controle', 'Fluxo de dados', 'Fluxo de mensagens', 4, 1, 1),
('Qual é a principal diferença entre um Diagrama de Classes e um Diagrama de Objetos?', 'O primeiro mostra estruturas teóricas, o segundo instâncias reais', 'O primeiro mostra objetos, o segundo classes', 'O primeiro mostra métodos, o segundo atributos', 'O primeiro é estático, o segundo dinâmico', 4, 1, 1),
('Como se representa um objeto em um Diagrama de Objetos?', 'NomeDoObjeto: NomeDaClasse', '[NomeDoObjeto]', 'NomeDaClasse()', 'NomeDaClasse -> NomeDoObjeto', 4, 1, 1),
('Qual é o foco desse diagrama?', 'Mostrar instâncias específicas de classes', 'Mostrar estados do sistema', 'Mostrar fluxo de atividades', 'Mostrar interações entre objetos', 4, 1, 1),
('O que NÃO pode ser representado nesse diagrama?', 'Métodos abstratos', 'Instâncias de classes', 'Valores dos atributos', 'Relações entre objetos', 4, 1, 1),
('Para qual tipo de análise esse diagrama é mais útil?', 'Análise estática do sistema', 'Análise comportamental', 'Modelagem de dados', 'Modelagem de sequência', 4, 1, 1),
('Qual é o nome dado ao relacionamento entre objetos?', 'Link', 'Associação', 'Herança', 'Composição', 4, 1, 1),
('Como se chama a relação entre dois objetos que compartilham uma associação?', 'Link', 'Conexão', 'Fluxo', 'Referência', 4, 1, 1),
('O que indica a notação "cliente1: Cliente" no diagrama?', 'Que cliente1 é uma instância da classe Cliente', 'Que Cliente é uma instância de cliente1', 'Que cliente1 é uma interface', 'Que cliente1 é uma classe', 4, 1, 1),
('Esse diagrama é mais útil para:', 'Validar modelos de classes', 'Desenvolver interfaces gráficas', 'Criar bancos de dados', 'Implementar algoritmos', 4, 1, 1),
('Como se chamam os valores associados aos atributos nos objetos?', 'Valores de instância', 'Atributos estáticos', 'Métodos concretos', 'Valores abstratos', 4, 1, 1),
('Qual é a principal característica do Diagrama de Sequência?', 'Mostra a colaboração entre objetos em ordem cronológica', 'Representa estados e transições', 'Exibe o fluxo de dados entre classes', 'Indica as associações entre atores e casos de uso', 4, 1, 1),
('Como são chamadas as linhas verticais pontilhadas que representam objetos?', 'Lifelines', 'Mensagens', 'Atividades', 'Estados', 4, 1, 1),
('O que é uma mensagem síncrona?', 'Uma mensagem que espera por uma resposta antes de continuar', 'Uma mensagem que não retorna resposta', 'Uma mensagem condicional', 'Uma mensagem paralela', 4, 1, 1),
('O que é uma mensagem assíncrona?', 'Uma mensagem que não espera resposta', 'Uma mensagem que espera resposta', 'Uma mensagem condicional', 'Uma mensagem repetida', 4, 1, 1),
('Como se representa uma operação retornando valor?', 'Seta tracejada voltando com retorno', 'Seta simples sem retorno', 'Retângulo com texto', 'Losango com decisão', 4, 1, 1),
('Qual elemento representa o tempo de vida de um objeto?', 'Lifeline', 'Mensagem', 'Objeto', 'Decisão', 4, 1, 1),
('Como se chama o retângulo sobre a lifeline que indica ativação de um objeto?', 'Ativação', 'Foco de controle', 'Vida', 'Tempo', 4, 1, 1),
('Qual é a finalidade desse diagrama?', 'Mostrar interações sequenciais entre objetos', 'Mostrar fluxo de atividades', 'Mostrar relações entre classes', 'Mostrar estados do sistema', 4, 1, 1),
('O que indica uma seta horizontal entre lifelines?', 'Troca de mensagens', 'Herança', 'Associação', 'Composição', 4, 1, 1),
('Como se chama o momento em que um objeto é criado durante a sequência?', 'Criação', 'Início', 'Nascimento', 'Geração', 4, 1, 1),
('Qual é o principal objetivo desse diagrama?', 'Mostrar os diferentes estados pelos quais um objeto pode passar', 'Mostrar fluxo de atividades', 'Mostrar interações entre objetos', 'Mostrar relações entre classes', 4, 1, 1),
('Qual símbolo representa o estado inicial?', 'Círculo preenchido', 'Círculo com borda dupla', 'Quadrado', 'Triângulo', 4, 1, 1),
('Qual símbolo representa o estado final?', 'Círculo com borda dupla', 'Círculo preenchido', 'Quadrado', 'Triângulo', 4, 1, 1),
('Como se chama a mudança de um estado para outro?', 'Transição', 'Evento', 'Ação', 'Conduta', 4, 1, 1),
('O que é um evento?', 'Um gatilho que causa uma transição', 'Um estado terminal', 'Uma condição booleana', 'Uma ação contínua', 4, 1, 1),
('Como se chama uma transição automática sem evento?', 'Transição interna', 'Transição externa', 'Auto-transição', 'Loop', 4, 1, 1),
('Como se chama um estado composto com subestados?', 'Estado aninhado', 'Estado simples', 'Estado terminal', 'Estado inicial', 4, 1, 1),
('O que significa a notação [condição] em uma transição?', 'A transição só ocorre se a condição for verdadeira', 'A transição é opcional', 'A transição é temporizada', 'A transição é assíncrona', 4, 1, 1),
('Qual é o nome do estado que indica pausa ou espera?', 'Estado histórico', 'Estado final', 'Estado suspenso', 'Estado intermediário', 4, 1, 1),
('Como se chama a propriedade que permite lembrar o último estado visitado?', 'Histórico', 'Reinicialização', 'Recorrência', 'Memória', 4, 1, 1),
('Qual é a principal função do Diagrama de Comunicação?', 'Mostrar a troca de mensagens entre objetos em ordem sequencial', 'Mostrar fluxo de atividades', 'Mostrar estados do objeto', 'Mostrar relações entre classes', 4, 1, 1),
('Como são numeradas as mensagens nesse diagrama?', 'Com números decimais para mostrar a sequência', 'Em ordem alfabética', 'Com cores', 'Com letras', 4, 1, 1),
('Qual é a principal vantagem desse diagrama?', 'Destacar a organização estrutural e as interações', 'Mostrar fluxo de controle', 'Mostrar hierarquia de classes', 'Mostrar dependências', 4, 1, 1),
('Como se chama a linha conectando dois objetos?', 'Link', 'Mensagem', 'Associação', 'Conector', 4, 1, 1),
('O que é uma mensagem de retorno?', 'Uma mensagem que volta para o remetente após uma chamada', 'Uma mensagem perdida', 'Uma mensagem condicional', 'Uma mensagem assíncrona', 4, 1, 1),
('Como se representa um objeto?', 'Com nome seguido de dois pontos e classe', 'Com círculo', 'Com elipse', 'Com quadrado vazio', 4, 1, 1),
('Como se chama a relação entre objetos nesse diagrama?', 'Link', 'Mensagem', 'Associação', 'Herança', 4, 1, 1),
('O que é uma mensagem síncrona?', 'Uma mensagem que espera por uma resposta', 'Uma mensagem que não retorna resposta', 'Uma mensagem condicional', 'Uma mensagem repetida', 4, 1, 1),
('Como se chama a forma de organizar os objetos visualmente?', 'Layout estrutural', 'Organização lógica', 'Posição temporal', 'Diagrama de fluxo', 4, 1, 1),
('O que NÃO é priorizado nesse diagrama?', 'Ordem temporal detalhada', 'Links entre objetos', 'Mensagens trocadas', 'Estrutura organizacional', 4, 1, 1);