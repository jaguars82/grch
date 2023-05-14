<template>
  <q-card class="q-ml-sm">
    <q-card-section>

      <q-select outlined v-model="formfields.deal_type" :options="filters.deal_type.options" label="Тип операции" options-dense>
        <template v-slot:append>
          <q-icon
            v-if="formfields.deal_type !== null"
            class="cursor-pointer"
            name="clear"
            @click.stop.prevent="formfields.deal_type = null"
          />
        </template>
      </q-select>

      <q-select outlined v-model="formfields.category" :options="filterCategory" label="Категория" options-dense>
        <template v-slot:append>
          <q-icon
            v-if="formfields.category !== null"
            class="cursor-pointer"
            name="clear"
            @click.stop.prevent="formfields.category = null"
          />
        </template>
      </q-select>

      <h5>Цена:</h5>
      <div class="row">
        <div class="col-6">
          <q-input outlined v-model="formfields.price.min" label="От">
            <template v-slot:append>
              <q-icon
                v-if="formfields.price.min !== null"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="formfields.price.min = null"
              />
            </template>
          </q-input>
        </div>
        <div class="col-6">
          <q-input outlined v-model="formfields.price.max" label="До">
            <template v-slot:append>
              <q-icon
                v-if="formfields.price.max !== null"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="formfields.price.max = null"
              />
            </template>
          </q-input>
        </div>
      </div>
      <q-range
        :model-value="formfields.price"
        @change="val => { formfields.price = val }"
        :min="Math.floor(parseFloat(ranges.price.min))"
        :max="Math.ceil(parseFloat(ranges.price.max))"
        label
      />

      <h5>Количество комнат:</h5>
      <q-btn-group>
        <q-btn v-for="(button, index) in roomButtons"
          :key="index"
          :color="button.color"
          :label="button.label"
          @click="setRoomButtonActive(button, index)"
        ></q-btn>
      </q-btn-group>

      <h5>Общая площадь:</h5>
      <div class="row">
        <div class="col-6">
          <q-input outlined v-model="formfields.area.min" label="От">
            <template v-slot:append>
              <q-icon
                v-if="formfields.area.min !== null"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="formfields.area.min = null"
              />
            </template>
          </q-input>
        </div>
        <div class="col-6">
          <q-input outlined v-model="formfields.area.max" label="До">
            <template v-slot:append>
              <q-icon
                v-if="formfields.area.max !== null"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="formfields.area.max = null"
              />
            </template>
          </q-input>
        </div>
      </div>
      <q-range
        :model-value="formfields.area"
        @change="val => { formfields.area = val }"
        :min="Math.floor(parseFloat(ranges.area.min))"
        :max="Math.ceil(parseFloat(ranges.area.max))"
        label
      />

      <q-select outlined v-model="formfields.district" :options="filterDistricts" label="Район" multiple options-dense use-chips>
        <template v-slot:append>
          <q-icon
            v-if="formfields.district !== null"
            class="cursor-pointer"
            name="clear"
            @click.stop.prevent="formfields.district = null"
          />
        </template>
        <!--<template v-slot:after-options>
          <q-btn>Применить</q-btn>
        </template>-->
      </q-select>

      <q-select outlined v-model="formfields.street" :options="filterStreets" label="Улица" use-input hide-selected fill-input options-dense @filter="filterStreetList">
        <template v-slot:append>
          <q-icon
            v-if="formfields.street !== null"
            class="cursor-pointer"
            name="clear"
            @click.stop.prevent="formfields.street = null"
          />
        </template>
      </q-select>


      <pre>{{ filterParams }}</pre>
      <pre>{{ ranges }}</pre>
      <pre>{{ formfieldsTest }}</pre>

    </q-card-section>
  </q-card>
</template>

<script>
import { ref, computed, watch } from 'vue'
import useEmitter from '@/composables/use-emitter'

export default {
props: {
  filterParams: {
    type: Object,
    default: {}
  },
  ranges: {
    type: Object
  },
  secondaryCategories: {
    type: Object,
  },
  districts: {
    type: Array
  },
  streetList: {
    type: Array
  }
},
setup(props) {
  const filters = ref({
    deal_type: {
      options: [
        { label: 'продажа', value: 1 },
        { label: 'аренда', value: 2 },
      ]
    },
  })

  const filterCategory = computed(() => {
    const categoryOptions = []
    const categoriesArray = []
    
    for (const id in props.secondaryCategories) {
      categoriesArray.push(props.secondaryCategories[id])
    }

    categoriesArray.forEach(category => {
      categoryOptions.push({ label: category.name, disable: true })
      category.subcats.forEach(subcat => {
        categoryOptions.push({ label: subcat.name, value: subcat.id })
      })
    })
    return categoryOptions
  })

  const filterDistricts = computed(() => {
    const districts = []
    props.districts.forEach(district => {
      districts.push({ label: district.name, value: district.id })
    })
    return districts
  })

  const streetListRef = ref(props.streetList)

  const filterStreets = computed(() => {
    const streets = []
    streetListRef.value.forEach(street => {
      streets.push({ label: street.fullname, value: { street_name: street.street_name, street_type_id: street.street_type_id } })
    })
    return streets
  })

  const filterStreetList = (val, update, abort) => {
    update(() => {
      const needle = val.toLowerCase()
      streetListRef.value = props.streetList.filter(elem => elem.street_name.toLowerCase().indexOf(needle) > -1)
    })
  }

  const formfields = ref({
    deal_type: props.filterParams.deal_type ? filters.value.deal_type.options.find(elem => { return elem.value == props.filterParams.deal_type }) : null,
    category: props.filterParams.category ? filterCategory.value.find(elem => { return elem.value == props.filterParams.category }) : null,
    price: { min: props.filterParams.priceFrom ? props.filterParams.priceFrom : null, max: props.filterParams.priceTo ? props.filterParams.priceTo : null},
    rooms: props.filterParams.rooms ? props.filterParams.rooms : [],
    area: { min: props.filterParams.areaFrom ? props.filterParams.areaFrom : null, max: props.filterParams.areaTo ? props.filterParams.areaTo : null},
    district: props.filterParams.district && props.filterParams.district.length > 0 ? filterDistricts.value.filter(elem => { return props.filterParams.district.includes(elem.value) }) : null,
    street: props.filterParams.street ? filterStreets.value.find(elem => { return elem.label ==  props.filterParams.street.label }) : null,
  })

  const formfieldsTest = ref({
    district: null,
    street: null
  })

  const roomButtons = ref([
    {
      label: '1',
      val: '1',
      color: formfields.value.rooms.indexOf('1') >= 0 ? 'primary' : 'white',
      onOff: formfields.value.rooms.indexOf('1') >= 0 ? true : false
    },
    {
      label: '2',
      val: '2',
      color: formfields.value.rooms.indexOf('2') >= 0 ? 'primary' : 'white',
      onOff: formfields.value.rooms.indexOf('2') >= 0 ? true : false
    },
    {
      label: '3',
      val: '3',
      color: formfields.value.rooms.indexOf('3') >= 0 ? 'primary' : 'white',
      onOff: formfields.value.rooms.indexOf('3') >= 0 ? true : false
    },
    {
      label: '4',
      val: '4',
      color: formfields.value.rooms.indexOf('4') >= 0 ? 'primary' : 'white',
      onOff: formfields.value.rooms.indexOf('4') >= 0 ? true : false
    },
    {
      label: '5',
      val: '5',
      color: formfields.value.rooms.indexOf('5') >= 0 ? 'primary' : 'white',
      onOff: formfields.value.rooms.indexOf('5') >= 0 ? true : false
    },
    {
      label: '5+',
      val: '5+',
      color: formfields.value.rooms.indexOf('5+') >= 0 ? 'primary' : 'white',
      onOff: formfields.value.rooms.indexOf('5+') >= 0 ? true : false
    },
  ])

  const setRoomButtonActive = (button, index) => {
    if(button.onOff) {
      roomButtons.value[index].color = 'white'
      roomButtons.value[index].onOff = false
      const elemInd = formfields.value.rooms.indexOf(button.val)
      formfields.value.rooms.splice(elemInd, 1)
    } else {
      roomButtons.value[index].color = 'primary'
      roomButtons.value[index].onOff = true
      formfields.value.rooms.push(button.val)
    }
  }

  const emitter = useEmitter()
  watch(formfields.value, () => { 
    // console.log(formfields.value)
    emitter.emit('secondary-filter-changed', formfields.value)
  })

  return {
    filters,
    filterCategory,
    filterDistricts,
    filterStreets,
    filterStreetList,
    roomButtons,
    setRoomButtonActive,
    formfields,
    formfieldsTest
  }
 }
}
</script>