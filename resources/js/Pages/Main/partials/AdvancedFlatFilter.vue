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
    clearable
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
    :key="developerOptions.length" 
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
import { ref, computed, watch, watchEffect, nextTick } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import axios from 'axios'
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
    // Region
    const regionOptions = computed(() => idNameObjToOptions(props.regions))
    const initRegionSelect = computed(() => selectOneFromOptionsList(regionOptions.value, props.searchModel.region_id))
    const regionSelect = ref(initRegionSelect.value)

    // City (sattlement)
    const refrashedCities = ref(null)

    const cityOptions = computed(() => {
      let options = []
      if (refrashedCities.value !== null) {
        refrashedCities.value.forEach(city => {
          options.push({
            label: city.name,
            value: city.id,
            is_region_center: city.is_region_center,
          })
        })
      } else {
        options = idNameObjToOptions(props.cities)
      }
      return options
    })

    const initCitySelect = computed(() => selectOneFromOptionsList(cityOptions.value, props.searchModel.city_id))
    const citySelect = ref(initCitySelect.value)

    // District of a sattlement
    const refrashedDistricts = ref(null)

    const districtOptions = computed(() => {
      let options = []
      if (refrashedDistricts.value !== null) {
        refrashedDistricts.value.forEach(district => {
          options.push({
            label: district.name,
            value: district.id,
          })
        })
      } else {
        options = idNameObjToOptions(props.districts)
      }
      return options
    })
    const initDistrictSelect = computed(() => selectMultipleFromOptionsList(districtOptions.value, props.searchModel.district))
    const districtSelect = ref(initDistrictSelect.value)

    // Developer
    const refrashedDevelopers = ref(null)
    const developerOptions = ref(idNameObjToOptions(props.developers))

    watchEffect(() => {
      developerOptions.value = refrashedDevelopers.value
        ? idNameObjToOptions(refrashedDevelopers.value)
        : idNameObjToOptions(props.developers)
    })
    
    /*const developerOptions = computed(() => {
      let options = []
      if (refrashedDevelopers.value !== null) {
        options = idNameObjToOptions(refrashedDevelopers.value)
      } else {
        options = idNameObjToOptions(props.developers)
      }
      return options
    })*/

    const initDeveloperSelect = computed(() => selectMultipleFromOptionsList(developerOptions.value, props.searchModel.developer))
    const developerSelect = ref(initDeveloperSelect.value)
    const isLoadingDevelopers = ref(false)

    // Newbuilding Complexes
    const refrashedNewbuildingComplexes = ref(null)

    const newbuildingComplexOptions = computed(() => {
      let options = []
      if (refrashedNewbuildingComplexes.value !== null) {
        refrashedNewbuildingComplexes.value.forEach(nbc => {
          options.push({
            label: nbc.name,
            value: nbc.id,
          })
        })
      } else {
        options = idNameObjToOptions(props.newbuildingComplexes)
      }
      return options
    })
    const initNewbuildingComplexSelect = computed(() => selectMultipleFromOptionsList(newbuildingComplexOptions.value, props.searchModel.newbuilding_complex))
    const newbuildingComplexSelect = ref(initNewbuildingComplexSelect.value)

    // Newbuildings
    const refrashedNewbuildings = ref(null)

    const newbuildingOptions = computed(() => {
      let options = []
      if (refrashedNewbuildings.value !== null) {
        refrashedNewbuildings.value.forEach(newbuilding => {
          options.push({
            label: newbuilding.name,
            value: newbuilding.id,
          })
        })
      } else {
        options = idNameObjToOptions(props.positions)
      }
      return options
    })
    const initNewbuildingSelect = computed(() => selectMultipleFromOptionsList(newbuildingOptions.value, props.searchModel.newbuilding_array))
    const newbuildingSelect = ref(initNewbuildingSelect.value)

    const materialOptions = computed(() => idNameObjToOptions(props.material))
    const initMaterialSelect = computed(() => selectOneFromOptionsList(materialOptions.value, props.searchModel.material))
    const materialSelect = ref(initMaterialSelect.value)

    const deadlineYearOptions = computed(() => idNameObjToOptions(props.deadlineYears))
    const initDeadlineYearSelect = computed(() => selectOneFromOptionsList(deadlineYearOptions.value, props.searchModel.deadlineYear))
    const deadlineYearSelect = ref(initDeadlineYearSelect.value)
   
    /** amount of rooms */
    const roomsSelect = ref(('roomsCount' in props.searchModel) ? [...props.searchModel.roomsCount] : [])
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

    /* watch filter fields and re-initialize dependent filds */
    // Cities for region
    watch (regionSelect, (regionId) => {
      axios.post('/city/get-for-region?id=' + regionId)
      .then(function (response) {
        refrashedCities.value = response.data
      })
      .catch(function (error) {
        console.log(error)
      })
    }, { deep: true })

    // Districts of the sattlement
    watch (citySelect, (cityId) => {
      axios.post('/city/get-for-city?id=' + cityId)
      .then(function (response) {
        refrashedDistricts.value = response.data
      })
      .catch(function (error) {
        console.log(error)
      })
    }, { deep: true })

    // Developers
    const fetchDevelopers = async (cityId = null, regionId = null, districtId = null) => {
      
      if (Array.isArray(districtId) && districtId.length === 0) {
        districtId = null;
      }

      let action, argumentName, locationId;

      if (districtId) {
        action = 'get-developers-for-city-district';
        argumentName = 'district_id';
        locationId = districtId;
      } else if (cityId) {
        action = 'get-developers-for-city';
        argumentName = 'city_id';
        locationId = cityId;
      } else {
        action = 'get-developers-for-region';
        argumentName = 'region_id';
        locationId = regionId;
      }

      isLoadingDevelopers.value = true; // set the flag of loading developers

      try {
        const response = await axios.post(`/developer/${action}`, { [argumentName]: locationId });
        
        developerOptions.value = idNameObjToOptions(response.data)
        
        await nextTick()

      } catch (error) {
        console.error("Error loading developers:", error);
      } finally {
        isLoadingDevelopers.value = false // reset the loading flag
      }
    }

    // Developers on citySelect/regionSelect/districtSelect change
    watch([citySelect, regionSelect, districtSelect], ([newCityId, newRegionId, newDistrictId]) => {

      developerSelect.value = null
      newbuildingComplexSelect.value = null

      fetchDevelopers(newCityId, newRegionId, newDistrictId)
    })

    // Newbuilding Complexes for selected developers (considering location)
    watch (developerSelect, (developerId) => {
      newbuildingComplexSelect.value = null

      axios.post('/newbuilding-complex/get-for-developer?id=' + developerId, { city_id: citySelect.value, region_id: regionSelect.value, district_id: districtSelect.value })
      .then(function (response) {
        refrashedNewbuildingComplexes.value = response.data
      })
      .catch(function (error) {
        console.log(error)
      })
    }, { deep: true })

    // Newbuildings for the Newbuilding Complex
    watch (newbuildingComplexSelect, (nbcId) => {
      newbuildingSelect.value = null

      axios.post('/newbuilding/get-for-newbuilding-complex?id=' + nbcId)
      .then(function (response) {
        refrashedNewbuildings.value = response.data
      })
      .catch(function (error) {
        console.log(error)
      })
    }, { deep: true })


    // Emit an event on filteres change
    const emitChanges = async () => {
      await nextTick() // await finishing all reactive processes
      emitter.emit('flat-filter-changed', filterState.value)
    }

    /* Return filters to initial values if user closes filter confirmation dialog */
    emitter.on('close-filter-change-dialog', payload => {
      Inertia.get(`/site/${payload.action}`, { [payload.searchType]: props.searchModel }, { /*preserveState: true,*/ preserveScroll: true })
    })

    return {
      regionOptions,
      regionSelect,
      cityOptions,
      citySelect,
      districtOptions,
      districtSelect,
      developerSelect,
      developerOptions,
      isLoadingDevelopers,
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
      emitChanges,
    }
  }
}
</script>