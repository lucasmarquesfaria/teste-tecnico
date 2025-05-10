# Sistema de Gerenciamento de Ordens de Serviço

## Sobre o Sistema

Este é um sistema de gerenciamento de ordens de serviço desenvolvido com Laravel, onde técnicos podem gerenciar os serviços prestados para clientes. O sistema permite:

- Autenticação de usuários (técnicos e clientes)
- Criação, visualização, edição e atualização de ordens de serviço
- Notificações por e-mail quando uma ordem de serviço é concluída
- Dashboard com estatísticas e resumos
- Filtros de ordens de serviço por status, data e texto

## Requisitos

- PHP 8.1+
- Composer
- MySQL ou outro banco de dados compatível
- Node.js e NPM (para compilação de assets, opcional)

## Instalação

1. Clone o repositório
2. Instale as dependências:
   ```
   composer install
   ```
3. Configure o arquivo `.env` com suas informações de banco de dados e e-mail
4. Execute as migrações:
   ```
   php artisan migrate
   ```
5. Execute os seeders para criar dados de teste:
   ```
   php artisan db:seed
   ```
6. Inicie o servidor de desenvolvimento:
   ```
   php artisan serve
   ```

## Usuários de Teste

O sistema já vem com alguns usuários predefinidos para teste:

### Técnico
- Email: tecnico@exemplo.com
- Senha: password

### Cliente
- Email: cliente@exemplo.com
- Senha: password

## Processamento de Filas e Notificações

As notificações por e-mail são processadas em filas para melhor desempenho. Para executar o processamento de filas, execute:

```
php artisan queue:work
```

Para ambiente de produção, recomenda-se usar um supervisor ou systemd para manter o worker de filas em execução:

### Exemplo com Supervisor (Linux)

```
[program:laravel-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path-to-your-project/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path-to-your-project/storage/logs/worker.log
stopwaitsecs=3600
```

## Funcionalidades

- **Dashboard**: Visualização rápida de estatísticas e ordens recentes
- **Gerenciamento de Ordens**: Criação, visualização, edição e atualização
- **Filtros**: Busca por status, data e texto
- **Notificações**: E-mails automáticos para clientes quando uma ordem é concluída

## Estrutura do Código

- `/app/Http/Controllers`: Controladores da aplicação
- `/app/Models`: Modelos do banco de dados
- `/app/Events`: Eventos disparados pela aplicação
- `/app/Listeners`: Listeners que respondem aos eventos
- `/app/Policies`: Políticas de autorização
- `/resources/views`: Views da aplicação

## Notificações de conclusão de OS

O listener responsável por notificar o cliente ao concluir uma ordem de serviço é o `NotifyClientOfCompletion` (implementa ShouldQueue). Todas as referências anteriores a `SendServiceOrderCompletedEmail` foram substituídas para aderir ao enunciado.

## Licença

Este projeto está licenciado sob a [MIT license](https://opensource.org/licenses/MIT).
