# Arquitetura da API

## Papel da aplicacao

`api-brindes` e a camada intermediaria entre:

- `plataforma-brindes`, origem dos dados
- `site-brindes`, consumidor publico

A API consulta o ERP/CRM, transforma os dados em um contrato estavel e expoe apenas o que o site precisa.

## Camadas principais

- `app/Controllers`: entrada HTTP e orquestracao da resposta
- `app/Services`: composicao de datasets e regra de integracao
- `app/Repositories`: leitura do ERP/CRM e adaptadores de conteudo
- `app/Serializers`: contrato final entregue ao site
- `app/Middleware`: validacao, token, abilities e rate limit
- `app/Core`: bootstrap HTTP, container, roteador, logger e tratamento global de erro
- `app/Config`: configuracao por ambiente

## Melhorias desta revisao

- roteador com suporte a parametros como `/api/v1/products/{idOrSlug}`
- deteccao de `405 Method Not Allowed`
- `request_id` em todas as respostas
- `rate_limit` e metadados operacionais no envelope JSON
- autenticacao por token com abilities separadas por contexto
- middlewares distintos para validacao, autenticacao, permissao e limite de requisicoes
- serializers dedicados para nao acoplar o front ao formato cru do ERP
- fallback controlado para datasets sem mapeamento completo ou indisponibilidade temporaria do ERP

## Separacao entre publico e protegido

Rotas publicas:

- `GET /health`
- `GET /api/v1/site/settings`
- `GET /api/v1/site/navigation`
- `GET /api/v1/categories/main`
- `GET /api/v1/categories`
- `GET /api/v1/homepage`
- `GET /api/v1/products`
- `GET /api/v1/products/promotions`
- `GET /api/v1/products/launches`
- `GET /api/v1/products/search`
- `GET /api/v1/products/{idOrSlug}`
- `GET /api/v1/pages/about`
- `GET /api/v1/pages/faq`
- `GET /api/v1/pages/contact`

Rotas protegidas:

- `GET /api/v1/admin/auth/me`
- `GET /api/v1/admin/banners`
- `PUT|PATCH /api/v1/admin/banners`
- `GET /api/v1/admin/site-texts`
- `PUT|PATCH /api/v1/admin/site-texts`
- `GET /api/v1/admin/categories`
- `PUT|PATCH /api/v1/admin/categories`
- `GET /api/v1/admin/products`
- `GET /api/v1/admin/products/{idOrSlug}`
- `PUT|PATCH /api/v1/admin/products/{idOrSlug}`

## Fluxo de requisicao

1. `public/index.php` captura a request
2. `bootstrap/app.php` carrega env, config, logger, banco e container
3. `routes/api.php` registra as rotas e middlewares
4. `Router` casa rota literal ou parametrizada
5. middlewares validam payload, token, ability e rate limit
6. controller chama o service
7. service consulta repositories e aplica serializacao
8. `Application` acrescenta metadados operacionais e registra log de acesso

## Contrato de resposta

```json
{
  "success": true,
  "message": "Products loaded successfully.",
  "data": {},
  "error": null,
  "meta": {
    "timestamp": "2026-04-21T20:00:00-03:00",
    "version": "v1",
    "request_id": "abc123"
  }
}
```
