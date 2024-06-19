/** APPLICATION (booking, reservation) */

/**
 * returns object with params for
 * application status change form 
 * according to application status and user role
 * or false
 */
function getApplicationFormParamsByStatus (applicationStatus, userRole) {
  /** The list of aviable operations with the application according to user's role */
  const operations = {
    approve_app_by_admin: {
      role: 'admin',
      operationLabel: 'Подтвердите получение заявки от агента',
      operation: 'approve_app_by_admin',
      formCaption: 'Подтверждение заявки на бронь',
      formContent: '<p>При подтверждении заявки представителю застройщика будет направлен соответствующий запрос</p> \
      <p>Застройщик должен подтвердить бронирование через Личный Кабинет, по телефону или электронное почте</p>',
      submitLabel: 'Подтвердить'
    },
    approve_reservation_from_developer_by_admin: {
      role: 'admin',
      operationLabel: 'Подтвердите одобрение брони застройщиком',
      operation: 'approve_reservation_from_developer_by_admin',
      formCaption: 'Подтверждение одобрения брони зайстройщиком',
      formContent: '<p>После подтверждения агенту, подавшему заявку на бронирование, будет направлено уведомление.</p> \
      <p>Агент должен принять заявку в работу</p>',
      submitLabel: 'Подтвердить'
    },
    approve_reservation_by_developer: {
      role: 'developer_repres',
      operationLabel: 'Подтвердите бронь объекта',
      operation: 'approve_reservation_by_developer',
      formCaption: 'Подтвердите бронь',
      formContent: '<p>Бронь можно подтвердить <strong>через форму</strong> на этой странице, по телефону <strong>+7 905 049-19-97</strong> или на почту <strong>project_manager@grvrn.ru</strong>.</p> \
      <p>При подтверждении письмом на электронную почту не забудьте указать ФИО менеджера и подробные условия бронирования.</p> \
      <p>Перед подтверждении брони, пожалуйста, убедитесь, что все мероприятия, связанные с изменением статуса объекта в информационных системах, завершены.</p>',
      submitLabel: 'Подтвердить'
    },
    take_in_work_by_agent: {
      role: 'agent',
      operationLabel: 'Подтвердите получение одобрения брони и примите заявку в работу',
      operation: 'take_in_work_by_agent',
      formCaption: 'Подтверждение приёма заявки в работу',
      formContent: '<p>Если нужно предоставить какие-либо документы для застройщика, загрузите их в поле ниже.</p> \
      <p>При подтверждаении заявка приобретает статус <strong>"В работе"</strong>.</p>\
      <!--<p>После завершения сделки, пожалуйста, сообщите об этом дополнительно на странице заявки.</p>-->',
      submitLabel: 'Взять в работу'
    },
    take_in_work_by_manager: {
      role: 'manager',
      operationLabel: 'Подтвердите получение одобрения брони и возьмите заявку в работу',
      operation: 'take_in_work_by_manager',
      formCaption: 'Подтверждение приёма заявки в работу',
      formContent: '<p>Если нужно предоставить какие-либо документы для застройщика, загрузите их в поле ниже.</p> \
      <p>При подтверждаении заявка приобретает статус <strong>"В работе"</strong>.</p>\
      <!--<p>После завершения сделки, пожалуйста, сообщите об этом дополнительно на странице заявки.</p>-->',
      submitLabel: 'Взять в работу'
    },
    upload_developer_docpack: {
      role: 'developer_repres',
      operationLabel: 'Загрузить документы для агента',
      operation: 'upload_developer_docpack',
      formCaption: 'Загрузка документов для агента',
      formContent: '<p>Если нужно предоставить какие-либо документы для агента, загрузите их в поле ниже.</p>',
      submitLabel: 'Загрузить'
    },
    upload_ddu_by_agent: {
      role: 'agent',
      operationLabel: 'Загрузите Договор долевого участия и заполните информацию об оплате',
      operation: 'upload_ddu_by_agent',
      formCaption: 'Загрузка ДДУ',
      formContent: '<p>После проверки информации и обработки заявки Вы сможете выставить счет на оплату КВ.</p>\
      <p>Образец документа будет доступен на странице заявки.</p>',
      submitLabel: 'Загрузить'
    },
    upload_ddu_by_manager: {
      role: 'manager',
      operationLabel: 'Загрузите Договор долевого участия и заполните информацию об оплате',
      operation: 'upload_ddu_by_manager',
      formCaption: 'Загрузка ДДУ',
      formContent: '<p>После проверки информации и обработки заявки Вы сможете выставить счет на оплату КВ.</p>\
      <p>Образец документа будет доступен на странице заявки.</p>',
      submitLabel: 'Загрузить'
    },
    /*report_success_deal_by_agent: {
      role: 'agent',
      operationLabel: 'Подтвердите завершение сделки, загрузите документы',
      operation: 'report_success_deal_by_agent',
      formCaption: 'Подтверждение завершения сделки',
      formContent: '<p>После отравки подтверждения информация поступит для проверки администратору площадки.</p>\
      <p>Пожалуйста, дождитесь её окончания.</p>',
      submitLabel: 'Подтвердить'
    },
    report_success_deal_by_manager: {
      role: 'manager',
      operationLabel: 'Подтвердите завершение сделки, загрузите документы',
      operation: 'report_success_deal_by_manager',
      formCaption: 'Подтверждение завершения сделки',
      formContent: '<p>После отравки подтверждения информация поступит для проверки администратору площадки.</p>\
      <p>Пожалуйста, дождитесь её окончания.</p>',
      submitLabel: 'Подтвердить'
    },*/
    issue_invoice_to_developer: {
      role: 'admin',
      operationLabel: 'Выставить счёт застройщику',
      operation: 'issue_invoice_to_developer',
      formCaption: 'Выставление счёта на оплату вознаграждения застройщику',
      formContent: '<p>Выставляя счёт, убедитесь, что все процедуры, связанные с оплатой объекта успешно завершены.</p> \
      <p>После получения выплаты от застройщика, пожалуйста, подтвердите этот факт на странице заявки.</p>',
      submitLabel: 'Выставить счёт' 
    },
    report_payment_from_developer: {
      role: 'developer_repres',
      operationLabel: 'Подтвердить выплату вознаграждения',
      operation: 'report_payment_from_developer',
      formCaption: 'Подтвердите выплату вознаграждения',
      formContent: '<p>Оплату можно подтвердить на странице заявки, по телефону <strong>+7 905 049-19-97</strong> или на почту <strong>project_manager@grvrn.ru</strong>.</p>',
      submitLabel: 'Подтвердить'
    },
    confirm_payment_from_developer_by_admin: {
      role: 'admin',
      operationLabel: 'Подтвердить получение оплаты от застройщика',
      operation: 'confirm_payment_from_developer_by_admin',
      formCaption: 'Подтверждение получения вознаграждения от застройщика',
      formContent: '<p>После подтверждениия агенту будет доступен для скачивания и последующего предоставления отчет-акт.</p>',
      submitLabel: 'Подтвердить'
    },
    issue_report_act_by_agent: {
      role: 'agent',
      operationLabel: 'Загрузить Отчет-Акт и Счет',
      operation: 'issue_report_act',
      formCaption: 'Загрузите отчет-акт и счет',
      formContent: '<p>После загрузки и обработки Отчета-Акта и Счета будет произведена выплата вознаграждения</p>',
      submitLabel: 'Загрузить'
    },
    issue_report_act_by_manager: {
      role: 'manager',
      operationLabel: 'Загрузить Отчет-Акт и Счет',
      operation: 'issue_report_act',
      formCaption: 'Загрузите отчет-акт и счет',
      formContent: '<p>После загрузки и обработки Отчета-Акта и Счета будет произведена выплата вознаграждения</p>',
      submitLabel: 'Загрузить'
    },
    confirm_recieving_success_deal_info_by_admin: {
      role: 'admin',
      operationLabel: 'Подтвердите получение информации и документов о завершени сделки',
      operation: 'confirm_recieving_success_deal_info_by_admin',
      formCaption: 'Подтверждение получания информации',
      formContent: '<p>Отправляя форму Вы подтверждаете, что получили информацию о завершении сделки и соответсвующие документы.</p> \
      <p>После проверки информации, пожалуйста, подтвердите успешное завершение сделки</p>',
      submitLabel: 'Подтвердить' 
    },
    confirm_success_deal_by_admin: {
      role: 'admin',
      operationLabel: 'Подтвердите сделку, завершите заявку',
      operation: 'confirm_success_deal_by_admin',
      formCaption: 'Подтверждение сделки',
      formContent: '<p>Отправляя форму Вы подтверждаете завершение сделки.</p>',
      submitLabel: 'Подтвердить'
    }
  }

  /** Configure allowed operations for each application's status */
  const configuration = {
    1: [operations.approve_app_by_admin],
    2: [operations.approve_reservation_from_developer_by_admin, operations.approve_reservation_by_developer],
    3: [operations.approve_reservation_from_developer_by_admin, operations.take_in_work_by_agent, operations.take_in_work_by_manager],
    4: [operations.take_in_work_by_agent, operations.take_in_work_by_manager],
    16: [operations.upload_developer_docpack, operations.upload_ddu_by_agent, operations.upload_ddu_by_manager],
    17: [operations.upload_ddu_by_agent, operations.upload_ddu_by_manager],
    5: [operations.upload_ddu_by_agent, operations.upload_ddu_by_manager],
    6: [operations.issue_invoice_to_developer],
    7: [operations.report_payment_from_developer, operations.confirm_payment_from_developer_by_admin],
    13: [operations.confirm_payment_from_developer_by_admin],
    14: [operations.issue_report_act_by_agent, operations.issue_report_act_by_manager],
    9: [operations.confirm_recieving_success_deal_info_by_admin],
    10: [operations.confirm_success_deal_by_admin],
    11: [],
    12: [operations.upload_developer_docpack, operations.upload_ddu_by_agent, operations.upload_ddu_by_manager]
  }

  let formParams = []

  if (applicationStatus in configuration) {
    formParams = configuration[applicationStatus].filter((el) => {
      return el.role === userRole
    })
  }

  return formParams.length > 0 ? formParams[0] : false

}

/** COMMERCIAL PROPOSAL */
/** Initial settings for commercial view */
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