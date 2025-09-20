````markdown
**URL Base:** `http://localhost/api-bjj-app-v2` (ajuste conforme seu ambiente)

**Cabeçalhos comuns para todas as requisições:**
- `Content-Type: application/json`
- `Authorization: Bearer SEU_TOKEN_AQUI` (substitua SEU_TOKEN_AQUI por um token válido)

> Observação importante
- Todas as respostas seguem o wrapper: `{ "data": ... }` em caso de sucesso.
- Em caso de erro, a API retorna status HTTP apropriado e corpo `{ "message": "Descrição do erro" }`.
- Campos de vídeo nos nodes (video_url, video_poster) são armazenados no banco apenas com o NOME do arquivo. A API devolve a URL pública montada com `BASE_URL + 'assets/...'`.

---

### 1) Listar Planos do Usuário

Retorna todos os planos do usuário autenticado.

**Endpoint:** `endpoint/plano-jogo/listar.php`

**Método:** GET

**Parâmetros de Query:**
—

**Exemplo de Requisição:**
```
GET /endpoint/plano-jogo/listar.php HTTP/1.1
Host: sua-api.com
Authorization: Bearer seu_token_aqui
```

---

### 2) Obter um Plano por ID (com árvore completa)

Retorna um plano e toda a árvore de nodes.

**Endpoint:** `endpoint/plano-jogo/obter.php`

**Método:** GET

**Parâmetros de Query:**
- `id` (obrigatório): ID do plano

**Exemplo de Requisição:**
```
GET /endpoint/plano-jogo/obter.php?id=123 HTTP/1.1
Host: sua-api.com
Authorization: Bearer seu_token_aqui
```

---

### 3) Criar Plano

Cria um plano para o usuário autenticado.

**Endpoint:** `endpoint/plano-jogo/criar.php`

**Método:** POST

**Formato:** JSON

**Corpo da Requisição:**
```json
{
  "nome": "Plano para competição",
  "descricao": "...",
  "categoria": "Competição"
}
```

**Exemplo de Requisição no Postman:**
1. Método POST
2. URL `sua-api.com/endpoint/plano-jogo/criar.php`
3. Headers: `Authorization: Bearer seu_token_aqui`, `Content-Type: application/json`
4. Body: `raw` JSON conforme acima

---

### 4) Atualizar Plano

Atualiza os dados do plano (nome/descrição/categoria).

**Endpoint:** `endpoint/plano-jogo/atualizar.php`

**Método:** POST

**Formato:** JSON

**Corpo da Requisição:**
```json
{
  "id": 123,
  "nome": "Novo nome",
  "descricao": "Nova descrição",
  "categoria": "Competição"
}
```

**Exemplo de Requisição no Postman:**
1. Método POST
2. URL `sua-api.com/endpoint/plano-jogo/atualizar.php`
3. Headers: `Authorization: Bearer seu_token_aqui`, `Content-Type: application/json`
4. Body: `raw` JSON conforme acima

---

### 5) Excluir Plano

Remove um plano do usuário.

**Endpoint:** `endpoint/plano-jogo/excluir.php`

**Método:** POST

**Formato:** JSON

**Corpo da Requisição:**
```json
{ "id": 123 }
```

**Exemplo de Requisição no Postman:**
1. Método POST
2. URL `sua-api.com/endpoint/plano-jogo/excluir.php`
3. Headers: `Authorization: Bearer seu_token_aqui`, `Content-Type: application/json`
4. Body: `raw` JSON conforme acima

---

### 6) Adicionar Nó

Adiciona um node à árvore do plano (como raiz ou filho de outro node).

Tipos de node: `tecnica`, `acao`, `certo`, `errado`.

**Endpoint:** `endpoint/plano-jogo/adicionar-node.php`

**Método:** POST

**Formato:** JSON

**Corpo da Requisição:**
```json
{
  "planoId": 123,
  "parentId": null,
  "node": {
    "nome": "Quebra de pegada",
    "tipo": "tecnica",
    "descricao": "...",
    "tecnicaId": 456,
    "categoria": "passador",
    "posicao": "Guarda Fechada",
    "passos": ["..."],
    "observacoes": ["..."],
    "video_url": null,
    "video_poster": null,
    "video": null
  }
}
```

> Observação sobre vídeos nos nodes
- `video_url` e `video_poster` devem ser enviados como NOME de arquivo quando aplicável. A API salva apenas o nome e responde a URL pública completa como `BASE_URL + 'assets/...'`.

**Exemplo de Requisição no Postman:**
1. Método POST
2. URL `sua-api.com/endpoint/plano-jogo/adicionar-node.php`
3. Headers: `Authorization: Bearer seu_token_aqui`, `Content-Type: application/json`
4. Body: `raw` JSON conforme acima

---

### 7) Remover Nó

Remove um node (e seus filhos) da árvore do plano.

**Endpoint:** `endpoint/plano-jogo/remover-node.php`

**Método:** POST

**Formato:** JSON

**Corpo da Requisição:**
```json
{ "planoId": 123, "nodeId": "n1b" }
```

**Exemplo de Requisição no Postman:**
1. Método POST
2. URL `sua-api.com/endpoint/plano-jogo/remover-node.php`
3. Headers: `Authorization: Bearer seu_token_aqui`, `Content-Type: application/json`
4. Body: `raw` JSON conforme acima

---

### Formatos de retorno (resumo)

- Listar planos:
  - `{ "data": { "planos": [{ "id", "nome", "descricao", "categoria", "dataCriacao", "dataAtualizacao" }] } }`
- Obter plano com árvore:
  - `{ "data": { "plano": { "id", "nome", "descricao", "categoria", "dataCriacao", "dataAtualizacao", "nodes": [...] } } }`
- Criar/Atualizar/Excluir:
  - Sucesso em `{ "data": ... }`
  - Erro em `{ "message": "..." }`

### Campos de datas

- Datas retornadas em formato ISO 8601 UTC com sufixo `Z`, por exemplo: `2025-09-20T12:34:56Z`.

````