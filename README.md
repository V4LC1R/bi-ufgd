# Bi-UFGD - API Documentation

## Requisitos

### Docker

- Docker Engine 20.10+
- Docker Compose 2.0+

### Local

- PHP 8.2+
- Composer 2.x
- PostgreSQL 15+
- MySQL 8.0+
- MongoDB 7+
- Redis

---

## Instalação

### Com Docker

```bash
# Clone e configure
git clone <repositorio>
cd bi-ufgd
cp .env.example .env

# Inicie os containers
docker-compose up -d

# Instale dependências
docker exec -it bi-ufgd-app composer install

# Configure a aplicação
docker exec -it bi-ufgd-app php artisan key:generate
docker exec -it bi-ufgd-app php artisan migrate

# Inicie as filas
docker exec -it bi-ufgd-app php artisan queue:work --queue=exec,build,validate
```

### Local

```bash
# Clone e configure
git clone <repositorio>
cd bi-ufgd
cp .env.example .env

# Configure o .env com suas credenciais

# Instale dependências
composer install

# Configure a aplicação
php artisan key:generate
php artisan migrate

# Inicie o servidor
php artisan serve

# Inicie as filas (em outro terminal)
php artisan queue:work --queue=exec,build,validate
```

---

## Filas

```bash
# Iniciar workers
php artisan queue:work --queue=exec,build,validate

# Ver jobs falhados
php artisan queue:failed

# Reprocessar job
php artisan queue:retry {id}

# Reprocessar todos
php artisan queue:retry all

# Reiniciar workers
php artisan queue:restart
```

---

## API Endpoints

Base URL: `http://localhost:8080/api`

### Auth

#### Registrar

```http
POST /auth/register
```

```json
{
  "name": "Nome",
  "email": "email@example.com",
  "password": "senha",
  "document": "000.000.000-00",
  "entity_id": 1
}
```

#### Login

```http
POST /auth
```

```json
{
  "email": "email@example.com",
  "password": "senha"
}
```

---

### Connection

#### Criar

```http
POST /connection
```

```json
{
  "name": "graduacao",
  "connection": {
    "host": "mysql",
    "port": "3306",
    "user": "root",
    "password": "root-pass",
    "type": "mysql",
    "database": "dw_ufgd_graduacao"
  },
  "tables": [
    {
      "type": "dimension",
      "name": "dimensao_curso",
      "alias": "d_cur",
      "columns": {
        "chave_curso": "number:pk",
        "curso": "string"
      }
    },
    {
      "type": "fact",
      "name": "fato",
      "alias": "fto",
      "columns": {
        "chave_curso": "number:fk:dimensao_curso.chave_curso",
        "media_aproveitamento": "number"
      }
    }
  ]
}
```

#### Atualizar

```http
PATCH /connection/{id}
```

#### Listar

```http
GET /connection
```

#### Estrutura

```http
GET /connection/{name}/struct
GET /connection/{name}/fact
```

#### Dados Dimensão

```http
GET /connection/{id}/dimension?table={table}&page=1&perPage=15
```

---

### Query

#### Criar

```http
POST /querry
```

```json
{
  "connectionName": "graduacao",
  "description": "Descrição",
  "fact": {
    "limit": 100,
    "columns": {
      "media_aproveitamento": {
        "name": "media_aproveitamento",
        "aggregates": ["avg"],
        "alias": {
          "avg": "Media"
        }
      }
    }
  },
  "dimensions": [
    {
      "table": "dimensao_curso",
      "columns": ["curso"]
    }
  ],
  "sub-dimension": []
}
```

**Agregações:** `sum`, `avg`, `count`, `min`, `max`, `:list`

**Operadores:** `=`, `>`, `<`, `>=`, `<=`, `:range`, `:in`

#### Atualizar

```http
PATCH /querry/{id}
```

#### Visualizar SQL

```http
GET /querry/{id}
```

#### Build PreSQL

```http
GET /querry/build/{id}
```

#### Resultado

```http
GET /querry/result/{uuid}
```

#### Listar por Conexão

```http
GET /querry/by-connection/{connection_id}
```

---

## Exemplos

### Query com Filtro

```json
{
  "connectionName": "graduacao",
  "description": "Média últimos 10 anos",
  "fact": {
    "limit": 100,
    "columns": {
      "media_aproveitamento": {
        "name": "media_aproveitamento",
        "aggregates": ["avg"]
      }
    }
  },
  "dimensions": [
    {
      "table": "dimensao_tempo",
      "columns": ["disciplina_ano"],
      "filter": {
        "disciplina_ano": {
          "op": ":range",
          "value": [2013, 2022]
        }
      }
    }
  ]
}
```

### Query com Sub-dimensão

```json
{
  "connectionName": "graduacao",
  "description": "Matriculados por faixa etária",
  "fact": {
    "limit": 100,
    "columns": {
      "numero_estudantes": {
        "name": "numero_estudantes",
        "aggregates": ["sum"]
      }
    }
  },
  "dimensions": [],
  "sub-dimension": [
    {
      "table": "dimensao_faixa_etaria",
      "columns": ["faixa_etaria"]
    }
  ]
}
```

---

## Comandos Úteis

```bash
# Cache
php artisan cache:clear
php artisan config:clear
php artisan optimize:clear

# Migrations
php artisan migrate
php artisan migrate:rollback

# Docker
docker-compose up -d
docker-compose down
docker-compose logs -f
docker exec -it bi-ufgd-app bash
```
