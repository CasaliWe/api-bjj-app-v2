# Documentação da API de Técnicas

Esta documentação descreve os endpoints disponíveis para o gerenciamento de técnicas de BJJ na API. Use estas instruções para testar os endpoints via Postman ou integrá-los ao seu frontend.

## Pré-requisitos

Para usar estes endpoints, você precisa:

1. Ter um usuário registrado no sistema
2. Obter um token de acesso válido (via login)
3. Incluir o token em todas as requisições no formato `Authorization: Bearer {seu_token}`

## Endpoints Disponíveis

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

## Respostas

Todos os endpoints retornam respostas no formato JSON com a seguinte estrutura:

```json
{
  "success": true|false,
  "message": "Mensagem de sucesso ou erro",
  "data": {
    // Dados específicos do endpoint, ou null em caso de erro
  }
}
```

## Tratamento de Erros

Em caso de erro, a API retornará:

1. Um código de status HTTP apropriado (400, 401, 404, 500, etc.)
2. Um objeto JSON com:
   - `success`: false
   - `message`: Descrição do erro
   - `data`: null

## Observações Importantes

1. Os arquivos de vídeo são limitados a 20MB e formato MP4
2. Os vídeos e posters são armazenados em `admin/assets/imagens/arquivos/tecnicas/`
3. A API automaticamente gera um poster a partir do primeiro frame do vídeo
4. Para arrays de passos e observações, envie como strings JSON válidas
5. Para vídeos verticais, envie os parâmetros `video_width` e `video_height` para que o poster seja gerado na proporção correta

## Exemplo de Fluxo Completo

1. Listar posições disponíveis
2. Criar uma nova técnica com vídeo
3. Listar técnicas do usuário para confirmar a criação
4. Atualizar uma técnica
5. Alternar destaque ou visibilidade
6. Listar técnicas da comunidade
7. Excluir uma técnica