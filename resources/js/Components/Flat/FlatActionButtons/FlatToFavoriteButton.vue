<template>
  <q-btn
    round
    unelevated
    :outline="flatIsFavorite"
    icon="bookmark"
    :color="flatIsFavorite ? 'white' : 'primary'"
    :class="{ 'text-orange': flatIsFavorite, 'text-white': !flatIsFavorite }"
    @click="changeFlatFavoriteStatus"
  >
    <q-tooltip :offset="[0, 10]" :delay="1000">
      <span v-if="flatIsFavorite">удалить из Избранного</span>
      <span v-else>добавить в Избранное</span>
    </q-tooltip>
  </q-btn>
</template>

<script>
import { ref } from 'vue'
import axios from 'axios'

export default {
  props: {
    user: Object,
    flat: Object,
  },
  setup (props) {
    const flatIsFavorite = ref('isFavorite' in props.flat ? props.flat.isFavorite : false)

    const changeFlatFavoriteStatus = () => {
      const url = flatIsFavorite.value ? '/favorite/delete-flat?flatId=' : '/favorite/create?flatId=' 
      axios.post(url + props.flat.id)
      .then(function (response) {
        flatIsFavorite.value = !flatIsFavorite.value
      })
      .catch(function (error) {
        console.log(error)
      })
    }

    return { flatIsFavorite, changeFlatFavoriteStatus }
  }
}
</script>