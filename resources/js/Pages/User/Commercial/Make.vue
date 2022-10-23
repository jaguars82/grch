<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer title="Коммерческое предложение">
        <template v-slot:content>
          <h5>Действие с выбранным объектом:</h5>
          <q-btn
            color="primary"
            unelevated
            label="Сформировать новое КП"
            @click="crateNew"
          />
          <q-btn class="q-ml-md" color="primary" unelevated label="Добавить к существующему КП" />
        </template>
      </RegularContentContainer>
      <FlatListItem class="q-mt-md q-ml-md" :flat="flat"></FlatListItem>
    </template>
  </ProfileLayout>
</template>

<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import FlatListItem from '@/Components/Flat/FlatListItem.vue'
import { asDateTime } from '@/helpers/formatter'

export default {
  components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
    FlatListItem
  },
  props: {
    flat: Object,
  }, 
  setup(props) {

    const formfields = ref({
      mode: ''
    })

    function crateNew() {
      formfields.value.mode = 'new'
      Inertia.post(`/user/commercial/make?&flatId=${props.flat.id}`, formfields.value)
    }

    return { crateNew }

  },
}
</script>
