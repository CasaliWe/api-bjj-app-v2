**URL Base:** `http://localhost/api-bjj-app-v2` (ajuste conforme seu ambiente)

**Cabeçalhos comuns para todas as requisições:**
- `Content-Type: application/json`
- `Authorization: Bearer SEU_TOKEN_AQUI` (substitua SEU_TOKEN_AQUI por um token válido)

### 1. Listar Técnicas do Usuário

Retorna todas as técnicas do usuário autenticado, com suporte a filtros e paginação.

**Endpoint:** `endpoint/tecnicas/listar.php`

**Método:** GET

**Parâmetros de Query:**
- `pagina` (opcional, default: 1): Número da página para paginação
- `limite` (opcional, default: 20): Número de itens por página
- `categoria` (opcional): Filtrar por categoria (guardeiro/passador)
- `posicao` (opcional): Filtrar por posição específica

**Exemplo de Requisição:**
```
GET /endpoint/tecnicas/listar.php?pagina=1&limite=10&categoria=guardeiro HTTP/1.1
Host: sua-api.com
Authorization: Bearer seu_token_aqui
```

### 2. Buscar Técnicas da Comunidade

Retorna as técnicas públicas compartilhadas pela comunidade, com suporte a pesquisa e paginação.

**Endpoint:** `endpoint/tecnicas/comunidade.php`

**Método:** GET

**Parâmetros de Query:**
- `pagina` (opcional, default: 1): Número da página para paginação
- `limite` (opcional, default: 20): Número de itens por página
- `termo` (opcional): Termo para pesquisa em nome, categoria ou posição

**Exemplo de Requisição:**
```
GET /endpoint/tecnicas/comunidade.php?pagina=1&limite=10&termo=armlock HTTP/1.1
Host: sua-api.com
Authorization: Bearer seu_token_aqui
```

### 3. Listar Posições

Retorna todas as posições disponíveis para uso nas técnicas (padrão e do usuário).

**Endpoint:** `endpoint/tecnicas/posicoes.php`

**Método:** GET

**Exemplo de Requisição:**
```
GET /endpoint/tecnicas/posicoes.php HTTP/1.1
Host: sua-api.com
Authorization: Bearer seu_token_aqui
```

### 4. Criar Técnica

Cria uma nova técnica para o usuário, com suporte a upload de vídeo.

**Endpoint:** `endpoint/tecnicas/criar.php`

**Método:** POST

**Formato:** multipart/form-data

**Parâmetros:**
- `nome` (obrigatório): Nome da técnica
- `categoria` (obrigatório): Categoria (guardeiro/passador)
- `posicao` (obrigatório): Posição da técnica
- `passos` (opcional): Array JSON de passos como string, ex: `["Passo 1", "Passo 2"]`
- `observacoes` (opcional): Array JSON de observações como string, ex: `["Obs 1", "Obs 2"]`
- `nota` (opcional): Nota de 1 a 5
- `destacado` (opcional): Se a técnica é destacada (0 ou 1)
- `publica` (opcional): Se a técnica é pública (0 ou 1)
- `video` (opcional): URL do vídeo externo
- `videoFile` (opcional): Arquivo de vídeo (MP4, máx. 20MB)
- `video_width` (opcional): Largura do vídeo em pixels (importante para vídeos verticais)
- `video_height` (opcional): Altura do vídeo em pixels (importante para vídeos verticais)

**Exemplo de Requisição no Postman:**
1. Selecione o método POST
2. Insira a URL `sua-api.com/endpoint/tecnicas/criar.php`
3. Na aba "Headers", adicione `Authorization: Bearer seu_token_aqui`
4. Na aba "Body", selecione "form-data"
5. Adicione os campos acima, marcando `videoFile` como tipo "File"

### 5. Atualizar Técnica

Atualiza uma técnica existente do usuário.

**Endpoint:** `endpoint/tecnicas/atualizar.php`

**Método:** POST

**Formato:** multipart/form-data

**Parâmetros:**
- `id` (obrigatório): ID da técnica a ser atualizada
- `nome` (obrigatório): Nome da técnica
- `categoria` (obrigatório): Categoria (guardeiro/passador)
- `posicao` (obrigatório): Posição da técnica
- `passos` (opcional): Array JSON de passos como string
- `observacoes` (opcional): Array JSON de observações como string
- `nota` (opcional): Nota de 1 a 5
- `destacado` (opcional): Se a técnica é destacada (0 ou 1)
- `publica` (opcional): Se a técnica é pública (0 ou 1)
- `video` (opcional): URL do vídeo externo
- `videoFile` (opcional): Novo arquivo de vídeo (substituirá o existente)
- `video_width` (opcional): Largura do vídeo em pixels (importante para vídeos verticais)
- `video_height` (opcional): Altura do vídeo em pixels (importante para vídeos verticais)

**Exemplo de Requisição no Postman:**
Similar ao endpoint de criação, mas incluindo o ID da técnica.

### 6. Excluir Técnica

Remove uma técnica do usuário.

**Endpoint:** `endpoint/tecnicas/excluir.php`

**Método:** POST

**Formato:** JSON

**Corpo da Requisição:**
```json
{
  "id": 123
}
```

**Exemplo de Requisição no Postman:**
1. Selecione o método POST
2. Insira a URL `sua-api.com/endpoint/tecnicas/excluir.php`
3. Na aba "Headers", adicione:
   - `Authorization: Bearer seu_token_aqui`
   - `Content-Type: application/json`
4. Na aba "Body", selecione "raw" e tipo "JSON"
5. Insira o JSON com o ID da técnica a ser excluída

### 7. Alterar Destaque

Altera o status de destaque de uma técnica.

**Endpoint:** `endpoint/tecnicas/destaque.php`

**Método:** POST

**Formato:** JSON

**Corpo da Requisição:**
```json
{
  "id": 123,
  "destacado": true
}
```

**Exemplo de Requisição no Postman:**
1. Selecione o método POST
2. Insira a URL `sua-api.com/endpoint/tecnicas/destaque.php`
3. Na aba "Headers", adicione:
   - `Authorization: Bearer seu_token_aqui`
   - `Content-Type: application/json`
4. Na aba "Body", selecione "raw" e tipo "JSON"
5. Insira o JSON com o ID e o novo status de destaque

### 8. Alterar Visibilidade

Altera a visibilidade (pública/privada) de uma técnica.

**Endpoint:** `endpoint/tecnicas/visibilidade.php`

**Método:** POST

**Formato:** JSON

**Corpo da Requisição:**
```json
{
  "id": 123,
  "publica": true
}
```

**Exemplo de Requisição no Postman:**
1. Selecione o método POST
2. Insira a URL `sua-api.com/endpoint/tecnicas/visibilidade.php`
3. Na aba "Headers", adicione:
   - `Authorization: Bearer seu_token_aqui`
   - `Content-Type: application/json`
4. Na aba "Body", selecione "raw" e tipo "JSON"
5. Insira o JSON com o ID e o novo status de visibilidade

### 9. Criar posição

**Endpoint:** `endpoint/tecnicas/adicionar-posicoes.php`

**Método:** POST

**Formato:** JSON

**Corpo da Requisição:**
```json
{
  "nome": "nome posição",
}
```

**Exemplo de Requisição no Postman:**
1. Selecione o método POST
2. Insira a URL `sua-api.com/endpoint/tecnicas/adicionar-posicoes.php`
3. Na aba "Headers", adicione:
   - `Authorization: Bearer seu_token_aqui`
   - `Content-Type: application/json`
4. Na aba "Body", selecione "raw" e tipo "JSON"
5. Insira o JSON com o ID e o novo status de visibilidade


### 10. Exluir posição

**Endpoint:** `endpoint/tecnicas/excluir-posicoes.php`

**Método:** POST

**Formato:** JSON

**Corpo da Requisição:**
```json
{
  "nome": "nome posição",
}
```

**Exemplo de Requisição no Postman:**
1. Selecione o método POST
2. Insira a URL `sua-api.com/endpoint/tecnicas/excluir-posicoes.php`
3. Na aba "Headers", adicione:
   - `Authorization: Bearer seu_token_aqui`
   - `Content-Type: application/json`
4. Na aba "Body", selecione "raw" e tipo "JSON"
5. Insira o JSON com o ID e o novo status de visibilidade

