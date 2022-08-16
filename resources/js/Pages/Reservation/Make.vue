<template>
  <div>
    <MainLayout>
      <template v-slot:main>
        <h2>Заявка на бронирование квартиры</h2>
        <FlatListItem :flat="flat"></FlatListItem>
      </template>
    </MainLayout>
  </div>
</template>

<script>

import MainLayout from '../../Layouts/MainLayout.vue'
import FlatListItem from '../../Components/Flat/FlatListItem.vue'
import { ref } from 'vue'

export default ({
  components: {
    MainLayout,
    FlatListItem
  },
  props: {
    flat: Object
  },
  setup(props) {
  },
})
</script>
