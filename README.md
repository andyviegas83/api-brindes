# API Brinde

Base profissional em PHP moderno para a API que intermedia a comunicacao entre:

- `plataforma-brindes` (ERP/CRM)
- `site-brindes` (site institucional/comercial)

Nesta revisao, o projeto entrega uma base mais robusta para operacao comercial real: roteamento com parametros, respostas JSON padronizadas, middlewares de validacao, autenticacao por token, controle de abilities, rate limit, logs, serializacao e separacao clara entre rotas publicas e protegidas.

## Requisitos

- PHP 8.2+
- Composer

## Como rodar localmente

1. Copie o arquivo de ambiente:

```powershell
Copy-Item .env.example .env
```

2. Ajuste as variaveis do `.env`.
3. Gere o autoload:

```bash
composer install
```

4. Suba o servidor:

```bash
composer serve
```

5. Teste os endpoints principais:

```bash
curl http://localhost:8080/health
curl http://localhost:8080/api/v1/homepage
curl -H "Authorization: Bearer change-this-admin-token" http://localhost:8080/api/v1/admin/auth/me
```

## Estrutura principal

- `app/`: controllers, services, repositories, serializers, middleware e infraestrutura
- `bootstrap/`: inicializacao da aplicacao
- `routes/`: definicao central das rotas
- `public/`: front controller unico
- `storage/`: rate limit e arquivos internos
- `logs/`: logs de erro e acesso
- `docs/`: documentacao tecnica

## Documentacao

- [docs/architecture.md](docs/architecture.md)
- [docs/integracao-erp.md](docs/integracao-erp.md)
- [docs/maintenance.md](docs/maintenance.md)
