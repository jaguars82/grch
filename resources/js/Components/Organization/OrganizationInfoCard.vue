<template>

  <q-card
    class="full-height cursor-pointer"
    @click="goToView"
    @mouseenter="focusOn"
    @mouseleave="focusOff"
  >
    <q-card-section>
      <q-img class="item-img" fit="scale-down" :src="`/uploads/${logo}`" :alt="name" />
    </q-card-section>
    <q-card-section>
      <p class="text-h4 text-center">{{ name }}</p>
    </q-card-section>
    <q-card-section>
      <div v-if="address">
        <q-icon class="q-mr-xs" name="location_on" />
        <span>{{ address }}</span>
      </div>
      <div v-if="url">
        <a target="_blank" :href="url">
          <q-icon class="q-mr-xs" name="language" />
          {{ url }}
        </a>
      </div>
      <div v-if="email">
        <a :href="`mailto:${email}`">
          <q-icon class="q-mr-xs" name="alternate_email" />
          {{ email }}
        </a>
      </div>
      <div v-if="phone">
        <q-icon class="q-mr-xs" name="call" />
        {{ phone }}
      </div>
    </q-card-section>
  </q-card>
</template>

<script>
export default {
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
    icon: {
      type: String,
      default: 'source_environment'
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
    pathToView: {
      type: String,
      default: ''
    },
  },
  setup(props) {
    const focusOn = function (event) {
      event.target.classList.add('shadow-15')
    }

    const focusOff = function (event) {
      event.target.classList.remove('shadow-15')
    }

    const goToView = function () {
      Inertia.get(props.pathToView, { id: props.id })
    }
    return { focusOn, focusOff, goToView }
  }
}
</script>

<style scoped>
.item-img {
  height: 120px;
}
</style>