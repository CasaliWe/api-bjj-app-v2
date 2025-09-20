**URL Base:** `http://localhost/api-bjj-app-v2` (ajuste conforme seu ambiente)

**Cabeçalhos comuns para todas as requisições:**
- `Content-Type: application/json`
- `Authorization: Bearer SEU_TOKEN_AQUI` (substitua SEU_TOKEN_AQUI por um token válido)

### Obter Perfil de Usuário

**Endpoint:** `endpoint/user/getProfile.php`

**Método:** GET

**Parâmetros de Query:**
- `bjj_id` (obrigatório): ID do usuário cujo perfil deseja visualizar

**Resposta de Sucesso:**
```json
{
  "success": true,
  "message": "Perfil obtido com sucesso",
  "data": {
    "profile": {
      "id": "123",
      "bjj_id": "j1234",
      "nome": "João Silva",
      "email": "joao.silva@example.com",
      "imagem": "joao-silva.jpg",
      "whatsapp": "(54) 9 9999-9999",
      "idade": 30,
      "peso": 80,
      "faixa": "Azul",
      "graduacao": "3 graus",
      "competidor": "Sim",
      "estilo": "Competitivo",
      "guarda": "De La Riva",
      "posicao": "100kg",
      "finalizacao": "Armlock",
      "academia": "BJJ Elite",
      "cidade": "Porto Alegre",
      "estado": "RS",
      "pais": "Brasil",
      "instagram": "joaosilva",
      "youtube": "joaosilvabjj",
      "tiktok": "joaosilvabjj",
      "bio": "Praticante de Jiu-Jitsu há 5 anos."
    }
  }
}
```

### Obter Treinos Públicos

**Endpoint:** `endpoint/user/getPublicTrainings.php`

**Método:** GET

**Parâmetros de Query:**
- `bjj_id` (obrigatório): ID do usuário cujos treinos deseja visualizar
- `pagina` (opcional, default: 1): Número da página para paginação
- `limite` (opcional, default: 10): Número de itens por página

**Resposta de Sucesso:**
```json
{
  "success": true,
  "message": "Treinos públicos obtidos com sucesso",
  "data": {
    "treinos": [
      {
        "id": "456",
        "tipo": "gi",
        "diaSemana": "segunda",
        "horario": "19:30",
        "numeroAula": 42,
        "data": "2025-09-10",
        "imagens": [
          "https://url-da-imagem-1.jpg",
          "https://url-da-imagem-2.jpg"
        ],
        "observacoes": "Texto com as observações do treino",
        "isPublico": true,
        "usuario": {
          "nome": "João Silva",
          "imagem": "joao-silva.jpg",
          "faixa": "Azul"
        }
      }
    ],
    "pagination": {
      "currentPage": 1,
      "totalPages": 5,
      "totalItems": 48,
      "itemsPerPage": 10
    }
  }
}
```

### Obter Competições Públicas

**Endpoint:** `endpoint/user/getPublicCompetitions.php`

**Método:** GET

**Parâmetros de Query:**
- `bjj_id` (obrigatório): ID do usuário cujas competições deseja visualizar
- `pagina` (opcional, default: 1): Número da página para paginação
- `limite` (opcional, default: 10): Número de itens por página

**Resposta de Sucesso:**
```json
{
  "success": true,
  "message": "Competições públicas obtidas com sucesso",
  "data": {
    "competicoes": [
      {
        "id": "789",
        "nome": "Campeonato Brasileiro de Jiu-Jitsu",
        "data": "2025-10-15",
        "local": "São Paulo, SP",
        "modalidade": "gi",
        "categoria": "Adulto",
        "resultado": "Ouro",
        "imagens": [
          "https://url-da-imagem-1.jpg",
          "https://url-da-imagem-2.jpg"
        ],
        "observacoes": "Texto com as observações da competição",
        "isPublico": true,
        "usuario": {
          "nome": "João Silva",
          "imagem": "joao-silva.jpg",
          "faixa": "Azul"
        }
      }
    ],
    "pagination": {
      "currentPage": 1,
      "totalPages": 2,
      "totalItems": 12,
      "itemsPerPage": 10
    }
  }
}
```

### Obter Técnicas Públicas

**Endpoint:** `endpoint/user/getPublicTechniques.php`

**Método:** GET

**Parâmetros de Query:**
- `bjj_id` (obrigatório): ID do usuário cujas técnicas deseja visualizar
- `pagina` (opcional, default: 1): Número da página para paginação
- `limite` (opcional, default: 10): Número de itens por página

**Resposta de Sucesso:**
```json
{
  "success": true,
  "message": "Técnicas públicas obtidas com sucesso",
  "data": {
    "tecnicas": [
      {
        "id": "101",
        "nome": "Armlock da Guarda",
        "categoria": "guardeiro",
        "posicao": "Guarda Fechada",
        "passos": ["Passo 1", "Passo 2", "Passo 3"],
        "observacoes": ["Observação 1", "Observação 2"],
        "nota": 5,
        "video": "https://www.youtube.com/watch?v=example",
        "video_url": "/uploads/videos/tecnica_1_video.mp4",
        "video_poster": "/uploads/videos/tecnica_1_poster.jpg",
        "destacado": false,
        "publica": true,
        "criado_em": "2025-09-01T10:00:00",
        "atualizado_em": "2025-09-05T15:30:00",
        "usuario": {
          "nome": "João Silva",
          "imagem": "joao-silva.jpg",
          "faixa": "Azul"
        }
      }
    ],
    "pagination": {
      "currentPage": 1,
      "totalPages": 3,
      "totalItems": 25,
      "itemsPerPage": 10
    }
  }
}
```
