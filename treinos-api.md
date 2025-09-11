# Documentação da API de Treinos

Esta documentação descreve todas as endpoints da API de treinos, seus parâmetros, respostas esperadas e estrutura de dados.

## Índice

1. [Visão Geral](#visão-geral)
2. [Endpoints](#endpoints)
   - [Listar Treinos](#listar-treinos)
   - [Listar Treinos da Comunidade](#listar-treinos-da-comunidade)
   - [Criar Treino](#criar-treino)
   - [Atualizar Treino](#atualizar-treino)
   - [Excluir Treino](#excluir-treino)
   - [Alterar Visibilidade](#alterar-visibilidade)
   - [Upload de Imagens](#upload-de-imagens)
3. [Estrutura de Dados](#estrutura-de-dados)
4. [Esquema do Banco de Dados](#esquema-do-banco-de-dados)
5. [Códigos de Erro](#códigos-de-erro)

## Visão Geral

A API de Treinos permite gerenciar treinos de jiu-jitsu, incluindo:

- Listar treinos pessoais com filtros (tipo, dia da semana) e paginação
- Listar treinos públicos da comunidade com paginação
- Criar, atualizar e excluir treinos
- Alterar a visibilidade de treinos (público/privado)
- Fazer upload de imagens para treinos

Todas as requisições requerem um token JWT válido no cabeçalho `Authorization`. O formato esperado é:

```
Authorization: Bearer {token}
```

## Endpoints

### Listar Treinos

Retorna a lista de treinos do usuário logado com suporte a filtros e paginação.

**URL**: `endpoint/treinos/listar.php`

**Método**: POST

**Autenticação**: Requerida

**Corpo da Requisição**:
```json
{
  "pagina": 1,
  "limite": 20,
  "tipo": "gi",
  "diaSemana": "segunda"
}
```

**Parâmetros**:
- `pagina` (number): Número da página atual (começa em 1)
- `limite` (number): Quantidade de itens por página
- `tipo` (string, opcional): Filtro por tipo de treino ("gi", "nogi" ou "todos")
- `diaSemana` (string, opcional): Filtro por dia da semana ("segunda", "terca", etc, ou "todos")

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

**Método**: POST

**Autenticação**: Requerida

**Corpo da Requisição**:
```json
{
  "pagina": 1,
  "limite": 20
}
```

**Parâmetros**:
- `pagina` (number): Número da página atual (começa em 1)
- `limite` (number): Quantidade de itens por página

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

**Autenticação**: Requerida

**Corpo da Requisição**:
```json
{
  "numeroAula": 1,
  "tipo": "gi",
  "diaSemana": "segunda",
  "horario": "19:30",
  "data": "2025-09-10",
  "observacoes": "Texto com as observações do treino",
  "isPublico": false
}
```

**Parâmetros**:
- `numeroAula` (number): Número sequencial da aula
- `tipo` (string): Tipo de treino ("gi" ou "nogi")
- `diaSemana` (string): Dia da semana ("segunda", "terca", etc.)
- `horario` (string): Horário do treino no formato HH:MM
- `data` (string): Data do treino no formato YYYY-MM-DD
- `observacoes` (string): Texto com observações sobre o treino
- `isPublico` (boolean): Indica se o treino é público (true) ou privado (false)

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

**Autenticação**: Requerida

**Corpo da Requisição**:
```json
{
  "id": 1,
  "numeroAula": 1,
  "tipo": "gi",
  "diaSemana": "segunda",
  "horario": "19:30",
  "data": "2025-09-10",
  "observacoes": "Texto com as observações do treino",
  "isPublico": false
}
```

**Parâmetros**:
- `id` (number): ID do treino a ser atualizado
- `numeroAula` (number): Número sequencial da aula
- `tipo` (string): Tipo de treino ("gi" ou "nogi")
- `diaSemana` (string): Dia da semana ("segunda", "terca", etc.)
- `horario` (string): Horário do treino no formato HH:MM
- `data` (string): Data do treino no formato YYYY-MM-DD
- `observacoes` (string): Texto com observações sobre o treino
- `isPublico` (boolean): Indica se o treino é público (true) ou privado (false)

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

## Estrutura de Dados

### Treino

| Campo       | Tipo      | Descrição                                                |
|-------------|-----------|----------------------------------------------------------|
| id          | number    | Identificador único do treino                            |
| numeroAula  | number    | Número sequencial da aula                                |
| tipo        | string    | Tipo de treino: "gi" ou "nogi"                           |
| diaSemana   | string    | Dia da semana: "segunda", "terca", etc.                  |
| horario     | string    | Horário no formato HH:MM                                 |
| data        | string    | Data no formato YYYY-MM-DD                               |
| imagens     | string[]  | Array de URLs das imagens do treino                      |
| observacoes | string    | Texto com observações sobre o treino                     |
| isPublico   | boolean   | Indica se o treino é público (true) ou privado (false)   |
| usuario     | Object    | Dados do usuário (apenas para treinos públicos)          |

### Usuário (informações resumidas para treinos públicos)

| Campo | Tipo   | Descrição                                    |
|-------|--------|----------------------------------------------|
| nome  | string | Nome do usuário                              |
| imagem| string | URL da imagem de perfil                      |
| faixa | string | Faixa do usuário (branca, azul, roxa, etc.) |

### Paginação

| Campo         | Tipo   | Descrição                               |
|---------------|--------|------------------------------------------|
| currentPage   | number | Número da página atual                   |
| totalPages    | number | Número total de páginas                  |
| totalItems    | number | Número total de itens                    |
| itemsPerPage  | number | Número de itens por página               |

## Esquema do Banco de Dados

Sugestão de esquema de banco de dados para implementação:

```sql
CREATE TABLE treinos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  numero_aula INT NOT NULL,
  tipo ENUM('gi', 'nogi') NOT NULL,
  dia_semana ENUM('segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo') NOT NULL,
  horario TIME NOT NULL,
  data DATE NOT NULL,
  observacoes TEXT,
  is_publico BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES user(id) ON DELETE CASCADE
);

CREATE TABLE treinos_imagens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  treino_id INT NOT NULL,
  url VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (treino_id) REFERENCES treinos(id) ON DELETE CASCADE
);
```

## Códigos de Erro

| HTTP Status | Código | Mensagem                              | Descrição                                    |
|-------------|--------|-----------------------------------------|----------------------------------------------|
| 400         | 1001   | "Parâmetros inválidos ou ausentes"      | Dados da requisição inválidos ou incompletos |
| 401         | 1002   | "Não autorizado"                         | Token de autenticação inválido ou expirado   |
| 403         | 1003   | "Acesso negado"                         | Usuário não tem permissão para esta operação |
| 404         | 1004   | "Treino não encontrado"                 | O treino solicitado não existe               |
| 500         | 1005   | "Erro interno do servidor"              | Erro não especificado no processamento       |

## Observações de Implementação

1. Todas as endpoints devem validar o token JWT e verificar se o usuário tem permissão para acessar o recurso solicitado
2. Ao excluir um treino, todas as suas imagens associadas devem ser excluídas também
3. O upload de imagens deve validar o tipo de arquivo (apenas imagens), tamanho máximo e quantidade máxima de arquivos
4. A alteração de visibilidade de um treino para público só deve ser permitida se o treino tiver pelo menos uma imagem
5. Os treinos da comunidade devem mostrar apenas treinos marcados como públicos
6. Os nomes dos arquivos de imagem devem ser gerados usando UUIDs para evitar conflitos
