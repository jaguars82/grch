<template>
  <template v-if="showIndicator">
    <q-btn flat dense round :size="size" :class="[size === 'xs' ? 'q-ml-sm' : 'q-ml-xs']" @click.stop="handleButtonClick">
      <q-icon
        class="cursor-pointer"
        name="arrow_drop_up"
        v-if="isIncrease"
        :color="iconColor"
      />
      <q-icon
        class="cursor-pointer"
        name="arrow_drop_down"
        v-if="isDecrease"
        :color="iconColor"
      />

      <q-tooltip v-model="showTooltip" anchor="top middle" self="bottom middle" :offset="[10, 10]">
        {{ tooltipText }}
      </q-tooltip>

      <q-popup-proxy @before-show="hideTooltip" @hide="hideTooltip">
        <q-card class="q-pa-sm">
          <q-card-section class="text-h5">
            {{ formattedDate }} {{ tooltipText }} на {{ priceDifference }} ₽
          </q-card-section>
          <q-card-section>
            <div class="text-grey-7">Текущая цена - {{ Number(latest.price_cash).toLocaleString("ru-RU") }} ₽</div>
            <div class="text-grey-7">Старая цена - {{ Number(previous.price_cash).toLocaleString("ru-RU") }} ₽</div>
          </q-card-section>
        </q-card>
      </q-popup-proxy>
    </q-btn>
  </template>
</template>

<script>
import { defineComponent, computed, ref } from "vue"
import { date } from "quasar"

export default defineComponent({
  name: "PriceChangeIndicator",
  props: {
    data: {
      type: Array,
      required: true,
    },
    size: {
      type: String,
      default: "md",
    },
  },
  setup(props) {
    // do not render if data has less 2 entries
    if (props.data.length < 2) return { showIndicator: false }

    const sortedData = computed(() =>
      [...props.data].sort(
        (a, b) => new Date(b.price_updated_at) - new Date(a.price_updated_at)
      )
    )

    const latest = computed(() => sortedData.value[0])
    const previous = computed(() => sortedData.value[1])

    // Check for 2 weeks since the latest price change
    const isRecent = computed(() => {
      const twoWeeksAgo = Date.now() - 14 * 24 * 60 * 60 * 1000
      return new Date(latest.value.price_updated_at) >= new Date(twoWeeksAgo)
    })

    if (!isRecent.value) return { showIndicator: false }

    // The direction of price movement
    const movementType = computed(() => latest.value.movement)
    const isIncrease = computed(() => movementType.value === 1)
    const isDecrease = computed(() => movementType.value === 2)

    // Price difference
    const priceDifference = computed(() => {
      return Math.abs(
        Number(latest.value.price_cash) - Number(previous.value.price_cash)
      ).toLocaleString("ru-RU")
    })

    // Format the date
    const formattedDate = computed(() =>
      date.formatDate(latest.value.price_updated_at, "DD.MM.YYYY")
    )

    // The icon color
    const iconColor = computed(() => (isIncrease.value ? "red" : "green"))

    // Tooltip text
    const tooltipText = computed(() =>
      isIncrease.value ? "цена выросла" : "цена снизилась"
    )

    // Tooltip visibility
    const showTooltip = ref(false)

    const hideTooltip = () => {
      showTooltip.value = false
    }

    const handleButtonClick = (event) => {
      event.stopPropagation()
    }

    return {
      showIndicator: true,
      latest,
      previous,
      isIncrease,
      isDecrease,
      priceDifference,
      formattedDate,
      iconColor,
      tooltipText,
      showTooltip,
      hideTooltip,
      handleButtonClick,
    }
  },
})
</script>