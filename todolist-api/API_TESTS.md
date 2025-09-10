# Teste da API TodoList com JWT

## 1. Registrar um novo usuário
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Teste Usuario",
    "email": "teste@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'

## 2. Fazer login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "teste@example.com",
    "password": "password123"
  }'

## 3. Obter informações do usuário autenticado (substitua SEU_TOKEN_AQUI pelo token recebido no login)
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"

## 4. Listar categorias (protegido)
curl -X GET http://localhost:8000/api/categories \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"

## 5. Criar uma categoria (protegido)
curl -X POST http://localhost:8000/api/categories \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Trabalho"
  }'

## 6. Fazer logout
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
