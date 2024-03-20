<template>
  <div class="row q-col-gutter-none">

    <div class="col-12 col-sm-6 q-py-xs" :class="{'q-pr-none': $q.screen.xs }">
      <q-select
        outlined
        v-model="optfields.developer_select"
        :options="developerOptions"
        label="Застройщик"
        options-dense
        @update:model-value="onDeveloperSelect"
      >
        <template v-slot:append>
          <q-icon
            v-if="optfields.developer_select !== null"
            class="cursor-pointer"
            name="clear"
            @click.stop.prevent="() => { 
              optfields.developer_select = null
              optfields.buildingComplex_select = null
              optfields.building_select = null
              optfields.entrance_select = null
              optfields.flat_select = null
            }"
          />
        </template>
      </q-select>
    </div>

    <div class="col-12 col-sm-6 q-py-xs q-pr-none">
      <q-select
        outlined
        v-model="optfields.buildingComplex_select"
        :options="buildingComplexOptions"
        label="Жилой комплекс"
        options-dense
        :disable="!buildingComplexOptions.length"
        @update:model-value="onBuildingComplexSelect"
      >
        <template v-slot:append>
          <q-icon
            v-if="optfields.buildingComplex_select !== null"
            class="cursor-pointer"
            name="clear"
            @click.stop.prevent="() => { 
              optfields.buildingComplex_select = null
              optfields.building_select = null
              optfields.entrance_select = null
              optfields.flat_select = null
            }"
          />
        </template>
      </q-select>
    </div>

    <div class="col-12 col-sm-4 q-py-xs" :class="{'q-pr-none': $q.screen.xs }">
      <q-select
        outlined
        v-model="optfields.building_select"
        :options="buildingOptions"
        label="Позиция"
        options-dense
        :disable="!buildingOptions.length"
        @update:model-value="onBuildingSelect"
      >
        <template v-slot:append>
          <q-icon
            v-if="optfields.building_select !== null"
            class="cursor-pointer"
            name="clear"
            @click.stop.prevent="() => { 
              optfields.building_select = null
              optfields.entrance_select = null
              optfields.flat_select = null
            }"
          />
        </template>
      </q-select>
    </div>

    <div class="col-12 col-sm-4 q-py-xs" :class="{'q-pr-none': $q.screen.xs }">
      <q-select
        outlined
        v-model="optfields.entrance_select"
        :options="entranceOptions"
        label="Подъезд"
        options-dense
        :disable="!entranceOptions.length"
        @update:model-value="onEntranceSelect"
      >
        <template v-slot:append>
          <q-icon
            v-if="optfields.entrance_select !== null"
            class="cursor-pointer"
            name="clear"
            @click.stop.prevent="() => { 
              optfields.entrance_select = null
              optfields.flat_select = null
            }"
          />
        </template>
      </q-select>
    </div>

    <div class="col-12 col-sm-4 q-py-xs q-pr-none">
      <q-select
        outlined
        v-model="optfields.flat_select"
        :options="flatOptions"
        label="Квартира"
        options-dense
        :disable="!flatOptions.length"
      >
        <template v-slot:append>
          <q-icon
            v-if="optfields.flat_select !== null"
            class="cursor-pointer"
            name="clear"
            @click.stop.prevent="optfields.flat_select = null"
          />
        </template>
      </q-select>
    </div>

  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

export default {
  props: {
    developers: {
      type: Object,
      default: {}
    },
    buildingComplexes: {
      type: Array,
      default: []
    },
    buildings: {
      type: Array,
      default: []
    },
    entrances: {
      type: Array,
      default: []
    },
    flats: {
      type: Array,
      default: []
    },
    initialParams: {
      type: Object,
      default: {
        developerId: '',
        complexId: '',
        buildingId: '',
        entranceId: '',
      }
    }
  },
  setup (props) {
    const developers = ref({})
    const buildingComplexes = ref([])
    const buildings = ref([])
    const entrances = ref([])
    const flats = ref([])

    const initialSelections = computed(() => {
      const developer_id = props.initialParams.developerId ? props.initialParams.developerId : false
      const complex_id = props.initialParams.complexId ? props.initialParams.complexId : false
      const complex = complex_id ? buildingComplexes.find(compl => compl.id == complex_id) : false
      const building_id = props.initialParams.buildingId ? props.initialParams.buildingId : false
      const building = building_id ? buildings.find(build => build.id == building_id) : false
      const entrance_id = props.initialParams.entranceId ? props.initialParams.entranceId : false
      const entrance = entrance_id ? entrances.find(entr => entr.id == entrance_id) : false

      return {
        developer: { label: developers[developer_id], value: developer_id },
        complex: complex ? { label: complex.name, value: complex.id } : '',
        building: building ? { label: building.name, value: building.id } : '',
        entrance: entrance ? { label: entrance.name, value: entrance.id } : '',
      }
    })

    const optfields = ref({
      developer_select: initialSelections.value.developer,
      buildingComplex_select: initialSelections.value.complex,
      building_select: initialSelections.value.building,
      entrance_select: initialSelections.value.entrance,
      flat_select: null
    })

    const developerOptions = computed(() => {
      const options = []
      Object.keys(developers.value).forEach(index => {
        options.push({ label: developers.value[index], value: index })
      })
      return options
    })

    const onDeveloperSelect = () => {
      optfields.value.buildingComplex_select = null
      optfields.value.building_select = null
      optfields.value.entrance_select = null
      optfields.value.flat_select = null
      axios.post(`/newbuilding-complex/get-for-developer?id=${optfields.value.developer_select.value}`)
      .then(function (response) {
        buildingComplexes.value = response.data
      })
      .catch(function (error) {
        console.log(error)
      })
    }

    const buildingComplexOptions = computed(() => {
      const options = []
        if (optfields.value.developer_select !== null) {
        buildingComplexes.value.forEach(complex => {
          options.push({ label: complex.name, value: complex.id })
        })
      }
      return options
    })

    const onBuildingComplexSelect = () => {
      optfields.value.building_select = null
      optfields.value.entrance_select = null
      optfields.value.flat_select = null
      axios.post(`/newbuilding/get-for-newbuilding-complex?id=${optfields.value.buildingComplex_select.value}`)
      .then(function (response) {
        buildings.value = response.data
      })
      .catch(function (error) {
        console.log(error)
      })
    }

    const buildingOptions = computed(() => {
      const options = []
        if (optfields.value.buildingComplex_select !== null) {
        buildings.value.forEach(building => {
          options.push({ label: building.name, value: building.id })
        })
      }
      return options
    })

    const onBuildingSelect = () => {
      optfields.value.entrance_select = null
      optfields.value.flat_select = null
      axios.post(`/entrance/get-for-newbuilding?id=${optfields.value.building_select.value}`)
      .then(function (response) {
        entrances.value = response.data
      })
      .catch(function (error) {
        console.log(error)
      })
    }

    const entranceOptions = computed(() => {
      const options = []
        if (optfields.value.building_select !== null) {
        entrances.value.forEach(entrance => {
          options.push({ label: entrance.name, value: entrance.id })
        })
      }
      return options
    })

    const onEntranceSelect = () => {
      optfields.value.flat_select = null
      axios.post(`/entrance/get-flats-by-entrance?id=${optfields.value.entrance_select.value}`)
      .then(function (response) {
        flats.value = response.data
      })
      .catch(function (error) {
        console.log(error)
      })
    }

    const flatOptions = computed(() => {
      const options = []
        if (optfields.value.entrance_select !== null) {
        flats.value.forEach(flat => {
          options.push({ label: `№ ${flat.number} (${flat.floor} этаж)`, value: flat.id })
        })
      }
      return options
    })

    onMounted (() => {
      // Get developers
      axios.post('/developer/get-developers')
      .then(function (response) {
        developers.value = response.data
      })
      .catch(function (error) {
        console.log(error)
      })
    })

    return {
      optfields,
      developerOptions,
      onDeveloperSelect,
      buildingComplexOptions,
      onBuildingComplexSelect,
      buildingOptions,
      onBuildingSelect,
      entranceOptions,
      onEntranceSelect,
      flatOptions
    }

  }
}
</script>