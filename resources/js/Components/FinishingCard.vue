<template>
  <q-card flat bordered>
    <q-card-section>
      <div class="card-header-content">
        <div class="header-text">
          <div>
            <span class="text-h4">{{ finishing.name }}</span>
          </div>
        </div>
      </div>
    </q-card-section>
    <q-card-section>
      <div>{{ finishing.detail }}</div>
      <div v-if="finishing.furnishImages && finishing.furnishImages.length > 0">
        <img v-for="image of finishing.furnishImages" :key="image.id" :name="image.id" :src="`/uploads/${image.image}`" />
        <!--<q-carousel
          transition-prev="slide-right"
          transition-next="slide-left"
          animated
          control-color="primary"
          class="rounded-borders"
        >
          <q-carousel-slide v-for="image of finishing.furnishImages" :key="image.id" :name="image.id">
            <img :src="`/uploads/${image.image}`" />
          </q-carousel-slide>

        </q-carousel>-->
      </div>
    </q-card-section>
  </q-card>
</template>

<script>
export default {
  name: 'NewbuildingComplexCard',
  props: {
    finishing: Array,
  },
}
</script>

<style scoped>
.card-header-content {
  display: flex;
  align-items: center;
}
.header-text {
  display: flex;
  flex-direction: column;
}
</style>