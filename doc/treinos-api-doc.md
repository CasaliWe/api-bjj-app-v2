**URL Base:** `http://localhost/api-bjj-app-v2` (ajuste conforme seu ambiente)

**Cabeçalhos comuns para todas as requisições:**
- `Content-Type: application/json`
- `Authorization: Bearer SEU_TOKEN_AQUI` (substitua SEU_TOKEN_AQUI por um token válido)

### Listar Treinos

Retorna a lista de treinos do usuário logado com suporte a filtros e paginação.

**URL**: `endpoint/treinos/listar.php`

**Método**: GET

**Autenticação**: Requerida

**Parâmetros de Query:**
- `pagina` (number, opcional): Número da página atual (começa em 1, padrão: 1)
- `limite` (number, opcional): Quantidade de itens por página (padrão: 20)
- `tipo` (string, opcional): Filtro por tipo de treino ("gi", "nogi" ou "todos")
- `diaSemana` (string, opcional): Filtro por dia da semana ("segunda", "terca", etc, ou "todos")

**Exemplo de Requisição:**
```
GET /endpoint/treinos/listar.php?pagina=1&limite=20&tipo=gi&diaSemana=segunda HTTP/1.1
Host: sua-api.com
Authorization: Bearer seu_token_aqui
```

**Resposta de Sucesso**:
```json
{
  "success": true,
  "message": "Treinos obtidos com sucesso",
  "data": {
    "treinos": [
      {
        "id": 1,
        "numeroAula": 1,
        "tipo": "gi",
        "diaSemana": "segunda",
        "horario": "19:30",
        "data": "2025-09-10",
        "imagens": [
          "https://url-da-imagem-1.jpg",
          "https://url-da-imagem-2.jpg"
        ],
        "observacoes": "Texto com as observações do treino",
        "isPublico": false,
        "usuario": {
          "nome": "Nome do Usuário",
          "imagem": "URL da imagem de perfil",
          "faixa": "azul"
        }
      }
    ],
    "pagination": {
      "currentPage": 1,
      "totalPages": 5,
      "totalItems": 100,
      "itemsPerPage": 20
    }
  }
}
```

### Listar Treinos da Comunidade

Retorna a lista de treinos públicos da comunidade com paginação.

**URL**: `endpoint/treinos/comunidade.php`

**Método**: GET

**Autenticação**: Requerida

**Parâmetros de Query:**
- `pagina` (number, opcional): Número da página atual (começa em 1, padrão: 1)
- `limite` (number, opcional): Quantidade de itens por página (padrão: 20)

**Exemplo de Requisição:**
```
GET /endpoint/treinos/comunidade.php?pagina=1&limite=20 HTTP/1.1
Host: sua-api.com
Authorization: Bearer seu_token_aqui
```

**Resposta de Sucesso**:
```json
{
  "success": true,
  "message": "Treinos da comunidade obtidos com sucesso",
  "data": {
    "treinos": [
      {
        "id": 1,
        "numeroAula": 1,
        "tipo": "gi",
        "diaSemana": "segunda",
        "horario": "19:30",
        "data": "2025-09-10",
        "imagens": [
          "https://url-da-imagem-1.jpg",
          "https://url-da-imagem-2.jpg"
        ],
        "observacoes": "Texto com as observações do treino",
        "isPublico": true,
        "usuario": {
          "nome": "Nome do Usuário",
          "imagem": "URL da imagem de perfil",
          "faixa": "azul"
        }
      }
    ],
    "pagination": {
      "currentPage": 1,
      "totalPages": 5,
      "totalItems": 100,
      "itemsPerPage": 20
    }
  }
}
```

### Criar Treino

Cria um novo treino para o usuário logado.

**URL**: `endpoint/treinos/criar.php`

**Método**: POST

**Formato:** `multipart/form-data`

**Autenticação**: Requerida

**Campos do formulário:**
- `numeroAula` (number): Número sequencial da aula
- `tipo` (string): Tipo de treino ("gi" ou "nogi")
- `diaSemana` (string): Dia da semana ("segunda", "terca", etc.)
- `horario` (string): Horário do treino no formato HH:MM
- `data` (string): Data do treino no formato YYYY-MM-DD
- `observacoes` (string): Texto com observações sobre o treino
- `isPublico` (boolean): Indica se o treino é público (true) ou privado (false)
- `imagens[]`: Arrays de arquivos de imagem (múltiplos arquivos)

**Exemplo de Teste no Postman:**
1. Crie uma requisição POST
2. URL: `{{base_url}}/endpoint/treinos/criar.php`
3. Adicione o header `Authorization: Bearer {{seu_token}}`
4. Selecione a opção "form-data"
5. Adicione os campos de texto conforme listado acima
6. Para adicionar imagens, adicione campos chamados `imagens[]` com o tipo "File" e selecione os arquivos a serem enviados
7. Envie a requisição

**Resposta de Sucesso**:
```json
{
  "success": true,
  "message": "Treino criado com sucesso",
  "data": {
    "id": 1,
    "numeroAula": 1,
    "tipo": "gi",
    "diaSemana": "segunda",
    "horario": "19:30",
    "data": "2025-09-10",
    "imagens": [],
    "observacoes": "Texto com as observações do treino",
    "isPublico": false
  }
}
```

### Atualizar Treino

Atualiza um treino existente do usuário logado.

**URL**: `endpoint/treinos/atualizar.php`

**Método**: POST

**Formato:** `multipart/form-data`

**Autenticação**: Requerida

**Campos do formulário:**
- `id` (number, obrigatório): ID do treino a ser atualizado
- `numeroAula` (number): Número sequencial da aula
- `tipo` (string): Tipo de treino ("gi" ou "nogi")
- `diaSemana` (string): Dia da semana ("segunda", "terca", etc.)
- `horario` (string): Horário do treino no formato HH:MM
- `data` (string): Data do treino no formato YYYY-MM-DD
- `observacoes` (string): Texto com observações sobre o treino
- `isPublico` (boolean): Indica se o treino é público (true) ou privado (false)
- `imagens[]`: Arrays de novos arquivos de imagem (múltiplos arquivos)
- `imagensExistentes[]`: Array com IDs das imagens a manter

**Exemplo de Teste no Postman:**
1. Crie uma requisição POST
2. URL: `{{base_url}}/endpoint/treinos/atualizar.php`
3. Adicione o header `Authorization: Bearer {{seu_token}}`
4. Selecione a opção "form-data"
5. Adicione os campos de texto conforme listado acima
6. Para adicionar novas imagens, adicione campos chamados `imagens[]` com o tipo "File" e selecione os arquivos a serem enviados
7. Para manter imagens existentes, adicione campos chamados `imagensExistentes[]` com os IDs das imagens que deseja manter
8. Envie a requisição
**Resposta de Sucesso**:
```json
{
  "success": true,
  "message": "Treino atualizado com sucesso",
  "data": {
    "id": 1,
    "numeroAula": 1,
    "tipo": "gi",
    "diaSemana": "segunda",
    "horario": "19:30",
    "data": "2025-09-10",
    "imagens": [
      "https://url-da-imagem-1.jpg",
      "https://url-da-imagem-2.jpg"
    ],
    "observacoes": "Texto com as observações do treino",
    "isPublico": false
  }
}
```

### Excluir Treino

Exclui um treino do usuário logado.

**URL**: `endpoint/treinos/excluir.php`

**Método**: POST

**Autenticação**: Requerida

**Corpo da Requisição**:
```json
{
  "id": 1
}
```

**Parâmetros**:
- `id` (number): ID do treino a ser excluído

**Resposta de Sucesso**:
```json
{
  "success": true,
  "message": "Treino excluído com sucesso",
  "data": null
}
```

### Alterar Visibilidade

Altera a visibilidade (público/privado) de um treino do usuário logado.

**URL**: `endpoint/treinos/visibilidade.php`

**Método**: POST

**Autenticação**: Requerida

**Corpo da Requisição**:
```json
{
  "id": 1,
  "isPublico": true
}
```

**Parâmetros**:
- `id` (number): ID do treino
- `isPublico` (boolean): Novo status de visibilidade (true para público, false para privado)

**Resposta de Sucesso**:
```json
{
  "success": true,
  "message": "Visibilidade do treino alterada com sucesso",
  "data": {
    "id": 1,
    "isPublico": true
  }
}
```

### Upload de Imagens

Faz upload de imagens para um treino existente.

**URL**: `endpoint/treinos/upload-imagem.php`

**Método**: POST

**Autenticação**: Requerida

**Corpo da Requisição**: FormData multipart contendo:
- `id` (number): ID do treino
- `imagens[]` (array de arquivos): Array de arquivos de imagem

**Resposta de Sucesso**:
```json
{
  "success": true,
  "message": "Imagens carregadas com sucesso",
  "data": {
    "imagens": [
      "https://url-da-imagem-1.jpg",
      "https://url-da-imagem-2.jpg"
    ]
  }
}
```
