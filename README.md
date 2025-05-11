# Sistema de Gerenciamento de Ordens de Serviço 🛠️

### Principais funcionalidades

✅ Técnicos podem criar e gerenciar ordens de serviço  
✅ Clientes podem acompanhar suas ordens  
✅ Notificações automáticas por e-mail quando serviços são concluídos  
✅ Interface amigável e responsiva  
✅ Sistema de filas para processamento eficiente de e-mails

## Como começar 🚀

### O que você vai precisar

* PHP 8.1 ou superior
* Composer
* MySQL ou SQLite
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

## Eventos e Notificações

O sistema utiliza o padrão Observer através de eventos e listeners para notificar os clientes:

1. Quando uma ordem de serviço é marcada como concluída, o evento `ServiceOrderCompleted` é disparado
2. O listener `NotifyClientOfCompletion` escuta esse evento e envia a notificação por e-mail
3. A notificação é processada de forma assíncrona utilizando filas

### Prevenção de e-mails duplicados

O sistema implementa um mecanismo de cache para evitar envio duplicado de notificações:

```php
// Verificar se esta notificação já foi enviada
$cacheKey = 'order_completed_notification_' . $serviceOrder->id;
if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
    return;
}

// Envio da notificação...

// Marcar como enviado (expira após 1 hora)
\Illuminate\Support\Facades\Cache::put($cacheKey, true, 3600);
```

## Filas e Jobs

As notificações são processadas através de filas para melhorar o desempenho:

- As notificações implementam `ShouldQueue`
- O sistema utiliza a fila `database` por padrão
- Cada job tem configurado até 3 tentativas em caso de falha