<template>
  <div>
    <MainLayout>
      <template v-slot:main>
        <q-card class="q-my-md shadow-7">
          <q-card-section>
            <h3 class="text-center">Заявка на бронирование квартиры {{ user.first_name }} {{ user.role }}</h3>
          </q-card-section>
          <q-card-section>
            <q-form
              @submit="onSubmit"
              @reset="onReset"
              class="q-gutter-md"
            >
            <div class="text-center">
              <q-btn label="Отправить заявку" type="submit" color="primary"/>
              <q-btn label="Сбросить" type="reset" color="primary" flat class="q-ml-sm" />
            </div>
            </q-form>
          </q-card-section>
        </q-card>
        <FlatListItem :flat="flat"></FlatListItem>
      </template>
    </MainLayout>
  </div>
</template>

<script>

import { usePage } from '@inertiajs/inertia-vue3'
import { computed } from 'vue'
import MainLayout from '../../Layouts/MainLayout.vue'
import FlatListItem from '../../Components/Flat/FlatListItem.vue'

export default ({
  components: {
    MainLayout,
    FlatListItem
  },
  props: {
    flat: Object
  },
  setup(props) {

    const user = computed(() => usePage().props.value.auth.user)

    const onSubmit = () => console.log('AreYou Sure?')
    const onReset = () => console.log('Cansel?')

    return { user, onSubmit, onReset }
  },
})
</script>
