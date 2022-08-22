<template>
  <ProfileLayout>
    <template v-slot:main>
    <p>Профиль пользователя</p>
    </template>
  </ProfileLayout>
</template>

<script>
import ProfileLayout from '../../../Layouts/ProfileLayout.vue'

export default ({
  components: {
    ProfileLayout
  },
  setup() {
    
  },
})
</script>
