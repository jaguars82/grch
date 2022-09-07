<template>
    <Loading v-if="loading" />
    <template v-else>
      <div v-if="statusChangesForm">
        <p>Требуемое действие:</p>
        <p>{{ statusChangesForm.operationLabel }}</p>
        <q-form
          @submit="onSubmit"
        >
          <input type="hidden" v-model="formfields.operation">
          <q-btn :label="statusChangesForm.submitLabel" type="submit" color="primary"/>
        </q-form>
      </div>
      <!--<div v-if="user.role in formConfigurationByStatus[application.status]">
      <div>
        <p>Требуемое действие:</p>
        <p>{{ formConfigurationByStatus[application.status][user.role].operationLabel }}</p>
        <q-form
          @submit="onSubmit"
        >
          <input type="hidden" v-model="formfields.operation">
          <q-btn :label="formConfigurationByStatus[application.status][user.role].submitLabel" type="submit" color="primary"/>
        </q-form>
      </div>-->
    </template>
</template>

<script>
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import Loading from "@/Components/Elements/Loading.vue"
import { userInfo } from '@/composables/shared-data'

export default {
  props: {
    application: {
      type: Object
    },
    statusChangeResult: {
      type: String
    }
  },
  components: {
    Loading
  },
  setup(props) {
    const { user } = userInfo()
    const loading = ref(false)

    /*const formConfigurationByStatus = ref({
      1: {
        admin: {
          operationLabel: 'Подтвердите получение заявки от агента',
          operation: 'approve_app_by_admin',
          submitLabel: 'Подтвердить'        }
      }
    })*/

    const formConfigurationByStatus = {
      1: 
        [
          {
            role: 'admin',
            operationLabel: 'Подтвердите получение заявки от агента',
            operation: 'approve_app_by_admin',
            submitLabel: 'Подтвердить'
          }
        ],
      2:
        []
    }

    const formfields = ref({ operation: '' })
    const statusChangesForm = ref(false)

    const refreshData = () => {
      const formParams = formConfigurationByStatus[props.application.status].filter((el) => {
        return el.role === user.value.role
      })

      if(Array.isArray(formParams) && formParams.length > 0) {
        statusChangesForm.value = formParams[0]
        formfields.value = { operation: statusChangesForm.value.operation }
      }
    }

    refreshData()

    /*****const formParams = formConfigurationByStatus[props.application.status].filter((el) => {
      return el.role === user.value.role
    })
    const statusChangesForm = formParams.length > 0 ? formParams[0] : false
    const formfields = ref(
      {
        operation: statusChangesForm ? statusChangesForm.operation : '',
      }
    )*****/

    /*const formfields = ref(
      {
        operation: user.value.role in formConfigurationByStatus.value[props.application.status] ? formConfigurationByStatus.value[props.application.status][user.value.role].operation : '',
      }
    )*/

    function onSubmit() {
      loading.value = true
      Inertia.post(`/user/application/view?id=${props.application.id}`, formfields.value)
      Inertia.on('finish', (event) => {
        refreshData()
        loading.value = false
      })
    }

    return { loading, statusChangesForm, formfields, onSubmit }
    //return { user, loading, formConfigurationByStatus, formfields, onSubmit }

  },
}
</script>
