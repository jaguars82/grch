<template>
  <q-card flat bordered>
    <q-card-section>
      <div class="card-header-content">
        <img class="newbuilding-complex-logo q-pr-md" v-if="newbuildingComplex.logo" :src="`/uploads/${newbuildingComplex.logo}`" />
        <q-avatar class="q-pr-md" v-else icon="location_city" />
        <div class="header-text">
          <div>
            <span class="text-h4">{{ newbuildingComplex.name }}</span>
          </div>
          <div v-if="developer.name">
            <span>{{ developer.name }}</span>
          </div>
          <div v-if="newbuildingComplex.address">
            <span>{{ newbuildingComplex.address }}</span>
          </div>
        </div>
      </div>
    </q-card-section>
    <q-card-section>
      {{ newbuildingComplex.detail }}
    </q-card-section>
  </q-card>
</template>

<script>
export default {
  name: 'NewbuildingComplexCard',
  props: {
    newbuildingComplex: Array,
    developer: {
      type: Array,
      default: []
    }
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