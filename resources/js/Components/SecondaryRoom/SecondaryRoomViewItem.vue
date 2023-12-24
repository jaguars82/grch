<template>
  <q-card
    class="q-ma-md shadow-12"
  >
    <q-card-section horizontal>
      <q-card-section class="col-8">
        <p class="text-h4">
          <span v-if="isFlat" class="text-capitalize">{{ roomTitle }}&nbsp;</span>
          <span>{{ category }}</span>
          <span v-if="roomArea">, {{ roomArea }}</span>
          <span v-if="roomFloor">, {{ roomFloor }}</span>
        </p>
        <p>
          <span v-if="address.city">{{ address.city }},&nbsp;</span>
          <span v-if="address.cityDistrict">{{ address.cityDistrict }},&nbsp;</span>
          <span v-if="address.streetHouse">{{ address.streetHouse }}</span>
        </p>
        <p>{{ creationDate }}</p>
      </q-card-section>
      <q-card-section class="col-4">
        <p class="text-h2 text-bold text-right text-blue-8 q-mb-xs">{{ roomPrice }}</p>
        <p v-if="roomPricePerMeter" class="text-grey-7 text-right">
          {{ roomPricePerMeter }}
        </p>
      </q-card-section>
    </q-card-section>
  </q-card>

  <div class="row q-px-md">

    <div class="col-8">
      <q-card class="shadow-12">
        <q-card-section>
          <q-carousel
            v-if="room.images.length"
            swipeable
            animated
            v-model="slide"
            arrows
            thumbnails
            infinite
          >
            <q-carousel-slide
              v-for="image of room.images"
              :key="image.id"
              :name="image.id"
              :img-src="image.location_type === 'local' ? `/uploads/${image.filename}` : image.url"
              @click="onImageClick(image)"
            />
          </q-carousel>
        </q-card-section>
        <q-card-section>
          <h4>Описание объекта</h4>
          <div>
            {{ roomDetail }}
          </div>
        </q-card-section>
      </q-card>
    </div>

    <div class="col-4">
      <q-card class="shadow-12 q-ml-md q-mb-md">
        <q-card-section>
          <div class="row no-wrap justify-start items-center">
            <q-avatar v-if="advAuthor.photo" size="100px" class="q-mr-sm">
              <img :src="advAuthor.photo">
            </q-avatar>
            <div>
              <template v-if="advAuthor.fullName">
                <span class="text-bold">
                  {{ advAuthor.fullName }}
                </span>
                <br />
              </template>
              <template v-if="agency">
                <span>
                  агентство <b>"{{ agency.name }}"</b>
                </span>
                <br />
              </template>
              <template v-if="advAuthor.phone">
                <q-icon class="q-pr-xs" name="phone_enabled" />
                <span>
                  {{ advAuthor.phone }}
                </span>
                <br />
              </template>
              <template v-if="advAuthor.email">
                <q-icon class="q-pr-xs" name="mail" />
                <span>
                  {{ advAuthor.email }}
                </span>
              </template>
            </div>
          </div>
        </q-card-section>
      </q-card>
      <q-card class="shadow-12 q-ml-md q-mb-md">
        <q-card-section>
          <ParamPair
            v-for="param of paramPairs"
            :key="param.id"
            :paramName="param.name"
            :paramValue="param.value"
          />
        </q-card-section>
      </q-card>
      <q-card class="shadow-12 q-ml-md" v-if="room.longitude && room.latitude">
        <q-card-section>
          <div>
            <span v-if="address.city">{{ address.city }},&nbsp;</span>
            <span v-if="address.cityDistrict">{{ address.cityDistrict }},&nbsp;</span>
            <span v-if="address.streetHouse">{{ address.streetHouse }}</span>
          </div>
          <YandexMap
            :settings="yaMapsSettings"
            :coordinates="[room.latitude, room.longitude]"
            :zoom="16"
          >
            <YandexMarker
              marker-id="1"
              type="Point"
              :coordinates="[room.latitude, room.longitude]"
            ></YandexMarker>
          </YandexMap>
        </q-card-section>
      </q-card>
    </div>

  </div>

  <q-dialog
    v-model="imageViewer"
    persistent
    :maximized="true"
    transition-show="slide-up"
    transition-hide="slide-down"
  >
    <q-card>
      <q-bar>
        <q-space />
        <q-btn round dense flat icon="close" v-close-popup />
      </q-bar>
      <q-card-section class="q-pa-none full-height">
        <q-carousel
          class="full-height"
          v-if="room.images.length"
          swipeable
          animated
          v-model="slide"
          arrows
          thumbnails
          infinite
        >
          <q-carousel-slide
            v-for="image of room.images"
            :key="image.id"
            :name="image.id"
            :img-src="image.location_type === 'local' ? `/uploads/${image.filename}` : image.url"
            @click="imageViewer = false"
          />
        </q-carousel>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>
  
<script>
import { ref, computed } from 'vue'
import { asDateTime, asNumberString, asFloor, asArea, asCurrency, asPricePerArea } from '@/helpers/formatter'
import ParamPair from '@/Components/Elements/ParamPair.vue'
import { yaMapsSettings } from '@/configurations/custom-configs'
import { YandexMap, YandexMarker } from 'vue-yandex-maps'

  
export default {
  props: {
    room: {
      type: Object,
    },
    created: {
      type: String,
    },
    agency: {
      type: Object
    },
    author: {
      type: Object
    }
  },
  components: {
    ParamPair, YandexMap, YandexMarker
  },
  setup (props) {
    const slide = props.room.images.length ? ref(props.room.images[0].id) : ref(false)
    const category = computed(() => {
      if (props.room.category_DB) return props.room.category_DB.name
      if (props.room.category_string) return props.room.category_string
      return false
    })
    const isFlat = computed(() => {
      if (props.room.category_DB && props.room.category_DB.name.toLowerCase() == 'квартира') return true
      if (props.room.category_string && props.room.category_string.toLowerCase() == 'квартира') return true
      return false
    })
    const roomTitle = computed(() => `${asNumberString(props.room.rooms)}комнатная`)
    const roomFloor = computed(() => {
      if (props.room.floor && props.room.total_floors) return asFloor(props.room.floor, props.room.total_floors)
      return false
    })
    const roomArea = computed(() => asArea(props.room.area))
    const kitchenArea = computed(() => asArea(props.room.kitchen_area))
    const livingArea = computed(() => asArea(props.room.living_area))
    const roomPrice = computed(() => asCurrency(props.room.price))
    const roomPricePerMeter = computed(() => asPricePerArea(Math.round(props.room.unit_price)))
    const creationDate = computed(() => asDateTime(props.created))
    const address = computed(() => {
      const locationInfo = JSON.parse(props.room.location_info)
      const regionDistrict = props.room.region_district_DB ? props.room.region_district_DB.name : locationInfo && locationInfo.district ? locationInfo.district : ''
      const city = props.room.city_DB ? props.room.city_DB.name : locationInfo && locationInfo.locality_name ? locationInfo.locality_name : ''
      const cityDistrict = props.room.district_DB ? props.room.district_DB.name : locationInfo && locationInfo.non_admin_sub_locality_name ? locationInfo.non_admin_sub_locality_name : locationInfo && locationInfo.sub_locality_name ? locationInfo.sub_locality_name : ''
      const streetHouse = props.room.address ? props.room.address : locationInfo && locationInfo.address ? locationInfo.address : ''
      return { regionDistrict, city, cityDistrict, streetHouse }
    })
    const advAuthor = computed(() => {
      let fullName = ''
      let photo = ''
      let phone = ''
      let email = ''
      if (props.author.db) {
        const lastName = props.author.db.last_name ? `${props.author.db.last_name} ` : ''
        const firstName = props.author.db.first_name ? props.author.db.first_name : ''
        const middleName = props.author.db.middle_name ? ` ${props.author.db.middle_name}` : ''
        fullName = `${lastName}${firstName}${middleName}`
        photo = props.author.db.photo ? `/uploads/${props.author.db.photo}` : ''
        phone = props.author.db.phone ? props.author.db.phone : null
        email = props.author.db.email ? props.author.db.email : null
      } else {
        const authorParsed = JSON.parse(props.author.info)
        fullName = authorParsed.name ? authorParsed.name : 'Имя не указано'
        photo = authorParsed.photo ? authorParsed.photo : null
        phone = authorParsed.phones ? authorParsed.phones.join(', ') : null
        email = authorParsed.email ? authorParsed.email : null
      }
      return { fullName, photo, phone, email }
    })
    const roomDetail = computed(() => {
      return props.room.detail.replace(/<\/?[^>]+>/gi, "")
    })

    const paramPairs = ref([
      { id: 1, name: 'Публикация', value: creationDate },
      { id: 2, name: 'Количество комнат', value: props.room.rooms ? props.room.rooms : false },
      { id: 3, name: 'Общая площадь', value: roomArea ? roomArea : false },
      { id: 4, name: 'Площадь кухни', value: kitchenArea ? kitchenArea : false },
      { id: 5, name: 'Жилая площадь', value: livingArea ? livingArea : false },
      { id: 6, name: 'Этаж', value: props.room.floor ? props.room.floor : false },
      { id: 7, name: 'Этажность', value: props.room.total_floors ? props.room.total_floors : false },
      { id: 8, name: 'Статус недвижимости', value: category ? category : false },
      { id: 9, name: 'Количество балконов', value: props.room.balcony_amount ? props.room.balcony_amount : false },
      { id: 10, name: 'Количество лоджий', value: props.room.loggia_amount ? props.room.loggia_amount : false },
    ])

    const imageViewer = ref(false)

    const onImageClick = (image) => {
      imageViewer.value = true
    }

    return { slide, category, isFlat, roomTitle, roomFloor, roomArea, roomPrice, roomPricePerMeter, creationDate, address, advAuthor, roomDetail, yaMapsSettings, paramPairs, imageViewer, onImageClick }
  }
}
</script>

<style>
.yandex-container {
  width: 100%;
  height: 300px!important;
  margin-top: -35px;
}
</style>