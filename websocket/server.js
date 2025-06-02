require('dotenv').config();

const fs = require('fs');
const https = require('https');
const { Server } = require("socket.io");
const axios = require('axios');

// ssl keys from .env
const privateKey = fs.readFileSync(process.env.SSL_KEY_PATH, 'utf8');
const certificate = fs.readFileSync(process.env.SSL_CERT_PATH, 'utf8');

const credentials = { key: privateKey, cert: certificate };
const httpsServer = https.createServer(credentials);

const io = new Server(httpsServer, {
  cors: {
    origin: process.env.CORS_ORIGIN,
    methods: ["GET", "POST"]
  }
});

const API_URL = process.env.API_URL;

io.on("connection", (socket) => {
  console.log("A user connected:", socket.id);

  socket.on("sendMessage", async (message) => {
    try {
      const response = await axios.post(`${API_URL}/vidgets/messenger/create-message`, {
        ...message,
      });

      if (response.data.success) {
        io.emit("receiveMessage", {
          ...message,
          chat_id: response.data.chatId,
          created_at: response.data.created_at,
          token: response.data.token,
        });
      } else {
        console.error('Failed to save message:', response.data.errors);
      }
    } catch (error) {
      console.error('Error saving message:', error.response?.data || error.message);
    }
  });

  socket.on("disconnect", () => {
    console.log("User disconnected:", socket.id);
  });
});

const PORT = process.env.PORT || 3000;
httpsServer.listen(PORT, () => {
  console.log(`Socket.IO server running on port ${PORT}`);
});
