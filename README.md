# 📋 TodoList Full-Stack Application

Uma aplicação completa de gerenciamento de tarefas desenvolvida com Laravel + Vue.js + TypeScript, incluindo autenticação JWT, busca em tempo real e paginação.

## 🚀 Tecnologias Utilizadas

### Backend
- **Laravel 11** - Framework PHP
- **JWT Authentication** - Autenticação segura
- **PostgreSQL** - Banco de dados principal
- **MySQL** - Suporte alternativo
- **Docker (Laravel Sail)** - Containerização opcional

### Frontend
- **Vue.js 3** - Framework JavaScript reativo
- **TypeScript** - Tipagem estática
- **Pinia** - Gerenciamento de estado moderno
- **Axios** - Cliente HTTP com interceptors
- **Vite** - Build tool ultra-rápido
- **Tailwind CSS** - Framework CSS (opcional)

### Ferramentas de Desenvolvimento
- **Composer** - Gerenciador de dependências PHP
- **NPM** - Gerenciador de dependências Node.js
- **XAMPP/Laravel Sail** - Ambiente de desenvolvimento
- **Insomnia/Postman** - Testes da API

## 📁 Estrutura do Projeto

```
todolist-api-laravel/
├── todolist-api/           # Backend Laravel
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   └── Models/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── docker-compose.yml
├── todolist-frontend/      # Frontend Vue.js
│   ├── src/
│   │   ├── components/
│   │   ├── stores/
│   │   ├── services/
│   │   └── types/
│   └── package.json
└── README.md
```

## 🔧 Configuração e Instalação

### Pré-requisitos
- **PHP 8.1+** (recomendado 8.2)
- **Composer** - Gerenciador de dependências PHP
- **Node.js 18+** - Para o frontend
- **Git** - Controle de versão
- **Banco de dados**: PostgreSQL, MySQL ou SQLite

### 📋 Opções de Instalação
Escolha uma das duas opções abaixo:

---

## 🐳 Opção 1: Instalação com Docker (Laravel Sail)

### 1. Clone o repositório
```bash
git clone https://github.com/linhareseduardo/todolist-fullstack-laravel-vue.git
cd todolist-api-laravel
```

### 2. Configuração do Backend
```bash
cd todolist-api

# Instalar dependências
composer install

# Configurar ambiente
cp .env.example .env

# Subir containers Docker
./vendor/bin/sail up -d

# Gerar chave da aplicação
./vendor/bin/sail artisan key:generate

# Gerar chave JWT
./vendor/bin/sail artisan jwt:secret

# Executar migrações e seeders
./vendor/bin/sail artisan migrate --seed
```

### 3. Configuração do Frontend
```bash
cd ../todolist-frontend

# Instalar dependências
npm install

# Iniciar servidor de desenvolvimento
npm run dev
```

---

## 💻 Opção 2: Instalação com XAMPP/WAMP

### 1. Clone o repositório
```bash
git clone https://github.com/linhareseduardo/todolist-fullstack-laravel-vue.git
cd todolist-api-laravel
```

### 2. Configuração do Backend
```bash
cd todolist-api

# Instalar dependências do Composer
composer install

# Configurar arquivo de ambiente
cp .env.example .env
```

### 3. Configurar o arquivo `.env`
Edite o arquivo `.env` na pasta `todolist-api`:

```env
# Configuração da aplicação
APP_NAME="TodoList API"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Configuração do banco de dados
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=todolist_db
DB_USERNAME=root
DB_PASSWORD=

# JWT Configuration
JWT_SECRET=

# CORS
SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost:5174,localhost:5175
```

### 4. Gerar chaves e configurar banco
```bash
# Gerar chave da aplicação
php artisan key:generate

# Gerar chave JWT
php artisan jwt:secret

# Criar banco de dados (MySQL via phpMyAdmin)
# Acesse: http://localhost/phpmyadmin
# Crie um banco chamado: todolist_db

# Executar migrações e seeders
php artisan migrate --seed

# Iniciar servidor Laravel
php artisan serve --host=127.0.0.1 --port=8000
```

### 5. Configuração do Frontend
```bash
cd ../todolist-frontend

# Instalar dependências
npm install

# Iniciar servidor de desenvolvimento  
npm run dev
```

## 🌐 URLs da Aplicação

### Com Docker (Sail):
- **Frontend Vue.js**: http://localhost:5173 (ou 5174/5175)
- **Backend Laravel API**: http://localhost/api
- **pgAdmin**: http://localhost:8080
  - Email: `admin@todolist.com` 
  - Senha: `admin123`

### Com XAMPP:
- **Frontend Vue.js**: http://localhost:5173 (ou 5174/5175)
- **Backend Laravel API**: http://localhost:8000/api
- **phpMyAdmin**: http://localhost/phpmyadmin

## 🔐 Primeiro Acesso

### 1. Criar uma conta
Acesse o frontend e clique em "Registrar" para criar sua conta.

### 2. Fazer login
Use suas credenciais para acessar o sistema.

### 3. Começar a usar
- Crie suas primeiras categorias
- Adicione tarefas às categorias
- Use os filtros e busca para organizar

## 📊 Funcionalidades Principais

### 🔐 Sistema de Autenticação
- Registro de novos usuários
- Login com JWT (JSON Web Tokens)
- Logout seguro
- Proteção de rotas
- Dados isolados por usuário

### 📂 Gestão de Categorias
- ✅ Criar, editar, excluir categorias
- ✅ Busca em tempo real por nome
- ✅ Paginação (3 categorias por página)
- ✅ Contador de tarefas por categoria
- ✅ Interface responsiva com cards
- ✅ Dropdown com todas as categorias para seleção

### ✅ Gestão de Tarefas
- ✅ CRUD completo de tarefas
- ✅ **Busca em tempo real** por título e descrição
- ✅ **Paginação inteligente** (3 tarefas por página)
- ✅ Filtros combinados por:
  - Categoria
  - Status (Pendente, Em Progresso, Concluída)
  - Prioridade (Baixa, Média, Alta)
- ✅ Data de vencimento opcional
- ✅ Indicadores visuais de status e prioridade
- ✅ Alertas para tarefas vencidas
- ✅ Scroll automático para formulários de edição

### 🎨 Interface do Usuário
- ✅ Design moderno e responsivo
- ✅ Navegação por abas com estado isolado
- ✅ Animações suaves e transições
- ✅ Feedback visual para todas as ações
- ✅ Estados de loading e tratamento de erros
- ✅ Campos de busca integrados
- ✅ Reset automático de formulários
- ✅ Componentes reutilizáveis

## 🗄️ Banco de Dados

### Tabelas Principais

#### Categories
- `id` - Chave primária
- `name` - Nome da categoria
- `created_at` / `updated_at` - Timestamps

#### Tasks
- `id` - Chave primária
- `category_id` - Chave estrangeira
- `title` - Título da tarefa
- `description` - Descrição (opcional)
- `status` - pending, in_progress, completed
- `priority` - low, medium, high
- `due_date` - Data de vencimento (opcional)
- `created_at` / `updated_at` - Timestamps

## 🔌 API Endpoints

### 🔐 Autenticação
- `POST /api/register` - Registrar novo usuário
- `POST /api/login` - Fazer login
- `POST /api/logout` - Fazer logout
- `GET /api/user` - Obter dados do usuário autenticado

### 📂 Categorias
- `GET /api/categories` - Listar categorias (paginação + busca)
  - `?page=1` - Página específica
  - `?search=termo` - Buscar por nome
  - Sem parâmetros = todas as categorias
- `POST /api/categories` - Criar categoria
- `PUT /api/categories/{id}` - Atualizar categoria
- `DELETE /api/categories/{id}` - Excluir categoria

### ✅ Tarefas
- `GET /api/tasks` - Listar tarefas (paginação + filtros + busca)
  - `?page=1` - Página específica
  - `?search=termo` - Buscar por título/descrição
  - `?category_id=1` - Filtrar por categoria
  - `?status=pending` - Filtrar por status
  - `?priority=high` - Filtrar por prioridade
- `POST /api/tasks` - Criar tarefa
- `PUT /api/tasks/{id}` - Atualizar tarefa
- `DELETE /api/tasks/{id}` - Excluir tarefa

### 📄 Exemplos de Requisição

#### Criar Tarefa
```json
POST /api/tasks
{
  "title": "Minha nova tarefa",
  "description": "Descrição detalhada",
  "category_id": 1,
  "priority": "high",
  "status": "pending",
  "due_date": "2025-12-31"
}
```

#### Buscar Tarefas
```bash
GET /api/tasks?search=reunião&category_id=2&status=pending&page=1
```

## 🧪 Testes

### Backend (Laravel)
```bash
cd todolist-api

# Com Docker Sail
./vendor/bin/sail test

# Com XAMPP
php artisan test

# Executar testes específicos
php artisan test --filter TaskTest
```

### Frontend (Vue.js)
```bash
cd todolist-frontend

# Testes unitários
npm run test:unit

# Testes E2E (se configurados)
npm run test:e2e

# Executar testes com coverage
npm run test:coverage
```

## 🔧 Solução de Problemas

### Problema: Erro de CORS
**Solução**: Verifique se o `SANCTUM_STATEFUL_DOMAINS` no `.env` inclui a porta do frontend.

### Problema: JWT Token inválido
**Solução**: Execute `php artisan jwt:secret` para gerar nova chave.

### Problema: Banco de dados não conecta
**Solução**: Verifique as configurações do `.env` e se o banco foi criado.

### Problema: Categorias não carregam no dropdown
**Solução**: Verifique se a API está rodando e se o usuário está autenticado.

### Problema: Frontend não encontra a API
**Solução**: Confirme se a `baseURL` no `api.ts` está correta (http://localhost:8000/api).

## 🚀 Deploy

### Preparação para Produção

1. **Backend**:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

2. **Frontend**:
```bash
npm run build
```

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## � Histórico de Versões

### v2.1.0 (Atual)
- ✅ Busca em tempo real para tarefas e categorias
- ✅ Paginação inteligente (3 itens por página)
- ✅ Correção do dropdown de categorias
- ✅ Reset de estado entre navegação de abas
- ✅ Scroll automático para formulários de edição

### v2.0.0
- ✅ Sistema de autenticação JWT
- ✅ Isolamento de dados por usuário
- ✅ Interface Vue.js 3 + TypeScript
- ✅ Gerenciamento de estado com Pinia

### v1.0.0
- ✅ CRUD básico de tarefas e categorias
- ✅ API Laravel com PostgreSQL
- ✅ Interface responsiva

## �👨‍💻 Desenvolvedor

**Eduardo Linhares**
- GitHub: [@linhareseduardo](https://github.com/linhareseduardo)
- Repositório: [todolist-fullstack-laravel-vue](https://github.com/linhareseduardo/todolist-fullstack-laravel-vue)

## 🙏 Agradecimentos

- Laravel Framework
- Vue.js Team
- Comunidade Open Source

---

⭐ Se este projeto te ajudou, considere dar uma estrela no repositório!
