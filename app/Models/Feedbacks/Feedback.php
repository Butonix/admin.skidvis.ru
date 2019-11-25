<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 07.08.2019
 * Time: 23:35
 */

namespace App\Models\Feedbacks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Feedbacks\Feedback
 *
 * @mixin \Eloquent
 * @property int                                          $id
 * @property string|null                                  $text
 * @property string|null                                  $name
 * @property string|null                                  $phone
 * @property int|null                                     $feedback_type_id
 * @property string|null                                  $user_ip
 * @property string|null                                  $user_agent
 * @property \Illuminate\Support\Carbon|null              $created_at
 * @property \Illuminate\Support\Carbon|null              $updated_at
 * @property-read \App\Models\Feedbacks\FeedbackType|null $feedbackType
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feedbacks\Feedback whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feedbacks\Feedback whereFeedbackTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feedbacks\Feedback whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feedbacks\Feedback whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feedbacks\Feedback wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feedbacks\Feedback whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feedbacks\Feedback whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feedbacks\Feedback whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feedbacks\Feedback whereUserIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feedbacks\Feedback newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feedbacks\Feedback newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feedbacks\Feedback query()
 */
class Feedback extends Model {
    /**
     * @var string
     */
    protected $table = 'feedbacks';

    /**
     * @var array
     */
    protected $fillable = ['text', 'name', 'phone', 'feedback_type_id', 'user_ip', 'user_agent'];

    /**
     * @var array
     */
    protected static $rules = [
        'name'  => 'required|string:max255',
        'phone' => 'required|string|max:255',
    ];

    /**
     * @var array
     */
    protected static $messages = [
        'name.required'  => 'Укажите имя',
        'phone.required' => 'Укажите номер телефона',
    ];

    /**
     * @return array
     */
    public static function getRules(): array {
        return self::$rules;
    }

    /**
     * @return array
     */
    public static function getMessages(): array {
        return self::$messages;
    }

    /**
     * @return null|string
     */
    public function getText(): ?string {
        return $this->{'text'};
    }

    /**
     * @param null|string $text
     */
    public function setText(?string $text): void {
        $this->{'text'} = $text;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string {
        return $this->{'name'};
    }

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void {
        $this->{'name'} = $name;
    }

    /**
     * @return null|string
     */
    public function getPhone(): ?string {
        return $this->{'phone'};
    }

    /**
     * @param null|string $phone
     */
    public function setPhone(?string $phone): void {
        $this->{'phone'} = $phone;
    }

    /**
     * @return null|string
     */
    public function getUserIp(): ?string {
        return $this->{'user_ip'};
    }

    /**
     * @param null|string $user_ip
     */
    public function setUserIp(?string $user_ip): void {
        $this->{'user_ip'} = $user_ip;
    }

    /**
     * @return null|string
     */
    public function getUserAgent(): ?string {
        return $this->{'user_agent'};
    }

    /**
     * @param null|string $user_agent
     */
    public function setUserAgent(?string $user_agent): void {
        $this->{'user_agent'} = $user_agent;
    }

    /**
     * @return BelongsTo
     */
    public function feedbackType(): BelongsTo {
        return $this->belongsTo(FeedbackType::class);
    }

    /**
     * @return FeedbackType|null
     */
    public function getFeedbackType(): ?FeedbackType {
        return $this->feedbackType;
    }

    /**
     * @return null|string
     */
    public function getFeedbackTypeName(): ?string {
        $feedbackType = $this->getFeedbackType();

        if (is_null($feedbackType)) {
            return null;
        }

        return $feedbackType->getName();
    }

    /**
     * @return bool
     */
    public static function isNotificationEnabled(): bool {
        $envActivation = env('FEEDBACK_NOTIFICATION');

        return $envActivation ?? false;
    }
}
