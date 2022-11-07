<template>
  <ShareLayout>
    <template v-slot:main>
      <RegularContentContainer class="q-mt-md" :title="`Коммерческое предложение № ${commercial.number}`">
        <template v-slot:content>
          <UserInfoBar v-if="commercialSettings.initiator" :user="commercial.initiator" />
          <CompareTableFlats v-if="commercialSettings.compareTable && flats.length > 1" :flats="flats" />
          <template v-for="flat of flats" :key="flat.id">
            <FlatCommercialItem class="q-mt-md" :flat="flat" :configuration="commercialSettings.layouts" />
            <q-card flat v-if="flat.advantages.length > 0">
              <q-card-section>
                <AdvantagesBlock :advantages="flat.advantages" />
              </q-card-section>
            </q-card>
            <NewbuildingComplexCard v-if="commercialSettings.newbuildingComplex" :newbuildingComplex="flat.newbuildingComplex" :developer="flat.developer" />
            <DeveloperCard v-if="commercialSettings.developer" :developer="flat.developer" />
            <template v-if="commercialSettings.finishing">
              <FinishingCard v-for="finishing of flat.finishing" :key="finishing.id" :finishing="finishing" />
            </template>
          </template>
        </template>
      </RegularContentContainer>
    </template>
  </ShareLayout>
</template>

<script>
import { ref } from 'vue'
import ShareLayout from '@/Layouts/ShareLayout.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import UserInfoBar from '@/Components/Elements/UserInfoBar.vue'
import CompareTableFlats from '@/Components/CompareTableFlats.vue'
import FlatCommercialItem from '@/Components/Flat/FlatCommercialItem.vue'
import NewbuildingComplexCard from '@/Components/NewbuildingComplex/NewbuildingComplexCard.vue'
import DeveloperCard from '@/Components/Developer/DeveloperCard.vue'
import FinishingCard from '@/Components/FinishingCard.vue'
import AdvantagesBlock from '@/Components/Elements/AdvantagesBlock.vue'
import { initialCommercialSettings } from '@/composables/components-configurations'

export default {
  components: {
    ShareLayout,
    RegularContentContainer,
    UserInfoBar,
    CompareTableFlats,
    FlatCommercialItem,
    NewbuildingComplexCard,
    DeveloperCard,
    FinishingCard,
    AdvantagesBlock
  },
  props: {
    commercial: Array,
    commercialMode: String,
    flats: Array,
  },
  setup (props) {
    const commercialSettings = props.commercial.settings
      ? ref(JSON.parse(props.commercial.settings))
      : ref( initialCommercialSettings )

    return { commercialSettings }
  }
}
</script>