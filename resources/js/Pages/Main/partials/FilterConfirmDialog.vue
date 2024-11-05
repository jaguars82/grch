<template>
  <div class="absolute-top-right">
    <!--<q-menu v-model="isVisible" :offset="offset" max-height="300" persistent no-parent-event>
      <div class="dialog-content q-pa-md">
        <p>{{ newSearchPrecount }}</p>
        <div class="text-right">
          <q-btn class="q-mr-sm" unelevated color="orange" label="Применить" @click="onConfirmNewSearch" :disabled="newSearchPrecount < 1" />
          <q-btn round unelevated icon="close" @click="onCloseFilterDialog" />
        </div>
      </div>
    </q-menu>-->
    <q-menu v-model="isVisible" :offset="offset" max-height="300" persistent no-parent-event>
      <div class="q-pa-md q-rounded q-elevation-3 bg-white relative-position">
        
        <q-btn
          dense
          flat
          round
          color="grey"
          icon="close"
          size="xs"
          class="absolute-top-right q-mt-xs q-mr-xs"
          @click="onCloseFilterDialog"
        >
          <q-tooltip anchor="top middle" self="center middle">Отменить изменения</q-tooltip>
        </q-btn>

        <div class="q-gutter-sm flex items-center text-primary q-mb-md">
          <q-icon name="filter_list" color="primary" size="md" />
          <span class="text-h6 font-bold text-uppercase">Фильтр изменен</span>
        </div>

        <p class="text-body2 text-center text-grey-7 q-my-sm">
          Объектов по новым критериям поиска:
        </p>
        <p class="text-h4 text-center text-primary text-bold q-my-sm">{{ newSearchPrecount }}</p>

        <div class="row justify-center q-mt-md">
          <q-btn
            unelevated
            color="orange"
            label="Показать"
            icon="search"
            @click="onConfirmNewSearch"
            :disabled="newSearchPrecount < 1"
          />
        </div>
      </div>
    </q-menu>
  </div>
</template>

<script>
import { ref, computed, nextTick } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import axios from 'axios'
import { useQuasar } from 'quasar'
import useEmitter from '@/composables/use-emitter'

export default {
  props: {
    action: {
      type: String,
      default: 'search'
    },
    searchType: {
      type: String,
      default: 'AdvancedFlatSearch'
    }
  },
  emits: ['confirm', 'close'],
  setup(props) {
    const $q = useQuasar()

    const offset = computed(() => {
      const vertOffset = Math.round($q.screen.height / 2.5)
      const horizOffset = $q.screen.gt.md ? 610 : 510
      return [horizOffset, vertOffset]
    })

    const isVisible = ref(false)

    const filterValues = ref(null)

    const newSearchPrecount = ref(0)

    const emitter = useEmitter()
    emitter.on('flat-filter-changed', async (payload) => {
      filterValues.value = payload
      isVisible.value = true
      
      try {
        const response = await axios.post('/site/pre-search', { AdvancedFlatSearch: payload })
        newSearchPrecount.value = response.data
        nextTick(() => {
          // Render after all the data renewal
        })
      } catch (error) {
        console.log(error)
      }
    })

    const onConfirmNewSearch = () => {
      Inertia.get(`/site/${props.action}`, { [props.searchType]: filterValues.value }, { preserveState: true })
      isVisible.value = false
    }

    const onCloseFilterDialog = () => {
      emitter.emit('close-filter-change-dialog', { action: props.action, searchType: props.searchType })
    }

    return { isVisible, offset, newSearchPrecount, onConfirmNewSearch, onCloseFilterDialog };
  },
}
</script>