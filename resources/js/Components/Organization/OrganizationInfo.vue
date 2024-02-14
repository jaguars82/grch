<template>
  <div class="row items-start justify-between">
    <div class="col-10 col-sm-12">
      <p class="q-mt-xs q-mb-sm" :class="{ 'text-h1': $q.screen.gt.sm, 'text-h2': $q.screen.sm, 'text-h3': $q.screen.xs }">{{ name }}</p>
      <p v-if="address" class="q-mt-xs text-grey">
        <q-icon :size="$q.screen.xs ? 'sm' : 'md'" color="grey" class="q-mr-xs" name="location_on" />
        <span :class="{ 'text-h4': $q.screen.gt.xs,'text-h5': $q.screen.xs }">{{ address }}</span>
      </p>
    </div>
    <div class="col-2 lt-sm">
      <img :src="logo ? `/uploads/${logo}` : `/img/${noLogoImg}`" :alt="name" />
    </div>
  </div>
  <div class="row justify-between items-center">
    <div class="col-12 col-sm-7">
      <div v-if="url">
        <a target="_blank" :href="url">
          <q-icon :size="$q.screen.xs ? 'sm' : 'md'" :class="{ 'q-mr-xs': $q.screen.xs, 'q-mr-sm': $q.screen.gt.xs }" name="language" />
          <span :class="{ 'text-h4': $q.screen.gt.xs,'text-h5': $q.screen.xs }">{{ url }}</span>
        </a>
      </div>
      <div v-if="email">
        <a :href="`mailto:${email}`">
          <q-icon :size="$q.screen.xs ? 'sm' : 'md'" :class="{ 'q-mr-xs': $q.screen.xs, 'q-mr-sm': $q.screen.gt.xs }" name="alternate_email" />
          <span :class="{ 'text-h4': $q.screen.gt.xs,'text-h5': $q.screen.xs }">{{ email }}</span>
        </a>
      </div>
      <div v-if="phone">
        <q-icon :size="$q.screen.xs ? 'sm' : 'md'" :class="{ 'q-mr-xs': $q.screen.xs, 'q-mr-sm': $q.screen.gt.xs }" name="call" />
        <span :class="{ 'text-h4': $q.screen.gt.xs,'text-h5': $q.screen.xs }">{{ phone }}</span>
      </div>
    </div>
    <div class="col-4 gt-xs">
      <q-img class="organization-logo" fit="scale-down" :src="logo ? `/uploads/${logo}` : `/img/${noLogoImg}`" :alt="name" />
    </div>
  </div>
  <div v-if="detail" class="row">
    <div class="col-12">
      <div class="q-ma-md">
        <TitleSubtitle title="Описание" />
      </div>
      <q-separator />
      <div class="q-ma-md" v-html="detail"></div>
    </div>
  </div>
</template>

<script>
import TitleSubtitle from '@/Components/Elements/TitleSubtitle.vue'

export default {
  components: {
    TitleSubtitle
  },
  props: {
      id: {
      type: Number,
    },
    name: {
      type: String,
      default: ''
    },
    address: {
      type: String,
      default: ''
    },
    logo: {
      type: String,
      default: ''
    },
    noLogoImg: {
      type: String,
      default: ''
    },
    url: {
      type: String,
      default: ''
    },
    email: {
      type: String,
      default: ''
    },
    phone: {
      type: String,
      default: ''
    },
    detail: {
      type: String,
      default: ''
    },
  }
}
</script>

<style scoped>
  .row.justify-between::before, .row.justify-between::after {
    display: none;
  }
  .organization-logo {
    max-height: 200px;
  }
  .ocomplex-item-img {
    height: 90px;
  }
  .fitwindow {
    height: 100vh;
    max-height: 100vh;
    width: 100%;
  }
</style>