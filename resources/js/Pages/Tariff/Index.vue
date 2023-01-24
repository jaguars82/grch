<template>
  <MainLayout>
    <template v-slot:main>
      <q-card class="q-my-md shadow-7">
        <q-card-section>
          <h3 class="text-center">Таблица тарифов</h3>
        </q-card-section>
        <q-card-section>
          <div v-for="developer of developers" :key="developer.id">
            <template v-if="developer.complexes.length">
              <p class="text-h4 q-mt-md q-mb-xs">{{ developer.name }}</p>
              <table class="tarifftable full-width">
                <tr>
                  <th class="text-center">Жилой комплекс</th>
                  <th class="text-center">Размер вознаграждения</th>
                  <th class="text-center">Сроки выплаты вознаграждения</th>
                </tr>
                <tr v-for="complex of developer.complexes" :key="complex.id">
                  <td>{{ complex.name }}</td>
                  <td>
                    <div v-for="(tariff, i) of complex.tariffs" :key="i">
                      <span v-if="tariff.tariffType === 'percent'">{{ tariff.amountPercent }}%</span>
                      <span v-else-if="tariff.tariffType === 'currency'">{{ tariff.amountCurrency }} ₽</span>
                      <span v-else-if="tariff.tariffType === 'custom'">{{ tariff.amountCustom }}</span>
                      <span v-if="tariff.annotation"> - {{ tariff.annotation }}</span>
                    </div>
                  </td>
                  <td>{{ complex.termsOfPayment }}</td>
                </tr>
              </table>
            </template>
          </div>
        </q-card-section>
      </q-card>
    </template>
  </MainLayout>
</template>

<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import MainLayout from '@/Layouts/MainLayout.vue'
import Loading from "@/Components/Elements/Loading.vue"

export default {
  props: {
    model: {
      type: Array,
      derfault: []
    },
    developers: {
      type: Array,
      default: []
    }
  },
  components: {
    MainLayout, Loading
  },
  setup() {
    
  },
}
</script>

<style scoped>
.tarifftable {
  border-collapse: collapse;
}

table td, table th {
  border: 1px solid #888;
  padding: 5px 10px;
}
</style>