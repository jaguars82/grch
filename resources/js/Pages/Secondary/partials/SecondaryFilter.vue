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

      <q-select outlined v-model="formfields.agency" :options="filterAgencies" label="Агентство" multiple options-dense use-chips>
        <template v-slot:append>
          <q-icon
            v-if="formfields.agency !== null"
            class="cursor-pointer"
            name="clear"
            @click.stop.prevent="formfields.agency = null"
          />
        </template>
      </q-select>

      <q-select outlined v-model="formfields.statusLabel" :options="filterSatusLabels" label="Статус объявления" multiple options-dense use-chips>
        <template v-slot:append>
          <q-icon
            v-if="formfields.statusLabel !== null"
            class="cursor-pointer"
            name="clear"
            @click.stop.prevent="formfields.statusLabel = null"
          />
        </template>
      </q-select>

      <h5>Цена:</h5>
      <div class="row">
        <div class="col-6 q-pr-xs">
          <q-input outlined v-model="formfields.price.min" label="От">
            <template v-slot:prepend>
              <q-icon
                v-if="formfields.price.min != '' && formfields.price.min != ranges.price.min"
                size="xs"
                class="cursor-pointer"
                name="refresh"
                @click.stop.prevent="formfields.price.min = ranges.price.min"
              />
            </template>
          </q-input>
        </div>
        <div class="col-6 q-pl-xs">
          <q-input outlined v-model="formfields.price.max" label="До">
            <template v-slot:prepend>
              <q-icon
                v-if="formfields.price.max != '' && formfields.price.max != ranges.price.max"
                size="xs"
                class="cursor-pointer"
                name="refresh"
                @click.stop.prevent="formfields.price.max = ranges.price.max"
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
      <q-btn-group unelevated>
        <q-btn v-for="(button, index) in roomButtons"
          :key="index"
          :color="button.color"
          :text-color="button.textColor"
          :label="button.label"
          @click="setRoomButtonActive(button, index)"
        ></q-btn>
      </q-btn-group>

      <h5>Общая площадь:</h5>
      <div class="row">
        <div class="col-6 q-pr-xs">
          <q-input outlined v-model="formfields.area.min" label="От">
            <template v-slot:prepend>
              <q-icon
                v-if="formfields.area.min != '' && formfields.area.min != ranges.area.min"
                size="xs"
                class="cursor-pointer"
                name="refresh"
                @click.stop.prevent="formfields.area.min = ranges.area.min"
              />
            </template>
            <template v-slot:append>
              <q-icon
                v-if="formfields.area.min !== null"
                size="xs"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="formfields.area.min = null"
              />
            </template>
          </q-input>
        </div>
        <div class="col-6 q-pl-xs">
          <q-input outlined v-model="formfields.area.max" label="До">
            <template v-slot:prepend>
              <q-icon
                v-if="formfields.area.max != '' && formfields.area.max != ranges.area.max"
                size="xs"
                class="cursor-pointer"
                name="refresh"
                @click.stop.prevent="formfields.area.max = ranges.area.max"
              />
            </template>
            <template v-slot:append>
              <q-icon
                v-if="formfields.area.max !== null"
                size="xs"
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

      <h5>Этаж:</h5>
      <div class="row">
        <div class="col-6 q-pr-xs">
          <q-input outlined v-model="formfields.floor.min" label="От">
            <template v-slot:prepend>
              <q-icon
                v-if="formfields.floor.min != '' && formfields.floor.min != ranges.floor.min"
                size="xs"
                class="cursor-pointer"
                name="refresh"
                @click.stop.prevent="formfields.floor.min = ranges.floor.min"
              />
            </template>
            <template v-slot:append>
              <q-icon
                v-if="formfields.floor.min !== null"
                size="xs"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="formfields.floor.min = null"
              />
            </template>
          </q-input>
        </div>
        <div class="col-6 q-pl-xs">
          <q-input outlined v-model="formfields.floor.max" label="До">
            <template v-slot:prepend>
              <q-icon
                v-if="formfields.floor.max != '' && formfields.floor.max != ranges.floor.max"
                size="xs"
                class="cursor-pointer"
                name="refresh"
                @click.stop.prevent="formfields.floor.max = ranges.floor.max"
              />
            </template>
            <template v-slot:append>
              <q-icon
                v-if="formfields.floor.max !== null"
                size="xs"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="formfields.floor.max = null"
              />
            </template>
          </q-input>
        </div>
      </div>
      <q-range
        :model-value="formfields.floor"
        @change="val => { formfields.floor = val }"
        :min="parseInt(ranges.floor.min)"
        :max="parseInt(ranges.floor.max)"
        label
      />

      <h5>Этажность:</h5>
      <div class="row">
        <div class="col-6 q-pr-xs">
          <q-input outlined v-model="formfields.totalFloors.min" label="От">
            <template v-slot:prepend>
              <q-icon
                v-if="formfields.totalFloors.min != '' && formfields.totalFloors.min != ranges.total_floors.min"
                size="xs"
                class="cursor-pointer"
                name="refresh"
                @click.stop.prevent="formfields.totalFloors.min = ranges.total_floors.min"
              />
            </template>
            <template v-slot:append>
              <q-icon
                v-if="formfields.totalFloors.min !== null"
                size="xs"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="formfields.totalFloors.min = null"
              />
            </template>
          </q-input>
        </div>
        <div class="col-6 q-pl-xs">
          <q-input outlined v-model="formfields.totalFloors.max" label="До">
            <template v-slot:prepend>
              <q-icon
                v-if="formfields.totalFloors.max != '' && formfields.totalFloors.max != ranges.total_floors.max"
                size="xs"
                class="cursor-pointer"
                name="refresh"
                @click.stop.prevent="formfields.totalFloors.max = ranges.total_floors.max"
              />
            </template>
            <template v-slot:append>
              <q-icon
                v-if="formfields.totalFloors.max !== null"
                size="xs"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="formfields.totalFloors.max = null"
              />
            </template>
          </q-input>
        </div>
      </div>
      <q-range
        :model-value="formfields.totalFloors"
        @change="val => { formfields.totalFloors = val }"
        :min="parseInt(ranges.total_floors.min)"
        :max="parseInt(ranges.total_floors.max)"
        label
      />

      <q-btn 
        unelevated
        size="sm"
        :icon="showMoreFilterParams ? 'expand_less' : 'expand_more'"
        :label="showMoreFilterParams ? 'Меньше параметров' : 'Больше параметров'"
        @click="showMoreFilterParams = !showMoreFilterParams"
      />

      <template v-if="showMoreFilterParams">
      <h5>Площадь кухни:</h5>
      <div class="row">
        <div class="col-6 q-pr-xs">
          <q-input outlined v-model="formfields.kitchenArea.min" label="От">
            <template v-slot:prepend>
              <q-icon
                v-if="formfields.kitchenArea.min != '' && formfields.kitchenArea.min != ranges.kitchen_area.min"
                size="xs"
                class="cursor-pointer"
                name="refresh"
                @click.stop.prevent="formfields.kitchenArea.min = ranges.kitchen_area.min"
              />
            </template>
            <template v-slot:append>
              <q-icon
                v-if="formfields.kitchenArea.min !== null"
                size="xs"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="formfields.kitchenArea.min = null"
              />
            </template>
          </q-input>
        </div>
        <div class="col-6 q-pl-xs">
          <q-input outlined v-model="formfields.kitchenArea.max" label="До">
            <template v-slot:prepend>
              <q-icon
                v-if="formfields.kitchenArea.max != '' && formfields.kitchenArea.max != ranges.kitchen_area.max"
                size="xs"
                class="cursor-pointer"
                name="refresh"
                @click.stop.prevent="formfields.kitchenArea.max = ranges.kitchen_area.max"
              />
            </template>
            <template v-slot:append>
              <q-icon
                v-if="formfields.kitchenArea.max !== null"
                size="xs"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="formfields.kitchenArea.max = null"
              />
            </template>
          </q-input>
        </div>
      </div>
      <q-range
        :model-value="formfields.kitchenArea"
        @change="val => { formfields.kitchenArea = val }"
        :min="Math.floor(parseFloat(ranges.kitchen_area.min))"
        :max="Math.ceil(parseFloat(ranges.kitchen_area.max))"
        label
      />

      <h5>Жилая площадь:</h5>
      <div class="row">
        <div class="col-6 q-pr-xs">
          <q-input outlined v-model="formfields.livingArea.min" label="От">
            <template v-slot:prepend>
              <q-icon
                v-if="formfields.livingArea.min != '' && formfields.livingArea.min != ranges.living_area.min"
                size="xs"
                class="cursor-pointer"
                name="refresh"
                @click.stop.prevent="formfields.livingArea.min = ranges.living_area.min"
              />
            </template>
            <template v-slot:append>
              <q-icon
                v-if="formfields.livingArea.min !== null"
                size="xs"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="formfields.livingArea.min = null"
              />
            </template>
          </q-input>
        </div>
        <div class="col-6 q-pl-xs">
          <q-input outlined v-model="formfields.livingArea.max" label="До">
            <template v-slot:prepend>
              <q-icon
                v-if="formfields.livingArea.max != '' && formfields.livingArea.max != ranges.living_area.max"
                size="xs"
                class="cursor-pointer"
                name="refresh"
                @click.stop.prevent="formfields.livingArea.max = ranges.living_area.max"
              />
            </template>
            <template v-slot:append>
              <q-icon
                v-if="formfields.livingArea.max !== null"
                size="xs"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="formfields.livingArea.max = null"
              />
            </template>
          </q-input>
        </div>
      </div>
      <q-range
        :model-value="formfields.livingArea"
        @change="val => { formfields.livingArea = val }"
        :min="Math.floor(parseFloat(ranges.living_area.min))"
        :max="Math.ceil(parseFloat(ranges.living_area.max))"
        label
      />

      <h5>Количество балконов:</h5>
      <div class="row">
        <div class="col-6 q-pr-xs">
          <q-input outlined v-model="formfields.balconyAmount.min" label="От">
            <template v-slot:prepend>
              <q-icon
                v-if="formfields.balconyAmount.min != '' && formfields.balconyAmount.min != ranges.balcony_amount.min"
                size="xs"
                class="cursor-pointer"
                name="refresh"
                @click.stop.prevent="formfields.balconyAmount.min = ranges.balcony_amount.min"
              />
            </template>
            <template v-slot:append>
              <q-icon
                v-if="formfields.balconyAmount.min !== null"
                size="xs"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="formfields.balconyAmount.min = null"
              />
            </template>
          </q-input>
        </div>
        <div class="col-6 q-pl-xs">
          <q-input outlined v-model="formfields.balconyAmount.max" label="До">
            <template v-slot:prepend>
              <q-icon
                v-if="formfields.balconyAmount.max != '' && formfields.balconyAmount.max != ranges.balcony_amount.max"
                size="xs"
                class="cursor-pointer"
                name="refresh"
                @click.stop.prevent="formfields.balconyAmount.max = ranges.balcony_amount.max"
              />
            </template>
            <template v-slot:append>
              <q-icon
                v-if="formfields.balconyAmount.max !== null"
                size="xs"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="formfields.balconyAmount.max = null"
              />
            </template>
          </q-input>
        </div>
      </div>
      <q-range
        :model-value="formfields.balconyAmount"
        @change="val => { formfields.balconyAmount = val }"
        :min="parseInt(ranges.balcony_amount.min)"
        :max="parseInt(ranges.balcony_amount.max)"
        label
      />

      <h5>Количество лоджий:</h5>
      <div class="row">
        <div class="col-6 q-pr-xs">
          <q-input outlined v-model="formfields.loggiaAmount.min" label="От">
            <template v-slot:prepend>
              <q-icon
                v-if="formfields.loggiaAmount.min != '' && formfields.loggiaAmount.min != ranges.loggia_amount.min"
                size="xs"
                class="cursor-pointer"
                name="refresh"
                @click.stop.prevent="formfields.loggiaAmount.min = ranges.loggia_amount.min"
              />
            </template>
            <template v-slot:append>
              <q-icon
                v-if="formfields.loggiaAmount.min !== null"
                size="xs"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="formfields.loggiaAmount.min = null"
              />
            </template>
          </q-input>
        </div>
        <div class="col-6 q-pl-xs">
          <q-input outlined v-model="formfields.loggiaAmount.max" label="До">
            <template v-slot:prepend>
              <q-icon
                v-if="formfields.loggiaAmount.max != '' && formfields.loggiaAmount.max != ranges.loggia_amount.max"
                size="xs"
                class="cursor-pointer"
                name="refresh"
                @click.stop.prevent="formfields.loggiaAmount.max = ranges.loggia_amount.max"
              />
            </template>
            <template v-slot:append>
              <q-icon
                v-if="formfields.loggiaAmount.max !== null"
                size="xs"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="formfields.loggiaAmount.max = null"
              />
            </template>
          </q-input>
        </div>
      </div>
      <q-range
        :model-value="formfields.loggiaAmount"
        @change="val => { formfields.loggiaAmount = val }"
        :min="parseInt(ranges.loggia_amount.min)"
        :max="parseInt(ranges.loggia_amount.max)"
        label
      />

      <q-checkbox v-model="formfields.windowviewStreet" label="Вид из окон на улицу" />
      <q-checkbox v-model="formfields.windowviewYard" label="Вид из окон во двор" />
      <q-checkbox v-model="formfields.panoramicWindows" label="Панорамные окна" />

      <h5>Год сдачи/постройки:</h5>
      <div class="row">
        <div class="col-6 q-pr-xs">
          <q-input outlined v-model="formfields.builtYear.min" label="От">
            <template v-slot:prepend>
              <q-icon
                v-if="formfields.builtYear.min != '' && formfields.builtYear.min != ranges.built_year.min"
                size="xs"
                class="cursor-pointer"
                name="refresh"
                @click.stop.prevent="formfields.builtYear.min = ranges.built_year.min"
              />
            </template>
            <template v-slot:append>
              <q-icon
                v-if="formfields.builtYear.min !== null"
                size="xs"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="formfields.builtYear.min = null"
              />
            </template>
          </q-input>
        </div>
        <div class="col-6 q-pl-xs">
          <q-input outlined v-model="formfields.builtYear.max" label="До">
            <template v-slot:prepend>
              <q-icon
                v-if="formfields.builtYear.max != '' && formfields.builtYear.max != ranges.built_year.max"
                size="xs"
                class="cursor-pointer"
                name="refresh"
                @click.stop.prevent="formfields.builtYear.max = ranges.built_year.max"
              />
            </template>
            <template v-slot:append>
              <q-icon
                v-if="formfields.builtYear.max !== null"
                size="xs"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="formfields.builtYear.max = null"
              />
            </template>
          </q-input>
        </div>
      </div>
      <q-range
        :model-value="formfields.builtYear"
        @change="val => { formfields.builtYear = val }"
        :min="parseInt(ranges.built_year.min)"
        :max="parseInt(ranges.built_year.max)"
        label
      />

      <q-checkbox v-model="formfields.concierge" label="Консьерж" />
      <q-checkbox v-model="formfields.rubbishChute" label="Мусоропровод" />
      <q-checkbox v-model="formfields.gasPipe" label="Газоснабжение" />
      <q-checkbox v-model="formfields.closedTerritory" label="Закрытая территория" />
      <q-checkbox v-model="formfields.playground" label="Детская площадка" />
      <q-checkbox v-model="formfields.undergroundParking" label="Подземная парковка" />
      <q-checkbox v-model="formfields.groundParking" label="Наземная парковка" />
      <q-checkbox v-model="formfields.openParking" label="Открытая парковка" />
      <q-checkbox v-model="formfields.multilevelParking" label="Многоуровневая парковка" />
      <q-checkbox v-model="formfields.barrier" label="Шлагбаум" />

      </template>

      <!--
      <pre>{{ filterParams }}</pre>
      <pre>{{ ranges }}</pre>
      <pre>{{ formfieldsTest }}</pre>
      -->

    </q-card-section>
  </q-card>
</template>

<script>
import { ref, computed, watch } from 'vue'
import { useSecondaryFilter } from '@/stores/SecondaryFilterStore'
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
  agencies: {
    type: Array
  },
  statusLabelTypes: {
    type: Object
  },
  districts: {
    type: Array
  },
  streetList: {
    type: Array
  }
},
setup(props) {
  const filterStore = useSecondaryFilter()

  const showMoreFilterParams = ref(false)

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

  const filterAgencies = computed(() => {
    const agencies = []
    props.agencies.forEach(agency => {
      agencies.push({ label: agency.name, value: agency.id })
    })
    return agencies
  })

  const filterSatusLabels = computed(() => {
    const labels = []
    for (const id in props.statusLabelTypes) {
      labels.push({ value: id, label: props.statusLabelTypes[id] })
    }
    return labels
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
      streetListRef.value = props.streetList.filter(elem => {
        if (elem.street_name) {
          return elem.street_name.toLowerCase().indexOf(needle) > -1
        }
      })
    })
  }

  const formfields = ref({
    deal_type: props.filterParams.deal_type ? filters.value.deal_type.options.find(elem => { return elem.value == props.filterParams.deal_type }) : filterStore.deal_type,
    category: props.filterParams.category ? filterCategory.value.find(elem => { return elem.value == props.filterParams.category }) : filterStore.category,
    agency: props.filterParams.agency ? filterAgencies.value.filter(elem => { return props.filterParams.agency.includes(elem.value) }) : filterStore.agency,
    statusLabel: props.filterParams.statusLabel ? filterSatusLabels.value.filter(elem => { return props.filterParams.statusLabel.includes(elem.value) }) : filterStore.statusLabel,
    price: { min: props.filterParams.priceFrom ? props.filterParams.priceFrom : props.ranges.price.min, max: props.filterParams.priceTo ? props.filterParams.priceTo : props.ranges.price.max},
    rooms: props.filterParams.rooms ? props.filterParams.rooms : filterStore.rooms,
    //rooms: props.filterParams.rooms ? props.filterParams.rooms : [],
    area: { min: props.filterParams.areaFrom ? props.filterParams.areaFrom : filterStore.area.min, max: props.filterParams.areaTo ? props.filterParams.areaTo : filterStore.area.max},
    district: props.filterParams.district && props.filterParams.district.length > 0 ? filterDistricts.value.filter(elem => { return props.filterParams.district.includes(elem.value) }) : filterStore.district,
    street: props.filterParams.street ? filterStreets.value.find(elem => { return elem.label ==  props.filterParams.street.label }) : filterStore.street,
    floor: { min: props.filterParams.floorFrom ? props.filterParams.floorFrom : filterStore.floor.min, max: props.filterParams.floorTo ? props.filterParams.floorTo : filterStore.floor.max},
    totalFloors: { min: props.filterParams.totalFloorsFrom ? props.filterParams.totalFloorsFrom : filterStore.totalFloors.min, max: props.filterParams.totalFloorsTo ? props.filterParams.totalFloorsTo : filterStore.totalFloors.max},
    kitchenArea: { min: props.filterParams.kitchenAreaFrom ? props.filterParams.kitchenAreaFrom : filterStore.kitchenArea.min, max: props.filterParams.kitchenAreaTo ? props.filterParams.kitchenAreaTo : filterStore.kitchenArea.max},
    livingArea: { min: props.filterParams.livingAreaFrom ? props.filterParams.livingAreaFrom : filterStore.livingArea.min, max: props.filterParams.livingAreaTo ? props.filterParams.livingAreaTo : filterStore.livingArea.max},
    balconyAmount: { min: props.filterParams.balconyFrom ? props.filterParams.balconyFrom : filterStore.balconyAmount.min, max: props.filterParams.balconyTo ? props.filterParams.balconyTo : filterStore.balconyAmount.max},
    loggiaAmount: { min: props.filterParams.loggiaFrom ? props.filterParams.loggiaFrom : filterStore.loggiaAmount.min, max: props.filterParams.loggiaTo ? props.filterParams.loggiaTo : filterStore.loggiaAmount.max},
    windowviewStreet: props.filterParams.windowviewStreet ? true : filterStore.windowviewStreet,
    windowviewYard: props.filterParams.windowviewYard ? true : filterStore.windowviewYard,
    panoramicWindows: props.filterParams.panoramicWindows ? true : filterStore.panoramicWindows,
    builtYear: { min: props.filterParams.builtYearFrom ? props.filterParams.builtYearFrom : filterStore.builtYear.min, max: props.filterParams.builtYearTo ? props.filterParams.builtYearTo : filterStore.builtYear.max},
    concierge: props.filterParams.concierge ? true : filterStore.concierge,
    rubbishChute: props.filterParams.rubbishChute ? true : filterStore.rubbishChute,
    gasPipe: props.filterParams.gasPipe ? true : filterStore.gasPipe,
    closedTerritory: props.filterParams.closedTerritory ? true : filterStore.closedTerritory,
    playground: props.filterParams.playground ? true : filterStore.playground,
    undergroundParking: props.filterParams.undergroundParking ? true : filterStore.undergroundParking,
    groundParking: props.filterParams.groundParking ? true : filterStore.groundParking,
    openParking: props.filterParams.openParking ? true : filterStore.openParking,
    multilevelParking: props.filterParams.multilevelParking ? true : filterStore.multilevelParking,
    barrier: props.filterParams.barrier ? true : filterStore.barrier,

    /*deal_type: props.filterParams.deal_type ? filters.value.deal_type.options.find(elem => { return elem.value == props.filterParams.deal_type }) : null,
    category: props.filterParams.category ? filterCategory.value.find(elem => { return elem.value == props.filterParams.category }) : null,
    price: { min: props.filterParams.priceFrom ? props.filterParams.priceFrom : props.ranges.price.min, max: props.filterParams.priceTo ? props.filterParams.priceTo : props.ranges.price.max},
    rooms: props.filterParams.rooms ? props.filterParams.rooms : [],
    area: { min: props.filterParams.areaFrom ? props.filterParams.areaFrom : null, max: props.filterParams.areaTo ? props.filterParams.areaTo : null},
    district: props.filterParams.district && props.filterParams.district.length > 0 ? filterDistricts.value.filter(elem => { return props.filterParams.district.includes(elem.value) }) : null,
    street: props.filterParams.street ? filterStreets.value.find(elem => { return elem.label ==  props.filterParams.street.label }) : null,
    floor: { min: props.filterParams.floorFrom ? props.filterParams.floorFrom : null, max: props.filterParams.floorTo ? props.filterParams.floorTo : null},
    totalFloors: { min: props.filterParams.totalFloorsFrom ? props.filterParams.totalFloorsFrom : null, max: props.filterParams.totalFloorsTo ? props.filterParams.totalFloorsTo : null},
    kitchenArea: { min: props.filterParams.kitchenAreaFrom ? props.filterParams.kitchenAreaFrom : null, max: props.filterParams.kitchenAreaTo ? props.filterParams.kitchenAreaTo : null},
    livingArea: { min: props.filterParams.livingAreaFrom ? props.filterParams.livingAreaFrom : null, max: props.filterParams.livingAreaTo ? props.filterParams.livingAreaTo : null},
    balconyAmount: { min: props.filterParams.balconyFrom ? props.filterParams.balconyFrom : null, max: props.filterParams.balconyTo ? props.filterParams.balconyTo : null},
    loggiaAmount: { min: props.filterParams.loggiaFrom ? props.filterParams.loggiaFrom : null, max: props.filterParams.loggiaTo ? props.filterParams.loggiaTo : null},
    windowviewStreet: props.filterParams.windowviewStreet ? true : false,
    windowviewYard: props.filterParams.windowviewYard ? true : false,
    panoramicWindows: props.filterParams.panoramicWindows ? true : false,
    builtYear: { min: props.filterParams.builtYearFrom ? props.filterParams.builtYearFrom : null, max: props.filterParams.builtYearTo ? props.filterParams.builtYearTo : null},
    concierge: props.filterParams.concierge ? true : false,
    rubbishChute: props.filterParams.rubbishChute ? true : false,
    gasPipe: props.filterParams.gasPipe ? true : false,
    closedTerritory: props.filterParams.closedTerritory ? true : false,
    playground: props.filterParams.playground ? true : false,
    undergroundParking: props.filterParams.undergroundParking ? true : false,
    groundParking: props.filterParams.groundParking ? true : false,
    openParking: props.filterParams.openParking ? true : false,
    multilevelParking: props.filterParams.multilevelParking ? true : false,
    barrier: props.filterParams.barrier ? true : false,*/
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
      textColor: formfields.value.rooms.indexOf('1') >= 0 ? 'white' : 'grey',
      onOff: formfields.value.rooms.indexOf('1') >= 0 ? true : false
    },
    {
      label: '2',
      val: '2',
      color: formfields.value.rooms.indexOf('2') >= 0 ? 'primary' : 'white',
      textColor: formfields.value.rooms.indexOf('2') >= 0 ? 'white' : 'grey',
      onOff: formfields.value.rooms.indexOf('2') >= 0 ? true : false
    },
    {
      label: '3',
      val: '3',
      color: formfields.value.rooms.indexOf('3') >= 0 ? 'primary' : 'white',
      textColor: formfields.value.rooms.indexOf('3') >= 0 ? 'white' : 'grey',
      onOff: formfields.value.rooms.indexOf('3') >= 0 ? true : false
    },
    {
      label: '4',
      val: '4',
      color: formfields.value.rooms.indexOf('4') >= 0 ? 'primary' : 'white',
      textColor: formfields.value.rooms.indexOf('4') >= 0 ? 'white' : 'grey',
      onOff: formfields.value.rooms.indexOf('4') >= 0 ? true : false
    },
    {
      label: '5',
      val: '5',
      color: formfields.value.rooms.indexOf('5') >= 0 ? 'primary' : 'white',
      textColor: formfields.value.rooms.indexOf('5') >= 0 ? 'white' : 'grey',
      onOff: formfields.value.rooms.indexOf('5') >= 0 ? true : false
    },
    {
      label: '5+',
      val: '5+',
      color: formfields.value.rooms.indexOf('5+') >= 0 ? 'primary' : 'white',
      textColor: formfields.value.rooms.indexOf('5+') >= 0 ? 'white' : 'grey',
      onOff: formfields.value.rooms.indexOf('5+') >= 0 ? true : false
    },
  ])

  const setRoomButtonActive = (button, index) => {
    if(button.onOff) {
      roomButtons.value[index].color = 'white'
      roomButtons.value[index].textColor = 'grey'
      roomButtons.value[index].onOff = false
      const elemInd = formfields.value.rooms.indexOf(button.val)
      formfields.value.rooms.splice(elemInd, 1)
    } else {
      roomButtons.value[index].color = 'primary'
      roomButtons.value[index].textColor = 'white'
      roomButtons.value[index].onOff = true
      formfields.value.rooms.push(button.val)
    }
  }

  const emitter = useEmitter()
  watch(formfields.value, () => { 
    emitter.emit('secondary-filter-changed', formfields.value)
  })

  return {
    filterStore,
    showMoreFilterParams,
    filters,
    filterCategory,
    filterDistricts,
    filterAgencies,
    filterSatusLabels,
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