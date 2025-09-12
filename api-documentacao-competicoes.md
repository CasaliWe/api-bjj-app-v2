# Documentação da API de Competições

Este documento descreve os endpoints da API de Competições e como testá-los usando o Postman.

## Requisitos Gerais

- Todas as requisições (exceto autenticação) exigem um token de autenticação válido no cabeçalho `Authorization: Bearer [token]`.
- Os tokens podem ser obtidos através do endpoint de login existente.
- Para requisições POST com envio de arquivos, utilize o formato `multipart/form-data`.
- Para requisições POST sem envio de arquivos, utilize o formato `application/json`.

## Endpoints Disponíveis

### 1. Listar Competições do Usuário

Lista todas as competições do usuário autenticado, com suporte a paginação e filtros.

**Endpoint:** `GET /endpoint/competicoes/listar.php`

**Parâmetros de Query:**
- `pagina` (opcional, padrão: 1): Número da página para paginação
- `limite` (opcional, padrão: 10): Quantidade de itens por página
- `modalidade` (opcional): Filtrar por modalidade ('gi' ou 'nogi')
- `busca` (opcional): Termo para buscar no nome do evento, cidade, colocação ou observações

**Exemplo de Teste no Postman:**
1. Crie uma requisição GET
2. URL: `{{base_url}}/endpoint/competicoes/listar.php?pagina=1&limite=10&modalidade=gi&busca=São Paulo`
3. Adicione o header `Authorization: Bearer {{seu_token}}`
4. Envie a requisição

**Resposta de Sucesso (200 OK):**
```json
{
  "status": "success",
  "message": "Competições listadas com sucesso",
  "data": {
    "competicoes": [
      {
        "id": 1,
        "nomeEvento": "Copa São Paulo de Jiu-Jitsu",
        "cidade": "São Paulo, SP",
        "data": "2025-05-15",
        "modalidade": "gi",
        "colocacao": "1º lugar",
        "numeroLutas": 4,
        "numeroVitorias": 4,
        "numeroDerrotas": 0,
        "numeroFinalizacoes": 2,
        "observacoes": "Consegui finalizar na semifinal com um armlock e na final com um triângulo.",
        "isPublico": true,
        "imagens": [
          {
            "id": 1,
            "url": "https://exemplo.com/imagem1.jpg"
          }
        ]
      }
    ],
    "pagination": {
      "currentPage": 1,
      "totalPages": 5,
      "totalItems": 45,
      "itemsPerPage": 10
    }
  }
}
```

### 2. Listar Competições da Comunidade

Lista todas as competições públicas de todos os usuários, com suporte a paginação e filtros.

**Endpoint:** `GET /endpoint/competicoes/comunidade.php`

**Parâmetros de Query:**
- `pagina` (opcional, padrão: 1): Número da página para paginação
- `limite` (opcional, padrão: 10): Quantidade de itens por página
- `modalidade` (opcional): Filtrar por modalidade ('gi' ou 'nogi')
- `busca` (opcional): Termo para buscar no nome do evento, cidade, colocação ou observações

**Exemplo de Teste no Postman:**
1. Crie uma requisição GET
2. URL: `{{base_url}}/endpoint/competicoes/comunidade.php?pagina=1&limite=10`
3. Adicione o header `Authorization: Bearer {{seu_token}}`
4. Envie a requisição

**Resposta de Sucesso (200 OK):**
```json
{
  "status": "success",
  "message": "Competições da comunidade listadas com sucesso",
  "data": {
    "competicoes": [
      {
        "id": 1,
        "nomeEvento": "Copa São Paulo de Jiu-Jitsu",
        "cidade": "São Paulo, SP",
        "data": "2025-05-15",
        "modalidade": "gi",
        "colocacao": "1º lugar",
        "numeroLutas": 4,
        "numeroVitorias": 4,
        "numeroDerrotas": 0,
        "numeroFinalizacoes": 2,
        "observacoes": "Consegui finalizar na semifinal com um armlock e na final com um triângulo.",
        "usuario": {
          "id": 1,
          "nome": "João Silva",
          "foto": "https://exemplo.com/foto-usuario.jpg",
          "faixa": "roxa"
        },
        "imagens": [
          {
            "id": 1,
            "url": "https://exemplo.com/imagem1.jpg"
          }
        ]
      }
    ],
    "pagination": {
      "currentPage": 1,
      "totalPages": 5,
      "totalItems": 45,
      "itemsPerPage": 10
    }
  }
}
```

### 3. Criar Nova Competição

Cria uma nova competição para o usuário autenticado, com suporte a upload de múltiplas imagens.

**Endpoint:** `POST /endpoint/competicoes/criar.php`

**Formato:** `multipart/form-data`

**Campos do formulário:**
- `nomeEvento` (obrigatório): Nome do evento
- `cidade`: Cidade/Estado onde ocorreu
- `data`: Data do evento (formato: YYYY-MM-DD)
- `modalidade`: 'gi' (com kimono) ou 'nogi' (sem kimono)
- `colocacao`: Posição obtida
- `numeroLutas`: Número total de lutas
- `numeroVitorias`: Número de vitórias
- `numeroDerrotas`: Número de derrotas
- `numeroFinalizacoes`: Número de finalizações
- `observacoes`: Observações sobre a competição
- `isPublico`: 1 (público) ou 0 (privado)
- `imagens[]`: Arrays de arquivos de imagem (múltiplos arquivos)

**Exemplo de Teste no Postman:**
1. Crie uma requisição POST
2. URL: `{{base_url}}/endpoint/competicoes/criar.php`
3. Adicione o header `Authorization: Bearer {{seu_token}}`
4. Selecione a opção "form-data"
5. Adicione os campos de texto conforme listado acima
6. Para adicionar imagens, adicione campos chamados `imagens[]` com o tipo "File" e selecione os arquivos a serem enviados
7. Envie a requisição

**Resposta de Sucesso (200 OK):**
```json
{
  "status": "success",
  "message": "Competição criada com sucesso",
  "data": {
    "id": 1,
    "nomeEvento": "Copa São Paulo de Jiu-Jitsu",
    "cidade": "São Paulo, SP",
    "data": "2025-05-15",
    "modalidade": "gi",
    "colocacao": "1º lugar",
    "numeroLutas": 4,
    "numeroVitorias": 4,
    "numeroDerrotas": 0,
    "numeroFinalizacoes": 2,
    "observacoes": "Consegui finalizar na semifinal com um armlock e na final com um triângulo.",
    "isPublico": true,
    "imagens": [
      {
        "id": 1,
        "url": "https://exemplo.com/imagem1.jpg"
      }
    ]
  }
}
```

### 4. Atualizar Competição

Atualiza uma competição existente do usuário autenticado, com suporte a adição e remoção de imagens.

**Endpoint:** `POST /endpoint/competicoes/atualizar.php`

**Formato:** `multipart/form-data`

**Campos do formulário:**
- `id` (obrigatório): ID da competição
- `nomeEvento` (obrigatório): Nome do evento
- `cidade`: Cidade/Estado onde ocorreu
- `data`: Data do evento (formato: YYYY-MM-DD)
- `modalidade`: 'gi' (com kimono) ou 'nogi' (sem kimono)
- `colocacao`: Posição obtida
- `numeroLutas`: Número total de lutas
- `numeroVitorias`: Número de vitórias
- `numeroDerrotas`: Número de derrotas
- `numeroFinalizacoes`: Número de finalizações
- `observacoes`: Observações sobre a competição
- `isPublico`: 1 (público) ou 0 (privado)
- `imagens[]`: Arrays de novos arquivos de imagem (múltiplos arquivos)
- `imagensExistentes[]`: Array com IDs das imagens a manter

**Exemplo de Teste no Postman:**
1. Crie uma requisição POST
2. URL: `{{base_url}}/endpoint/competicoes/atualizar.php`
3. Adicione o header `Authorization: Bearer {{seu_token}}`
4. Selecione a opção "form-data"
5. Adicione os campos de texto conforme listado acima
6. Para adicionar novas imagens, adicione campos chamados `imagens[]` com o tipo "File" e selecione os arquivos a serem enviados
7. Para manter imagens existentes, adicione campos chamados `imagensExistentes[]` com os IDs das imagens que deseja manter
8. Envie a requisição

**Resposta de Sucesso (200 OK):**
```json
{
  "status": "success",
  "message": "Competição atualizada com sucesso",
  "data": {
    "id": 1,
    "nomeEvento": "Copa São Paulo de Jiu-Jitsu (Atualizado)",
    "cidade": "São Paulo, SP",
    "data": "2025-05-15",
    "modalidade": "gi",
    "colocacao": "1º lugar",
    "numeroLutas": 4,
    "numeroVitorias": 4,
    "numeroDerrotas": 0,
    "numeroFinalizacoes": 2,
    "observacoes": "Observações atualizadas sobre a competição.",
    "isPublico": true,
    "imagens": [
      {
        "id": 1,
        "url": "https://exemplo.com/imagem1.jpg"
      },
      {
        "id": 3,
        "url": "https://exemplo.com/imagem3-nova.jpg"
      }
    ]
  }
}
```

### 5. Excluir Competição

Exclui uma competição existente do usuário autenticado, incluindo todas as suas imagens.

**Endpoint:** `POST /endpoint/competicoes/excluir.php`

**Formato:** `application/json`

**Corpo da requisição:**
```json
{
  "id": 1
}
```

**Exemplo de Teste no Postman:**
1. Crie uma requisição POST
2. URL: `{{base_url}}/endpoint/competicoes/excluir.php`
3. Adicione o header `Authorization: Bearer {{seu_token}}`
4. Adicione o header `Content-Type: application/json`
5. No corpo da requisição, adicione o JSON com o ID da competição a ser excluída
6. Envie a requisição

**Resposta de Sucesso (200 OK):**
```json
{
  "status": "success",
  "message": "Competição excluída com sucesso"
}
```

### 6. Alterar Visibilidade da Competição

Altera a visibilidade (pública/privada) de uma competição existente do usuário autenticado.

**Endpoint:** `POST /endpoint/competicoes/visibilidade.php`

**Formato:** `application/json`

**Corpo da requisição:**
```json
{
  "id": 1,
  "isPublico": true
}
```

**Exemplo de Teste no Postman:**
1. Crie uma requisição POST
2. URL: `{{base_url}}/endpoint/competicoes/visibilidade.php`
3. Adicione o header `Authorization: Bearer {{seu_token}}`
4. Adicione o header `Content-Type: application/json`
5. No corpo da requisição, adicione o JSON com o ID da competição e a nova visibilidade
6. Envie a requisição

**Resposta de Sucesso (200 OK):**
```json
{
  "status": "success",
  "message": "Visibilidade da competição alterada com sucesso",
  "data": {
    "id": 1,
    "isPublico": true
  }
}
```

### 7. Remover Imagem de Competição

Remove uma imagem específica de uma competição existente do usuário autenticado.

**Endpoint:** `POST /endpoint/competicoes/remover-imagem.php`

**Formato:** `application/json`

**Corpo da requisição:**
```json
{
  "competicaoId": 1,
  "imagemId": 2
}
```

**Exemplo de Teste no Postman:**
1. Crie uma requisição POST
2. URL: `{{base_url}}/endpoint/competicoes/remover-imagem.php`
3. Adicione o header `Authorization: Bearer {{seu_token}}`
4. Adicione o header `Content-Type: application/json`
5. No corpo da requisição, adicione o JSON com o ID da competição e o ID da imagem a ser removida
6. Envie a requisição

**Resposta de Sucesso (200 OK):**
```json
{
  "status": "success",
  "message": "Imagem removida com sucesso",
  "data": {
    "competicaoId": 1,
    "imagensRestantes": [
      {
        "id": 1,
        "url": "https://exemplo.com/imagem1.jpg"
      }
    ]
  }
}
```

## Códigos de Erro

Os endpoints podem retornar os seguintes códigos de erro:

- `400 Bad Request`: Requisição inválida ou parâmetros faltando
- `401 Unauthorized`: Token de autenticação inválido ou expirado
- `403 Forbidden`: Usuário não tem permissão para acessar o recurso
- `404 Not Found`: Recurso não encontrado
- `405 Method Not Allowed`: Método HTTP não permitido
- `500 Internal Server Error`: Erro interno do servidor

Exemplo de resposta de erro:
```json
{
  "status": "error",
  "message": "Competição não encontrada ou você não tem permissão para editá-la",
  "errorCode": 404,
  "data": null
}
```

## Configuração do Postman

Para facilitar os testes, você pode criar uma coleção no Postman com as seguintes variáveis de ambiente:

- `base_url`: URL base da sua API (ex: http://localhost/api-bjj-app-v2)
- `token`: Token de autenticação obtido ao fazer login

Também recomenda-se criar uma pasta específica para os endpoints de competições na sua coleção, organizando as requisições conforme a documentação acima.