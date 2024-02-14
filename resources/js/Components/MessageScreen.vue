<template>
  <q-card :class="standAlone ? '' : 'no-shadow'">
    <q-card-section class="text-center">
      <q-icon size="lg" :color="icon.color" :name="icon.picture" />
      <p v-if="textMessages.title" class="q-mt-md text-h4">{{ textMessages.title }}</p>
      <p v-if="textMessages.subtitle" class="text-h5">{{ textMessages.subtitle }}</p>
    </q-card-section>
    <q-card-section v-if="textMessages.content">
      <p>{{ textMessages.content }}</p>
    </q-card-section>
    <q-card-section v-if="textMessages.note">
      <p>{{ textMessages.note }}</p>
    </q-card-section>
    <q-card-actions v-if="actions" align="right">
      <q-btn
        v-for="action of actions"
        :key="action.id"
        :padding="$q.screen.gt.xs ? 'xs md' : 'sm'"
        :color="action.style.color ? action.style.color : 'pimary'"
        unelevated
        :outline="action.style.outline ? action.style.outline : false"
        :rounded="$q.screen.gt.xs"
        :round="$q.screen.xs"
        :class="action.style.class ? action.style.class : ''"
        :icon="action.icon"
        :label="$q.screen.gt.xs ? action.text : ''"
        @click="action.action"
      />
    </q-card-actions>
  </q-card>
</template>

<script>
import { computed } from 'vue'

export default ({
  props: {
    standAlone: {
      type: Boolean,
      default: false
    },
    type: {
      type: String,
      default: 'info' // possible values: 'info', 'success', 'error'
    },
    textMessages: {
      type: Object,
      default: { title: '', subtitle: '', content: '',  note: '' }
    },
    actions: {
      type: Array, // array of objects like { id: some id, icon: button icon, text: button text, action: name of a handler function, style: { class: button class, color: button color, outlined, flat, rounded } }
    }
  },
  setup(props) {
    const icon = {}
    switch (props.type) {
        case 'success':
          icon.picture = 'check_circle'
          icon.color = 'positive'
          break
        case 'error':
          icon.picture = 'cancel'
          icon.color = 'negative'
          break
        default:
          icon.picture = 'info'
          icon.color = 'info'
      }

    return { icon }
  },
})
</script>
