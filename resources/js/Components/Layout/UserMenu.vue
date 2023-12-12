<template>
  {{ user }}
</template>

<script>
import { userInfo } from '@/composables/shared-data'

export default {
  setup() {
    const { user } = userInfo()

    return { user }
  }
}
</script>