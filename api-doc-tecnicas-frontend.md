# Como o frontend deve enviar largura e altura do vídeo para o poster

## Objetivo
Permitir que o poster gerado pela API tenha a mesma proporção do vídeo enviado (vertical ou horizontal), sem depender de ferramentas externas.

## O que mudou na API
- As rotas de criação (`criar.php`) e atualização (`atualizar.php`) de técnica agora aceitam dois campos opcionais no FormData:
  - `video_width`: largura do vídeo (em pixels)
  - `video_height`: altura do vídeo (em pixels)
- Esses campos **não são salvos no banco**. Servem apenas para gerar o poster proporcional ao vídeo.

## Como enviar pelo frontend
1. Ao selecionar o vídeo, obtenha as dimensões usando JavaScript:
   ```js
   const video = document.createElement('video');
   video.preload = 'metadata';
   video.onloadedmetadata = function() {
     const width = video.videoWidth;
     const height = video.videoHeight;
     // Adicione ao FormData
     formData.append('video_width', width);
     formData.append('video_height', height);
   };
   video.src = URL.createObjectURL(file);
   ```
2. Envie junto com os outros campos do FormData:
   ```js
   formData.append('videoFile', file);
   formData.append('video_width', width);
   formData.append('video_height', height);
   // ...outros campos
   fetch('URL_DA_API', { method: 'POST', body: formData, headers: { Authorization: 'Bearer ...' } })
   ```

## Observações
- Se não enviar os campos, o poster será gerado no formato padrão 640x360 (horizontal).
- Não é necessário enviar esses campos se não quiser garantir a proporção.
- Não precisa se preocupar com banco de dados, é só informativo para o PHP.

## Recomendações
- Sempre envie `video_width` e `video_height` para garantir que o poster fique igual ao vídeo, seja vertical ou horizontal.
- Teste com vídeos de diferentes proporções para validar o resultado.

---
Dúvidas ou ajustes, só avisar!