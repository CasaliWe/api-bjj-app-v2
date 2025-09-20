**URL Base:** `http://localhost/api-bjj-app-v2` (ajuste conforme seu ambiente)

**Cabeçalhos comuns para todas as requisições:**
- `Content-Type: application/json`
- `Authorization: Bearer SEU_TOKEN_AQUI` (substitua SEU_TOKEN_AQUI por um token válido)

### 1. Listar Treinos

Lista os treinos do usuário autenticado com opções de filtro e paginação.

**URL:** `/endpoint/treinos/listar.php`  
**Método:** `GET`  
**Parâmetros de Query:**
- `pagina` (opcional): Número da página (padrão: 1)
- `limite` (opcional): Itens por página (padrão: 20)
- `tipo` (opcional): Filtro por tipo de treino (ex: 'kimono', 'nogi', ou 'todos')
- `diaSemana` (opcional): Filtro por dia da semana (ex: 'segunda', 'terca', ou 'todos')

**Exemplo de Requisição:**
```
GET /endpoint/treinos/listar.php?pagina=1&limite=10&tipo=kimono
```

**Resposta de Sucesso:**
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
        "horario": "19:00",
        "data": "2023-09-10",
        "observacoes": "Treino de guarda",
        "isPublico": true,
        "imagens": [
          "https://seu-site.com/admin/assets/imagens/arquivos/treinos/treino_123456_1631234567.webp"
        ],
        "usuario": {
          "nome": "João Silva",
          "imagem": "perfil_123.webp",
          "faixa": "marrom"
        }
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

### 2. Listar Treinos da Comunidade

Lista os treinos públicos de todos os usuários.

**URL:** `/endpoint/treinos/comunidade.php`  
**Método:** `GET`  
**Parâmetros de Query:**
- `pagina` (opcional): Número da página (padrão: 1)
- `limite` (opcional): Itens por página (padrão: 20)

**Exemplo de Requisição:**
```
GET /endpoint/treinos/comunidade.php?pagina=1&limite=10
```

**Resposta:** Similar ao endpoint de listar treinos.

### 3. Criar Treino

Cria um novo treino com dados e imagens opcionais.

**URL:** `/endpoint/treinos/criar.php`  
**Método:** `POST`  
**Tipo de Conteúdo:** `multipart/form-data`  
**Parâmetros:**
- `numeroAula` (obrigatório): Número da aula (inteiro)
- `tipo` (obrigatório): Tipo de treino (string)
- `diaSemana` (obrigatório): Dia da semana (string)
- `horario` (obrigatório): Horário do treino (string no formato HH:MM)
- `data` (obrigatório): Data do treino (string no formato YYYY-MM-DD)
- `observacoes` (opcional): Observações sobre o treino (string)
- `isPublico` (opcional): Se o treino é público (boolean, padrão: false)
- `imagens[]` (opcional): Array de arquivos de imagem para upload

**Exemplo de Requisição com curl:**
```bash
curl -X POST \
  'https://seu-site.com/endpoint/treinos/criar.php' \
  -H 'Authorization: Bearer seu_token_aqui' \
  -F 'numeroAula=1' \
  -F 'tipo=gi' \
  -F 'diaSemana=segunda' \
  -F 'horario=19:00' \
  -F 'data=2023-09-10' \
  -F 'observacoes=Treino de guarda' \
  -F 'isPublico=true' \
  -F 'imagens[]=@/caminho/para/imagem1.jpg' \
  -F 'imagens[]=@/caminho/para/imagem2.png'
```

**Exemplo de Requisição com Postman:**
1. Selecione o método `POST`
2. Insira a URL completa
3. Na aba `Headers`, adicione o cabeçalho `Authorization: Bearer seu_token_aqui`
4. Na aba `Body`, selecione `form-data`
5. Adicione todos os campos de texto
6. Para as imagens, adicione um campo chamado `imagens[]` com tipo `File` e selecione o arquivo. Para múltiplas imagens, adicione vários campos com o mesmo nome `imagens[]`.

**Resposta de Sucesso:**
```json
{
  "success": true,
  "message": "Treino criado com sucesso",
  "data": {
    "id": 1,
    "numeroAula": 1,
    "tipo": "nogi",
    "diaSemana": "segunda",
    "horario": "19:00",
    "data": "2023-09-10",
    "observacoes": "Treino de guarda",
    "isPublico": true,
    "imagens": [
      "https://seu-site.com/admin/assets/imagens/arquivos/treinos/treino_123456_1631234567.webp",
      "https://seu-site.com/admin/assets/imagens/arquivos/treinos/treino_654321_1631234568.webp"
    ]
  }
}
```

### 4. Atualizar Treino

Atualiza um treino existente e permite adicionar novas imagens.

**URL:** `/endpoint/treinos/atualizar.php`  
**Método:** `POST` (Usa POST em vez de PUT para suportar upload de arquivos)  
**Tipo de Conteúdo:** `multipart/form-data`  
**Parâmetros:**
- `id` (obrigatório): ID do treino a ser atualizado
- `numeroAula` (obrigatório): Número da aula (inteiro)
- `tipo` (obrigatório): Tipo de treino (string)
- `diaSemana` (obrigatório): Dia da semana (string)
- `horario` (obrigatório): Horário do treino (string no formato HH:MM)
- `data` (obrigatório): Data do treino (string no formato YYYY-MM-DD)
- `observacoes` (opcional): Observações sobre o treino (string)
- `isPublico` (opcional): Se o treino é público (boolean, padrão: false)
- `imagens[]` (opcional): Array de arquivos de imagem para adicionar ao treino

**Exemplo de Requisição com curl:**
```bash
curl -X POST \
  'https://seu-site.com/endpoint/treinos/atualizar.php' \
  -H 'Authorization: Bearer seu_token_aqui' \
  -F 'id=1' \
  -F 'numeroAula=2' \
  -F 'tipo=gi' \
  -F 'diaSemana=terca' \
  -F 'horario=20:00' \
  -F 'data=2023-09-11' \
  -F 'observacoes=Treino de passagem de guarda' \
  -F 'isPublico=true' \
  -F 'imagens[]=@/caminho/para/imagem3.jpg'
```

**Resposta de Sucesso:** Similar ao endpoint de criar treino.

### 5. Excluir Treino

Exclui um treino existente.

**URL:** `/endpoint/treinos/excluir.php`  
**Método:** `POST`  
**Tipo de Conteúdo:** `application/json`  
**Corpo da Requisição:**
```json
{
  "id": 1
}
```

**Resposta de Sucesso:**
```json
{
  "success": true,
  "message": "Treino excluído com sucesso",
  "data": null
}
```

### 6. Alterar Visibilidade

Altera a visibilidade (público/privado) de um treino.

**URL:** `/endpoint/treinos/visibilidade.php`  
**Método:** `POST`  
**Tipo de Conteúdo:** `application/json`  
**Corpo da Requisição:**
```json
{
  "id": 1,
  "isPublico": true
}
```

**Resposta de Sucesso:**
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


### 7. Remover Imagem

Remove uma imagem específica de um treino.

**URL:** `/endpoint/treinos/remover-imagem.php`  
**Método:** `POST`  
**Tipo de Conteúdo:** `application/json`  
**Corpo da Requisição:**
```json
{
  "treinoId": 1,
  "imagemId": 5
}
```

**Resposta de Sucesso:**
```json
{
  "success": true,
  "message": "Imagem removida com sucesso",
  "data": {
    "imagem_removida": 5
  }
}
```