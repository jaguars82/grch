<template>
  <q-card
    flat
    bordered
    class="full-height cursor-pointer"
    @click="goToView"
    @mouseenter="focusOn"
    @mouseleave="focusOff"
  >
    <q-card-section>
      <div class="card-header-content">
        <img class="logo q-pr-md" v-if="logo" :src="`/uploads/${logo}`" />
        <q-avatar class="q-pr-md" v-else :icon="icon" />
        <div class="header-text">
          <div class="text-right">
            <span class="text-h4">{{ name }}</span>
          </div>
          <div class="text-right" v-if="address">
            <span>{{ address }}</span>
          </div>
          <div class="text-right" v-if="url">
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
        </div>
      </div>
    </q-card-section>
    <q-card-section v-if="detail" v-html="detail">
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
.card-header-content {
  display: flex;
  align-items: center;
}
.card-header-content :first-child {
  max-width: 50%;
}
.card-header-content :last-child {
  flex-grow: 1;
}
.logo {
  height: 80px;
}
.header-text {
  display: flex;
  flex-direction: column;
  justify-items: center;
  align-items: flex-end;
}
</style>