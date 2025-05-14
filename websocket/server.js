const { Server } = require("socket.io");
const http = require("http");
const server = http.createServer();
const io = new Server(server, {
  cors: {
    origin: "*", // Specify frontend url on prod, e.g. "https://grch.ru"
    methods: ["GET", "POST"]
  }
});

io.on("connection", (socket) => {
  console.log("A user connected:", socket.id);

  // Обработка получения сообщения от клиента
  socket.on("sendMessage", (message) => {
    // Транслируем сообщение всем подключенным клиентам
    io.emit("receiveMessage", message);
  });

  socket.on("disconnect", () => {
    console.log("User disconnected:", socket.id);
  });
});

const PORT = 3000;
server.listen(PORT, () => {
  console.log(`Socket.IO server running on port ${PORT}`);
});