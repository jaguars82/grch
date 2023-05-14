<template>
  <MainLayout :secondaryColumns="3">
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <h3>Создание объявления</h3>
      <Loading v-if="loading" />
      <q-form v-else @submit="onSubmit">
        <q-card>
          <q-card-section>
            <h4>Информация об объекте</h4>

            <div class="row q-col-gutter-none">

              <div class="col-md-6 col-sm-12 q-py-xs">
                <q-select
                  outlined
                  v-model="formfields.deal_type"
                  :options="dealTypeOptions"
                  label="Тип сделки*"
                  options-dense
                >
                </q-select>
              </div>

              <div class="col-md-6 col-sm-12 q-py-xs">
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

              <div class="col-md-4 col-sm-6 col-xs-12 q-py-xs">
                <q-select
                  outlined
                  v-model="formfields.roooms"
                  :options="roomsOptions"
                  label="Количество комнат*"
                  options-dense
                />
              </div>

              <div class="col-md-4 col-sm-6 col-xs-12 q-py-xs">
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

              <div class="col-md-4 col-sm-6 col-xs-12 q-py-xs">
                <q-input outlined type="number" v-model.number="formfields.floor" label="Этаж*" />
              </div>

              <div class="col-md-4 col-sm-6 col-xs-12 q-py-xs">
                <q-input outlined type="number" min="1" v-model.number="formfields.total_floors" label="Этажность*" />
              </div>

              <div class="col-md-4 col-sm-6 col-xs-12 q-py-xs">
                <q-select
                  outlined
                  v-model="formfields.material"
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

          </q-card-section>
        </q-card>

        <q-card class="q-mt-md">
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

        <q-card class="q-mt-md">
          <q-card-section>
            <h4>Местоположение</h4>
            <div class="row q-col-gutter-none">
              <div class="col-sm-6 q-py-xs">
                <q-input
                  outlined
                  v-model="formfields.latitude"
                  label="Широта"
                  @update:model-value="onCoordChange('latitude')"
                />
              </div>
              <div class="col-sm-6 q-py-xs">
                <q-input
                  outlined
                  v-model="formfields.longitude"
                  label="Долгота"
                  @update:model-value="onCoordChange('longitude')"
                />
              </div>
            </div>
            <yandex-map
              :settings="yaMapsSettings"
              :coords="coords"
              zoom="16"
              ymap-class="ya-map-container"
            >
              <ymap-marker
                marker-id="1"
                marker-type="Placemark"
                :coords="coords"
                :options="{ draggable: true }"
                :icon="{iconLayout: 'default#imageWithContent', iconImageHref: '/img/icons/placemark.svg', imageSize: [35, 35], imageOffset: [-17.5, -35]}"
                @dragend="onMarkerMove"
                cluster-name="1"
              ></ymap-marker>
            </yandex-map>
          </q-card-section>
        </q-card>

        <div class="q-mt-md text-right">
          <q-btn
            unelevated
            label="Отправить на модерацию"
            type="submit" color="primary"
            :disable="canSendForm === false"
          />
        </div>

      </q-form>
    </template>
    <template v-slot:secondary>
      <pre>{{ formfields }}</pre>
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
import { yandexMap, ymapMarker } from 'vue-yandex-maps'
import { userInfo } from '@/composables/shared-data'

export default {
  components: {
    MainLayout, RegularContentContainer, Breadcrumbs, TrippleStateLabel, Loading, yandexMap, ymapMarker
  },
  props: {
    secondaryCategories: {
      type: Object,
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
  },
  setup (props) {
    const breadcrumbs = ref([
      {
        id: 1,
        label: 'Главная',
        icon: 'home',
        url: '/',
        data: false,
        options: 'native'
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

    const { user } = userInfo()

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
      { label: 'Больше 5 комнат', value: '5+' },
    ])

    const categoryOptions = computed(() => {
      const options = []
      const categoriesArray = []
      
      for (const id in props.secondaryCategories) {
        categoriesArray.push(props.secondaryCategories[id])
      }

      categoriesArray.forEach(category => {
        options.push({ label: category.name, disable: true })
        category.subcats.forEach(subcat => {
          options.push({ label: subcat.name, value: subcat.id })
        })
      })
      return options
    })

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
      for (let i = 1900; i <= new Date().getFullYear(); i++)
      options.push(i)
      return options
    })

    const coords = ref([51.66109664713779, 39.20007322181243])
    
    const onMarkerMove = event => {
      console.log(event.originalEvent.target.geometry._coordinates)
      formfields.value.latitude = event.originalEvent.target.geometry._coordinates[0]
      formfields.value.longitude = event.originalEvent.target.geometry._coordinates[1]
      coords.value[0] = event.originalEvent.target.geometry._coordinates[0]
      coords.value[1] = event.originalEvent.target.geometry._coordinates[1]
    }

    const formfields = ref({
      deal_type: null,
      category: null,
      price: null,
      rooms: null,
      area: null,
      kitchen_area: null,
      living_area: null,
      bathroom: null,
      floor: null,
      total_floors: null,
      material: null,
      quality: null,
      renovation: null,
      built_year: null,
      balcony_amount: 0,
      loggia_amount: 0,
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
      longitude: null,
      latitude: null
    })

    const uploadedImages = ref([])

    const imagePreviews = ref([])
    const imagesLoading = ref(false)

    const onDeleteImage = (ind) => {
      //imagePreviews.value = []
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
      const requiredFields = ['deal_type', 'category', 'price', 'rooms', 'area', 'floor', 'total_floors', 'balcony_amount', 'loggia_amount']
      requiredFields.forEach(field => {
        if (formfields.value[field] === null) result = false
      })
      return result
    })

    function onSubmit() {
      loading.value = true
      Inertia.post(`/user/secondary/create`, formfields.value)
      Inertia.on('finish', (event) => {
        loading.value = false
      })
    }

    const onCoordChange = (coordinate) => {
      switch (coordinate) {
        case 'latitude':
          coords.value[0] = formfields.value.latitude
          break
        case 'longitude':
          coords.value[1] = formfields.value.longitude
      }
    }

    /* watch(formfields.value, (val) => {
      //coords.value[0] = val
      console.log(val.latitude)
    }) 
    watch(formfields.value, (val) => {
      //coords.value[1] = val
      console.log(val.longitude)
    }) */

    return {
      user,
      breadcrumbs,
      loading,
      dealTypeOptions,
      roomsOptions,
      categoryOptions,
      materialOptions,
      renovationOptions,
      bathroomOptions,
      qualityOptions,
      yearOptions,
      imagesLoading,
      uploadedImages,
      onDeleteImage,
      imagePreviews,
      yaMapsSettings,
      coords,
      onMarkerMove,
      formfields,
      canSendForm,
      onSubmit,
      onCoordChange
    }

  }
  
}
</script>

<style>
.ya-map-container {
  width: 100%;
  height: 400px!important;
  margin-top: -35px;
}
</style>