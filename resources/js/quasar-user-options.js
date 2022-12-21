import 'quasar/src/css/index.sass'
//import './styles/quasar.sass'
import { Notify } from 'quasar'
import lang from 'quasar/lang/ru.js'
import '@quasar/extras/roboto-font/roboto-font.css'
import '@quasar/extras/material-icons/material-icons.css'
import '@quasar/extras/material-icons-outlined/material-icons-outlined.css'

// To be used on app.use(Quasar, { ... })
export default {
  config: {},
  plugins: {
    Notify
  },
  lang: lang
}