/**
 * returns object with params for
 * application status change form 
 * according to application status and user role
 * or false
 */
function getApplicationFormParamsByStatus (applicationStatus, userRole) { 
  const configuration = {
    1: 
      [
        {
          role: 'admin',
          operationLabel: 'Подтвердите получение заявки от агента',
          operation: 'approve_app_by_admin',
          formCaption: 'Подтверждение заявки на бронь',
          formContent: '<p>При подтверждении заявки представителю застройщика будет направлен соответствующий запрос</p> \
                        <p>Застройщик должен подтвердить бронирование через Личный Кабинет, по телефону или электронное почте</p>',
          submitLabel: 'Подтвердить'
        }
      ],
    2:
      [
        {
          role: 'admin',
          operationLabel: 'Подтвердите одобрение брони застройщиком',
          operation: 'approve_reservation_from_developer_by_admin',
          formCaption: 'Подтверждение одобрения брони зайстройщиком',
          formContent: '<p>После подтверждения агенту, подавшему заявку на бронирование, будет направлено уведомление.</p> \
                        <p>Агент должен принять заявку в работу</p>',
          submitLabel: 'Подтвердить'
        },
        {
          role: 'developer_repres',
          operationLabel: 'Подтвердите бронь объекта',
          operation: 'approve_reservation_by_developer',
          formCaption: 'Подтвердите бронь',
          formContent: '<p>Бронь можно подтвердить <strong>через форму</strong> на этой странице, по телефону <strong>+7 905 049-19-97</strong> или на почту <strong>project_manager@grvrn.ru</strong>.</p> \
                        <p>При подтверждении письмом на электронную почту не забудьте указать ФИО менеджера и подробные условия бронирования.</p> \
                        <p>Перед подтверждении брони, пожалуйста, убедитесь, что все мероприятия, связанные с изменением статуса объекта в информационных системах, завершены.</p>',
          submitLabel: 'Подтвердить'
        }
      ],
    3:
      [
        {
          role: 'admin',
          operationLabel: 'Подтвердите одобрение брони застройщиком',
          operation: 'approve_reservation_from_developer_by_admin',
          formCaption: 'Подтверждение одобрения брони зайстройщиком',
          formContent: '<p>После подтверждения агенту, подавшему заявку на бронирование, будет направлено уведомление.</p> \
                        <p>Агент должен принять заявку в работу</p>',
          submitLabel: 'Подтвердить'
        },
        /*{
          role: 'agent',
          operationLabel: 'Подтвердить получение одобрения брони и взять заявку в работу',
          operation: 'take_in_work_by_agent',
          submitLabel: 'Взять в работу'
        },
        {
          role: 'manager',
          operationLabel: 'Подтвердить получение одобрения брони и взять заявку в работу',
          operation: 'take_in_work_by_manager',
          submitLabel: 'Взять в работу'
        },*/
      ],
    4:
      [
        {
          role: 'agent',
          operationLabel: 'Подтвердите получение одобрения брони и примите заявку в работу',
          operation: 'take_in_work_by_agent',
          formCaption: 'Подтверждение приёма заявки в работу',
          formContent: '<p>При подтверждаении заявка приобретает статус <strong>"В работе"</strong>.</p>\
                        <!--<p>После завершения сделки, пожалуйста, сообщите об этом дополнительно на странице заявки.</p>-->',
          submitLabel: 'Взять в работу'
        },
        {
          role: 'manager',
          operationLabel: 'Подтвердите получение одобрения брони и возьмите заявку в работу',
          operation: 'take_in_work_by_manager',
          formCaption: 'Подтверждение приёма заявки в работу',
          formContent: '<p>При подтверждаении заявка приобретает статус <strong>"В работе"</strong>.</p>\
                        <!--<p>После завершения сделки, пожалуйста, сообщите об этом дополнительно на странице заявки.</p>-->',
          submitLabel: 'Взять в работу'
        },
      ],
    5:
      [
        {
          role: 'agent',
          operationLabel: 'Подтвердите завершение сделки, загрузите документы',
          operation: 'report_success_deal_by_agent',
          formCaption: 'Подтверждение завершения сделки',
          formContent: '<p>После отравки подтверждения информация поступит для проверки администратору площадки.</p>\
                        <p>Пожалуйста, дождитесь её окончания.</p>',
          submitLabel: 'Подтвердить'
        },
        {
          role: 'manager',
          operationLabel: 'Подтвердите завершение сделки, загрузите документы',
          operation: 'report_success_deal_by_manager',
          formCaption: 'Подтверждение завершения сделки',
          formContent: '<p>После отравки подтверждения информация поступит для проверки администратору площадки.</p>\
                        <p>Пожалуйста, дождитесь её окончания.</p>',
          submitLabel: 'Подтвердить'
        },
      ],
    9:
      [
        {
          role: 'admin',
          operationLabel: 'Подтвердите получение информации и документов о завершени сделки',
          operation: 'confirm_recieving_success_deal_info_by_admin',
          formCaption: 'Подтверждение получания информации',
          formContent: '<p>Отправляя форму Вы подтверждаете, что получили информацию о завершении сделки и соответсвующие документы.</p> \
                        <p>После проверки информации, пожалуйста, подтвердите успешное завершение сделки</p>',
          submitLabel: 'Подтвердить'       
        }
      ],
    10:
      [
        {
          role: 'admin',
          operationLabel: 'Подтвердите сделку, завершите заявку',
          operation: 'confirm_success_deal_by_admin',
          formCaption: 'Подтверждение сделки',
          formContent: '<p>Отправляя форму Вы подтверждаете завершение сделки.</p>',
          submitLabel: 'Подтвердить'       
        }
      ],
    11: [],
    12: []
  }

  let formParams = []

  if (applicationStatus in configuration) {
    formParams = configuration[applicationStatus].filter((el) => {
      return el.role === userRole
    })
  }

  return formParams.length > 0 ? formParams[0] : false

}

const initialCommercialSettings = { 
  compareTable: true,
  initiator: true,
  developer: false,
  newbuildingComplex: false,
  finishing: false,
  layouts: {
    group: {
      show: true,
      flat: true,
      floor: true,
      entrance: false,
      genplan: true
    },
    separate: {
      flat: false,
      floor: false,
      entrance: false,
      genplan: false            
    }
  },
  map: true
}

export { getApplicationFormParamsByStatus, initialCommercialSettings }