<template>
    <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer :title="`Коммерческое предложение №${commercial.number}`">
        <template v-slot:content>

          <q-bar class="q-px-none bg-white">
            
            <q-btn unelevated label="Поделиться" icon="share">
              <q-menu auto-close>
                <q-list>
                  <q-item clickable v-ripple>
                    <q-item-section avatar>
                      <q-icon name="link"/>
                    </q-item-section>
                    <q-item-section @click="shareDialogs.copyLink = true">
                      Ссылка
                    </q-item-section>
                  </q-item>
                  <q-item clickable v-ripple>
                    <q-item-section avatar>
                      <q-icon name="mail"/>
                    </q-item-section>
                    <q-item-section @click="shareDialogs.sendEmail = true">
                      Электронная почта
                    </q-item-section>
                  </q-item>
                  <q-item clickable v-ripple>
                    <q-item-section avatar>
                      <q-icon name="picture_as_pdf"/>
                    </q-item-section>
                    <q-item-section @click="savePDF">
                      Скачать PDF
                    </q-item-section>
                  </q-item>
                  <q-item clickable v-ripple>
                    <q-item-section avatar>
                      <q-icon name="send"/>
                    </q-item-section>
                    <q-item-section>
                      <a target="_blank" class="undecorated" :href="`https://telegram.me/share/url?url=https://grch.ru/share/commercial?id=${commercial.id}&text=Коммерческое предложение №${commercial.number}`">Telegram</a>
                    </q-item-section>
                  </q-item>
                  <q-item clickable v-ripple>
                    <q-item-section avatar>
                      <q-icon name="phone"/>
                    </q-item-section>
                    <q-item-section>
                      <a target="_blank" class="undecorated" :href="`https://api.whatsapp.com/send/?text=Коммерческое предложение №${commercial.number}: https://grch.ru/share/commercial?id=${commercial.id}`">WhatsApp</a>
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-menu>
            </q-btn>

            <q-btn unelevated label="Настройки" icon="checklist">
              <q-menu>
                <q-list>

                  <q-item tag="label" v-if="flats.length > 1" v-ripple>
                    <q-item-section side top>
                      <q-checkbox v-model="commercialSettings.compareTable" />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>Таблица сравнения</q-item-label>
                      <q-item-label caption>
                        Отображать таблицу сравнения объектов
                      </q-item-label>
                    </q-item-section>
                  </q-item>

                  <q-item tag="label" v-ripple>
                    <q-item-section side top>
                      <q-checkbox v-model="commercialSettings.layouts.group.show" />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>Планировки <!--группой--></q-item-label>
                      <q-item-label caption>
                        Отображать планировку квартиры, этажа, генплан в группе
                      </q-item-label>
                    </q-item-section>
                  </q-item>

                  <!--
                  <q-item tag="label" v-ripple>
                    <q-item-section side top>
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>Планировки отдельно</q-item-label>
                      <q-item-label caption>
                        Настроить перечень и расположение планировок, показываемых отдельно
                      </q-item-label>
                    </q-item-section>
                    <q-item-section>
                      <q-btn icon="dashboard_customize">
                        <q-menu>
                          <q-list>
                            <q-item>
                              <q-item-section side top>
                                <q-checkbox v-model="commercialSettings.layouts.separate.flat" />
                              </q-item-section>
                              <q-item-section>
                                <q-item-label>План квартиры</q-item-label>
                              </q-item-section>
                            </q-item>
                            <q-item>
                              <q-item-section side top>
                                <q-checkbox v-model="commercialSettings.layouts.separate.floor" />
                              </q-item-section>
                              <q-item-section>
                                <q-item-label>План этажа</q-item-label>
                              </q-item-section>
                            </q-item>
                            <q-item>
                              <q-item-section side top>
                                <q-checkbox v-model="commercialSettings.layouts.separate.entrance" />
                              </q-item-section>
                              <q-item-section>
                                <q-item-label>Расположение подъезда</q-item-label>
                              </q-item-section>
                            </q-item>
                            <q-item>
                              <q-item-section side top>
                                <q-checkbox v-model="commercialSettings.layouts.separate.genplan" />
                              </q-item-section>
                              <q-item-section>
                                <q-item-label>Генплан</q-item-label>
                              </q-item-section>
                            </q-item>
                          </q-list>
                        </q-menu>
                      </q-btn>
                    </q-item-section>
                  </q-item>
                  -->

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
                <q-btn
                  class="q-mt-sm"
                  label="Сохранить настройки"
                  icon="save"
                  @click="saveSettings"
                  :disable="JSON.stringify(commercialSettings) == commercial.settings"
                />
              </q-menu>
            </q-btn>
          </q-bar>

          <q-dialog v-model="shareDialogs.copyLink" persistent>
            <q-card>
              <q-card-section class="row items-center">
                <div class="col-2">
                  <q-avatar icon="link" color="primary" text-color="white" />
                </div>
                <div class="col-10 q-px-md">
                  <p class="q-mb-sm">Ссылка на КП:</p>
                  <a target="_blank" :href="`https://grch.ru/share/commercial?id=${commercial.id}`">https://grch.ru/share/commercial?id={{ commercial.id }}</a>
                </div>
              </q-card-section>

              <q-card-actions align="right">
                <q-btn flat label="Закрыть" color="primary" v-close-popup />
                <!--<q-btn flat label="Скопировать в буфер" color="primary" v-close-popup />-->
              </q-card-actions>
            </q-card>
          </q-dialog>

          <q-dialog v-model="shareDialogs.sendEmail" persistent>
            <q-card style="min-width: 350px">
              <q-card-section>
                <div class="text-h6">Электронная почта получателя</div>
              </q-card-section>

              <q-card-section class="q-pt-none">
                <q-input dense v-model="formfields.email" autofocus @keyup.enter="prompt = false" />
                <p> {{ formfields.email }} </p>
              </q-card-section>

              <q-card-actions align="right" class="text-primary">
                <q-btn flat label="Отмена" v-close-popup />
                <q-btn flat label="Отправить" v-close-popup @click="onSendEmail" />
              </q-card-actions>
            </q-card>
          </q-dialog>

          <q-card flat bordered class="q-mt-sm">
            <q-card-section>
              <Loading v-if="PDFloading" size="md" text="Идёт подготовка PDF-файла" />
          
            <q-card class="hidden q-mb-md">
              <q-card-section>
                <div class="text-right"><q-btn flat icon="close" @click="closePDFLink" /></div>
                Если загрузка файла не началась автоматически, 
              <a
                class="d-hidden"
                ref="pdfLink"
                download
                :href="`/downloads/Коммерческое предложение - ${commercial.number}.pdf`"
              >
              воспользуйтесь ссылкой
              </a>
              </q-card-section>
            </q-card>

              <UserInfoBar v-if="commercialSettings.initiator" :user="commercial.initiator" />
            </q-card-section>
          </q-card>

          <CompareTableFlats v-if="commercialSettings.compareTable && flats.length > 1" :flats="flats" />

          <template v-for="flat in flats" :key="flat.id">
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
  </ProfileLayout>
</template>

<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import UserInfoBar from '@/Components/Elements/UserInfoBar.vue'
import CompareTableFlats from '@/Components/CompareTableFlats.vue'
import FlatCommercialItem from '@/Components/Flat/FlatCommercialItem.vue'
import NewbuildingComplexCard from '@/Components/NewbuildingComplex/NewbuildingComplexCard.vue'
import DeveloperCard from '@/Components/Developer/DeveloperCard.vue'
import FinishingCard from '@/Components/FinishingCard.vue'
import AdvantagesBlock from '@/Components/Elements/AdvantagesBlock.vue'
import Loading from '@/Components/Elements/Loading.vue'
import { initialCommercialSettings } from '@/composables/components-configurations'

export default {
  components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
    UserInfoBar,
    CompareTableFlats,
    FlatCommercialItem,
    NewbuildingComplexCard,
    DeveloperCard,
    FinishingCard,
    AdvantagesBlock,
    Loading
  },
  props: {
    commercial: Array,
    commercialMode: String,
    flats: Array,
    operation: String,
    status: String
  },
  setup(props) {
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
        label: 'Коммерческие предложения',
        icon: 'share',
        url: '/user/commercial/index',
        data: false,
        options: false
      },
      {
        id: 4,
        label: `№ ${props.commercial.number}`,
        icon: 'description',
        url: '/user/commercial/index',
        data: {id: props.commercial.id},
        options: false
      },
    ])

    const PDFloading = ref(false)
    const PDFLinkAutoclicked = ref(false)
    // const PDFReady = ref(false)

    const formfields = ref({})
    const clearFormFields = function () {
      formfields.value = {}
    }

    const shareDialogs = ref({
      copyLink: false,
      sendEmail: false,
    })

    const commercialSettings = props.commercial.settings
      ? ref(JSON.parse(props.commercial.settings))
      : ref(initialCommercialSettings)

    const pdfContent = ref(null)
    const pdfLink = ref(null)

    const savePDF = function () {
      formfields.value.operation = 'pdf'
      PDFloading.value = true
      PDFLinkAutoclicked.value = false
      Inertia.post(`/user/commercial/view?id=${props.commercial.id}`, formfields.value)
      Inertia.on('finish', (event) => {
        PDFloading.value = false
        clearFormFields()
        //PDFReady.value = true
        if(PDFLinkAutoclicked.value === false) {
          pdfLink.value.click()
        }
        PDFLinkAutoclicked.value = true
      })
    }

    const onSendEmail = function () {
      formfields.value.operation = 'email'
      Inertia.post(`/user/commercial/view?id=${props.commercial.id}`, formfields.value)
      Inertia.on('finish', (event) => {
        clearFormFields()
      })
    }

    /*const closePDFLink = function () {
      PDFReady.value = false
    }*/

    const saveSettings = function () {
      formfields.value.operation = 'settings'
      formfields.value.settings = commercialSettings.value
      Inertia.post(`/user/commercial/view?id=${props.commercial.id}`, formfields.value)
    }

    return { 
      breadcrumbs,
      formfields,
      shareDialogs,
      commercialSettings,
      PDFloading,
      pdfLink,
      pdfContent,
      savePDF,
      onSendEmail,
      //PDFReady,
      //closePDFLink,
      saveSettings
    }
  },
}
</script>

<style scoped>
a.undecorated {
  color: inherit;
  text-decoration: none;
}
</style>