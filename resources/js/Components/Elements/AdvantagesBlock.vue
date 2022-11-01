<template>
  <p class="text-h4">Преимущества</p>
  <div class="advantages-container">
    <div class="advantage-container q-ma-sm" v-for="advantage of advantages" :key="advantage.id">
      <q-avatar size="md" square v-if="advantage.icon">
        <img :src="`/uploads/${advantage.icon}`">
      </q-avatar>
      <q-avatar size="md" v-else icon="verified" />
      <div class="q-px-sm">{{ advantage.name }}</div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    advantages: Array
  },
}
</script>

<style scoped>
.advantages-container {
  display: flex;
  flex-wrap: wrap;
}

.advantage-container {
  display: flex;
  align-items: center;
}
</style>