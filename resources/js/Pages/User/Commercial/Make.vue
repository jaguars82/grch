<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer title="Коммерческое предложение">
        <template v-slot:content>
          <loading v-if="loading" size="md" text="Идёт подготовка коммерческого предложения" />
          <template v-else>
            <h5>Действие с выбранным объектом:</h5>
            <q-btn
              color="primary"
              unelevated
              label="Сформировать новое КП"
              @click="crateNew"
            />
            <q-btn
              v-if="commercials.length > 0"
              class="q-ml-md"
              color="primary"
              unelevated
              label="Добавить к существующему КП"
            >
              <q-menu auto-close>
                <q-list>
                  <q-item
                    v-for="commercial in commercials"
                    :key="commercial.id"
                    clickable
                    v-ripple
                    @click="addTo(commercial.id)"
                  >
                    №&nbsp;<strong>{{ commercial.number }}</strong>&nbsp;от {{ asDateTime(commercial.created_at) }}
                  </q-item>
                </q-list>
              </q-menu>
            </q-btn>
          </template>
        </template>
      </RegularContentContainer>
      <FlatListItem class="q-mt-md q-ml-md" :flat="flat"></FlatListItem>
    </template>
  </ProfileLayout>
</template>

<script>
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import FlatListItem from '@/Components/Flat/FlatListItem.vue'
import { asDateTime } from '@/helpers/formatter'
import Loading from '@/Components/Elements/Loading.vue'

export default {
  components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
    FlatListItem,
    Loading
  },
  props: {
    flat: Object,
    commercials: Object
  }, 
  setup(props) {

    const loading = ref(false)

    const formfields = ref({
      mode: ''
    })

    function crateNew() {
      loading.value = true
      formfields.value.mode = 'new'
      Inertia.post(`/user/commercial/make?&flatId=${props.flat.id}`, formfields.value)
    }

    function addTo(commersialId) {
      loading.value = true
      formfields.value.mode = 'add'
      formfields.value.commercialId = commersialId
      Inertia.post(`/user/commercial/make?&flatId=${props.flat.id}`, formfields.value)
    }

    return { loading, crateNew, addTo, asDateTime }

  },
}
</script>
