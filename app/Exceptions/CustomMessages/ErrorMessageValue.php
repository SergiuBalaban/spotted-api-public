<?php

namespace App\Exceptions\CustomMessages;

class ErrorMessageValue
{
    const ERROR_SMS_MESSAGE = 'SMS code is incorrect. Please try again';

    const ERROR_SMS_CODE = 'error_wrong_sms_code';

    const ERROR_MAX_REPORTS_MESSAGE = 'You have exceded the maximum amount of reports for today';

    const ERROR_MAX_REPORTS_CODE = 'error_max_reports_reached';

    const ERROR_SMS_MAX_ATTEMPTS_MESSAGE = 'You have hit the limit for SMS codes. Try again in [MINUTES] minutes';

    const ERROR_SMS_MAX_ATTEMPTS_CODE = 'error_max_sms_attempts';

    const ERROR_USER_BLOCKED_AFTER_SMS_ATTEMPTS_MESSAGE = 'Your phone number was blocked doe to security reasons. Please try again next time.';

    const ERROR_USER_BLOCKED_AFTER_SMS_ATTEMPTS_CODE = 'error_user_blocker_after_sms_attempts';

    const ERROR_CREATE_PET_MESSAGE = 'You can have only one pet';

    const ERROR_CREATE_PET_CODE = 'error_one_pet_creation';

    const ERROR_MAX_IMAGES_PER_GALLERY_MESSAGE = 'You have exceded the maximum uploads of pet images.';

    const ERROR_MAX_IMAGES_PER_GALLERY_CODE = 'error_max_pet_gallery_images_reached';

    const ERROR_MISSING_PET_MESSAGE = 'This pet is already missing';

    const ERROR_MISSING_PET_CODE = 'error_pet_missing';

    const ERROR_NOT_MISSING_PET_MESSAGE = 'This pet is not missing';

    const ERROR_NOT_MISSING_PET_CODE = 'error_pet_not_missing';

    const ERROR_DUPLICATE_REPORT_MESSAGE = 'You already report a pet from this location';

    const ERROR_DUPLICATE_REPORT_CODE = 'report_pet_same_location';

    const ERROR_NOT_MISSING_PET_FROM_USER_MESSAGE = 'This user have no missing pets';

    const ERROR_NOT_MISSING_PET_FROM_USER_CODE = 'error_user_have_no_missing_pet';

    const ERROR_PHONE_NUMBER_MESSAGE = 'Wrong format phone number';

    const ERROR_PHONE_NUMBER_CODE = 'error_wrong_phone_number';

    const ERROR_SMS_EXPIRED_MESSAGE = 'Your sms code was expired';

    const ERROR_SMS_EXPIRED_CODE = 'error_expired_sms_code';

    const ERROR_TRACKED_REPORTED_PET_ALREADY_MARKED_MESSAGE = 'You cannot marked this tracked reported pet twice';

    const ERROR_TRACKED_REPORTED_PET_TO_THE_SAME_USER_MESSAGE = 'You cannot marked your own tracked reported pet';

    const ERROR_TRACKED_REPORTED_PET_WITHOUT_MISSING_PET_MESSAGE = 'There are no missing pet';

    const ERROR_TRACKED_REPORTED_PET_WITH_DIFFERENT_CATEGORY_MESSAGE = 'Pet category is different from reported pet';

    const ERROR_CHAT_INACTIVE_MESSAGE = 'This chat is not active. You are not allow to perform this action';

    const ERROR_UNAUTHORIZED_MESSAGE = 'This action is unauthorized.';

    public function __construct() {}
}
