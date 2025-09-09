# 📋 TodoList Full-Stack Application

Uma aplicação completa de gerenciamento de tarefas desenvolvida com Laravel + Vue.js + TypeScript.

## 🚀 Tecnologias Utilizadas

### Backend
- **Laravel 12** - Framework PHP
- **PostgreSQL 15** - Banco de dados
- **Docker** - Containerização (Laravel Sail)
- **Insomnia** - Testes da API

### Frontend
- **Vue.js 3** - Framework JavaScript
- **TypeScript** - Tipagem estática
- **Pinia** - Gerenciamento de estado
- **Axios** - Cliente HTTP
- **Vite** - Build tool e dev server

### Ferramentas de Desenvolvimento
- **pgAdmin** - Interface web para PostgreSQL
- **ESLint + Prettier** - Qualidade de código
- **Docker Compose** - Orquestração de containers

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
- Docker Desktop
- Node.js 18+
- Git

### 1. Clone o repositório
```bash
git clone [URL_DO_SEU_REPOSITORIO]
cd todolist-api-laravel
```

### 2. Configuração do Backend (Laravel)
```bash
cd todolist-api
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
```

### 3. Configuração do Frontend (Vue.js)
```bash
cd ../todolist-frontend
npm install
npm run dev
```

## 🌐 URLs da Aplicação

- **Frontend Vue.js**: http://localhost:5174
- **Backend Laravel API**: http://localhost:8000/api
- **pgAdmin**: http://localhost:8080
  - Email: `admin@todolist.com`
  - Senha: `admin123`

## 📊 Funcionalidades

### ✅ Gestão de Categorias
- Criar, editar, excluir categorias
- Visualizar quantidade de tarefas por categoria
- Interface responsiva com cards

### ✅ Gestão de Tarefas
- CRUD completo de tarefas
- Filtros por:
  - Categoria
  - Status (Pendente, Em Progresso, Concluída)
  - Prioridade (Baixa, Média, Alta)
- Data de vencimento
- Indicadores visuais de status
- Alertas para tarefas vencidas

### ✅ Interface do Usuário
- Design moderno e responsivo
- Navegação por abas
- Animações suaves
- Feedback visual para ações
- Estados de loading e erro

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

### Categorias
- `GET /api/categories` - Listar categorias
- `POST /api/categories` - Criar categoria
- `PUT /api/categories/{id}` - Atualizar categoria
- `DELETE /api/categories/{id}` - Excluir categoria

### Tarefas
- `GET /api/tasks` - Listar tarefas
- `POST /api/tasks` - Criar tarefa
- `PUT /api/tasks/{id}` - Atualizar tarefa
- `DELETE /api/tasks/{id}` - Excluir tarefa

## 🧪 Testes

### Backend
```bash
cd todolist-api
./vendor/bin/sail test
```

### Frontend
```bash
cd todolist-frontend
npm run test:unit
npm run test:e2e
```

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

## 👨‍💻 Desenvolvedor

**Carlos Eduardo**
- GitHub: [@seuusuario](https://github.com/seuusuario)
- LinkedIn: [Seu LinkedIn](https://linkedin.com/in/seulinkedin)

## 🙏 Agradecimentos

- Laravel Framework
- Vue.js Team
- Comunidade Open Source

---

⭐ Se este projeto te ajudou, considere dar uma estrela no repositório!
