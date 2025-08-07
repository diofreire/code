
# 📘 API RESTful de Usuários - CodeIgniter 3

Este projeto é uma API RESTful desenvolvida com **PHP (CodeIgniter 3)** para gerenciar usuários. Ela permite **criar, atualizar, deletar e listar usuários**, com respostas em formato **JSON** e autenticação via **JWT**.

---

## 🎯 Objetivo

Construir uma API RESTful que:

- ✅ Liste todos os usuários (`GET`)
- ✅ Crie um novo usuário (`POST`)
- ✅ Atualize um usuário existente (`PUT`)
- ✅ Delete um usuário (`DELETE`)

Com os seguintes cuidados:

- 🔒 Autenticação via JWT
- ✅ Validação dos dados de entrada
- ⚠️ Tratamento de erros padronizado

---

## 🛠️ Tecnologias Utilizadas

- **PHP >= 7.0**
- **CodeIgniter 3**
- **REST_Controller** (para suporte à arquitetura REST)
- **php-jwt** (para autenticação via JSON Web Token)
- **MySQL** (como banco de dados)

---

## 🚀 Antes de Começar

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/seu-repositorio.git
cd seu-repositorio
```

### 2. Configure o banco de dados

Crie o banco e a tabela com o seguinte script SQL:

```sql
-- Banco de dados: `code`
CREATE DATABASE code CHARACTER SET utf8 COLLATE utf8_general_ci;
USE code;

-- Tabela `users`
CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) COLLATE utf8mb4_0900_as_ci NOT NULL,
  `email` VARCHAR(100) COLLATE utf8mb4_0900_as_ci NOT NULL,
  `senha` VARCHAR(255) COLLATE utf8mb4_0900_as_ci NOT NULL,
  `admin` TINYINT(1) NOT NULL DEFAULT '0',
  `criado_em` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_ci;

-- Dados iniciais
INSERT INTO `users` (`id`, `nome`, `email`, `senha`, `criado_em`, `admin`) VALUES
(1, 'Administrador', 'admin@admin.com', '$2y$10$dwfeImY.TKyTUXu9TQLZne.wwvh2k6jtQo0.6TCmhDptCPlm3MRz6', '2025-08-07 00:03:39', 1);
```

### 3. Configure o CodeIgniter

- Edite o arquivo `application/config/database.php` com as credenciais do seu MySQL.
- Configure o `base_url` em `application/config/config.php`.

### 4. Certifique-se de que as dependências estão instaladas

- A biblioteca `REST_Controller` deve estar disponível em `application/libraries/REST_Controller.php`
- O `php-jwt` pode ser instalado via Composer ou incluído manualmente.

---

## 🧪 Endpoints da API

| Método | Rota             | Descrição                  |
|--------|------------------|----------------------------|
| GET    | /users           | Lista todos os usuários    |
| POST   | /users           | Cria um novo usuário       |
| PUT    | /users/{id}      | Atualiza um usuário        |
| DELETE | /users/{id}      | Deleta um usuário          |

> 🔐 Todos os endpoints exigem autenticação com JWT (exceto login).

---

## 🔐 Autenticação com JWT

### Login

Faça uma requisição `POST` para `/auth/login` com os seguintes dados:

```json
{
  "email": "admin@admin.com",
  "senha": "admin"
}
```

A resposta conterá um token:

```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJh..."
}
```

Use o token no header das demais requisições:

```
Authorization: Bearer <seu_token_aqui>
```

---

## ✅ Validação e Erros

A API realiza:

- Verificação de campos obrigatórios
- Validação de formato (ex: e-mail)
- Mensagens claras em caso de erro

Formato de erro:

```json
{
  "status": false,
  "message": "Mensagem de erro descritiva"
}
```

---

## 🤝 Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para abrir uma _issue_ ou enviar um _pull request_ com melhorias ou correções.

---

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais informações.

---

Feito com 💙 usando PHP e CodeIgniter 3.
