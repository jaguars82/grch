<template>
  <q-page-sticky position="bottom-right" :offset="[18, 18]">
    <q-btn fab icon="arrow_upward_alt" color="primary" @click="goToTop" />
  </q-page-sticky>
</template>

<script>
export default {
  setup () {
    const goToTop = () => {
      window.scrollTo(0, 0)
    }
    return { goToTop }
  }
}
</script>