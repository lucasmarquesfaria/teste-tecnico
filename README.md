# Sistema de Gerenciamento de Ordens de Serviço 🛠️

### Principais funcionalidades

✅ Técnicos podem criar e gerenciar ordens de serviço  
✅ Clientes podem acompanhar suas ordens  
✅ Notificações automáticas por e-mail quando serviços são concluídos  
✅ Interface amigável e responsiva  
✅ Sistema de filas para processamento eficiente de e-mails  
✅ Dashboard analítico com gráficos e estatísticas em tempo real

## Como começar 🚀

### O que você vai precisar

* PHP 8.1 ou superior
* Composer
* MySQL ou SQLite
* Node.js e npm (para compilação de assets e Chart.js)
* Extensões básicas do PHP (PDO, Mbstring, etc)

### Instalação em 4 passos

1. **Clone o repositório e instale as dependências**
   ```bash
   git clone https://github.com/lucasmarquesfaria/teste-tecnico
   cd teste-tecnico
   composer install
   ```

2. **Configure o ambiente**
   ```bash
   # Crie o arquivo .env
   copy .env.example .env
   
   # Gere a chave da aplicação
   php artisan key:generate
   ```

3. **Configure o banco de dados no arquivo .env**
   ```
   # Para MySQL
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_DATABASE=sistema_os
   DB_USERNAME=root
   DB_PASSWORD=sua_senha
   
   # OU para SQLite (mais simples para testes)
   DB_CONNECTION=sqlite
   # Crie o arquivo database/database.sqlite
   ```

4. **Execute as migrações e seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Instale as dependências do Node.js e compile os assets**
   ```bash
   npm install
   npm run dev
   ```

## Como usar o sistema 🖥️

1. **Inicie o servidor**
   ```bash
   php artisan serve
   ```

2. **Inicie o processador de filas** (para envio de e-mails)
   ```bash
   php artisan queue:work
   ```

3. **Acesse o sistema**: http://localhost:8000

### Usuários para teste

👨‍💼 **Técnico**
- Email: tecnico@exemplo.com 
- Senha: password

👩‍💻 **Cliente**
- Email: cliente@exemplo.com
- Senha: password

### Fluxo básico de uso

1. Faça login como técnico
2. Crie uma nova ordem de serviço para um cliente
3. Atualize o status da OS para "concluída" quando o serviço estiver pronto
4. O sistema enviará automaticamente um e-mail para o cliente informando sobre a conclusão
5. (Apenas técnicos) Acesse o Dashboard Analítico para visualizar estatísticas e gráficos sobre as ordens de serviço

## Executando os testes ⚙️

```bash
# Windows
.\run-tests.bat

# Linux/macOS
php artisan test
```

## Como funciona por dentro? 🧩

O sistema utiliza:
- Eventos e listeners do Laravel para disparar notificações
- Sistema de filas para processamento assíncrono de e-mails
- Cache para evitar envio duplicado de notificações
- Políticas de acesso para controlar permissões

### Estrutura simplificada
- **Controllers**: Gerenciam requisições e respostas
- **Models**: Representam as entidades do sistema (Usuários, Ordens de serviço)
- **Events & Listeners**: Cuidam da notificação quando uma OS é concluída
- **Policies**: Controlam quem pode fazer o quê no sistema

## Dashboard Analítico 📊

### O que é?

O Dashboard Analítico fornece uma visão geral do desempenho do sistema de ordens de serviço através de gráficos e estatísticas. **Acesso exclusivo para a área técnica** (usuários com função de técnico).

### Principais funcionalidades

- 📈 Distribuição de status das ordens de serviço (gráfico de pizza)
- 📊 Ordens de serviço criadas por mês (gráfico de barras)
- ⏱️ Tempo médio de conclusão das ordens
- 📝 Estatísticas gerais (total de ordens, taxa de conclusão, etc.)
- 👨‍🔧 Análise de desempenho específica para técnicos (últimas 8 semanas)

### Como acessar (apenas para técnicos)

1. Faça login no sistema com conta de técnico
2. No dashboard principal, clique no card "Dashboard Analítico"
3. Ou acesse diretamente pela URL: http://localhost:8000/analytics

### Configuração do Chart.js

O sistema utiliza a biblioteca Chart.js para renderização dos gráficos. Para garantir o funcionamento correto:

1. Certifique-se de que o Chart.js foi instalado:
   ```bash
   npm install chart.js
   ```

2. Execute o compilador de assets para processar os arquivos JS:
   ```bash
   npm run dev   # Para ambiente de desenvolvimento
   # ou
   npm run prod  # Para ambiente de produção
   ```

### Personalização dos gráficos

Os gráficos são configuráveis através da edição do arquivo `resources/views/analytics/index.blade.php`. Você pode modificar cores, tipos de gráficos e outras opções de exibição conforme necessário.