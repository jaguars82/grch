<template>
  <q-chip 
    color="orange" 
    class="text-white" 
    :class="{'q-pr-xs': hasExpiration}"
  >
    {{ labelName }}
    <q-badge v-if="hasExpiration" rounded class="q-ml-xs q-px-xs q-py-none" color="white">
      <q-icon
        size="16px"
        color="orange"
        name="schedule"
      />
      <div class="q-mx-xs text-orange text-h6">
        <span v-if="countdownText !== 'период истёк'">ещё </span>
        {{ countdownText }}
      </div>
      <!--<q-tooltip anchor="top left" self="bottom middle">
        <div>Установлен до {{ formattedExpirationDate }}</div>
        <div>
          <span v-if="countdownText !== 'Период истёк'">осталось: </span>
          {{ countdownText }}
        </div>
      </q-tooltip>-->
    </q-badge>
  </q-chip>
</template>

<script setup>
import { computed } from 'vue'
import { asDate } from '@/helpers/formatter'
import { useCountdown } from '@/composables/useCountdown'

const props = defineProps({
  label: {
    type: Object,
    required: true,
  },
})

const hasExpiration = computed(() => props.label.has_expiration_date)
const labelName = computed(() => props.label.type.name)
const formattedExpirationDate = computed(() => 
  hasExpiration.value ? asDate(props.label.expires_at) : ''
)

const { formattedTime: countdownText } = hasExpiration.value 
  ? useCountdown(props.label.expires_at) 
  : { formattedTime: '' }
</script>