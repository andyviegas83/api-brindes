# Manutencao e evolucao

## Onde alterar o contrato publico

- rotas: `routes/api.php`
- controllers: `app/Controllers`
- services: `app/Services`
- serializers: `app/Serializers`

## Onde configurar seguranca

- tokens: `.env` e `app/Config/auth.php`
- middlewares: `app/Middleware`
- limites de request e rate limit: `app/Config/api.php`

## Onde conectar futuras escritas do ERP/CRM

Os endpoints protegidos de `PUT` e `PATCH` hoje estao como scaffold controlado. A implementacao real deve entrar nos controllers:

- `app/Controllers/Admin/ContentController.php`
- `app/Controllers/Admin/CatalogController.php`

Esses controllers devem delegar a novos services de escrita e, de preferencia, a repositories especificos para operacoes administrativas.

## Pontos de atencao

- trocar os tokens de exemplo antes de subir para producao
- revisar os SQLs do `ContentAdapterRepository` quando o mapeamento do ERP estiver fechado
- substituir fallbacks mockados de `app/Config/site.php` por consultas reais conforme cada entidade ficar disponivel
- adicionar testes automatizados para middleware, rotas e serializacao
