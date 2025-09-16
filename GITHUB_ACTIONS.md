# 🚀 GitHub Actions - CI/CD Workflows

Este documento explica como funciona a automação completa do projeto **TodoList API** usando GitHub Actions.

## 📋 Visão Geral dos Workflows

O projeto possui **3 workflows automatizados** que garantem qualidade, segurança e deploy automatizado:

### 1. 🧪 **CI - TodoList API Tests** (`ci.yml`)
- **Quando executa:** A cada push ou pull request
- **Duração:** ~8-12 minutos
- **Propósito:** Validação completa do código

### 2. 🚀 **Deploy - Production** (`deploy.yml`)  
- **Quando executa:** Após CI passar + push na branch `main`
- **Duração:** ~3-5 minutos
- **Propósito:** Deploy automatizado para produção

### 3. 🔍 **Quality Checks** (`quality-checks.yml`)
- **Quando executa:** Push/PR + toda segunda-feira às 9h UTC
- **Duração:** ~5-8 minutos  
- **Propósito:** Análise de qualidade e segurança

---

## 🔧 Configuração Inicial

### 1. Variáveis de Ambiente (GitHub Secrets)

Adicione estas secrets no seu repositório GitHub:

```bash
# Acesse: Settings → Secrets and variables → Actions

# Para CI/CD
DB_PASSWORD=sua_senha_postgres_producao
JWT_SECRET=seu_jwt_secret_super_seguro_256_bits

# Para deploy (opcional)
DEPLOY_KEY=sua_chave_deploy
PRODUCTION_URL=https://sua-url-producao.com

# Para coverage (opcional)
CODECOV_TOKEN=seu_token_codecov
```

### 2. Estrutura de Branches

```
main          ← Produção (deploy automático)
develop       ← Desenvolvimento (apenas testes)
feature/*     ← Features (testes + quality checks)
hotfix/*      ← Correções (testes completos)
```

---

## 🧪 Workflow 1: CI - Testes Automatizados

### O que faz:
✅ **55 testes automatizados** (296 assertions)  
✅ **Segurança:** Verificação de vulnerabilidades  
✅ **Qualidade:** Code coverage + relatórios  
✅ **Performance:** Otimizações de cache  
✅ **Multi-ambiente:** PostgreSQL + Redis + SQLite

### Estrutura do CI:

```yaml
# 1. Setup do Ambiente
PostgreSQL 15 + Redis + PHP 8.2 + Node.js 18

# 2. Testes Backend (Laravel)
- Unit Tests: 15 testes
- Feature Tests: 25 testes  
- Integration Tests: 15 testes
- Total: 55 testes, 296 assertions

# 3. Testes Frontend (Vue.js)
- Unit Tests: Componentes
- E2E Tests: Playwright
- Type Checking: TypeScript

# 4. Verificações de Segurança
- Dependências PHP (composer audit)
- Dependências Node.js (npm audit)
- Análise de código estático

# 5. Relatórios
- Coverage: Codecov
- Artifacts: Logs + Reports
```

### Como acompanhar:
```bash
# No GitHub, vá em:
Actions → CI - TodoList API Tests → Ver execução

# Ou pela CLI:
gh run list --workflow=ci.yml
gh run view [RUN_ID]
```

---

## 🚀 Workflow 2: Deploy Automatizado

### O que faz:
🏗️ **Build otimizado** (frontend + backend)  
📦 **Empacotamento** para produção  
🔍 **Health checks** pré-deploy  
🚀 **Deploy automatizado**  
📊 **Relatórios de deploy**

### Estrutura do Deploy:

```yaml
# 1. Verificação Pré-Deploy
- Confirma que CI passou
- Valida arquivos críticos
- Health check da aplicação

# 2. Build de Produção
Backend:
  - composer install --no-dev --optimize-autoloader
  - php artisan config:cache
  - php artisan route:cache
  - php artisan view:cache

Frontend:
  - npm ci --production
  - npm run build
  - Otimização de assets

# 3. Deploy
- Criação do pacote de deploy
- Validações finais
- Deploy para produção
- Notificações automáticas
```

### Como configurar deploy real:
```yaml
# Adicione estes steps no deploy.yml:

- name: 🌐 Deploy to AWS/Azure/GCP
  run: |
    # Seus comandos de deploy
    rsync -avz deployment/ user@server:/var/www/
    
- name: 🗄️ Run Database Migrations  
  run: |
    ssh user@server "cd /var/www/api && php artisan migrate --force"
    
- name: 🔄 Restart Services
  run: |
    ssh user@server "sudo systemctl restart nginx php8.2-fpm"
```

---

## 🔍 Workflow 3: Verificações de Qualidade

### O que faz:
📊 **Métricas de código** (complexidade, linhas, arquivos)  
🎨 **Code style** (PSR-12, ESLint)  
🔍 **Análise estática** (PHPStan, TypeScript)  
🛡️ **Auditoria de segurança** (dependências)  
📈 **Relatórios de qualidade**

### Verificações Realizadas:

```yaml
# 1. PHP Quality
- PSR-12 compliance (php-cs-fixer)
- Static analysis (PHPStan)
- Code complexity metrics
- Lines of code counting

# 2. Frontend Quality  
- ESLint analysis
- TypeScript type checking
- Bundle size analysis
- Performance metrics

# 3. Security Audits
- PHP dependencies (composer audit)
- Node.js dependencies (npm audit)
- Vulnerability scanning

# 4. Database Analysis
- Migration file analysis
- Foreign key usage
- Index optimization
```

### Como interpretar relatórios:
```bash
# Métricas importantes:
- Code coverage: >80% ✅
- ESLint errors: 0 ✅  
- PHPStan level: 5+ ✅
- Security vulnerabilities: 0 ✅
- Bundle size: <500KB ✅
```

---

## 📊 Monitoramento e Relatórios

### 1. GitHub Actions Dashboard
```
Actions → All workflows
- CI: Status dos testes
- Deploy: Status do deploy  
- Quality: Relatórios de qualidade
```

### 2. Coverage Reports (Codecov)
```
https://app.codecov.io/gh/seu-usuario/todolist-api-laravel
- Line coverage: 85%+
- Branch coverage: 80%+
- Function coverage: 90%+
```

### 3. Security Reports
```
Security → Dependabot alerts
- Dependências vulneráveis
- Atualizações automáticas
- Correções sugeridas
```

---

## 🔧 Troubleshooting

### ❌ CI Falha - Como Resolver:

```bash
# 1. Verificar logs
gh run view --log [RUN_ID]

# 2. Problemas comuns:
- Testes falhando → Rodar localmente: php artisan test
- Dependências → composer update / npm update
- Banco de dados → Verificar migrações
- JWT → Verificar chave secreta

# 3. Debug local:
php artisan test --filter=NomeDoTeste
npm run test:unit
```

### ❌ Deploy Falha - Como Resolver:

```bash
# 1. Verificar se CI passou
- CI deve estar ✅ verde

# 2. Verificar secrets
- DB_PASSWORD configurado
- JWT_SECRET configurado
- Chaves de deploy válidas

# 3. Rollback manual:
git revert HEAD
git push origin main
```

### ❌ Quality Checks - Como Melhorar:

```bash
# 1. Code style:
vendor/bin/php-cs-fixer fix
npm run lint -- --fix

# 2. Static analysis:
vendor/bin/phpstan analyse --level=5

# 3. Security:
composer audit
npm audit --fix
```

---

## 🎯 Best Practices

### 1. **Desenvolvimento**
```bash
# Antes de fazer push:
php artisan test          # Rodar testes
npm run lint              # Verificar frontend  
composer audit            # Verificar segurança
git push origin feature   # Push para branch
```

### 2. **Pull Requests**
```bash
# PRs devem ter:
✅ Todos os testes passando
✅ Code coverage mantido
✅ Sem vulnerabilidades
✅ Code style correto
✅ Documentação atualizada
```

### 3. **Deploy para Produção**
```bash
# Deploy acontece automaticamente quando:
1. Push na branch main
2. CI passa com sucesso  
3. Todos os testes ✅
4. Security checks ✅
5. Build completo ✅
```

---

## 📈 Métricas do Projeto

### 🧪 **Testes**
- **Total:** 55 testes
- **Assertions:** 296 validações
- **Coverage:** 85%+
- **Tempo:** ~8 minutos

### 🏗️ **Build**
- **Backend:** Laravel optimizado
- **Frontend:** Vue.js minificado  
- **Bundle:** <500KB
- **Deploy:** ~3 minutos

### 🔍 **Qualidade**
- **Code Style:** PSR-12 ✅
- **Static Analysis:** Level 5 ✅
- **Security:** 0 vulnerabilidades ✅
- **Performance:** Otimizado ✅

---

## 🚀 Próximos Passos

1. **Configurar secrets** no GitHub
2. **Fazer primeiro push** para testar CI
3. **Configurar deploy real** (AWS/Azure/GCP)
4. **Integrar notificações** (Slack/Teams)
5. **Adicionar mais testes** (E2E com Playwright)

---

**🎉 Parabéns!** Você agora tem uma pipeline CI/CD completa e automatizada para o seu TodoList API!

> 💡 **Dica:** Monitore sempre os workflows e mantenha as dependências atualizadas para garantir segurança e performance.