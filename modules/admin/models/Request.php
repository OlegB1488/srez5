<?php

namespace app\modules\admin\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "request".
 *
 * @property int $id
 * @property string|null $status
 * @property string $name
 * @property string $before_img
 * @property string|null $after_img
 * @property string|null $why_not
 * @property int $category_id
 * @property string $created_at
 * @property int $created_by
 * @property int $updatet_by
 *
 * @property Category $category
 */
class Request extends \yii\db\ActiveRecord
{

    public $imageFile1;
    public $imageFile2;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->status = 'Новая';
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request';
    }
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updatet_by',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name','category_id'], 'required'],
            [['why_not'], 'string'],
            [['category_id', 'created_by', 'updatet_by'], 'integer'],
            [['created_at'], 'safe'],
            [['status', 'name', 'before_img', 'after_img'], 'string', 'max' => 255],
            [['imageFile1'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg, bmp', 'maxSize' => 10 * 1024 * 1024],
            [['imageFile2'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, bmp', 'maxSize' => 10 * 1024 * 1024],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],

            ['imageFile2', 'required', 'when' => function($model, $attribute) {
                return $model->status === 'Решена';

            }, 'enableClientValidation' => false],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Статус заявки',
            'name' => 'Название',
            'before_img' => 'Фото до',
            'after_img' => 'Фото после',
            'why_not' => 'Причина отказа',
            'category_id' => 'Категория',
            'created_at' => 'Created At',
            'created_by' => 'Автор',
            'updatet_by' => 'Updatet By',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    public function upload()
    {
        if ($this->validate()) {
            if ($this->imageFile1) {
                $path1 = 'uploads/' . $this->imageFile1->baseName . '.' . $this->imageFile1->extension;
                $this->imageFile1->saveAs($path1);
                $this->before_img=$path1;
            }
            if ($this->imageFile2) {
                $path2 = 'uploads/' . $this->imageFile2->baseName . '.' . $this->imageFile2->extension;
                $this->imageFile2->saveAs($path2);
                $this->before_img=$path2;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return array
     */
    public static function ListStatus(){
        $arr = [
            'Новая' => 'Новая',
            'Решена' => 'Решена',
            'Отклонена' => 'Отклонена',
        ];
        if (Yii::$app->user->can('admin')){
            array_merge($arr,['Отклонена' => 'Отклонена']);
        }
        return $arr;
    }

}
