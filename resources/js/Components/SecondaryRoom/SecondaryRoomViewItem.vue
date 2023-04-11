<template>
    <q-card
        class="q-my-md shadow-12"
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

    <div class="row">

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
                :img-src="image.url"
              />
            </q-carousel>
          </q-card-section>
          <q-card-section>
            <h4>Описание объекта</h4>
            <div>
              {{ room.detail }}
            </div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-4">
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
            <yandex-map
              :settings="yaMapsSettings"
              :coords="[room.latitude, room.longitude]"
              zoom="16"
              ymap-class="ya-map-container"
            >
              <ymap-marker
                marker-id="1"
                marker-type="placemark"
                :coords="[room.latitude, room.longitude]"
                :balloon="{header: 'header', body: 'body', footer: 'footer'}"
                :icon="{color: 'green'}"
                cluster-name="1"
              ></ymap-marker>
            </yandex-map>
          </q-card-section>
        </q-card>
      </div>

    </div>

    <!--<q-card>
      <q-card-section horizontal>
        <q-carousel
          v-if="room.images.length"
          class="col-5"
          animated
          v-model="slide"
          arrows
          :navigation="false"
          infinite
        >
          <q-carousel-slide
            v-for="image of room.images"
            :key="image.id"
            :name="image.id"
            :img-src="image.url"
          />
        </q-carousel>
        <q-card-section class="col-7">
          <p class="text-h4">
            <span v-if="isFlat" class="text-capitalize">{{ roomTitle }}&nbsp;</span>
            <span>{{ category }}</span>
            <span v-if="roomArea">, {{ roomArea }}</span>
            <span v-if="roomFloor">, {{ roomFloor }}</span>
          </p>
          <p>{{ creationDate }}</p>
          <p>
            <span v-if="address.city">{{ address.city }},&nbsp;</span>
            <span v-if="address.cityDistrict">{{ address.cityDistrict }},&nbsp;</span>
            <span v-if="address.streetHouse">{{ address.streetHouse }}</span>
          </p>
          <p class="text-h2 text-bold text-blue-8 q-mb-xs">{{ roomPrice }}</p>
          <p v-if="roomPricePerMeter" class="text-grey-7">
            {{ roomPricePerMeter }}
          </p>
          <div class="row justify-end items-center">
            <div>
              {{ advAuthor.fullName }}
            </div>
            <q-avatar v-if="advAuthor.photo" class="q-ml-sm">
              <img :src="advAuthor.photo">
            </q-avatar>
          </div>
        </q-card-section>
      </q-card-section>
    </q-card>-->
  </template>
  
<script>
import { ref, computed } from 'vue'
import { asDateTime, asNumberString, asFloor, asArea, asCurrency, asPricePerArea } from '@/helpers/formatter'
import ParamPair from '@/Components/Elements/ParamPair.vue'
import { yaMapsSettings } from '@/configurations/custom-configs'
import { yandexMap, ymapMarker } from 'vue-yandex-maps'

  
export default {
  props: {
    room: {
      type: Object,
    },
    created: {
      type: String,
    },
    author: {
      type: Object
    }
  },
  components: {
    ParamPair, yandexMap, ymapMarker
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
      const regionDistrict = props.room.region_district_DB ? props.room.region_district_DB.name : locationInfo.district ? locationInfo.district : ''
      const city = props.room.city_DB ? props.room.city_DB.name : locationInfo.locality_name ? locationInfo.locality_name : ''
      const cityDistrict = props.room.district_DB ? props.room.district_DB.name : locationInfo.non_admin_sub_locality_name ? locationInfo.non_admin_sub_locality_name : locationInfo.sub_locality_name ? locationInfo.sub_locality_name : ''
      const streetHouse = props.room.address ? props.room.address : locationInfo.address ? locationInfo.address : ''
      return { regionDistrict, city, cityDistrict, streetHouse }
    })
    const advAuthor = computed(() => {
      const authorParsed = JSON.parse(props.author.info)
      const fullName = authorParsed.name
      const photo = authorParsed.photo

      return { fullName, photo }
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

    return { slide, category, isFlat, roomTitle, roomFloor, roomArea, roomPrice, roomPricePerMeter, creationDate, address, advAuthor, yaMapsSettings, paramPairs }
  }
}
</script>

<style>
.ya-map-container {
  width: 100%;
  height: 300px!important;
  margin-top: -35px;
}
</style>