<template>
  <div class="row items-center" :class="{ 'cursor-pointer': isLink }" @click="goToComplex">
    <!--<div class="col">-->
      <q-img
        v-if="logo"
        fit="scale-down"
        :class="{ 'size-sm': $q.screen.lt.md, 'size-md': $q.screen.gt.sm }"
        :src="`/uploads/${logo}`"
      />
    <!--</div>-->
    <div class="col-8 q-pl-xs">
      <div class="column">
        <span class="text-5">{{ name }}</span>
        <span v-if="address" class="text-caption text-grey-7">{{ address }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia'

export default {
  props: {
    id: [Number, String],
    logo: String,
    name: String,
    address: String,
    isLink: {
      type: Boolean,
      default: true,
    }
  },
  setup (props) {
    const goToComplex = () => {
      if (props.isLink) {
        Inertia.get('/newbuilding-complex/view', { id: props.id })
      }
    }
    return { goToComplex }
  }
}
</script>

<style scoped>
.size-sm {
  width: 60px;
  height: 60px;
}
.size-md {
  width: 80px;
  height: 80px;
}
</style>