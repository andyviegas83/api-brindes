# Integracao com ERP/CRM

## Origem da verdade

O ERP/CRM em `plataforma-brindes` continua sendo a origem dos dados. A API nao replica regra de negocio do ERP; ela apenas consulta, organiza e publica um contrato JSON estavel.

## Onde a API le os dados

Leitura principal:

- `app/Repositories/ErpCompanySettingsRepository.php`
- `app/Repositories/ErpCategoryRepository.php`
- `app/Repositories/ErpProductRepository.php`

Conteudo institucional e blocos ainda em fase de mapeamento:

- `app/Repositories/ContentAdapterRepository.php`

## Como configurar a conexao

Arquivo principal:

- `.env`

Campos mais importantes:

```env
ERP_DATABASE_URL=postgresql://postgres:postgres@localhost:5432/plataforma_brindes?schema=public
ERP_DB_HOST=127.0.0.1
ERP_DB_PORT=5432
ERP_DB_DATABASE=plataforma_brindes
ERP_DB_USERNAME=postgres
ERP_DB_PASSWORD=postgres
ERP_DB_SCHEMA=public
```

## Como os datasets sao montados

- `CompanySettings` vem do repositorio do ERP
- `Category` e `Product` vem dos repositorios do ERP
- banners, depoimentos, blog, textos e blocos institucionais passam pelo `ContentAdapterRepository`
- quando ainda nao existe SQL de mapeamento, a API devolve fallback configurado em `app/Config/site.php`

## Onde adaptar para o ERP real

- nomes de tabelas: `app/Config/integrations.php`
- conexoes e schema: `app/Config/database.php`
- SQLs de adaptacao de conteudo: `.env`
- serializacao final entregue ao site: `app/Serializers`

## Regra de ouro

O site nunca deve ler o banco do ERP diretamente. Toda adaptacao de nomes de campos, filtros e regras de exposicao deve passar pela API.
