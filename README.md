<div align="center">
  <h1>Cabeleleila Leila - Sistema de Agendamento</h1>
  <p>Sistema completo para gestão de agendamentos online, permitindo que clientes marquem serviços e a proprietária (Leila) gerencie o negócio com visão operacional e gerencial.</p>
</div>

<div align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Angular-DD0031?style=for-the-badge&logo=angular&logoColor=white" alt="Angular">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker">
</div>

---

## 📋 Sobre o Sistema

O projeto **Cabeleleila Leila** é um sistema web personalizado que resolve o problema de agendamentos manuais, oferecendo uma interface amigável para clientes e um painel de controle robusto para a Leila. O objetivo é modernizar e otimizar a gestão do salão, aprimorando a experiência do cliente e fornecendo ferramentas para uma visão clara do desempenho do negócio.

### Diferenciais
O sistema conta com **lógica de negócio avançada** que vai além de um simples agendador:
- ✅ **Validação automática** de horários comerciais, intervalo de almoço e conflitos de agendamento
- ✅ **Sugestão inteligente** de agrupamento de serviços na mesma semana (ASK_UNIFY)
- ✅ **Recalcular sequencial** de horários quando agendamentos são alterados
- ✅ **Dashboard gerencial** com análise de desempenho semanal em tempo real
- ✅ **Regras de negócio configuráveis** (expediente, almoço, duração dos serviços)



## ✨ Funcionalidades Principais

### Para Clientes
- **Agendamento Online:** Clientes podem agendar um ou mais serviços de forma prática.
- **Alteração de Agendamento:**
  - Permitido alterar agendamentos pelo sistema com até **2 dias de antecedência**.
  - Com menos de 2 dias, a alteração deve ser feita por telefone.
- **Histórico de Agendamentos:** Visualização do histórico de serviços realizados, com acesso aos detalhes de cada agendamento.
- **Sugestão Inteligente:** O sistema identifica se a cliente possui mais de um agendamento na mesma semana e sugere unificá-los em uma única data.

### Para a Gestão (Leila)
- **Painel Operacional:** Acesso completo para alterar qualquer agendamento, confirmar horários e gerenciar o status de cada serviço (`Pendente`, `Finalizado`, `Cancelado`).
- **Painel Gerencial:** Dashboard com métricas e um relatório de desempenho semanal para auxiliar na tomada de decisões estratégicas (receita semanal, serviços concluídos, taxa de cancelamento, etc).

---

## 🧠 Inteligência de Negócio

O sistema implementa validações e regras inteligentes para otimizar o funcionamento do salão:

### ⏰ Gestão de Horários
- **Validação de Expediente:** O sistema verifica automaticamente se o horário solicitado está dentro do horário de funcionamento configurado (ex: 09:00 às 18:00).
- **Intervalo de Almoço:** Bloqueia agendamentos durante o intervalo de almoço (ex: 12:00 às 13:00), garantindo que serviços não sejam marcados neste período.
- **Duração Sequencial:** Cada serviço calcula automaticamente seu horário final baseado em sua duração, evitando sobreposição com serviços subsequentes.

### 🔄 Resolução Inteligente de Conflitos
- **Detecção de Conflitos:** Impede agendamento de serviços que conflitariam com outros já marcados no mesmo horário.
- **Sugestão de Agrupamento:** Se uma cliente possui um agendamento na mesma semana, o sistema detecta isso automaticamente e sugere agrupar os novos serviços na mesma data, **economizando tempo e melhorando a experiência**.
- **Validação de Semana:** Identifica todos os agendamentos da cliente na mesma semana para análise anterior à confirmação.

### 📅 Flexibilidade de Alterações
- Clientes podem alterar agendamentos com até **2 dias de antecedência** via sistema.
- Alterações com menos de 2 dias devem ser feitas por telefone (proteção do tempo de preparo).
- Admin tem acesso total para alterar qualquer agendamento com **recalcular automático dos horários dos serviços**.

### 📊 Rastreamento Operacional
- **Gestão de Status:** Cada serviço pode ter status independente (`Pendente`, `Finalizado`, `Cancelado`).
- **Histórico Completo:** Todo agendamento mantém histórico de alterações para auditoria.
- **Confirmação de Presença:** Admin pode confirmar presença do cliente ou cancelar agendamento.

---

## 🏗️ Tecnologias & Arquitetura

O projeto foi construído utilizando tecnologias modernas, seguindo o padrão **API First** e a arquitetura **MVC (Model-View-Controller)**.

*   **`Backend`**: API RESTful desenvolvida em **Laravel (PHP)**, utilizando Laravel Sanctum para autenticação.
*   **`Frontend`**: Single Page Application (SPA) interativa construída com **Angular (TypeScript)**.
*   **`Banco de Dados`**: **SQL** (MySQL) para persistência e gerenciamento dos dados.
*   **`Containerização`**: Ambiente de desenvolvimento padronizado com **Docker**.

---

## 🚀 Como Rodar o Projeto

Para subir todo o ecossistema (PHP, MySQL, Nginx e Node) de forma automatizada:

**1. Clone o repositório:**

```bash
git clone https://github.com/gusteugenio/salao-beleza-leila
cd salao-beleza-leila
```

**2. Crie o arquivo .env na raiz do projeto:**

```
touch .env
```

Abra o `.env` e configure as variáveis do banco de dados:

| Variável              | Descrição                    |
|-----------------------|------------------------------|
| `MYSQL_DATABASE`      | Nome do banco de dados       |
| `MYSQL_ROOT_PASSWORD` | Senha do root do MySQL       |
| `MYSQL_USER`          | Usuário do banco             |
| `MYSQL_PASSWORD`      | Senha do usuário             |

**3. Copie o `.env` do Laravel dentro do diretório `backend`:**

```bash
cd backend
cp .env.example .env
```

**4. Configure as variáveis de conexão com o banco:**

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=nome_do_banco_definido_no_.env_da_raiz
DB_USERNAME=usuario_definido_no_.env_da_raiz
DB_PASSWORD=senha_definida_no_.env_da_raiz
```

> ⚠️ **Atenção:** `DB_HOST=mysql` porque o nome do serviço no `docker-compose.yml` é `mysql`.

**5. Execute o script de setup:**

```bash
# Sube os containers
bash setup.sh
```

Após a execução, os serviços estarão disponíveis em:

- 🔗 **API Laravel** → `http://localhost:8000`
- 🌐 **Front-end Angular** → `http://localhost:4200`

---

## 🔐 Credenciais de Acesso (Padrão)

O sistema vem com dados de teste já criados pelas seeds. Use as seguintes credenciais para acessar:

### Admin/Gestão
```
Email: admin@salao.com
Senha: password123
```

### Clientes de Teste
```
Email: ana@email.com
Senha: password123
```
(Ou qualquer um dos clientes: beatriz@email.com, carolina@email.com, diana@email.com, eduarda@email.com)

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
- [x] Desenvolver telas de autenticação (login/registro).
- [x] Desenvolver telas da área do cliente (agendar, histórico).
- [x] Desenvolver telas da área de gestão (dashboard, lista de agendamentos).

---

## 📸 Imagens do Sistema

As screenshots e documentação visual do sistema podem ser encontradas em [`docs/images/`](./docs/images):

### Autenticação
- `login.png` - Tela de login
- `cadastro.png` - Tela de registro de usuários

### Interface do Cliente
- `lista-servicos.png` - Listagem de serviços disponíveis
- `escolha-horarios.png` - Seleção de data e horário
- `confirmacao-agendamento.png` - Confirmação do agendamento
- `lista-agendamentos-cliente.png` - Histórico de agendamentos do cliente
- `editar-agendamento.png` - Alteração de agendamento
- `detalhes-agendamento.png` - Detalhes completos do agendamento

### Dashboard Admin/Gestão
- `dashboard.png` - Visão geral e métricas gerenciais
- `lista-agendamentos-admin.png` - Lista completa de agendamentos
- `detalhes-agendamento-admin.png` - Detalhes de agendamento
- `editar-agendamento-servicos.png` - Editador de serviços do agendamento

### Landing Page
- `inicio.png` - Página inicial
- `sobre-nos.png` - Seção "Sobre Nós"
- `carrossel-servicos.png` - Carrossel de serviços destacados
- `avaliacoes.png` - Avaliações de clientes
- `contat.png` - Informações de contato

---

## �📬 Contato

<div align="center">
  <p>Desenvolvido por <strong>Gustavo Eugênio</strong></p>
  <a href="mailto:gustavoeugenio297@gmail.com">
    <img src="https://img.shields.io/badge/Gmail-D14836?style=for-the-badge&logo=gmail&logoColor=white" />
  </a>
  <a href="https://www.linkedin.com/in/gusteugenio/">
    <img src="https://img.shields.io/badge/LinkedIn-0077B5?style=for-the-badge&logo=linkedin&logoColor=white" />
  </a>
</div>
