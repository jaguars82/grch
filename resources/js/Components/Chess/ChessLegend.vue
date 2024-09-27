<template>
  <div class="q-pa-md">
    <!-- As a Panel -->
    <q-card class="no-shadow bordered q-pb-none">
      <q-card-section>
        <div class="flex">
        <div class="q-mr-md flex items-center" v-for="status of statusLegendData">
          <q-avatar size="xs" :color="status.color" />
          <div class="q-pl-xs text-grey-8">{{ status.label }}</div>
        </div>
        </div>
      </q-card-section>
    </q-card>
    <!-- As a Button (old variant) -->
    <!-- <q-btn class="text-grey" rounded color="white" unelevated size="sm" label="Легенда">
      <q-popup-proxy>
        <q-list>
          <q-item v-for="status of statusLegendData">
            <q-item-section avatar>
              <q-avatar size="sm" :color="status.color" />
            </q-item-section>
            <q-item-section>{{ status.label }}</q-item-section>
          </q-item>
        </q-list>
      </q-popup-proxy>
    </q-btn> -->
  </div>
</template>

<script>
import { computed } from 'vue'
import { flatStatusColors } from '@/composables/model-configurations'

export default {
  props: {
    statusLabels: Object,
    existingStatuses: Array,
  },
  setup (props) {
    const statusLegendData = computed(() => {
      const data = []
      props.existingStatuses.forEach(status => {
        data.push({ status: status, label: props.statusLabels[status], color: flatStatusColors[status] })
      })
      return data
    })
    return { statusLegendData }
  }
}
</script>