# 🚀 Pull Request - TodoList API

## 📋 Resumo
<!-- Descreva brevemente as mudanças deste PR -->

## 🔄 Tipo de Mudança
- [ ] 🐛 Bug fix (correção que resolve um problema)
- [ ] ✨ Nova funcionalidade (mudança que adiciona funcionalidade)  
- [ ] 💥 Breaking change (correção ou funcionalidade que causaria funcionalidade existente não funcionar como esperado)
- [ ] 📚 Documentação (mudanças apenas na documentação)
- [ ] 🔧 Manutenção (refatoração, otimização, limpeza de código)
- [ ] 🧪 Testes (adição ou correção de testes)
- [ ] 🚀 CI/CD (mudanças nos workflows de automação)

## 🔗 Issue/Feature Relacionada
<!-- Se aplicável, referencie a issue: Fixes #123, Closes #456 -->

## 🎯 Mudanças Realizadas
<!-- Liste as principais mudanças -->
- [ ] Mudança 1
- [ ] Mudança 2  
- [ ] Mudança 3

## 🏗️ Impacto Técnico

### Backend (Laravel API)
- [ ] Novos endpoints criados
- [ ] Mudanças no banco de dados
- [ ] Migrações necessárias
- [ ] Novas validações
- [ ] Mudanças na autenticação

### Frontend (Vue.js)  
- [ ] Novos componentes
- [ ] Mudanças no roteamento
- [ ] Novos stores/services
- [ ] Mudanças na UI

### Database
- [ ] Novas migrações
- [ ] Seeders atualizados
- [ ] Índices adicionados
- [ ] Constraints modificados

## 🧪 Testes
- [ ] Testes unitários adicionados/atualizados
- [ ] Testes de integração adicionados/atualizados
- [ ] Testes E2E adicionados/atualizados
- [ ] Todos os testes passam localmente
- [ ] Coverage mantido/melhorado

### Comandos para Testar:
```bash
# Backend
cd todolist-api
php artisan test

# Frontend  
cd todolist-frontend
npm run test:unit
npm run test:e2e
```

## 📸 Screenshots
<!-- Se aplicável, adicione screenshots das mudanças -->

## 🔍 Como Testar
1. Faça checkout desta branch: `git checkout feature/sua-branch`
2. Execute: `composer install` e `npm install`
3. Execute: `php artisan migrate` (se houver migrações)
4. Execute: `php artisan test`
5. Teste manualmente: [descreva os passos]

## 📋 Checklist
### Desenvolvimento
- [ ] O código segue os padrões do projeto (PSR-12, ESLint)
- [ ] Eu fiz uma auto-revisão do meu código
- [ ] Eu comentei meu código, principalmente em áreas difíceis de entender
- [ ] Eu fiz mudanças correspondentes na documentação
- [ ] Minhas mudanças não geram novos warnings
- [ ] Eu adicionei testes que provam que minha correção/funcionalidade funciona
- [ ] Testes unitários novos e existentes passam localmente com minhas mudanças

### CI/CD
- [ ] CI pipeline passa ✅
- [ ] Code coverage mantido (>80%)
- [ ] Security checks passam
- [ ] No breaking changes sem versioning

### Database
- [ ] Migrações são reversíveis (`down()` implementado)  
- [ ] Seeders atualizados se necessário
- [ ] Backup de dados considerado (se aplicável)

## 🚦 Status dos Workflows
- [ ] ✅ CI - Tests: Passando
- [ ] ✅ Quality Checks: Passando  
- [ ] ✅ Security Scan: Passando
- [ ] ✅ Build: Successful

## 🔐 Considerações de Segurança
- [ ] Não há dados sensíveis expostos
- [ ] Validações de input implementadas
- [ ] Autorização verificada
- [ ] JWT tokens manejados corretamente

## 📚 Documentação
- [ ] README atualizado (se necessário)
- [ ] API documentation atualizada
- [ ] Comentários inline adicionados
- [ ] Insomnia collection atualizada

## 🎁 Breaking Changes
<!-- Se há breaking changes, descreva-os aqui -->
- [ ] Não há breaking changes
- [ ] Breaking changes documentados abaixo:

## 📝 Notas Adicionais
<!-- Qualquer informação adicional que os revisores devem saber -->

---

## 👥 Para os Revisores
<!-- Instruções específicas para quem vai revisar -->

### 🔍 Focar em:
- [ ] Lógica de negócio está correta
- [ ] Segurança e autorização
- [ ] Performance (queries N+1, etc)
- [ ] Cobertura de testes adequada

### 🧪 Testei localmente:
- [ ] ✅ Backend API endpoints
- [ ] ✅ Frontend UI/UX
- [ ] ✅ Database migrations
- [ ] ✅ Autenticação JWT

---

**📊 Métricas deste PR:**
- Linhas adicionadas: `+XXX`
- Linhas removidas: `-XXX`  
- Arquivos modificados: `XX`
- Testes adicionados: `XX`