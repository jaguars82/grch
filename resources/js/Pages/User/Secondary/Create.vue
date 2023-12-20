<template>
  <MainLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <h3 class="q-mx-md">Создание объявления</h3>
      <Loading v-if="loading" />
      <q-form v-else @submit="onSubmit">
        <q-card class="q-mx-md">
          <q-card-section>
            <h4>Текст объявления</h4>
            <div class="row q-col-gutter-none">
              <div class="col-12">
                <q-input outlined v-model="formfields.detail" label="Введите текст объявления*" />
              </div>
            </div>
            <h4>Информация об объекте</h4>
            <div class="row q-col-gutter-none">

              <div class="col-md-6 col-sm-12 col-xs-12 q-py-xs">
                <q-select
                  outlined
                  v-model="formfields.dealType"
                  :options="dealTypeOptions"
                  label="Тип сделки*"
                  options-dense
                >
                </q-select>
              </div>

              <div class="col-md-6 col-sm-12 col-xs-12 q-py-xs">
                <q-select
                  outlined
                  v-model="formfields.category"
                  :options="categoryOptions"
                  label="Категория*"
                  options-dense
                />
              </div>

            </div>

            <div class="row q-col-gutter-none">

              <div class="col-md-4 col-sm-6 col-xs-12 q-py-xs">
                <q-input outlined type="number" v-model.number="formfields.price" label="Цена (руб.)*" />
              </div>

              <div :class="[formfields.rooms_select !== null && formfields.rooms_select.value == '5+' ? 'col-md-3' : 'col-md-5', 'col-sm-6', 'col-xs-12', 'q-py-xs']">
                <q-select
                  outlined
                  v-model="formfields.rooms_select"
                  :options="roomsOptions"
                  label="Количество комнат*"
                  options-dense
                />
              </div>

              <div v-if="formfields.rooms_select !== null && formfields.rooms_select.value == '5+'" class="col-md-2 col-sm-6 col-xs-12 q-py-xs">
                <q-input outlined type="number" step="1" min="6" v-model.number="formfields.rooms_input" label="Комнат" />
              </div>

              <div class="col-md-3 col-sm-6 col-xs-12 q-py-xs">
                <q-input outlined type="number" step="0.1" v-model.number="formfields.area" label="Общая площадь (м2)*" />
              </div>

              <div class="col-md-4 col-sm-6 col-xs-12 q-py-xs">
                <q-input outlined type="number" step="0.1" v-model.number="formfields.kitchen_area" label="Площадь кухни (м2)" />
              </div>

              <div class="col-md-4 col-sm-6 col-xs-12 q-py-xs">
                <q-input outlined type="number" step="0.1" v-model.number="formfields.living_area" label="Жилая площадь (м2)" />
              </div>

              <div class="col-md-4 col-sm-6 col-xs-12 q-py-xs">
                <q-select
                  outlined
                  v-model="formfields.bathroom"
                  :options="bathroomOptions"
                  label="Санузел"
                  options-dense
                />
              </div>

              <div class="col-md-3 col-sm-6 col-xs-12 q-py-xs">
                <q-input outlined type="number" v-model.number="formfields.floor" label="Этаж*" />
              </div>

              <div class="col-md-3 col-sm-6 col-xs-12 q-py-xs">
                <q-input outlined type="number" min="1" v-model.number="formfields.total_floors" label="Этажность*" />
              </div>

              <div class="col-md-3 col-sm-6 col-xs-12 q-py-xs">
                <q-select
                  outlined
                  v-model="formfields.building_series"
                  :options="buildingSeriesOptions"
                  label="Тип дома"
                  options-dense
                />
              </div>

              <div class="col-md-3 col-sm-6 col-xs-12 q-py-xs">
                <q-select
                  outlined
                  v-model="formfields.material_select"
                  :options="materialOptions"
                  label="Материал дома"
                  options-dense
                />
              </div>

              <div class="col-md-4 col-sm-6 col-xs-12 q-py-xs">
                <q-select
                  outlined
                  v-model="formfields.quality"
                  :options="qualityOptions"
                  label="Состояние помещения"
                  options-dense
                />
              </div>

              <div class="col-md-4 col-sm-6 col-xs-12 q-py-xs">
                <q-select
                  outlined
                  v-model="formfields.renovation"
                  :options="renovationOptions"
                  label="Отделка/ремонт"
                  options-dense
                />
              </div>

              <div class="col-md-4 col-sm-6 col-xs-12 q-py-xs">
                <q-select
                  outlined
                  v-model="formfields.built_year"
                  :options="yearOptions"
                  label="Год постройки/сдачи"
                  options-dense
                >
                  <template v-slot:append>
                    <q-icon
                      v-if="formfields.built_year !== null"
                      class="cursor-pointer"
                      name="clear"
                      @click.stop.prevent="formfields.built_year = null"
                    />
                  </template>
                </q-select>
              </div>

              <div class="col-md-3 col-sm-6 col-xs-12 q-py-xs">
                <q-input outlined type="number" min="0" v-model.number="formfields.balcony_amount" label="Количество балконов*" />
              </div>

              <div class="col-md-3 col-sm-6 col-xs-12 q-py-xs">
                <q-input outlined type="number" min="0" v-model.number="formfields.loggia_amount" label="Количество лоджий*" />
              </div>

              <div class="col-md-3 col-sm-6 col-xs-12 q-py-xs">
                <q-input outlined type="number" min="0" v-model.number="formfields.elevator_passenger_amount" label="Пассажирских лифтов" />
              </div>

              <div class="col-md-3 col-sm-6 col-xs-12 q-py-xs">
                <q-input outlined type="number" min="0" v-model.number="formfields.elevator_freight_amount" label="Грузовых лифтов" />
              </div>

            </div>

            <div class="row q-col-gutter-none">

              <div
                v-for="field of [ 
                  { label: 'Вид из окон на улицу', name: 'windowview_street' },
                  { label: 'Вид из окон во двор', name: 'windowview_yard' },
                  { label: 'Панорамные окна', name: 'panoramic_windows' },
                  { label: 'Гардеробная', name: 'dressing_room' },
                  { label: 'Консьерж', name: 'concierge' },
                  { label: 'Мусоропровод', name: 'rubbish_chute' },
                  { label: 'Газопровод', name: 'gas_pipe' },
                  { label: 'Стационарный телефон', name: 'phone_line' },
                  { label: 'Интернет', name: 'internet' },
                  { label: 'Закрытая территория', name: 'closed_territory' },
                  { label: 'Детская площадка', name: 'playground' },
                  { label: 'Шлагбаум', name: 'barrier' },
                  { label: 'Подземная парковка', name: 'underground_parking' },
                  { label: 'Наземная парковка', name: 'ground_parking' },
                  { label: 'Открытая парковка', name: 'open_parking' },
                  { label: 'Многоуровневая парковка', name: 'multilevel_parking' },
                ]"
                class="col-md-4 col-sm-6 col-xs-12 q-py-xs"
              >
                <div class="bg-grey-1 rounded-borders q-pb-sm">
                  <q-toggle
                    toggle-indeterminate
                    v-model="formfields[field.name]"
                    :label="field.label"
                    checked-icon="check"
                    unchecked-icon="clear"
                  />
                  <TrippleStateLabel :condition="formfields[field.name]" labelTrue="Есть" />
                </div>
              </div>

            </div>

            <div class="row q-col-gutter-none">
              
              <div class="col-12">
                <h4>Жилой комплекс</h4>
              </div>

              <div class="col-md-2 col-sm-6 col-xs-12 q-py-xs">
                <q-input
                  v-if="manualInputFlags.developer"
                  outlined
                  v-model="formfields.developer_input"
                  label="Застройщик"
                />
                <q-select
                  v-else
                  outlined
                  v-model="formfields.developer_select"
                  :options="developerOptions"
                  label="Застройщик"
                  options-dense
                  @update:model-value="onDeveloperSelect"
                >
                  <template v-slot:append>
                    <q-icon
                      v-if="formfields.developer_select !== null"
                      class="cursor-pointer"
                      name="clear"
                      @click.stop.prevent="() => { 
                        formfields.developer_select = null
                        formfields.buildingComplex_select = null
                        formfields.building_select = null
                        formfields.entrance_select = null
                        formfields.flat_select = null
                      }"
                    />
                  </template>
                </q-select>
                <q-checkbox size="xs" v-model="manualInputFlags.developer" label="Вручную" @update:model-value="inputFlagsChange('developer', ['newbuildingComplex', 'newbuilding', 'entrance', 'flat'])">
                  <q-tooltip>
                    включить/отключить ручной ввод застройщика
                  </q-tooltip>
                </q-checkbox>
              </div>

              <div class="col-md-3 col-sm-6 col-xs-12 q-py-xs">
                <q-input
                  v-if="manualInputFlags.newbuildingComplex"
                  outlined
                  v-model="formfields.buildingComplex_input"
                  label="Жилой комплекс"
                />
                <q-select
                  v-else
                  outlined
                  v-model="formfields.buildingComplex_select"
                  :options="buildingComplexOptions"
                  label="Жилой комплекс"
                  options-dense
                  :disable="!buildingComplexOptions.length"
                  @update:model-value="onBuildingComplexSelect"
                >
                  <template v-slot:append>
                    <q-icon
                      v-if="formfields.buildingComplex_select !== null"
                      class="cursor-pointer"
                      name="clear"
                      @click.stop.prevent="() => { 
                        formfields.buildingComplex_select = null
                        formfields.building_select = null
                        formfields.entrance_select = null
                        formfields.flat_select = null
                      }"
                    />
                  </template>
                </q-select>
                <q-checkbox size="xs" v-model="manualInputFlags.newbuildingComplex" label="Вручную" :disable="manualInputFlags.developer === true" @update:model-value="inputFlagsChange('newbuildingComplex', ['newbuilding', 'entrance', 'flat'])">
                  <q-tooltip>
                    включить/отключить ручной ввод ЖК
                  </q-tooltip>
                </q-checkbox>
              </div>

              <div class="col-md-3 col-sm-6 col-xs-12 q-py-xs">
                <q-input
                  v-if="manualInputFlags.newbuilding"
                  outlined
                  v-model="formfields.building_input"
                  label="Позиция"
                  :disable="!formfields.buildingComplex_input && !formfields.buildingComplex_select"
                />
                <q-select
                  v-else
                  outlined
                  v-model="formfields.building_select"
                  :options="buildingOptions"
                  label="Позиция"
                  options-dense
                  :disable="!buildingOptions.length"
                  @update:model-value="onBuildingSelect"
                >
                  <template v-slot:append>
                    <q-icon
                      v-if="formfields.building_select !== null"
                      class="cursor-pointer"
                      name="clear"
                      @click.stop.prevent="() => { 
                        formfields.building_select = null
                        formfields.entrance_select = null
                        formfields.flat_select = null
                      }"
                    />
                  </template>
                </q-select>
                <q-checkbox size="xs" v-model="manualInputFlags.newbuilding" label="Вручную" :disable="manualInputFlags.newbuildingComplex === true" @update:model-value="inputFlagsChange('newbuilding', ['entrance', 'flat'])">
                  <q-tooltip>
                    включить/отключить ручной ввод позиции
                  </q-tooltip>
                </q-checkbox>
              </div>

              <div class="col-md-2 col-sm-6 col-xs-12 q-py-xs">
                <q-input
                  v-if="manualInputFlags.entrance"
                  outlined
                  v-model="formfields.entrance_input"
                  label="Подъезд"
                  :disable="!formfields.buildingComplex_input && !formfields.buildingComplex_select"
                />
                <q-select
                  v-else
                  outlined
                  v-model="formfields.entrance_select"
                  :options="entranceOptions"
                  label="Подъезд"
                  options-dense
                  :disable="!entranceOptions.length"
                  @update:model-value="onEntranceSelect"
                >
                  <template v-slot:append>
                    <q-icon
                      v-if="formfields.entrance_select !== null"
                      class="cursor-pointer"
                      name="clear"
                      @click.stop.prevent="() => { 
                        formfields.entrance_select = null
                        formfields.flat_select = null
                      }"
                    />
                  </template>
                </q-select>
                <q-checkbox size="xs" v-model="manualInputFlags.entrance" label="Вручную" :disable="manualInputFlags.newbuilding === true" @update:model-value="inputFlagsChange('entrance', ['flat'])">
                  <q-tooltip>
                    включить/отключить ручной ввод подъезда
                  </q-tooltip>
                </q-checkbox>
              </div>

              <div class="col-md-2 col-sm-6 col-xs-12 q-py-xs">
                <q-input
                  v-if="manualInputFlags.flat"
                  outlined
                  v-model="formfields.flat_input"
                  label="Квартира"
                  :disable="!formfields.buildingComplex_input && !formfields.buildingComplex_select"
                />
                <q-select
                  v-else
                  outlined
                  v-model="formfields.flat_select"
                  :options="flatOptions"
                  label="Квартира"
                  options-dense
                  :disable="!flatOptions.length"
                >
                  <template v-slot:append>
                    <q-icon
                      v-if="formfields.flat_select !== null"
                      class="cursor-pointer"
                      name="clear"
                      @click.stop.prevent="formfields.flat_select = null"
                    />
                  </template>
                </q-select>
                <q-checkbox size="xs" v-model="manualInputFlags.flat" label="Вручную" :disable="manualInputFlags.entrance === true" >
                  <q-tooltip>
                    включить/отключить ручной ввод квартиры
                  </q-tooltip>
                </q-checkbox>
              </div>

            </div>

          </q-card-section>
        </q-card>

        <q-card class="q-mx-md q-mt-md">
          <q-card-section>
            <h4>Фотографии</h4>

            <div class="row q-col-gutter-none">
              <div class="col-12">
                <q-file
                  outlined
                  v-model="uploadedImages"
                  label="Перетащите или загрузите файлы"
                  counter
                  multiple
                  use-chips
                  accept=".jpg, image/*"
                >
                  <template v-slot:prepend>
                    <q-icon name="attach_file" />
                  </template>
                  <template v-slot:file="{ index, file }">
                    <q-chip
                      :removable="false"
                    >
                      <q-avatar>
                        <q-icon name="photo" />
                      </q-avatar>
                      <div class="ellipsis relative-position">
                        {{ file.name }}
                      </div>
                    </q-chip>
                  </template>
                </q-file>
              </div>
            </div>

            <Loading v-if="imagesLoading" />

            <template v-if="imagePreviews.length > 0">
              <div class="row">
                <div class="col-2 q-pa-xs" v-for="(image, ind) of imagePreviews">
                  <q-img :src="image">
                    <q-icon
                      class="absolute all-pointer-events"
                      size="28px"
                      name="close"
                      color="white"
                      style="top: 4px; right: 4px"
                      @click="onDeleteImage(ind)"
                    >
                      <q-tooltip>
                        Удалить
                      </q-tooltip>
                    </q-icon>
                  </q-img>
                </div>
              </div>
            </template>
          </q-card-section>
        </q-card>

        <q-card class="q-mx-md q-mt-md">
          <q-card-section>

            <h4>Адрес</h4>
            <div class="row q-col-gutter-none">

              <div class="col-md-3 col-sm-6 col-xs-12 q-py-xs">
                <q-select
                  outlined
                  v-model="formfields.region_select"
                  :options="regionOptions"
                  label="Регион"
                  options-dense
                  :disable="!regionOptions.length"
                  @update:model-value="onRegionSelect"
                >
                  <template v-slot:append>
                    <q-icon
                      v-if="formfields.region_select !== null"
                      class="cursor-pointer"
                      name="clear"
                      @click.stop.prevent="() => {
                        formfields.region_select = null
                        formfields.region_district_select = null
                        formfields.city_select = null
                        formfields.city_district_select = null
                      }"
                    />
                  </template>
                </q-select>
              </div>

              <div class="col-md-3 col-sm-6 col-xs-12 q-py-xs">
                <q-select
                  outlined
                  v-model="formfields.region_district_select"
                  :options="regionDistrictOptions"
                  label="Район региона"
                  options-dense
                  :disable="!regionDistrictOptions.length"
                  @update:model-value="onRegionDistrictSelect"
                >
                  <template v-slot:append>
                    <q-icon
                      v-if="formfields.region_district_select !== null"
                      class="cursor-pointer"
                      name="clear"
                      @click.stop.prevent="() => {
                        formfields.region_district_select = null
                      }"
                    />
                  </template>
                </q-select>
              </div>

              <div class="col-md-3 col-sm-6 col-xs-12 q-py-xs">
                <q-select
                  outlined
                  v-model="formfields.city_select"
                  :options="cityOptions"
                  label="Населенный пункт"
                  options-dense
                  :disable="!cityOptions.length"
                  @update:model-value="onCitySelect"
                >
                  <template v-slot:append>
                    <q-icon
                      v-if="formfields.city_select !== null"
                      class="cursor-pointer"
                      name="clear"
                      @click.stop.prevent="() => {
                        formfields.city_select = null
                        formfields.city_district = null
                      }"
                    />
                  </template>
                </q-select>
              </div>

              <div class="col-md-3 col-sm-6 col-xs-12 q-py-xs">
                <q-select
                  outlined
                  v-model="formfields.city_district_select"
                  :options="cityDistrictOptions"
                  label="Район города"
                  options-dense
                  :disable="!cityDistrictOptions.length"
                >
                  <template v-slot:append>
                    <q-icon
                      v-if="formfields.city_district_select !== null"
                      class="cursor-pointer"
                      name="clear"
                      @click.stop.prevent="formfields.city_district_select = null"
                    />
                  </template>
                </q-select>
              </div>

              <div class="col-md-3 col-sm-12 col-xs-12 q-py-xs">
                <q-select
                  outlined
                  v-model="formfields.street_type"
                  :options="streetTypeOptions"
                  label="Тип улицы"
                  options-dense
                  :disable="!streetTypeOptions.length"
                >
                  <template v-slot:append>
                    <q-icon
                      v-if="formfields.street_type !== null"
                      class="cursor-pointer"
                      name="clear"
                      @click.stop.prevent="formfields.street_type = null"
                    />
                  </template>
                </q-select>
              </div>

              <div class="col-sm-6 col-xs-12 q-py-xs">
                <q-select
                  outlined
                  v-model="formfields.street_name"
                  :options="streetOptions"
                  label="Улица"
                  use-input
                  hide-selected
                  fill-input
                  options-dense
                  new-value-mode="add-unique"
                  @filter="filterStreetList"
                >
                  <template v-slot:append>
                    <q-icon
                      v-if="formfields.street_name !== null"
                      class="cursor-pointer"
                      name="clear"
                      @click.stop.prevent="formfields.street_name = null"
                    />
                  </template>
                </q-select>
              </div>

              <div class="col-md-3 col-sm-6 col-xs-12 q-py-xs">
                <q-input
                  outlined
                  v-model="formfields.building_number"
                  label="№ дома"
                >
                  <template v-slot:append>
                    <q-icon
                      v-if="formfields.building_number !== null"
                      class="cursor-pointer"
                      name="clear"
                      @click.stop.prevent="formfields.building_number = null"
                    />
                  </template>
                </q-input>
              </div>

            </div>

            <h4>Местоположение</h4>
            <div class="row q-col-gutter-none">
              <div class="col-sm-6 col-xs-12 q-py-xs">
                <q-input
                  outlined
                  v-model="formfields.latitude"
                  label="Широта"
                  @update:model-value="onCoordChange('latitude')"
                />
              </div>
              <div class="col-sm-6 col-xs-12 q-py-xs">
                <q-input
                  outlined
                  v-model="formfields.longitude"
                  label="Долгота"
                  @update:model-value="onCoordChange('longitude')"
                />
              </div>
            </div>
            <div class="row">
              <template v-if="adressByCoords">
                <q-icon name="location_on" color="primary" />
                <p class="q-pl-xs">{{ adressByCoords }}</p>
              </template>
              <template v-else>
                <q-icon name="not_listed_location" color="grey" />
                <p class="q-pl-xs">Координаты не указаны</p>
              </template>
            </div>
            <YandexMap
              :settings="yaMapsSettings"
              :coordinates="coords"
              :zoom="16"
            >
              <YandexMarker
                marker-id="1"
                type="Point"
                :coordinates="coords"
                :options="{ draggable: true }"
                :events="['dragend']"
                @dragend="onMarkerMove"
              ></YandexMarker>
            </YandexMap>
          </q-card-section>
        </q-card>

        <div class="q-mx-md q-mt-md text-right">
          <q-btn
            unelevated
            label="Отправить на модерацию"
            type="submit" color="primary"
            :disable="canSendForm === false"
          />
        </div>

      </q-form>
    </template>
  </MainLayout>
</template>

<script>
import { ref, computed, watch } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import MainLayout from '@/Layouts/MainLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import Loading from "@/Components/Elements/Loading.vue"
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import TrippleStateLabel from '@/Components/Elements/TrippleStateLabel.vue'
import { yaMapsSettings } from '@/configurations/custom-configs'
import { loadYmap, YandexMap, YandexMarker } from 'vue-yandex-maps'
import { secondaryCategoryOptionList } from '@/composables/formatted-and-processed-data'
import { userInfo } from '@/composables/shared-data'

export default {
  components: {
    MainLayout, RegularContentContainer, Breadcrumbs, TrippleStateLabel, Loading, YandexMap, YandexMarker
  },
  props: {
    secondaryCategories: {
      type: Object,
    },
    buildingSeries: {
      type: Array,
      default:[]
    },
    buildingMaterials: {
      type: Array,
      default:[]
    },
    renovations: {
      type: Array,
      default: []
    },
    bathroomUnit: {
      type: Object,
      default: {}
    },
    quality: {
      type: Object,
      default: {}
    },
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
    regions: {
      type: Object,
      default: {}
    },
    regionDistricts: {
      type: Array,
      default: []
    },
    cities: {
      type: Array,
      default: []
    },
    cityDistricts: {
      type: Array,
      default: []
    },
    streetTypes: {
      type: Object,
      default: {}
    },
      streetList: {
      type: Array,
      default: []
    }
  },
  setup (props) {

    const { user } = userInfo()

    const formfields = ref({
      operation: 'create_add',
      author_id: user.value.id,
      agency_id: user.value.agency_id,
      creation_type: 2,
      detail: null,
      dealType: null,
      deal_type: null,
      category: null,
      category_id: null,
      price: null,
      unit_price: null,
      rooms_select: null,
      rooms_input: null,
      rooms: null,
      area: null,
      kitchen_area: null,
      living_area: null,
      bathroom: null,
      bathroom_index: null,
      floor: null,
      total_floors: null,
      material_select: null,
      material_id: null,
      building_series: null,
      building_series_id: null,
      quality: null,
      quality_index: null,
      renovation: null,
      renovation_id: null,
      built_year: null,
      balcony_amount: 0,
      loggia_amount: 0,
      elevator: false,
      elevator_passenger_amount: null,
      elevator_freight_amount: null,
      windowview_street: null,
      windowview_yard: null,
      panoramic_windows: null,
      rubbish_chute: null,
      gas_pipe: null,
      phone_line: null,
      internet: null,
      concierge: null,
      closed_territory: null,
      playground: null,
      underground_parking: null,
      ground_parking: null,
      open_parking: null,
      multilevel_parking: null,
      barrier: null,
      dressing_room: null,
      developer_select: null,
      developer_id: null,
      developer_string: null,
      developer_input: null,
      buildingComplex_select: null,
      newbuilding_complex_id: null,
      newbuilding_complex_string: null,
      buildingComplex_input: null,
      building_select: null,
      newbuilding_id: null,
      newbuilding_string: null,
      building_input: null,
      entrance_select: null,
      entrance_id: null,
      entrance_string: null,
      entrance_input: null,
      flat_select: null,
      flat_id: null,
      flat_input: null,
      number: null,
      region_select: null,
      region_id: null,
      region_district_select: null,
      region_district_id: null,
      city_select: null,
      city_id: null,
      city_district_select: null,
      district_id: null,
      street_type: null,
      street_type_id: null,
      street_name: null,
      building_number: null,
      longitude: null,
      latitude: null,
      images: []
    })

    const manualInputFlags = ref({
      developer: false,
      newbuildingComplex: false,
      newbuilding: false,
      entrance: false,
      flat: false,
    })

    const breadcrumbs = ref([
      {
        id: 1,
        label: 'Главная',
        icon: 'home',
        url: '/',
        data: false,
        options: false
      },
      {
        id: 2,
        label: 'Кабинет пользователя',
        icon: 'business_center',
        url: '/user/profile',
        data: false,
        options: false
      },
      {
        id: 3,
        label: 'Вторичная продажа',
        icon: 'home_work',
        url: '/user/secondary/index',
        data: false,
        options: false
      },
      {
        id: 4,
        label: 'Создать объявление',
        icon: 'post_add',
        url: '/user/secondary/create',
        data: false,
        options: false
      },
    ])

    const loading = ref(false)

    const dealTypeOptions = ref([
      { label: 'продажа', value: 1 },
      { label: 'аренда', value: 2 },
    ])

    const roomsOptions = ref([
      { label: '1 комната', value: 1 },
      { label: '2 комнаты', value: 2 },
      { label: '3 комнаты', value: 3 },
      { label: '4 комнаты', value: 4 },
      { label: '5 комнат', value: 5 },
      { label: '> 5 комнат', value: '5+' },
    ])

    const categoryOptions = secondaryCategoryOptionList(props.secondaryCategories)

    const materialOptions = computed(() => {
      const options = []
      props.buildingMaterials.forEach(material => {
        options.push({ label: material.name, value: material.id })
      })
      return options
    })

    const bathroomOptions = computed(() => {
      const options = []
      Object.keys(props.bathroomUnit).forEach(index => {
        options.push({ label: props.bathroomUnit[index], value: index })
      })
      return options
    })

    const buildingSeriesOptions = computed(() => {
      const options = []
      props.buildingSeries.forEach(series => {
        options.push({ label: series.name, value: series.id })
      })
      return options
    })

    const qualityOptions = computed(() => {
      const options = []
      Object.keys(props.quality).forEach(index => {
        options.push({ label: props.quality[index], value: index })
      })
      return options
    })

    const renovationOptions = computed(() => {
      const options = []
      props.renovations.forEach(renovation => {
        options.push({ label: renovation.name, value: renovation.id })
      })
      return options
    })

    const yearOptions = computed(() => {
      const options = []
      for (let i = new Date().getFullYear(); i >= 1900; i--)
      options.push(i)
      return options
    })

    const developerOptions = computed(() => {
      const options = []
      Object.keys(props.developers).forEach(index => {
        options.push({ label: props.developers[index], value: index })
      })
      return options
    })

    const onDeveloperSelect = () => {
      formfields.value.buildingComplex_select = null
      formfields.value.building_select = null
      formfields.value.entrance_select = null
      formfields.value.flat_select = null
      Inertia.visit('/user/secondary/create', {
        method: 'post',
        data: {
          regionId: formfields.value.region_select !== null ? formfields.value.region_select.value : null,
          regionDistrictId: formfields.value.region_select !== null ? formfields.value.region_select.value : null,
          cityId: formfields.value.city_select !== null ? formfields.value.city_select.value : null,

          developerId: formfields.value.developer_select.value
        },
        preserveState: true,
        preserveScroll: true,
        only: ['buildingComplexes']
      })
    }

    const buildingComplexOptions = computed(() => {
      const options = []
        if (formfields.value.developer_select !== null) {
        props.buildingComplexes.forEach(complex => {
          options.push({ label: complex.name, value: complex.id })
        })
      }
      return options
    })

    const onBuildingComplexSelect = () => {
      formfields.value.building_select = null
      formfields.value.entrance_select = null
      formfields.value.flat_select = null
      Inertia.visit('/user/secondary/create', {
        method: 'post',
        data: {
          regionId: formfields.value.region_select !== null ? formfields.value.region_select.value : null,
          regionDistrictId: formfields.value.region_select !== null ? formfields.value.region_select.value : null,
          cityId: formfields.value.city_select !== null ? formfields.value.city_select.value : null,

          developerId: formfields.value.developer_select.value,
          complexId: formfields.value.buildingComplex_select.value
        },
        preserveState: true,
        preserveScroll: true,
        only: ['buildings']
      })
    }

    const buildingOptions = computed(() => {
      const options = []
        if (formfields.value.buildingComplex_select !== null) {
        props.buildings.forEach(building => {
          options.push({ label: building.name, value: building.id })
        })
      }
      return options
    })

    const onBuildingSelect = () => {
      formfields.value.entrance_select = null
      formfields.value.flat_select = null
      Inertia.visit('/user/secondary/create', {
        method: 'post',
        data: {
          regionId: formfields.value.region_select !== null ? formfields.value.region_select.value : null,
          regionDistrictId: formfields.value.region_select !== null ? formfields.value.region_select.value : null,
          cityId: formfields.value.city_select !== null ? formfields.value.city_select.value : null,

          developerId: formfields.value.developer_select.value,
          complexId: formfields.value.buildingComplex_select.value,
          buildingId: formfields.value.building_select.value
        },
        preserveState: true,
        preserveScroll: true,
        only: ['entrances']
      })
    }

    const entranceOptions = computed(() => {
      const options = []
        if (formfields.value.building_select !== null) {
        props.entrances.forEach(entrance => {
          options.push({ label: entrance.name, value: entrance.id })
        })
      }
      return options
    })

    const onEntranceSelect = () => {
      formfields.value.flat_select = null
      Inertia.visit('/user/secondary/create', {
        method: 'post',
        data: {
          regionId: formfields.value.region_select !== null ? formfields.value.region_select.value : null,
          regionDistrictId: formfields.value.region_select !== null ? formfields.value.region_select.value : null,
          cityId: formfields.value.city_select !== null ? formfields.value.city_select.value : null,

          developerId: formfields.value.developer_select.value,
          complexId: formfields.value.buildingComplex_select.value,
          buildingId: formfields.value.building_select.value,
          entranceId: formfields.value.entrance_select.value
        },
        preserveState: true,
        preserveScroll: true,
        only: ['flats']
      })
    }

    const flatOptions = computed(() => {
      const options = []
        if (formfields.value.entrance_select !== null) {
        props.flats.forEach(flat => {
          options.push({ label: `№ ${flat.number} (${flat.floor} этаж)`, value: flat.id })
        })
      }
      return options
    })

    const inputFlagsChange = (currentField, dependedFields) => {
      if (manualInputFlags.value[currentField]) {
        dependedFields.forEach(field => {
          manualInputFlags.value[field] = true
        })
      }
    }

    const regionOptions = computed(() => {
      const options = []
      Object.keys(props.regions).forEach(index => {
        options.push({ label: props.regions[index], value: index })
      })
      return options
    })

    const onRegionSelect = () => {
      formfields.value.region_district_select = null
      formfields.value.city_select = null
      formfields.value.city_district = null
      Inertia.visit('/user/secondary/create', {
        method: 'post',
        data: {
          developerId: formfields.value.developer_select !== null ? formfields.value.developer_select.value : null,
          complexId: formfields.value.buildingComplex_select !== null ? formfields.value.buildingComplex_select.value : null,
          buildingId: formfields.value.building_select !== null ? formfields.value.building_select.value  : null,
          entranceId: formfields.value.entrance_select !== null ? formfields.value.entrance_select.value : null,
          regionId: formfields.value.region_select !== null ? formfields.value.region_select.value : null,
        },
        preserveState: true,
        preserveScroll: true,
        only: ['region_districts', 'cities']
      })
    }

    const regionDistrictOptions = computed(() => {
      const options = []
        if (formfields.value.region_select !== null) {
        props.regionDistricts.forEach(regionDistrict => {
          options.push({ label: regionDistrict.name, value: regionDistrict.id })
        })
      }
      return options
    })

    const onRegionDistrictSelect = () => {
      Inertia.visit('/user/secondary/create', {
        method: 'post',
        data: {
          developerId: formfields.value.developer_select !== null ? formfields.value.developer_select.value : null,
          complexId: formfields.value.buildingComplex_select !== null ? formfields.value.buildingComplex_select.value : null,
          buildingId: formfields.value.building_select !== null ? formfields.value.building_select.value  : null,
          entranceId: formfields.value.entrance_select !== null ? formfields.value.entrance_select.value : null,

          regionId: formfields.value.region_select !== null ? formfields.value.region_select.value : null,
          regionDistrictId: formfields.value.region_select !== null ? formfields.value.region_select.value : null
        },
        preserveState: true,
        preserveScroll: true,
        only: ['cities']
      })
    }

    const cityOptions = computed(() => {
      const options = []
        if (formfields.value.region_select !== null) {
        props.cities.forEach(city => {
          options.push({ label: city.name, value: city.id })
        })
      }
      return options
    })

    const onCitySelect = () => {
      formfields.value.city_district = null
      Inertia.visit('/user/secondary/create', {
        method: 'post',
        data: {
          developerId: formfields.value.developer_select !== null ? formfields.value.developer_select.value : null,
          complexId: formfields.value.buildingComplex_select !== null ? formfields.value.buildingComplex_select.value : null,
          buildingId: formfields.value.building_select !== null ? formfields.value.building_select.value  : null,
          entranceId: formfields.value.entrance_select !== null ? formfields.value.entrance_select.value : null,

          regionId: formfields.value.region_select !== null ? formfields.value.region_select.value : null,
          regionDistrictId: formfields.value.region_select !== null ? formfields.value.region_select.value : null,
          cityId: formfields.value.city_select !== null ? formfields.value.city_select.value : null
        },
        preserveState: true,
        preserveScroll: true,
        only: ['cityDistricts']
      })
    }

    const cityDistrictOptions = computed(() => {
      const options = []
        if (formfields.value.city_select !== null) {
        props.cityDistricts.forEach(cityDistrict => {
          options.push({ label: cityDistrict.name, value: cityDistrict.id })
        })
      }
      return options
    })

    const streetTypeOptions = computed(() => {
      const options = []
      Object.keys(props.streetTypes).forEach(index => {
        options.push({ label: props.streetTypes[index], value: index })
      })
      return options
    })

    const streetListRef = ref(props.streetList)

    const streetOptions = computed(() => {
      const streets = []
      streetListRef.value.forEach(street => {
        streets.push(street.street_name)
      })
      return streets
    })

    const filterStreetList = (val, update, abort) => {
      update(() => {
        const needle = val.toLowerCase()
        streetListRef.value = props.streetList.filter(elem => elem.street_name.toLowerCase().indexOf(needle) > -1)
      })
    }

    const coords = ref([51.66109664713779, 39.20007322181243])
    
    const onMarkerMove = event => {
      formfields.value.latitude = event.originalEvent.target.geometry._coordinates[0]
      formfields.value.longitude = event.originalEvent.target.geometry._coordinates[1]
      coords.value[0] = event.originalEvent.target.geometry._coordinates[0]
      coords.value[1] = event.originalEvent.target.geometry._coordinates[1]
      setAdressByCoords()
    }

    const adressByCoords = ref('')

    const setAdressByCoords = async() => {
      await loadYmap({ ...yaMapsSettings, debug: true })
      let geoCoder = ymaps.geocode([[formfields.value.latitude, formfields.value.longitude]])
      geoCoder.then(
        res => {
          
          let objs = res.geoObjects.toArray();
          adressByCoords.value = objs[0].properties.getAll().text
        }
      )
    }

    const uploadedImages = ref([])

    const imagePreviews = ref([])
    const imagesLoading = ref(false)

    const onDeleteImage = (ind) => {
      const halfBeforeTheUnwantedElement = uploadedImages.value.slice(0, ind)
      const halfAfterTheUnwantedElement = uploadedImages.value.slice(ind+1);
      uploadedImages.value = halfBeforeTheUnwantedElement.concat(halfAfterTheUnwantedElement);
    }
    
    watch(uploadedImages, () => {
      imagesLoading.value = true
      if (uploadedImages.value) {
        imagePreviews.value = []
        uploadedImages.value.forEach(file => {
          let reader = new FileReader()
          reader.readAsDataURL(file)
          reader.onload = function(e) {
            let data = reader.result
            imagePreviews.value.push(data)
          }
        })
      }
      imagesLoading.value = false
    })

    const canSendForm = computed(() => {
      let result = true
      const requiredFields = ['dealType', 'category', 'detail', 'price', 'rooms_select', 'area', 'floor', 'total_floors', 'balcony_amount', 'loggia_amount']
      requiredFields.forEach(field => {
        if (formfields.value[field] === null) result = false
      })
      return result
    })

    const onCoordChange = (coordinate) => {
      switch (coordinate) {
        case 'latitude':
          coords.value[0] = formfields.value.latitude
          break
        case 'longitude':
          coords.value[1] = formfields.value.longitude
      }
      setAdressByCoords()
    }

    function onSubmit() {
      loading.value = true
      formfields.value.deal_type = formfields.value.dealType.value
      formfields.value.category_id = formfields.value.category.value
      formfields.value.rooms = formfields.value.rooms_select.value !== '5+' ? formfields.value.rooms_select.value : formfields.value.rooms_input
      formfields.value.unit_price = Math.round(formfields.value.price / formfields.value.area * 100) / 100
      formfields.value.bathroom_index = formfields.value.bathroom !== null ? formfields.value.bathroom.value : null
      formfields.value.quality_index = formfields.value.quality !== null ? formfields.value.quality.value : null
      formfields.value.material_id = formfields.value.material_select !== null ? formfields.value.material_select.value : null
      formfields.value.renovation_id = formfields.value.renovation !== null ? formfields.value.renovation.value : null
      formfields.value.building_series_id = formfields.value.building_series !== null ? formfields.value.building_series.value : null
      formfields.value.elevator = formfields.value.elevator_passenger_amount || formfields.value.elevator_freight_amount ? true : false
      formfields.value.developer_id = formfields.value.developer_select !== null ? formfields.value.developer_select.value : null
      formfields.value.newbuilding_complex_id = formfields.value.buildingComplex_select !== null ? formfields.value.buildingComplex_select.value : null
      formfields.value.newbuilding_id = formfields.value.building_select !== null ? formfields.value.building_select.value : null
      formfields.value.entrance_id = formfields.value.entrance_select !== null ? formfields.value.entrance_select.value : null
      formfields.value.flat_id = formfields.value.flat_select !== null ? formfields.value.flat_select.value : null
      formfields.value.developer_string = manualInputFlags.value.developer === true && formfields.value.developer_input ? formfields.value.developer_input : null
      formfields.value.newbuilding_complex_string = manualInputFlags.value.newbuildingComplex === true && formfields.value.buildingComplex_input ? formfields.value.buildingComplex_input : null
      formfields.value.newbuilding_string = manualInputFlags.value.newbuilding === true && formfields.value.building_input ? formfields.value.building_input : null
      formfields.value.entrance_string = manualInputFlags.value.entrance === true && formfields.value.entrance_input ? formfields.value.entrance_input : null
      formfields.value.number = manualInputFlags.value.flat === true && formfields.value.flat_input ? formfields.value.flat_input : null
      formfields.value.region_id = formfields.value.region_select !== null ? formfields.value.region_select.value : null
      formfields.value.region_district_id = formfields.value.region_district_select !== null ? formfields.value.region_district_select.value : null
      formfields.value.city_id = formfields.value.city_select !== null ? formfields.value.city_select.value : null
      formfields.value.district_id = formfields.value.city_district_select !== null ? formfields.value.city_district_select.value : null
      formfields.value.images = uploadedImages.value
      console.log(formfields.value)
      Inertia.post(`/user/secondary/create`, formfields.value)
      Inertia.on('finish', (event) => {
        loading.value = false
      })
    }

    return {
      user,
      breadcrumbs,
      loading,
      dealTypeOptions,
      roomsOptions,
      categoryOptions,
      materialOptions,
      buildingSeriesOptions,
      renovationOptions,
      bathroomOptions,
      qualityOptions,
      yearOptions,
      developerOptions,
      onDeveloperSelect,
      buildingComplexOptions,
      onBuildingComplexSelect,
      buildingOptions,
      onBuildingSelect,
      entranceOptions,
      onEntranceSelect,
      flatOptions,
      inputFlagsChange,
      regionOptions,
      onRegionSelect,
      regionDistrictOptions,
      onRegionDistrictSelect,
      cityOptions,
      onCitySelect,
      cityDistrictOptions,
      streetTypeOptions,
      streetOptions,
      filterStreetList,
      imagesLoading,
      uploadedImages,
      onDeleteImage,
      imagePreviews,
      yaMapsSettings,
      coords,
      onMarkerMove,
      adressByCoords,
      formfields,
      manualInputFlags,
      canSendForm,
      onSubmit,
      onCoordChange
    }

  }
  
}
</script>

<style>
.yandex-container {
  width: 100%;
  height: 400px!important;
  margin-top: -35px;
}
</style>