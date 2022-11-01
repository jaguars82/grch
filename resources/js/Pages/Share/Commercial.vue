<template>
  <ShareLayout>
    <template v-slot:main>
      <RegularContentContainer class="q-mt-md" :title="`Коммерческое предложение № ${commercial.number}`">
        <template v-slot:content>
          <template v-for="flat of flats" :key="flat.id">
            <FlatCommercialItem class="q-mt-md q-ml-md" :flat="flat" />
            <AdvantagesBlock :advantages="flat.advantages" />
            <NewbuildingComplexCard :newbuildingComplex="flat.newbuildingComplex" :developer="flat.developer" />
            <DeveloperCard :developer="flat.developer" />
          </template>
        </template>
      </RegularContentContainer>
    </template>
  </ShareLayout>
</template>

<script>
import ShareLayout from '@/Layouts/ShareLayout.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import UserInfoBar from '@/Components/Elements/UserInfoBar.vue'
import FlatCommercialItem from '@/Components/Flat/FlatCommercialItem.vue'
import NewbuildingComplexCard from '../../Components/NewbuildingComplex/NewbuildingComplexCard.vue'
import DeveloperCard from '../../Components/Developer/DeveloperCard.vue'
import AdvantagesBlock from '@/Components/Elements/AdvantagesBlock.vue'

export default {
  components: {
    ShareLayout,
    RegularContentContainer,
    UserInfoBar,
    FlatCommercialItem,
    NewbuildingComplexCard,
    DeveloperCard,
    AdvantagesBlock
  },
  props: {
    commercial: Array,
    commercialMode: String,
    flats: Array,
  }  
}
</script>