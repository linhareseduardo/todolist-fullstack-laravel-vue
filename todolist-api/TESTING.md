# 🧪 Guia de Execução de Testes - TodoList API

## 📋 **Pré-requisitos**

### ✅ **Opção 1: PHP Instalado Localmente**
- PHP 8.2+ instalado e no PATH do sistema
- Composer instalado
- Extensões necessárias: `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`

### ✅ **Opção 2: Docker (Recomendado)**
```bash
# Usar Laravel Sail
./vendor/bin/sail up -d
./vendor/bin/sail artisan test
```

### ✅ **Opção 3: XAMPP/WAMP/Laragon**
- Instalar qualquer um desses ambientes
- Adicionar PHP ao PATH do Windows

---

## 🚀 **Como Executar os Testes**

### **1. Scripts Automáticos (Windows)**
```batch
# Executar todos os testes
./run-tests.bat

# Executar testes específicos
./test-specific.bat auth      # Testes de autenticação
./test-specific.bat task      # Testes de tarefas  
./test-specific.bat category  # Testes de categorias
./test-specific.bat endpoints # Testes utilitários
./test-specific.bat all       # Todos os testes
```

### **2. Comandos Manuais (se PHP estiver no PATH)**
```bash
# Todos os testes com formato limpo
php vendor/bin/phpunit --testdox

# Todos os testes com cores
php vendor/bin/phpunit --testdox --colors=always

# Teste específico por arquivo
php vendor/bin/phpunit tests/Feature/AuthTest.php --testdox
php vendor/bin/phpunit tests/Feature/TaskTest.php --testdox
php vendor/bin/phpunit tests/Feature/CategoryTest.php --testdox
php vendor/bin/phpunit tests/Feature/TestEndpointsTest.php --testdox

# Teste específico por método
php vendor/bin/phpunit --filter test_user_can_login
php vendor/bin/phpunit --filter test_tasks_can_be_filtered_by_priority

# Testes com cobertura detalhada
php vendor/bin/phpunit --coverage-text
```

### **3. Via Composer (se disponível)**
```bash
composer test
```

### **4. Via Laravel Artisan (se PHP estiver no PATH)**
```bash
php artisan test
php artisan test --filter AuthTest
php artisan test --coverage
```

---

## 📊 **Testes Disponíveis**

### 🔐 **AuthTest.php** (9 testes)
- ✅ `test_user_can_register`
- ✅ `test_user_cannot_register_with_invalid_data`
- ✅ `test_user_cannot_register_with_existing_email`
- ✅ `test_user_can_login`
- ✅ `test_user_cannot_login_with_invalid_credentials`
- ✅ `test_user_cannot_login_with_missing_credentials`
- ✅ `test_authenticated_user_can_get_user_info`
- ✅ `test_authenticated_user_can_logout`
- ✅ `test_authenticated_user_can_refresh_token`

### 📂 **CategoryTest.php** (CRUD Completo)
- ✅ Listagem de categorias por usuário
- ✅ Criação de categorias
- ✅ Visualização de categoria específica
- ✅ Atualização de categorias
- ✅ Exclusão de categorias
- ✅ Isolamento multi-usuário

### ✅ **TaskTest.php** (CRUD + Filtros)
- ✅ Operações CRUD completas
- ✅ Filtros por status (pending, in_progress, done)
- ✅ Filtros por prioridade (high, medium, low)
- ✅ Filtros por categoria
- ✅ Busca por texto (título/descrição)
- ✅ Filtros combinados
- ✅ Atualização de status
- ✅ Isolamento multi-usuário

### 🧪 **TestEndpointsTest.php** (6 testes)
- ✅ `test_simple_api_test_endpoint`
- ✅ `test_jwt_test_endpoint`
- ✅ `test_jwt_test_endpoint_without_users`
- ✅ `test_authenticated_user_can_get_user_info_via_middleware`
- ✅ `test_unauthenticated_user_cannot_get_user_info_via_middleware`
- ✅ `test_timezone_test_endpoint`

---

## 🔧 **Resolução de Problemas**

### ❌ **Erro: 'php' não é reconhecido**
**Soluções:**
1. **Instalar PHP**: Baixar do [php.net](https://www.php.net/downloads)
2. **Usar XAMPP**: Baixar do [apachefriends.org](https://www.apachefriends.org)
3. **Usar Laragon**: Baixar do [laragon.org](https://laragon.org)
4. **Adicionar ao PATH**: 
   - XAMPP: `C:\xampp\php`
   - WAMP: `C:\wamp64\bin\php\php8.x`
   - Laragon: `C:\laragon\bin\php\php8.x`

### ❌ **Erro de banco de dados**
```bash
# Configurar banco de teste
cp .env .env.testing
# Editar .env.testing com configurações de teste
php artisan migrate --env=testing
```

### ❌ **Erro de dependências**
```bash
composer install
php artisan key:generate
php artisan jwt:secret
```

---

## 📈 **Interpretando Resultados**

### ✅ **Teste Passou**
```
✓ User can register
✓ User can login
```

### ❌ **Teste Falhou**
```
✗ User cannot login with invalid credentials
  Failed asserting that 200 matches expected 401
```

### 📊 **Cobertura**
```
Code Coverage Report:
  Classes: 85.71% (6/7)
  Methods: 92.31% (12/13)
  Lines:   89.47% (85/95)
```

---

## 🎯 **Comandos Úteis**

```bash
# Executar apenas testes que falharam
php vendor/bin/phpunit --stop-on-failure

# Executar com mais verbosidade
php vendor/bin/phpunit --verbose

# Executar testes em paralelo (se suportado)
php vendor/bin/phpunit --parallel

# Listar todos os testes sem executar
php vendor/bin/phpunit --list-tests
```
