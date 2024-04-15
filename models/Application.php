<?php

namespace app\models;

use app\components\traits\FillAttributes;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "application".
 *
 * @property int $id
 * @property int $flat_id
 * @property int $applicant_id
 * @property int $status
 * @property string $client_firstname
 * @property string $client_lastname
 * @property string $client_middlename
 * @property string $client_phone
 * @property string $client_email
 * @property string $applicant_comment
 * @property string $manager_firstname
 * @property string $manager_lastname
 * @property string $manager_middlename
 * @property string $manager_phone
 * @property string $manager_email
 * @property string $reservation_conditions
 * @property string $admin_comment
 * @property int $is_active
 * @property int $is_toll
 * @property int $receipt_provided
 * @property int $ddu_provided
 * @property float $ddu_price
 * @property float $ddu_cash
 * @property float $ddu_mortgage
 * @property float $ddu_matcap
 * @property string $ddu_cash_paydate
 * @property string $ddu_mortgage_paydate
 * @property string $ddu_matcap_paydate
 * @property int $report_act_provided
 * @property int $agent_docpack_provided
 * @property int $developer_docpack_provided
 * @property string $created_at
 * @property string $updated_at
 * @property string $application_number
 * @property string $deal_success_docs
 * 
 * @property Flat $flat
 * @property Developer $developer
 * @property User $applicant
 * @property ApplicationDocument[] $documents
 * @property ApplicationDocument[] $reciepts
 * @property ApplicationDocument[] $agentDocpack
 * @property ApplicationDocument[] $developerDocpack
 * @property ApplicationDocument[] $ddus
 * @property ApplicationDocument[] $reportAct
 */
class Application extends ActiveRecord
{
    use FillAttributes;

    const STATUS_UNDEFINED = 0;
    const STATUS_RESERV_APPLICATED = 1;
    const STATUS_RESERV_AWAIT_FOR_APPROVAL = 2;
    const STATUS_RESERV_APPROVED_BY_DEVELOPER = 3;
    const STATUS_RESERV_APPROVED_BY_ADMIN = 4;
    const STATUS_APPLICATION_IN_WORK = 5;
    const STATUS_DDU_UPLOADED = 6;
    const STATUS_INVOICE_TO_DEVELOPER_ISSUED = 7;
    //const STATUS_RESERV_CANCEL_APPLICATED = 6;
    //const STATUS_RESERV_CANCELLED_BY_ADMIN = 7;
    const STATUS_APPLICATION_CANCELED_BY_ADMIN = 8;
    const STATUS_APPLICATION_APPROVAL_REQUEST = 9;
    const STATUS_APPLICATION_APPROVAL_PROCESS = 10;
    const STATUS_APPLICATION_SUCCESS = 11;
    const STATUS_SELF_RESERVED = 12;
    const STATUS_COMISSION_PAY_CONFIRMED_BY_DEVELOPER = 13;
    const STATUS_COMISSION_PAY_CONFIRMED_BY_ADMIN = 14;
    const STATUS_REPORT_ACT_UPLOADED = 15;
    const STATUS_APPLICATION_IN_WORK_AGENT_DOCPACK_PROVIDED = 16;
    const STATUS_APPLICATION_IN_WORK_DEVELOPER_DOCPACK_PROVIDED = 17;

    public static $status = [
        self::STATUS_UNDEFINED => 'Статус заявки неопределён',
        self::STATUS_RESERV_APPLICATED => 'Заявка на бронирование подана',
        self::STATUS_RESERV_AWAIT_FOR_APPROVAL => 'Заявка на бронирование ожидает подтверждения от застройщика',
        self::STATUS_RESERV_APPROVED_BY_DEVELOPER => 'Бронирование подтверждено застройщиком',
        self::STATUS_RESERV_APPROVED_BY_ADMIN => 'Бронирование подтверждено, ожидается приём заявки в работу',
        self::STATUS_APPLICATION_IN_WORK => 'Заявка в работе',
        self::STATUS_DDU_UPLOADED => 'ДДУ загружен, ожидается выставление счёта застройщику',
        self::STATUS_INVOICE_TO_DEVELOPER_ISSUED => 'Ожидается оплата от застройщика',
        //self::STATUS_RESERV_CANCEL_APPLICATED => 'Заявка на отмену брони',
        //self::STATUS_RESERV_CANCELLED_BY_ADMIN => 'Бронирование прекращено',
        self::STATUS_APPLICATION_CANCELED_BY_ADMIN => 'Заявка прекращена',
        self::STATUS_APPLICATION_APPROVAL_REQUEST => 'Подтверждающие документы (отчёт-акт) отправлены',
        self::STATUS_APPLICATION_APPROVAL_PROCESS => 'Документы получены, ожидается подтверждение и оплата',
        self::STATUS_APPLICATION_SUCCESS => 'Сделка успешно завершена',
        self::STATUS_SELF_RESERVED => 'Самостоятельное бронирование',
        self::STATUS_COMISSION_PAY_CONFIRMED_BY_DEVELOPER => 'Застройщик подтвердил выплату вознаграждения',
        self::STATUS_COMISSION_PAY_CONFIRMED_BY_ADMIN => 'Администратор подтвердил получение вознаграждения от застройщика',
        self::STATUS_APPLICATION_IN_WORK_AGENT_DOCPACK_PROVIDED => 'Заявка в работе. Пакет документов от агента загружен.',
        self::STATUS_APPLICATION_IN_WORK_DEVELOPER_DOCPACK_PROVIDED => 'Заявка в работе. Пакет документов от застройщика загружен.',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'application';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['flat_id', 'applicant_id', 'developer_id'/*, 'status'*/], 'required'],
            [['flat_id', 'applicant_id', 'developer_id', 'status'], 'integer'],
            [['client_firstname', 'client_lastname', 'client_middlename', 'client_phone', 'client_email',  'applicant_comment', 'manager_firstname', 'manager_lastname', 'manager_middlename', 'manager_phone', 'manager_email', 'reservation_conditions', 'admin_comment', 'ddu_cash_paydate', 'ddu_mortgage_paydate', 'ddu_matcap_paydate', 'application_number', 'deal_success_docs'], 'string'],
            [['ddu_price', 'ddu_cash', 'ddu_mortgage', 'ddu_matcap'], 'double'],
            [['created_at', 'updated_at'], 'safe'],
            [['is_active', 'is_toll', 'receipt_provided', 'ddu_provided', 'report_act_provided', 'agent_docpack_provided', 'developer_docpack_provided'], 'boolean'],
            [['flat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flat::className(), 'targetAttribute' => ['flat_id' => 'id']],
            [['developer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Developer::className(), 'targetAttribute' => ['developer_id' => 'id']],
            [['applicant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['applicant_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'flat_id' => 'ID квартиры',
            'applicant_id' => 'ID подавшего заявку агента (менеджера)',
            'status' => 'Текущий статус',
            'client_firstname' => 'Имя клиента',
            'client_lastname' => 'Фамилия клиента',
            'client_middlename' => 'Отчество клиента',
            'client_phone' => 'Телефон клиента',
            'client_email' => 'Email клиента',
            'applicant_comment' => 'Комментарий заявителя',
            'manager_firstname' => 'Имя менеджера застройщика',
            'manager_lastname' => 'Фамилия менеджера застройщика',
            'manager_middlename' => 'Отчество менеджера застройщика',
            'manager_phone' => 'Телефон менеджера застройщика',
            'manager_email' => 'Email менеджера застройщика',
            'admin_comment' => 'Комментарий адинистратора',
            'is_active' => 'Заявка активна',
        ];
    }

    public function getActiveApplications ()
    {
        return self::find()
            ->where(['is_active' => 1])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
    }

    public function getApplicationsByAuthor ($authorId)
    {
        return $this->find()
            ->where(['applicant_id' => $authorId, 'is_active' => 1])
            ->orderBy(['created_at' => SORT_DESC]);
    }

    public function getApplicationsForDeveloper ($developerId)
    {
        return $this->find()
            ->where(['developer_id' => $developerId, 'is_active' => 1])
            ->orderBy(['created_at' => SORT_DESC]);
    }

    public function getFlat ()
    {
        return $this->hasOne(Flat::className(), ['id' => 'flat_id']);
    }

    public function getDocuments ()
    {
        return $this->hasMany(ApplicationDocument::className(), ['application_id' => 'id']); 
    }

    public function getReciepts ()
    {
        return $this->hasMany(ApplicationDocument::className(), ['application_id' => 'id'])->andWhere(['category' => ApplicationDocument::CAT_RECIEPT]); 
    }

    public function getAgentDocpack ()
    {
        return $this->hasMany(ApplicationDocument::className(), ['application_id' => 'id'])->andWhere(['category' => ApplicationDocument::AGENT_DOCPACK]); 
    }

    public function getDeveloperDocpack ()
    {
        return $this->hasMany(ApplicationDocument::className(), ['application_id' => 'id'])->andWhere(['category' => ApplicationDocument::DEVELOPER_DOCPACK]); 
    }

    public function getDdus ()
    {
        return $this->hasMany(ApplicationDocument::className(), ['application_id' => 'id'])->andWhere(['category' => ApplicationDocument::CAT_DDU]); 
    }

    public function getReportAct ()
    {
        return $this->hasMany(ApplicationDocument::className(), ['application_id' => 'id'])->andWhere(['category' => ApplicationDocument::CAT_REPORT_ACT]); 
    }

    public function getHistory ()
    {
        return $this->hasMany(ApplicationHistory::className(), ['application_id' => 'id']); 
    }

    public function getApplicant ()
    {
        return $this->hasOne(User::className(), ['id' => 'applicant_id']);
    }

    /**
     * Check if a user has reserved an object
     */
    public static function checkApplicationByUserAndFlat($userId, $flatId)
    {
        $existingApplcations = self::find()->where(['applicant_id' => $userId])->andWhere(['flat_id' => $flatId])->andWhere(['is_active' => 1])->all();
        return count($existingApplcations) > 0 ? true : false;
    }
    
}