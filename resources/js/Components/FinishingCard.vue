<template>
  <q-card flat :bordered="border" class="q-my-sm">
    <q-card-section>
      <div class="card-header-content">
        <div class="header-text">
          <div>
            <span class="text-h4">{{ finishing.name }}</span>
          </div>
          <div>
            <span>информациия по отделке</span>
          </div>
        </div>
      </div>
    </q-card-section>
    <q-card-section>
      <div class="row">
        <div class="col-5" v-if="finishing.furnishImages && finishing.furnishImages.length > 0">
          <q-carousel
            v-if="finishing.furnishImages.length > 1"
            animated
            v-model="slide"
            arrows
            navigation
            infinite
          >
            <q-carousel-slide
              v-for="image of finishing.furnishImages"
              :key="image.id"
              :name="image.id"
              :img-src="`/uploads/${image.image}`"
            />
          </q-carousel>
          <img v-else :src="`/uploads/${finishing.furnishImages[0].image}`" />
        </div>
        <div :class="{ 'col-7': finishing.furnishImages && finishing.furnishImages.length > 0, 'col-12': !finishing.furnishImages, 'q-pl-md': true }">
          {{ finishing.detail }}
        </div>
      </div>
    </q-card-section>
  </q-card>
</template>

<script>
import { ref } from 'vue'
export default {
  name: 'FinishingCard',
  props: {
    finishing: Object,
    border: {
      type: Boolean,
      default: true
    }
  },
  setup (props) {
    const slide = ref(props.finishing.furnishImages[0].id)
    return { slide }
  }
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