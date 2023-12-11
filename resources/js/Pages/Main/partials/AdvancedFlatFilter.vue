<template>
  <q-select 
    outlined
    v-model="regionSelect"
    :options="regionOptions"
    label="Регион"
    dense
    emit-value
    map-options
    options-dense
    @update:model-value="emitChanges"
  />
  <q-select 
    outlined
    v-model="citySelect"
    :options="cityOptions"
    label="Населённый пункт"
    dense
    emit-value
    map-options
    options-dense
    @update:model-value="emitChanges"
  />
  <q-select 
    outlined
    v-model="districtSelect"
    :options="districtOptions"
    label="Район населённого пункта"
    dense
    emit-value
    map-options
    options-dense
    multiple
    use-chips
    @update:model-value="emitChanges"
  />
  <q-select 
    outlined
    v-model="developerSelect"
    :options="developerOptions"
    label="Застройщик"
    dense
    emit-value
    map-options
    options-dense
    multiple
    use-chips
    @update:model-value="emitChanges"
  />
  <q-select 
    outlined
    v-model="newbuildingComplexSelect"
    :options="newbuildingComplexOptions"
    label="Жилой комплекс"
    dense
    emit-value
    map-options
    options-dense
    multiple
    use-chips
    @update:model-value="emitChanges"
  />
  
  <h5>Количество комнат</h5>
  <RoomsAmountButtons :roomsAmount="roomsSelect" />
  <FlatTypeToggler :flatType="flatTypeSelect" />
  
  <h5>Стоимость</h5>
  <PriceRangeWithToggler
    :inputs="true"
    :priceRange="priceRangeSelect"
    :priceType="priceTypeSelect"
    :rangeEdges="{
      all: { min: rangeEdges.priceForAll.min, max: rangeEdges.priceForAll.max },
      m2: { min: rangeEdges.priceForM2.min, max: rangeEdges.priceForM2.max }
    }"
  />

  <RangeWithInputs title="Площадь" name="area" :model="areaRangeSelect" :edges="rangeEdges.area" />
  <RangeWithInputs title="Этаж" name="floor" :model="floorRangeSelect" :edges="rangeEdges.floor" />
  <RangeWithInputs title="Этажность" name="totalFloor" :model="totalFloorRangeSelect" :edges="rangeEdges.total_floor" />

  <q-select 
    outlined
    v-model="newbuildingSelect"
    :options="newbuildingOptions"
    label="Строительная позиция"
    dense
    emit-value
    map-options
    options-dense
    multiple
    use-chips
    @update:model-value="emitChanges"
  />
  <q-select 
    outlined
    clearable
    v-model="materialSelect"
    :options="materialOptions"
    label="Материал"
    dense
    emit-value
    map-options
    options-dense
    @update:model-value="emitChanges"
  />
  <q-checkbox v-model="newbuildingStatusSelect" label="Дом сдан" @update:model-value="emitChanges" />
  <q-select 
    outlined
    clearable
    v-model="deadlineYearSelect"
    :options="deadlineYearOptions"
    label="Год сдачи"
    dense
    emit-value
    map-options
    options-dense
    @update:model-value="emitChanges"
  />
</template>

<script>
import { ref, computed, watch } from 'vue'
import RoomsAmountButtons from '@/Components/Elements/RoomsAmountButtons.vue'
import FlatTypeToggler from '@/Components/Elements/FlatTypeToggler.vue'
import PriceRangeWithToggler from '@/Components/Elements/Ranges/PriceRangeWithToggler.vue'
import RangeWithInputs from '@/Components/Elements/Ranges/RangeWithInputs.vue'
import useEmitter from '@/composables/use-emitter'
import { idNameObjToOptions, selectOneFromOptionsList, selectMultipleFromOptionsList, getValueOfAnOption } from '@/composables/formatted-and-processed-data'

export default {
  props: {
    searchModel: Object,
    regions: Object,
    cities: Object,
    districts: Object,
    developers: Object,
    newbuildingComplexes: Object,
    positions: Object,
    material: Object,
    deadlineYears: Object,
    rangeEdges: Object,
  },
  components: {
    RoomsAmountButtons,
    FlatTypeToggler,
    PriceRangeWithToggler,
    RangeWithInputs
  },
  setup (props) {
    const emitter = useEmitter()

    /** selectable fields */
    const regionOptions = computed(() => idNameObjToOptions(props.regions))
    const initRegionSelect = computed(() => selectOneFromOptionsList(regionOptions.value, props.searchModel.region_id))
    const regionSelect = ref(initRegionSelect.value)

    const cityOptions = computed(() => idNameObjToOptions(props.cities))
    const initCitySelect = computed(() => selectOneFromOptionsList(cityOptions.value, props.searchModel.city_id))
    const citySelect = ref(initCitySelect.value)

    const districtOptions = computed(() => idNameObjToOptions(props.districts))
    const initDistrictSelect = computed(() => selectMultipleFromOptionsList(districtOptions.value, props.searchModel.district))
    const districtSelect = ref(initDistrictSelect.value)

    const developerOptions = computed(() => idNameObjToOptions(props.developers))
    const initDeveloperSelect = computed(() => selectMultipleFromOptionsList(developerOptions.value, props.searchModel.developer))
    const developerSelect = ref(initDeveloperSelect.value)

    const newbuildingComplexOptions = computed(() => idNameObjToOptions(props.newbuildingComplexes))
    const initNewbuildingComplexSelect = computed(() => selectMultipleFromOptionsList(newbuildingComplexOptions.value, props.searchModel.newbuilding_complex))
    const newbuildingComplexSelect = ref(initNewbuildingComplexSelect.value)

    const newbuildingOptions = computed(() => idNameObjToOptions(props.positions))
    const initNewbuildingSelect = computed(() => selectMultipleFromOptionsList(newbuildingOptions.value, props.searchModel.newbuilding_array))
    const newbuildingSelect = ref(initNewbuildingSelect.value)

    const materialOptions = computed(() => idNameObjToOptions(props.material))
    const initMaterialSelect = computed(() => selectOneFromOptionsList(materialOptions.value, props.searchModel.material))
    const materialSelect = ref(initMaterialSelect.value)

    const deadlineYearOptions = computed(() => idNameObjToOptions(props.deadlineYears))
    const initDeadlineYearSelect = computed(() => selectOneFromOptionsList(deadlineYearOptions.value, props.searchModel.deadlineYear))
    const deadlineYearSelect = ref(initDeadlineYearSelect.value)
   
    /** amount of rooms */
    const roomsSelect = ref(('roomsCount' in props.searchModel) ? props.searchModel.roomsCount : [])
    emitter.on('rooms-amont-changed', (payload) => {
      roomsSelect.value = payload
      emitChanges()
    })

    /** Type of flat */
    const flatTypeSelect = ref(props.searchModel.flatType)
    emitter.on('flat-type-changed', (payload) => {
      flatTypeSelect.value = payload
      emitChanges()
    })

    /** Price */
    const priceRangeSelect = ref({ min: props.searchModel.priceFrom ? props.searchModel.priceFrom : null, max: props.searchModel.priceTo ? props.searchModel.priceTo : null })
    emitter.on('price-changed', (payload) => {
      priceRangeSelect.value = payload
      emitChanges()
    })

    /** Toggle between 'price for all' and 'price for m2' */
    const priceTypeSelect = ref(props.searchModel.priceType)
    emitter.on('price-type-changed', (payload) => {
      priceTypeSelect.value = payload
    })

    /** Range values */
    /** Area */
    const areaRangeSelect = ref({ min: props.searchModel.areaFrom ? props.searchModel.areaFrom : null, max: props.searchModel.areaTo ? props.searchModel.areaTo : null })
    emitter.on('area-range-changed', (payload) => {
      console.log(payload)
      areaRangeSelect.value = payload
      emitChanges()
    })
    /** Floor */
    const floorRangeSelect = ref({ min: props.searchModel.floorFrom ? props.searchModel.floorFrom : null, max: props.searchModel.floorTo ? props.searchModel.floorTo : null })
    emitter.on('floor-range-changed', (payload) => {
      floorRangeSelect.value = payload
      emitChanges()
    })
    /** Total floors */
    const totalFloorRangeSelect = ref({ min: props.searchModel.totalFloorFrom ? props.searchModel.totalFloorFrom : null, max: props.searchModel.totalFloorTo ? props.searchModel.totalFloorTo : null })
    emitter.on('totalFloor-range-changed', (payload) => {
      totalFloorRangeSelect.value = payload
      emitChanges()
    })

    /** Newbuilding status checkbox */
    const newbuildingStatusSelect = ref( props.searchModel.newbuilding_status == 1 ? true : false )

    /** collect filter values & emit an event */
    const filterState = computed (() => {
      return {
        region_id: getValueOfAnOption(regionSelect.value),
        city_id: getValueOfAnOption(citySelect.value),
        district: getValueOfAnOption(districtSelect.value),
        developer: getValueOfAnOption(developerSelect.value),
        newbuilding_complex: getValueOfAnOption(newbuildingComplexSelect.value),
        newbuilding_array: getValueOfAnOption(newbuildingSelect.value),
        material: getValueOfAnOption(materialSelect.value),
        deadlineYear: getValueOfAnOption(deadlineYearSelect.value),
        roomsCount: roomsSelect.value ? roomsSelect.value : [],
        flatType: flatTypeSelect.value ? flatTypeSelect.value : '0',
        priceFrom: priceRangeSelect.value.min ? priceRangeSelect.value.min : "",
        priceTo: priceRangeSelect.value.max ? priceRangeSelect.value.max : "",
        priceType: priceTypeSelect.value ? priceTypeSelect.value : '0',
        areaFrom: areaRangeSelect.value.min ? areaRangeSelect.value.min : "",
        areaTo: areaRangeSelect.value.max ? areaRangeSelect.value.max : "",
        floorFrom: floorRangeSelect.value.min ? floorRangeSelect.value.min : "",
        floorTo: floorRangeSelect.value.max ? floorRangeSelect.value.max : "",
        totalFloorFrom: totalFloorRangeSelect.value.min ? totalFloorRangeSelect.value.min : "",
        totalFloorTo: totalFloorRangeSelect.value.max ? totalFloorRangeSelect.value.max : "",
        newbuilding_status: newbuildingStatusSelect.value ? 1 : ""
      }
    })

    const emitChanges = () => {
      emitter.emit('flat-filter-changed', filterState.value)
    }

    return {
      regionOptions,
      regionSelect,
      cityOptions,
      citySelect,
      districtOptions,
      districtSelect,
      developerSelect,
      developerOptions,
      newbuildingComplexSelect,
      newbuildingComplexOptions,
      newbuildingSelect,
      newbuildingOptions,
      materialSelect,
      materialOptions,
      deadlineYearSelect,
      deadlineYearOptions,
      roomsSelect,
      flatTypeSelect,
      priceRangeSelect,
      priceTypeSelect,
      areaRangeSelect,
      floorRangeSelect,
      totalFloorRangeSelect,
      newbuildingStatusSelect,
      emitChanges
    }
  }
}
</script>