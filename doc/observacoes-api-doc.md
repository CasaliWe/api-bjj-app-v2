**URL Base:** `http://localhost/api-bjj-app-v2` (ajuste conforme seu ambiente)

**Cabeçalhos comuns para todas as requisições:**
- `Content-Type: application/json`
- `Authorization: Bearer SEU_TOKEN_AQUI` (substitua SEU_TOKEN_AQUI por um token válido)

## 1. Listar Observações

**Método:** POST

**URL:** `/endpoint/observacoes/listar.php`

**Body (JSON):**
```json
{
  "filtros": {
    "tag": "todas",
    "termo": ""
  },
  "pagina": 1,
  "limite": 12
}
```

**Exemplos de Filtros:**

Filtrar por tag:
```json
{
  "filtros": {
    "tag": "treino",
    "termo": ""
  },
  "pagina": 1,
  "limite": 12
}
```

Buscar por termo:
```json
{
  "filtros": {
    "tag": "todas",
    "termo": "armlock"
  },
  "pagina": 1,
  "limite": 12
}
```

**Resposta esperada (sucesso):**
```json
{
  "success": true,
  "message": "Observações obtidas com sucesso",
  "data": {
    "observacoes": [
      {
        "id": 1,
        "titulo": "Treino de guarda",
        "conteudo": "Hoje treinei transições de guarda fechada para x-guard",
        "tag": "treino",
        "data": "2025-09-10T14:30:00",
        "usuarioId": 1
      },
      ...
    ],
    "paginacao": {
      "currentPage": 1,
      "totalPages": 5,
      "totalItems": 50
    }
  }
}
```

## 2. Obter Observação Específica

**Método:** POST

**URL:** `/endpoint/observacoes/obter.php`

**Body (JSON):**
```json
{
  "id": 1
}
```

**Resposta esperada (sucesso):**
```json
{
  "success": true,
  "message": "Observação obtida com sucesso",
  "data": {
    "id": 1,
    "titulo": "Treino de guarda",
    "conteudo": "Hoje treinei transições de guarda fechada para x-guard",
    "tag": "treino",
    "data": "2025-09-10T14:30:00",
    "usuarioId": 1
  }
}
```

**Resposta esperada (falha):**
```json
{
  "success": false,
  "message": "Observação não encontrada ou você não tem permissão para visualizá-la",
  "data": null
}
```

## 3. Adicionar Nova Observação

**Método:** POST

**URL:** `/endpoint/observacoes/adicionar.php`

**Body (JSON):**
```json
{
  "titulo": "Nova técnica de raspagem",
  "conteudo": "Aprendi uma raspagem da meia guarda que funciona muito bem contra oponentes mais pesados",
  "tag": "posicao"
}
```

**Resposta esperada (sucesso):**
```json
{
  "success": true,
  "message": "Observação adicionada com sucesso",
  "data": {
    "id": 10,
    "titulo": "Nova técnica de raspagem",
    "conteudo": "Aprendi uma raspagem da meia guarda que funciona muito bem contra oponentes mais pesados",
    "tag": "posicao",
    "data": "2025-09-10T15:45:00",
    "usuarioId": 1
  }
}
```

## 4. Atualizar Observação Existente

**Método:** PUT

**URL:** `/endpoint/observacoes/atualizar.php`

**Body (JSON):**
```json
{
  "id": 10,
  "titulo": "Técnica de raspagem atualizada",
  "conteudo": "Aprendi uma raspagem da meia guarda que funciona muito bem contra oponentes mais pesados. Atualização: testei hoje e funciona mesmo!",
  "tag": "posicao"
}
```

**Resposta esperada (sucesso):**
```json
{
  "success": true,
  "message": "Observação atualizada com sucesso",
  "data": {
    "id": 10,
    "titulo": "Técnica de raspagem atualizada",
    "conteudo": "Aprendi uma raspagem da meia guarda que funciona muito bem contra oponentes mais pesados. Atualização: testei hoje e funciona mesmo!",
    "tag": "posicao",
    "data": "2025-09-10T15:45:00",
    "usuarioId": 1
  }
}
```

## 5. Excluir Observação

**Método:** DELETE

**URL:** `/endpoint/observacoes/excluir.php`

**Body (JSON):**
```json
{
  "id": 10
}
```

**Resposta esperada (sucesso):**
```json
{
  "success": true,
  "message": "Observação excluída com sucesso",
  "data": null
}
```

**Resposta esperada (falha):**
```json
{
  "success": false,
  "message": "Observação não encontrada ou você não tem permissão para excluí-la",
  "data": null
}
```
