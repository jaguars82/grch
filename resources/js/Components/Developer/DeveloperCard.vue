<template>
  <q-card flat bordered>
    <q-card-section>
      <div class="card-header-content">
        <img class="developer-complex-logo q-pr-md" v-if="developer.logo" :src="`/uploads/${developer.logo}`" />
        <q-avatar class="q-pr-md" v-else icon="engineering" />
        <div class="header-text">
          <div>
            <span class="text-h4">{{ developer.name }}</span>
          </div>
          <div v-if="developer.address">
            <span>{{ developer.address }}</span>
          </div>
          <div v-if="developer.url">
            <a target="_blank" :href="developer.url">
                <q-icon class="q-mr-xs" name="language" />
                {{ developer.url }}
            </a>
          </div>
          <div v-if="developer.email">
            <a :href="`mailto:${developer.email}`">
                <q-icon class="q-mr-xs" name="alternate_email" />
                {{ developer.email }}
            </a>
          </div>
        </div>
      </div>
    </q-card-section>
    <q-card-section>
      {{ developer.detail }}
    </q-card-section>
  </q-card>
</template>

<script>
export default {
  name: 'DeveloperCard',
  props: {
    developer: Array,
  },
}
</script>

<style scoped>
.newbuilding-complex-logo {
  height: 80px;
}
.card-header-content {
  display: flex;
  align-items: center;
}
.header-text {
  display: flex;
  flex-direction: column;
}
</style>