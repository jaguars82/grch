<template>
  <q-card
    class="q-my-sm q-pa-sm cursor-pointer developer-resume-card"
    @click="goToDeveloper"
    @mouseenter="focusOn"
    @mouseleave="focusOff"
  >
    
    <q-img height="100px" fit="scale-down" :src="developer.logo ? `/uploads/${developer.logo}` : '/img/developer.png'" />
    <q-card-section>
      <div class="text-center">
        <span class="text-h4">{{ developer.name }}</span>
      </div>
    </q-card-section>
    <q-card-section>
      <ParamPair
        paramName="Жилых комплексов"
        :paramValue="developer.complexesAmount"
      />
      <ParamPair
        paramName="Квартир в продаже"
        :paramValue="developer.flatsAmount"
      />
      <ParamPair
        paramName="Действующих акций"
        :paramValue="developer.actionsAmount"
      />
    </q-card-section>
    <q-card-section v-html="developer.shortDescription">
    </q-card-section>
  </q-card>
</template>

<script>
import { Inertia } from '@inertiajs/inertia'
import ParamPair from '@/Components/Elements/ParamPair.vue'

export default {
  name: 'DeveloperResumeCard',
  components: { ParamPair },
  props: {
    developer: Object,
  },
  setup (props) {
    const goToDeveloper = () => {
      Inertia.get('/developer/view', { id: props.developer.id })
    }
    const focusOn = function (event) {
      event.target.classList.add('shadow-15')
    }
    const focusOff = function (event) {
      event.target.classList.remove('shadow-15')
    }
    return { goToDeveloper, focusOn, focusOff }
  }
}
</script>

<style scoped>
.developer-resume-card {
  width: 300px;
}
/*.card-header-content {
  display: flex;
  align-items: center;
}
.card-header-content :first-child {
  max-width: 50%;
}
.card-header-content :last-child {
  flex-grow: 1;
}*/
.developer-logo {
  height: 80px;
  max-height: 80px;
}
.header-text {
  display: flex;
  flex-direction: column;
  justify-items: center;
  align-items: flex-end;
}
</style>