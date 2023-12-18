<template>
  <q-card class="q-my-md" :class="{ 'shadow-7': shadow, 'no-shadow': !shadow }">
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