const { Server } = require("socket.io");
const http = require("http");
const axios = require('axios');

const server = http.createServer();
const io = new Server(server, {
  cors: {
    origin: "*", // Specify frontend url on prod, e.g. "https://grch.ru"
    methods: ["GET", "POST"]
  }
});

// URL of the GRCH agregator
const API_URL = 'https://grch.ru'; // production
// const API_URL = 'http://dev.grch.ru'; // development

io.on("connection", (socket) => {
  console.log("A user connected:", socket.id);

  socket.on("sendMessage", async (message) => {
    try {
      // Saving to database via API
      const response = await axios.post(`${API_URL}/vidgets/messenger/create-message`, {
        ...message,
      });
      
      if (response.data.success) {
        // if saved - send the message to clints
        io.emit("receiveMessage", {
          ...message,
          chat_id: response.data.chatId, // Chat ID from database
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

const PORT = 3000;
server.listen(PORT, () => {
  console.log(`Socket.IO server running on port ${PORT}`);
});