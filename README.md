# Produto CRUD

Projeto full stack para um teste tecnico com backend em Laravel, frontend em Vue e banco Postgres, separado em API + SPA e pronto para subir com Docker.

## Stack

- Backend: Laravel 13, PHP 8.4, Eloquent, Request Validation, Resource API
- Frontend: Vue 3, Vite, TypeScript, Tailwind CSS e componentes no estilo shadcn
- Banco: PostgreSQL 17
- Infra: Docker Compose

## Como rodar

1. Tenha Docker e Docker Compose instalados.
2. Na raiz do projeto execute:

```bash
docker compose up --build
```

3. Apos o bootstrap:

- Frontend: [http://localhost:8080](http://localhost:8080)
- Backend API: [http://localhost:8000/api/v1/products](http://localhost:8000/api/v1/products)

O container do backend instala dependencias, gera `APP_KEY`, roda migrations e seed automaticamente no startup.
O container do frontend aguarda o backend concluir a inicializacao para ficar disponivel, entao e esperado que ele apareca alguns instantes depois na primeira subida.
Na primeira execucao o backend pode levar alguns minutos enquanto instala dependencias PHP dentro do container antes de ficar saudavel.

## Estrutura

```text
backend/
  app/
    Http/
      Controllers/Api/
      Requests/Product/
      Resources/
    Models/
    Repositories/
      Contracts/
    Services/
      Contracts/
  database/
    factories/
    migrations/
    seeders/
  routes/
frontend/
  src/
    components/ui/
    lib/
    services/
    types/
docker-compose.yml
```

## Arquitetura

### Backend em camadas

- `Controller`: recebe a requisicao HTTP e delega a regra de negocio.
- `FormRequest`: centraliza validacoes e saneamento de entrada.
- `Service`: coordena o caso de uso da aplicacao.
- `Repository`: abstrai o acesso ao Eloquent.
- `Resource`: padroniza a saida JSON da API.
- `Model`: representa a entidade persistida.

Fluxo resumido:

```text
Request HTTP -> ProductController -> ProductService -> ProductRepository -> Eloquent/Product
                                                      -> ProductResource -> Response JSON
```

### Onde SOLID aparece

- `S - Single Responsibility`: cada classe tem uma responsabilidade unica. Controller nao consulta banco, Service nao valida HTTP e Repository nao conhece detalhes da camada web.
- `O - Open/Closed`: novas regras podem ser adicionadas no service ou novos repositories podem ser criados sem reescrever o fluxo inteiro.
- `L - Liskov Substitution`: o service depende de contratos; qualquer implementacao aderente ao contrato do repository pode substitui-la.
- `I - Interface Segregation`: contratos pequenos e focados (`ProductRepositoryInterface` e `ProductServiceInterface`).
- `D - Dependency Inversion`: controller e service dependem de abstracoes injetadas pelo container do Laravel.

## Entidade Produto

Campos implementados:

- `id`
- `uuid`
- `nome`
- `sku`
- `preco`
- `estoque_atual`
- `estoque_minimo`
- `ativo`
- `created_at`
- `updated_at`

## Endpoints

- `GET /api/v1/products`
- `POST /api/v1/products`
- `PUT /api/v1/products/{id}`
- `DELETE /api/v1/products/{id}`

Filtros suportados no index:

- `search`
- `ativo`
- `page`
- `per_page`

## UI

O frontend foi organizado como SPA consumindo a API e traz:

- listagem paginada
- busca por nome, sku e uuid
- filtro por status ativo/inativo
- cadastro e edicao no mesmo formulario
- exclusao com confirmacao
- indicadores de estoque e valor total do inventario

## Seeds

O projeto sobe com alguns produtos de exemplo para facilitar a demonstracao inicial.

## Testes

Backend:

```bash
cd backend
php artisan test
```

Frontend:

```bash
cd frontend
npm run build
```
