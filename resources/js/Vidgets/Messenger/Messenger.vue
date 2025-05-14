<template>
  <p>Сообщения</p>
  <div class="chat">
    <div v-for="(msg, index) in messages" :key="index" class="message">
      {{ msg }}
    </div>
  </div>
  <q-input v-model="message" placeholder="Введите сообщение" />
  <q-btn label="отправить" @click="sendMessage" />
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue';
import { io } from 'socket.io-client';

export default {
  setup() {
    const message = ref('');
    const messages = ref([]);
    let socket;

    // Подключение к Socket.IO серверу при монтировании компонента
    onMounted(() => {
      socket = io('http://dev.grch.ru:3000'); // Укажите адрес вашего сервера

      // Обработка получения сообщений от сервера
      socket.on('receiveMessage', (msg) => {
        messages.value.push(msg);
      });
    });

    onUnmounted(() => {
      socket.disconnect();
    });

    // Send a message
    const sendMessage = () => {
      if (message.value.trim()) {
        socket.emit('sendMessage', message.value);
        message.value = ''; // Очистка поля ввода
      }
    };

    return { message, messages, sendMessage };
  }
};
</script>

<style scoped>
.chat {
  max-height: 300px;
  overflow-y: auto;
  border: 1px solid #ccc;
  padding: 10px;
  margin-bottom: 10px;
}

.message {
  padding: 5px;
  border-bottom: 1px solid #eee;
}
</style>