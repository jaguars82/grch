<template>
    <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer title="Коммерческое предложение">
        <template v-slot:content>

          <q-bar class="q-px-none bg-white">
            
            <q-btn unelevated label="Поделиться" icon="send">
              <q-menu auto-close>
                <q-list>
                  <q-item clickable v-ripple>
                    <q-item-section avatar>
                      <q-icon name="link"/>
                    </q-item-section>
                    <q-item-section>
                      Ссылка
                    </q-item-section>
                  </q-item>
                  <q-item clickable v-ripple>
                    <q-item-section avatar>
                      <q-icon name="mail"/>
                    </q-item-section>
                    <q-item-section>
                      Электронная почта
                    </q-item-section>
                  </q-item>
                  <q-item clickable v-ripple>
                    <q-item-section avatar>
                      <q-icon name="picture_as_pdf"/>
                    </q-item-section>
                    <q-item-section>
                      Скачать PDF
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-menu>
            </q-btn>

            <q-btn unelevated label="Настройки" icon="checklist">
              <q-menu>
                <q-list>

                  <q-item tag="label" v-ripple>
                    <q-item-section side top>
                      <q-checkbox v-model="commercialSettings.initiator" />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>Сотрудник</q-item-label>
                      <q-item-label caption>
                        Отображать информацию о сотруднике
                      </q-item-label>
                    </q-item-section>
                  </q-item>

                  <q-item tag="label" v-ripple>
                    <q-item-section side top>
                      <q-checkbox v-model="commercialSettings.developer" />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>Застройщик</q-item-label>
                      <q-item-label caption>
                        Отображать информацию о застройщике
                      </q-item-label>
                    </q-item-section>
                  </q-item>

                  <q-item tag="label" v-ripple>
                    <q-item-section side top>
                      <q-checkbox v-model="commercialSettings.newbuildingComplex" />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>Жилой комплекс</q-item-label>
                      <q-item-label caption>
                        Отображать информацию о жилом комплексе
                      </q-item-label>
                    </q-item-section>
                  </q-item>

                  <q-item tag="label" v-ripple>
                    <q-item-section side top>
                      <q-checkbox v-model="commercialSettings.finishing" />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>Отделка</q-item-label>
                      <q-item-label caption>
                        Отображать информацию об отделке
                      </q-item-label>
                    </q-item-section>
                  </q-item>

                </q-list>
              </q-menu>
            </q-btn>
          </q-bar>

          <q-card flat bordered class="q-mt-sm">
            <q-card-section>
              <UserInfoBar :user="commercial.initiator" />
            </q-card-section>
          </q-card>
          <FlatCommercialItem class="q-mt-md q-ml-md" :flat="flats[0]"></FlatCommercialItem>
        </template>
      </RegularContentContainer>
    </template>
  </ProfileLayout>
</template>

<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import UserInfoBar from '@/Components/Elements/UserInfoBar.vue'
import FlatCommercialItem from '../../../Components/Flat/FlatCommercialItem.vue'

export default {
  components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
    UserInfoBar,
    FlatCommercialItem
  },
  props: {
    commercial: Array,
    commercialMode: String,
    flats: Array,
  },
  setup() {
    const commercialSettings = ref({
      initiator: true,
      developer: false,
      newbuildingComplex: false,
      finishing: false,
    })

    return { commercialSettings }
  },
}
</script>