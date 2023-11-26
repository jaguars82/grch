<template>
  <q-card class="q-my-md shadow-7">
    <q-card-section>
      <div class="text-h6">{{ item.title }}</div>
      <div class="text-subtitle2">{{ asDateTime(item.created_at) }}</div>
    </q-card-section>

    <q-card-section class="q-pt-none">
      {{ item.detail }}
    </q-card-section>
  </q-card>
</template>

<script>
import { asDateTime } from '@/helpers/formatter'

export default {
  name: 'NewsItem',
  props: {
    item: {
      type: Object,
      default: {}
    }
  },
  setup () {
    return {
      asDateTime
    }
  }
}
</script>