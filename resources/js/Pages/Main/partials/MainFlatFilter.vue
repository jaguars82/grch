<template>
  <div class="row justify-center q-my-lg" :class="{ 'width-98': $q.screen.lt.md, 'width-80': $q.screen.gt.sm, 'items-center': $q.screen.gt.sm }">
    <div class="col-12">
      <!-- Region Selector -->
      <RegionSelector :regions="regions" />
    </div>
    <div :class="{ 'col-12': $q.screen.xs, 'col-10': $q.screen.gt.xs }">


      <!-- Newbuilding Complex, Developer, Location combined selector -->
      <div class="row q-col-gutter-none">
        <div class="col-12 col-sm-10 col-md-8 no-padding">
          <q-input
            square
            outlined
            v-model="comboFieldValue"
            label="ЖК, застройщик, город, район, улица"
            class="search-input combo-field"
            :class="{ 'rounded-left': $q.screen.xs, 'rounded-right': $q.screen.xs || $q.screen.sm }"
            dense
            @update:model-value="onComboFieldChange"
          >
          </q-input>

          <!-- A list of variants to select -->
          <q-menu
            target=".combo-field"
            no-parent-event
            no-focus
            v-model="showComboFieldPopup"
            transition-show="scale"
            transition-hide="scale"
          >
            <div>Подсказки</div>
          </q-menu>

          <!-- Combined field current selections -->
          <!-- City -->
          <template v-if="cfSelectedCity.length">
            <q-chip v-for="city of cfSelectedCity" size="sm" class="q-mt-none cf-chip" outline color="primary" icon="near_me" removable @remove="citySelect = null">
              {{ city.label }}
            </q-chip>
          </template>
          <!-- District (of a city or a settlement) -->
          <template v-if="cfSelectedDistrict.length">
            <q-chip v-for="district of cfSelectedDistrict" size="sm" class="q-mt-none cf-chip" outline color="grey-8" icon="home_work" removable @remove="removeItemFromDistrictSelection(district.value)">
              {{ district.label }}
            </q-chip>
          </template>
          <!-- Developer -->
          <template v-if="cfSelectedDeveloper.length">
            <q-chip v-for="developer of cfSelectedDeveloper" size="sm" class="q-mt-none cf-chip" outline color="orange-9" icon="engineering" removable @remove="removeItemFromDeveloperSelection(developer.value)">
              {{ developer.label }}
            </q-chip>
          </template>
          <!-- Newbuilding Complex -->
          <template v-if="cfSelectedNewbuildingComplex.length">
            <q-chip v-for="complex of cfSelectedNewbuildingComplex" size="sm" class="q-mt-none cf-chip" outline color="positive" icon="location_city" removable @remove="removeItemFromNewbuildingComplexSelection(complex.value)">
              {{ complex.label }}
            </q-chip>
          </template>
        </div>
      </div>


      <div class="row q-col-gutter-none">
        <!-- Sattlement (city, town etc.) -->
        <div class="col-12 col-sm-4 col-md-2 no-padding">
          <q-select
            square
            outlined
            v-model="citySelect"
            :options="cityOptions"
            label="Населенный пункт"
            class="search-input"
            :class="{ 'rounded-left': true, 'rounded-right': $q.screen.xs }"
            @update:model-value="onCitySelect"
            emit-value
            map-options
            options-dense
            dense
          >
            <template v-slot:option="scope">
              <q-item v-bind="scope.itemProps">
                <q-item-section>
                  <span :class="{'text-bold': scope.opt.regionCenter == 1}">
                    {{ scope.opt.label }}
                  </span>
                </q-item-section>
              </q-item>
            </template>
          </q-select>
        </div>
        <!-- District of the sattlement -->
        <div class="col-12 col-sm-4 col-md-2 no-padding">
          <q-select
            square
            outlined
            v-model="districtSelect"
            :options="districtOptions"
            label="Район"
            class="search-input"
            :class="{ 'rounded-left': $q.screen.xs, 'rounded-right': $q.screen.xs }"
            multiple
            use-chips
            emit-value
            map-options
            options-dense
            dense
          >
          </q-select>
        </div>
        <!-- Amount of rooms and flat type (standart/euro/studio) -->
        <div class="col-12 col-sm-4 col-md-2 no-padding" style="cursor: pointer !important;">
          <q-input
            square
            outlined
            readonly
            v-model="roomsSelect"
            @click="showRoomsPopup = true"
            label="Комнат"
            class="search-input"
            :class="{ 'rounded-left': $q.screen.xs, 'rounded-right': $q.screen.xs || $q.screen.sm }"
            dense
          >
            <template v-slot:append>
              <q-icon name="edit_note" class="cursor-pointer">
                <q-popup-proxy
                  v-model="showRoomsPopup"
                  cover
                  transition-show="scale"
                  transition-hide="scale"
                >
                  <q-card>
                    <q-card-section>
                      <RoomsAmountButtons :roomsAmount="roomsSelect" />
                    </q-card-section>
                    <q-card-section>
                      <FlatTypeToggler :flatType="flatTypeSelect" />
                    </q-card-section>
                  </q-card>
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
        </div>
        <!-- Developer -->
        <div class="col-12 col-sm-4 col-md-2 no-padding">
          <q-select
            square
            outlined
            v-model="developerSelect"
            :options="developerOptions"
            label="Застройщик"
            class="search-input"
            :class="{ 'rounded-left': $q.screen.xs || $q.screen.sm, 'rounded-right': $q.screen.xs }"
            multiple
            use-chips
            emit-value
            map-options
            options-dense
            dense
          />
        </div>
        <!-- Newbuilding Complex -->
        <div class="col-12 col-sm-4 col-md-2 no-padding">
          <q-select
            square
            outlined
            v-model="newbuildingComplexesSelect"
            :options="newbuildingComplexesOptions"
            label="Жилой комплекс"
            class="search-input"
            :class="{ 'rounded-left': $q.screen.xs, 'rounded-right': $q.screen.xs }"
            multiple
            use-chips
            emit-value
            map-options
            options-dense
            dense
          />
        </div>
        <!-- Price -->
        <div class="col-12 col-sm-4 col-md-2 no-padding cursor-pointer">
          <q-input
            square
            outlined
            readonly
            v-model="priceLabel"
            @click="showPricePopup = true"
            label="Цена"
            class="search-input"
            :class = "{ 'rounded-left': $q.screen.xs, 'rounded-right': true }"
            dense
          >
            <template v-slot:append>
              <q-icon name="tune" class="cursor-pointer">
                <q-popup-proxy
                  v-model="showPricePopup"
                  cover
                  transition-show="scale"
                  transition-hide="scale"
                >
                  <q-card class="popup-controls-container">
                    <q-card-section>
                      <PriceRangeWithToggler
                        :priceRange="priceRangeSelect"
                        :priceType="priceTypeSelect"
                        :rangeEdges="{
                          all: { min: initialFilterParams.minFlatPrice, max: initialFilterParams.maxFlatPrice },
                          m2: { min: initialFilterParams.minM2Price, max: initialFilterParams.maxM2Price }
                        }"
                      />
                    </q-card-section>
                  </q-card>
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
        </div>
      </div>
    </div>
    <!-- Form action buttons -->
    <div class="col-2 self-start" :class="{ 'col-2': $q.screen.gt.xs, 'col-12': $q.screen.xs, 'text-right': $q.screen.xs, 'q-mt-sm': $q.screen.xs }">
      <q-btn color="primary" :size="$q.screen.sm ? 'sm' : 'md'" class="text-white q-ml-sm q-mr-xs" unelevated round icon="search" @click="search" />
      <q-btn color="white" :size="$q.screen.sm ? 'sm' : 'md'" class="text-grey-7" unelevated round icon="pin_drop" @click="mapSearch" />
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch, watchEffect, nextTick } from 'vue'
import { idNameObjToOptions } from '@/composables/formatted-and-processed-data'
import { Inertia } from '@inertiajs/inertia'
import axios from 'axios'
import RegionSelector from '@/Components/Elements/RegionSelector.vue'
import RoomsAmountButtons from '@/Components/Elements/RoomsAmountButtons.vue'
import useEmitter from '@/composables/use-emitter'
import FlatTypeToggler from '@/Components/Elements/FlatTypeToggler.vue'
import PriceRangeWithToggler from '@/Components/Elements/Ranges/PriceRangeWithToggler.vue'

export default {
  props: {
    initialFilterParams: Object,
    regions: Object,
    developers: Object,
    newbuildingComplexes: Object
  },
  components: { RegionSelector, RoomsAmountButtons, FlatTypeToggler, PriceRangeWithToggler },
  setup(props) {
    const emitter = useEmitter()

    const regionSelect = ref(null)

    const citiesForRegion = ref(null)
    const citySelect = ref(null)
    const cityOptions = computed(() => {
      const options = []
      if (citiesForRegion.value) {
        citiesForRegion.value.forEach(city => {
          options.push({ label: city.name, value: city.id, regionCenter: city.is_region_center })
        })
      }
      return options
    })

    const districtsOfCity = ref(null)
    const districtSelect = ref(null)
    const districtOptions = computed(() => {
      const options = []
      /*Object.keys(props.districts).forEach(districtId => {
        options.push({ label: props.districts[districtId], value: districtId })
      })*/
      if (districtsOfCity.value) {
        districtsOfCity.value.forEach(district => {
          options.push({ label: district.name, value: district.id })
        })
      }
      return options
    })

    const onCitySelect = () => {
      districtSelect.value = null
      axios.post('/city/get-for-city?id=' + citySelect.value)
      .then(function (response) {
        districtsOfCity.value = response.data
      })
      .catch(function (error) {
        console.log(error)
      })
    }

    const showRoomsPopup = ref(false)

    const developerSelect = ref(null)
    const refrashedDevelopers = ref(null)
    const developerOptions = ref(idNameObjToOptions(props.developers))

    watchEffect(() => {
      developerOptions.value = refrashedDevelopers.value
        ? idNameObjToOptions(refrashedDevelopers.value)
        : idNameObjToOptions(props.developers)
    })

    const isLoadingDevelopers = ref(false)
    
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
      newbuildingComplexesSelect.value = null

      fetchDevelopers(newCityId, newRegionId, newDistrictId)
    }, { deep: true })

    /*const onDeveloperSelect = () => {
      axios.post('/newbuilding-complex/get-for-developer?id=' + developerSelect.value, { region_id: regionSelect.value, city_id: citySelect.value, district_id: districtSelect.value })
      .then(function (response) {
        newbuildingComplexesForDevelopers.value = response.data
      })
      .catch(function (error) {
        console.log(error)
      })
    }*/

    watch (developerSelect, (newVal) => {
      axios.post('/newbuilding-complex/get-for-developer?id=' + newVal, { region_id: regionSelect.value, city_id: citySelect.value, district_id: districtSelect.value })
      .then(function (response) {
        newbuildingComplexesForDevelopers.value = response.data
      })
      .catch(function (error) {
        console.log(error)
      })
    }, { deep: true })

    const newbuildingComplexesForDevelopers = ref(null)
    const newbuildingComplexesSelect = ref(null)
    const newbuildingComplexesOptions = computed(() => {
      const options = []
      if (newbuildingComplexesForDevelopers.value) {
        newbuildingComplexesForDevelopers.value.forEach(nbc => {
          options.push({ label: nbc.name, value: nbc.id })
        })
      }
      return options
    })

    // remove excess positions from selected newbuilding complexes
    watch(
      newbuildingComplexesOptions,
      (newOptions) => {
        const validValues = newOptions.map(option => option.value)
        newbuildingComplexesSelect.value = newbuildingComplexesSelect.value.filter(value => validValues.includes(value))
      },
      { deep: true }
    )

    emitter.on('region-changed', (payload) => {
      regionSelect.value = payload
      axios.post('/city/get-for-region?id=' + payload)
      .then(function (response) {
        citiesForRegion.value = response.data
      })
      .catch(function (error) {
        console.log(error)
      })
    })

    const roomsSelect = ref([])
    emitter.on('rooms-amont-changed', (payload) => {
      roomsSelect.value = payload
    })

    const flatTypeSelect = ref('0')
    emitter.on('flat-type-changed', (payload) => {
      flatTypeSelect.value = payload
    })

    const showPricePopup = ref(false)
    const priceLabel = ref('')
    const priceRangeSelect = ref({ min: null, max: null })
    emitter.on('price-changed', (payload) => {
      priceLabel.value = `${payload.min} - ${payload.max}`
      priceRangeSelect.value = payload
    })

    const priceTypeSelect = ref('0')
    emitter.on('price-type-changed', (payload) => {
      priceTypeSelect.value = payload
    })

    /** Combo Field Logic */
    const comboFieldValue = ref(null)
    const showComboFieldPopup = ref(false)

    const onComboFieldChange = () => {
      showComboFieldPopup.value = true
    }

    /** Combofield selected values */
    const cfSelectedCity = computed(() => {
      return cityOptions.value.filter((option) => {
        return option.value == citySelect.value
      })
    })

    const cfSelectedDistrict = computed(() => {
      let items = []

      if (districtSelect.value === null || !districtSelect.value.length) return items

      items = districtOptions.value.filter((option) => {
        return districtSelect.value.includes(option.value)
      })

      return items
    })

    const removeItemFromDistrictSelection = (itemId) => {
      const itemIndex = districtSelect.value.indexOf(itemId)
      if (itemIndex !== -1) {
        districtSelect.value.splice(itemIndex, 1)
      }
    }

    const cfSelectedDeveloper = computed(() => {
      let items = []

      if (developerSelect.value === null || !developerSelect.value.length) return items

      items = developerOptions.value.filter((option) => {
        return developerSelect.value.includes(option.value)
      })

      return items
    })

    const removeItemFromDeveloperSelection = (itemId) => {
      const itemIndex = developerSelect.value.indexOf(itemId)
      if (itemIndex !== -1) {
        developerSelect.value.splice(itemIndex, 1)
      }
    }

    const cfSelectedNewbuildingComplex = computed(() => {
      let items = []

      if (newbuildingComplexesSelect.value === null || !newbuildingComplexesSelect.value.length) return items

      items = newbuildingComplexesOptions.value.filter((option) => {
        return newbuildingComplexesSelect.value.includes(option.value)
      })

      return items
    })

    const removeItemFromNewbuildingComplexSelection = (itemId) => {
      const itemIndex = newbuildingComplexesSelect.value.indexOf(itemId)
      if (itemIndex !== -1) {
        newbuildingComplexesSelect.value.splice(itemIndex, 1)
      }
    }

    const collectSearchFilter = () => {
      return {
        region_id: regionSelect.value,
        city_id: citySelect.value,
        district: districtSelect.value,
        roomsCount: roomsSelect.value,
        flatType: flatTypeSelect.value ? flatTypeSelect.value : '0',
        developer: developerSelect.value,
        newbuilding_complex: newbuildingComplexesSelect.value,
        priceType: priceRangeSelect.value ? priceTypeSelect.value : '0',
        priceFrom: priceRangeSelect.value.min,
        priceTo: priceRangeSelect.value.max
      }
    }

    const search = () => {
      Inertia.get(`site/search`, { AdvancedFlatSearch: collectSearchFilter() })
    }

    const mapSearch = () => {
      Inertia.get(`site/map`, { MapFlatSearch: collectSearchFilter() })
    }

    return {
      citySelect,
      cityOptions,
      districtSelect,
      districtOptions,
      onCitySelect,
      showRoomsPopup,
      developerSelect,
      developerOptions,
      //onDeveloperSelect,
      roomsSelect,
      flatTypeSelect,
      showPricePopup,
      priceLabel,
      priceRangeSelect,
      priceTypeSelect,
      newbuildingComplexesSelect,
      newbuildingComplexesOptions,
      comboFieldValue,
      showComboFieldPopup,
      onComboFieldChange,
      cfSelectedCity,
      cfSelectedDistrict,
      removeItemFromDistrictSelection,
      cfSelectedDeveloper,
      removeItemFromDeveloperSelection,
      cfSelectedNewbuildingComplex,
      removeItemFromNewbuildingComplexSelection,
      search,
      mapSearch,
    }
  }
}
</script>

<style>
.search-input .q-field__native {
  cursor: pointer !important;
}
.search-input.q-field--outlined .q-field__control:before {
  border-style: none;
}
</style>

<style scoped>
.search-input, .cf-chip {
  background-color: rgba(255,255,255,.7) !important;
}
.rounded-left {
  border-top-left-radius: 20px;
  border-bottom-left-radius: 20px;
}
.rounded-right {
  border-top-right-radius: 20px;
  border-bottom-right-radius: 20px;
}
</style>

