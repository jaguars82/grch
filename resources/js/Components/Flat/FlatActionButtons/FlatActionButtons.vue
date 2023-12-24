<template>
  <div class="row items-center">
    <div>
      <FlatToReservationButton
        v-if="res && ['admin', 'manager', 'agent'].includes(user.role)"
        :user="user"
        :flat="flat"
      />
    </div>
    <div class="q-mx-xs">
      <FlatToCommercialButton
        v-if="com && ['admin', 'manager', 'agent'].includes(user.role)"
        :user="user"
        :flat="flat"
      />
    </div>
    <div>
      <FlatToFavoriteButton
        v-if="fav && ['admin', 'manager', 'agent'].includes(user.role)"
        :user="user"
        :flat="flat"
      />
    </div>
  </div>
</template>

<script>
import { userInfo } from '@/composables/shared-data'
import FlatToCommercialButton from '@/Components/Flat/FlatActionButtons/FlatToCommercialButton.vue'
import FlatToFavoriteButton from '@/Components/Flat/FlatActionButtons/FlatToFavoriteButton.vue'
import FlatToReservationButton from '@/Components/Flat/FlatActionButtons/FlatToReservationButton.vue'

export default {
  components: {
    FlatToCommercialButton, FlatToFavoriteButton, FlatToReservationButton
  },
  props: {
    fav: {
      type: Boolean,
      default: true
    },
    com: {
      type: Boolean,
      default: true
    },
    res: {
      type: Boolean,
      default: true
    },
    flat: Object
  },
  setup () {
    const { user } = userInfo()
    return {
      user
    }
  }
}
</script>