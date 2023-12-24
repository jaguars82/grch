<template>
  <q-card class="q-my-md" :class="{ 'shadow-7': shadow, 'no-shadow': !shadow }">
    <q-card-section horizontal>
      <q-card-section>
        <TitleSubtitle :title="item.title" :subtitle="asDateTime(item.created_at)" :link="`/news/view?id=${item.id}`" />
        <q-chip v-if="item.category === 1" color="orange" class="text-white">Акция</q-chip>
        <q-chip v-else-if="item.category === 2" color="primary" class="text-white">Новость</q-chip>
      
        <div class="q-mt-lg ellipsis-3-lines">
          {{ item.detail }}
        </div>
      </q-card-section>
      <q-img v-if="item.image" class="col-4" :src="`/uploads/${item.image}`" />
    </q-card-section>
  </q-card>
</template>

<script>
import { asDateTime } from '@/helpers/formatter'
import TitleSubtitle from '@/Components/Elements/TitleSubtitle.vue'

export default {
  name: 'NewsItem',
  components: { TitleSubtitle },
  props: {
    item: {
      type: Object,
      default: {}
    },
    shadow: {
      type: Boolean,
      default: true
    }
  },
  setup () {
    return {
      asDateTime
    }
  }
}
</script>