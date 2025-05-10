# Guia de Teste do Sistema

Este documento fornece instruções passo a passo para testar o sistema de ordens de serviço.

## Configuração Inicial

1. Execute as migrações e seeders:
   ```
   php artisan migrate:fresh --seed
   ```

2. Inicie o servidor Laravel:
   ```
   php artisan serve
   ```

3. Em outro terminal, inicie o processador de filas:
   ```
   php artisan queue:work
   ```

## Fluxo de Teste Completo

### 1. Login como Técnico

- Acesse http://localhost:8000/
- Use credenciais: 
  - Email: tecnico@exemplo.com
  - Senha: password

### 2. Explorar Dashboard

- Visualize estatísticas de ordens de serviço
- Verifique clientes recentes
- Verifique ordens recentes

### 3. Criar uma Nova Ordem de Serviço

- Clique em "Nova OS" no menu superior
- Preencha os dados da ordem:
  - Título: "Manutenção de Computador"
  - Descrição: "Limpeza e atualização de software"
  - Selecione um cliente da lista
- Clique em "Criar OS"

### 4. Visualizar Lista de Ordens

- Acesse "Ordens de Serviço" no menu superior
- Teste os filtros:
  - Filtre por status "Pendente"
  - Filtre por data
  - Pesquise por termos no título ou descrição

### 5. Visualizar Detalhes da Ordem

- Na lista de ordens, clique em "Ver" para a ordem recém-criada
- Verifique se os detalhes estão corretos

### 6. Editar e Concluir uma Ordem

- Na tela de detalhes, clique em "Editar Ordem"
- Altere o status para "Concluída"
- Observe o aviso sobre envio de e-mail
- Clique em "Concluir e Notificar"

### 7. Verificar Processamento da Fila

- Observe a janela do terminal onde está executando `queue:work`
- Verifique se o e-mail foi processado

### 8. Testar como Cliente

- Faça logout
- Faça login como cliente:
  - Email: cliente@exemplo.com
  - Senha: password
- Verifique as ordens associadas ao cliente
- Verifique o dashboard do cliente

## Testando o Envio de E-mail

Para testar manualmente o envio de e-mail, use o comando:

```
php artisan app:test-email
```

Este comando mostrará as ordens de serviço disponíveis e permitirá selecionar uma para enviar um e-mail de teste.

## Verificação de Funcionalidades

- [ ] Autenticação de usuários funciona corretamente
- [ ] Dashboard exibe estatísticas apropriadas
- [ ] CRUD de ordens de serviço está completo
- [ ] Filtros de pesquisa funcionam
- [ ] Notificações por e-mail são enviadas
- [ ] Sistema de filas está processando corretamente
- [ ] Controles de acesso (políticas) estão aplicados
