import { createApp, h } from 'vue'
import { Quasar } from 'quasar'
import quasarUserOptions from './quasar-user-options'
import { createInertiaApp } from '@inertiajs/inertia-vue3'

createInertiaApp({
  resolve: name => require(`./Pages/${name}`),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(Quasar, quasarUserOptions)
      .mount(el)
  },
})