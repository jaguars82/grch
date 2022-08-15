<template>
  <div>
    <h1 class="the-header">Hello, {{ name }}!!!</h1>
    <p>{{test}} {{newsList}} </p>
    <div v-for="(news, i) in newsList" :key="news.id">
      {{i}} - {{ news.title }}
    </div>
    <div><span>{{ value1 }}</span> + <span>{{ value2 }}</span> = <span>{{ sum }}</span></div>
    <input type="text" v-model="value1" placeholder="первое число">
    <input type="text" v-model="value2" placeholder="второе число">
      <!--<q-btn class="bg-positive" @click="doSomething" label="Do something" />
  <q-icon name="alarm" />

    <q-item clickable v-ripple class="bg-grey-2">
      <q-item-section avatar>
        <q-avatar rounded>
          <img src="https://cdn.quasar.dev/img/chaosmonkey.png">
        </q-avatar>
      </q-item-section>

      <q-item-section>
        <q-item-label>
          Ganglia
        </q-item-label>
        <q-item-label caption>
          <q-badge color="yellow-6" text-color="black">
            3
            <q-icon
              name="warning"
              size="14px"
              class="q-ml-xs"
            />
          </q-badge>
        </q-item-label>
      </q-item-section>

      <q-item-section side>
        <span>2 min ago</span>
      </q-item-section>
    </q-item>

          <q-date
        v-model="date"
              />

    <q-color v-model="hex" class="my-picker" />-->

  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'

export default {
  props: {
    test: String,
    searchModel: Object,
    newsList: Object,
    newsDataProvider: Object,
    actionsDataProvider: Object,
    developerDataProvider: Object,
    agencyDataProvider: Object,
    bankDataProvider: Object,
    districts: Object,
    developers: Object,
    newbuildingComplexes: Object
  },
  setup(props) {
    
    const name = ref('VueFramework')
    const value1 = ref(0)
    const value2 = ref(0)
    const sum = computed(() => Number(value1.value) + Number(value2.value))
    const doSomething = () => console.log('wow!');


      console.log(props.newsList)

    return { name, value1, value2, sum, doSomething }

  },
  /*data () {
    return {
      name: 'Vue',
      value1: 0,
      value2: 0
    }
  },
  computed: {
    sum () {
      return this.value1 + this.value2
    }
  }*/
}
</script>

<style scoped>
  .the-header {
    color: green;
  }
</style>