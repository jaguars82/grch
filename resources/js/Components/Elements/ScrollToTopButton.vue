<template>
  <q-page-sticky position="bottom-right" :offset="[18, 18]">
    <q-btn 
      fab 
      icon="arrow_upward_alt" 
      color="primary" 
      @click="goToTop"
      :style="{
        opacity: showButton ? 1 : 0,
        transform: showButton ? 'translateY(0)' : 'translateY(20px)',
        transition: 'opacity 0.3s, transform 0.3s'
      }"
    />
  </q-page-sticky>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'

export default {
  setup () {
    const showButton = ref(false)

    const handleScroll = () => {
      showButton.value = window.scrollY > 200
    }

    const goToTop = () => {
      window.scrollTo({top: 0, behavior: 'smooth'})
    }
    
    onMounted(() => {
      window.addEventListener('scroll', handleScroll)
    })

    onUnmounted(() => {
      window.removeEventListener('scroll', handleScroll)
    })

    return { 
      goToTop,
      showButton
    }
  }
}
</script>