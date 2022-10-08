import { createApp, h } from 'vue'
import { Quasar } from 'quasar'
import quasarUserOptions from './quasar-user-options'
import { createInertiaApp, Link } from '@inertiajs/inertia-vue3'
import mitt from 'mitt'
import '../css/stylefixes.css' // ToDo - remove this style when migration to new front-end is finished

const emitter = mitt()

createInertiaApp({
  resolve: name => require(`./Pages/${name}`),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(Quasar, quasarUserOptions)
      .component('inertia-link', Link)
      app.config.globalProperties.emitter = emitter
      app.mount(el)
  },
})