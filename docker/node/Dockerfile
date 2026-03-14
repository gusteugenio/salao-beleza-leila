FROM node:20

WORKDIR /app

# Copia só package.json para instalar dependências (cache de npm)
COPY frontend/package*.json ./

RUN npm install

# Copia o restante do frontend
COPY frontend/ .

EXPOSE 4200

CMD ["npx", "ng", "serve", "--host", "0.0.0.0", "--port", "4200", "--poll", "2000"]
