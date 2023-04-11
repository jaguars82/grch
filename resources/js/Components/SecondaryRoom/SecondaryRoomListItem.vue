<template>
  <q-card
      class="q-my-md shadow-7"
  >
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
      <q-card-section :class="room.images.length ? 'col-7' : 'col-12'">
        <inertia-link :href="`/secondary/view?id=${advId}`">
        <p class="text-h4">
          <span v-if="isFlat" class="text-capitalize">{{ roomTitle }}&nbsp;</span>
          <span>{{ category }}</span>
          <span v-if="roomArea">, {{ roomArea }}</span>
          <span v-if="roomFloor">, {{ roomFloor }}</span>
        </p>
        </inertia-link>
        <p>{{ creationDate }}</p>
        <p>
          <!--<span v-if="address.regionDistrict">{{ address.regionDistrict }},&nbsp;</span>-->
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
  </q-card>
</template>

<script>
import { ref, computed } from 'vue'
import { asDateTime, asNumberString, asFloor, asArea, asCurrency, asPricePerArea } from '@/helpers/formatter'

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
    },
    advId: {
      type: Number
    }
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

    return { slide, category, isFlat, roomTitle, roomFloor, roomArea, roomPrice, roomPricePerMeter, creationDate, address, advAuthor }
  }
}
</script>