<template>
  <q-card flat bordered>
    <q-card-section>
      <div class="card-header-content">
        <img class="newbuilding-complex-logo q-pr-md" v-if="newbuildingComplex.logo" :src="`/uploads/${newbuildingComplex.logo}`" />
        <q-avatar class="q-pr-md" v-else icon="location_city" />
        <div class="header-text">
          <div class="text-right">
            <span class="text-h4">{{ newbuildingComplex.name }}</span>
          </div>
          <div class="text-right" v-if="developer.name">
            <span>{{ developer.name }}</span>
          </div>
          <div class="text-right" v-if="newbuildingComplex.address">
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
.card-header-content {
  display: flex;
  align-items: center;
}
.card-header-content :first-child {
  max-width: 50%;
}
.card-header-content :last-child {
  flex-grow: 1;
}
.newbuilding-complex-logo {
  height: 80px;
}
.header-text {
  display: flex;
  flex-direction: column;
  justify-items: center;
  align-items: flex-end;
}
</style>