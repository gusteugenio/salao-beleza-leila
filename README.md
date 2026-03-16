<div align="center">
  <h1>Cabeleleila Leila Leila - Sistema de Agendamento</h1>
  <p>Sistema completo para gestão de agendamentos online, permitindo que clientes marquem serviços e a proprietária (Leila) gerencie o negócio com visão operacional e gerencial.</p>
</div>

<div align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Angular-DD0031?style=for-the-badge&logo=angular&logoColor=white" alt="Angular">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker">
  <img src="https://img.shields.io/badge/PHPUnit-8A96D3?style=for-the-badge&logo=phpunit&logoColor=white" alt="PHPUnit">
</div>

---

## 📋 Sobre o Sistema


O projeto **Cabeleleila Leila Leila** é um sistema web personalizado que resolve o problema de agendamentos manuais, oferecendo uma interface amigável para clientes e um painel de controle robusto para a Leila. O objetivo é modernizar e otimizar a gestão do salão, aprimorando a experiência do cliente e fornecendo ferramentas para uma visão clara do desempenho do negócio.

O sistema conta com regras de negócio inteligentes, como a sugestão de agrupamento de serviços na mesma semana e restrições de alteração baseadas na data do agendamento.

<br>

---

## ✨ Funcionalidades Principais

### Para Clientes
- **Agendamento Online:** Clientes podem agendar um ou mais serviços de forma prática.
- **Alteração de Agendamento:**
  - Permitido alterar agendamentos pelo sistema com até **2 dias de antecedência**.
  - Com menos de 2 dias, a alteração deve ser feita por telefone.
- **Histórico de Agendamentos:** Visualização do histórico de serviços realizados, com acesso aos detalhes de cada agendamento.
- **Sugestão Inteligente:** O sistema identifica se a cliente possui mais de um agendamento na mesma semana e sugere unificá-los em uma única data.

### Para a Gestão (Leila)
- **Painel Operacional:** Acesso para alterar qualquer agendamento, confirmar horários e gerenciar o status de cada serviço (`Pendente`, `Agendado`, `Finalizado`, `Cancelado`.).
- **Painel Gerencial:** Dashboard com métricas e um relatório de desempenho semanal para auxiliar na tomada de decisões estratégicas.

---

## 🏗️ Tecnologias & Arquitetura

O projeto foi construído utilizando tecnologias modernas, seguindo o padrão **API First** e a arquitetura **MVC (Model-View-Controller)**.

*   **`Backend`**: API RESTful desenvolvida em **Laravel (PHP)**, utilizando Laravel Sanctum para autenticação.
*   **`Frontend`**: Single Page Application (SPA) interativa construída com **Angular (TypeScript)**.
*   **`Banco de Dados`**: **SQL** (MySQL) para persistência e gerenciamento dos dados.
*   **`Containerização`**: Ambiente de desenvolvimento padronizado com **Docker**.

---

## 🚀 Como Rodar o Projeto

### 🐳 Docker

Para subir todo o ecossistema (PHP, MySQL, Nginx e Node) de forma automatizada:

```bash
# Clone o repositório
git clone https://github.com/gusteugenio/salao-beleza-leila
cd salao-beleza-leila

# Crie o arquivo .env na raiz do projeto
cp .env.example .env
```

Abra o `.env` e configure as variáveis do banco de dados:

| Variável              | Descrição                    |
|-----------------------|------------------------------|
| `MYSQL_DATABASE`      | Nome do banco de dados       |
| `MYSQL_ROOT_PASSWORD` | Senha do root do MySQL       |
| `MYSQL_USER`          | Usuário do banco             |
| `MYSQL_PASSWORD`      | Senha do usuário             |

```bash
# Suba os containers
docker compose up -d --build
```

Após a execução, os serviços estarão disponíveis em:

- 🔗 **API Laravel** → `http://localhost:8000`
- 🌐 **Front-end Angular** → `http://localhost:4200`

---

### ⚙️ Configuração do Laravel

**1. Copie o `.env` do Laravel dentro do diretório `backend`:**

```bash
cd backend
cp .env.example .env
```

**2. Configure as variáveis de conexão com o banco:**

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=nome_do_banco_definido_no_.env_da_raiz
DB_USERNAME=usuario_definido_no_.env_da_raiz
DB_PASSWORD=senha_definida_no_.env_da_raiz
```

> ⚠️ **Atenção:** `DB_HOST=mysql` porque o nome do serviço no `docker-compose.yml` é `mysql`.

**3. Instale o Sanctum:**

```bash
docker compose exec php composer require laravel/sanctum
docker compose exec php php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider"
```

**4. Rode as migrations e seeds dentro do container PHP:**

```bash
docker compose exec php php artisan migrate
```

```bash
docker compose exec php php artisan db:seed
```

Isso criará todas as tabelas necessárias no MySQL do container.

---

## ✅ Checklist de Desenvolvimento

Este é um checklist para guiar o desenvolvimento do projeto.

#### 🐳 Ambiente e Configuração
- [x] Configurar ambiente Docker com containers para Nginx, PHP, MySQL e Node.js.
- [x] Inicializar projeto Laravel e configurar `.env`.
- [x] Inicializar projeto Angular e configurar proxy para a API.

#### 🔧 Back-end (API com Laravel)
- [x] Modelar e criar migrations para o banco de dados.
- [x] Implementar autenticação de usuários (clientes e admin).
- [x] Implementar endpoints CRUD para serviços.
- [x] Implementar endpoints de agendamento com regras de negócio.
- [x] Implementar endpoints de gestão (status, alteração, etc.).
- [x] Implementar endpoints para o dashboard gerencial.

#### 🎨 Front-end (com Angular)
- [x] Criar layout principal e navegação.
- [ ] Desenvolver telas de autenticação (login/registro).
- [ ] Desenvolver telas da área do cliente (agendar, histórico).
- [ ] Desenvolver telas da área de gestão (dashboard, lista de agendamentos).

---

## 📬 Contato

<div align="center">
  <p>Desenvolvido por <strong>Gustavo Eugênio</strong></p>
  <a href="mailto:gustavoeugenio297@gmail.com">
    <img src="https://img.shields.io/badge/Gmail-D14836?style=for-the-badge&logo=gmail&logoColor=white" />
  </a>
  <a href="https://www.linkedin.com/in/gusteugenio/">
    <img src="https://img.shields.io/badge/LinkedIn-0077B5?style=for-the-badge&logo=linkedin&logoColor=white" />
  </a>
</div>
