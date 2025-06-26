<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\AppSetting
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting whereValue($value)
 */
	class AppSetting extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * App\Models\AttendanceReportDueDates
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportDueDates newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportDueDates newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportDueDates query()
 */
	class AttendanceReportDueDates extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\AccountingLedgerBook
 *
 * @property int $id
 * @property string $team_id
 * @property string $company_id
 * @property string $trainee_id
 * @property string|null $invoice_id
 * @property string|null $trainee_bank_payment_receipt_id
 * @property string $date
 * @property string $description
 * @property string|null $reference
 * @property string $account_name
 * @property string $debit
 * @property string $credit
 * @property string $balance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|AccountingLedgerBook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountingLedgerBook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountingLedgerBook query()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountingLedgerBook whereAccountName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountingLedgerBook whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountingLedgerBook whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountingLedgerBook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountingLedgerBook whereCredit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountingLedgerBook whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountingLedgerBook whereDebit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountingLedgerBook whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountingLedgerBook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountingLedgerBook whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountingLedgerBook whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountingLedgerBook whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountingLedgerBook whereTraineeBankPaymentReceiptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountingLedgerBook whereTraineeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountingLedgerBook whereUpdatedAt($value)
 */
	class AccountingLedgerBook extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\AttendanceReport
 *
 * @property string $id
 * @property string $team_id
 * @property string $course_batch_session_id
 * @property int|null $is_ready_for_review
 * @property int $status
 * @property string|null $submitted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $job_started_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\AttendanceReportRecord> $attendances
 * @property-read int|null $attendances_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Back\CourseBatchSession $course_batch_session
 * @property-read mixed $can_prepare_attendance
 * @property-read mixed $status_name
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReport whereCourseBatchSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReport whereIsReadyForReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReport whereJobStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReport whereSubmittedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReport whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReport whereUpdatedAt($value)
 */
	class AttendanceReport extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\AttendanceReportRecord
 *
 * @property string $id
 * @property string $team_id
 * @property string $attendance_report_id
 * @property string $course_id
 * @property string $course_batch_id
 * @property string $course_batch_session_id
 * @property \Illuminate\Support\Carbon $session_starts_at
 * @property string $session_ends_at
 * @property string $instructor_id
 * @property string $trainee_id
 * @property string|null $trainee_user_id
 * @property \Illuminate\Support\Carbon|null $attended_at
 * @property int $status
 * @property string|null $absence_reason
 * @property string|null $last_login_at
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Back\Course $course
 * @property-read \App\Models\Back\CourseBatchSession $course_batch_session
 * @property-read mixed $attended_at_timezone
 * @property-read mixed $session_starts_at_timezone
 * @property-read string $status_color
 * @property-read string $status_name
 * @property-read \App\Models\Back\Trainee $trainee
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\AttendanceReportRecordWarning> $warnings
 * @property-read int|null $warnings_count
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord whereAbsenceReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord whereAttendanceReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord whereAttendedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord whereCourseBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord whereCourseBatchSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord whereInstructorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord whereSessionEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord whereSessionStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord whereTraineeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord whereTraineeUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecord whereUpdatedAt($value)
 */
	class AttendanceReportRecord extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\AttendanceReportRecordWarning
 *
 * @property string $id
 * @property string|null $team_id
 * @property string $attendance_report_id
 * @property string $attendance_report_record_id
 * @property string $trainee_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Back\AttendanceReportRecord $attendance_report_record
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read mixed $created_at_timezone
 * @property-read \App\Models\Back\Trainee $trainee
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecordWarning newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecordWarning newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecordWarning query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecordWarning whereAttendanceReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecordWarning whereAttendanceReportRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecordWarning whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecordWarning whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecordWarning whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecordWarning whereTraineeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReportRecordWarning whereUpdatedAt($value)
 */
	class AttendanceReportRecordWarning extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\Audit
 *
 * @property string $id
 * @property string|null $team_id
 * @property string|null $user_type
 * @property string|null $user_id
 * @property string $event
 * @property string $auditable_type
 * @property string $auditable_id
 * @property array|null $old_values
 * @property array|null $new_values
 * @property string|null $url
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $tags
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $auditable
 * @property-read mixed $created_at_human
 * @property-read string|void $created_at_timezone
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $user
 * @method static \Illuminate\Database\Eloquent\Builder|Audit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Audit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Audit query()
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereAuditableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereAuditableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereNewValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereOldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereUserType($value)
 */
	class Audit extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\Center
 *
 * @property int $id
 * @property string $name
 * @property string|null $name_ar
 * @property string $domain_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Company> $companies
 * @property-read int|null $companies_count
 * @method static \Illuminate\Database\Eloquent\Builder|Center newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Center newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Center query()
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereDomainName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereNameAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereUpdatedAt($value)
 */
	class Center extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\CertificatesImport
 *
 * @property int $id
 * @property string $course_id
 * @property string $status
 * @property int $processed_count
 * @property int $total_count
 * @property string|null $started_at
 * @property string|null $completed_at
 * @property string $imported_by_id
 * @property string $filepath
 * @property array|null $failed_rows
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $can_issue
 * @property-read mixed $status_text
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\CertificatesImportsRow> $rows
 * @property-read int|null $rows_count
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImport query()
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImport whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImport whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImport whereFailedRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImport whereFilepath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImport whereImportedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImport whereProcessedCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImport whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImport whereTotalCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImport whereUpdatedAt($value)
 */
	class CertificatesImport extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\CertificatesImportsRow
 *
 * @property int $id
 * @property int $certificates_import_id
 * @property string $trainee_id
 * @property string $course_id
 * @property string|null $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Back\Trainee $trainee
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImportsRow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImportsRow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImportsRow query()
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImportsRow whereCertificatesImportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImportsRow whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImportsRow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImportsRow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImportsRow whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImportsRow whereTraineeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CertificatesImportsRow whereUpdatedAt($value)
 */
	class CertificatesImportsRow extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\Company
 *
 * @property string $id
 * @property string|null $code
 * @property string $name_ar
 * @property string|null $name_en
 * @property string|null $cr_number
 * @property string|null $contact_number
 * @property string|null $company_rep
 * @property string|null $company_rep_mobile
 * @property string|null $address
 * @property string|null $email
 * @property string $team_id
 * @property int|null $monthly_subscription_per_trainee
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $shelf_number
 * @property int|null $entity_id
 * @property string|null $is_ptc_net
 * @property int|null $region_id
 * @property string|null $salesperson_email
 * @property string|null $salesperson_name
 * @property int|null $center_id
 * @property string|null $recruitment_company_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\CompanyAlias> $aliases
 * @property-read int|null $aliases_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $allowed_users
 * @property-read int|null $allowed_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Back\Center|null $center
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\CompanyAttendanceReport> $company_attendance_reports
 * @property-read int|null $company_attendance_reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\CompanyMail> $company_mails
 * @property-read int|null $company_mails_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\CompanyContract> $contracts
 * @property-read int|null $contracts_count
 * @property-read string|void $created_at_timezone
 * @property-read string $resource_label
 * @property-read string $resource_type
 * @property-read \route $show_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Invoice> $invoices
 * @property-read int|null $invoices_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Back\RecruitmentCompany|null $recruitmentCompany
 * @property-read \App\Models\Back\Region|null $region
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Resignation> $resignations
 * @property-read int|null $resignations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Trainee> $trainees
 * @property-read int|null $trainees_count
 * @method static \Database\Factories\Back\CompanyFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Company query()
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCompanyRep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCompanyRepMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereContactNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCrNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereIsPtcNet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereMonthlySubscriptionPerTrainee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereNameAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereRecruitmentCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereSalespersonEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereSalespersonName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereShelfNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Company withoutTrashed()
 */
	class Company extends \Eloquent implements \App\Models\SearchableLabels, \OwenIt\Auditing\Contracts\Auditable, \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\CompanyAlias
 *
 * @property int $id
 * @property string $company_id
 * @property string|null $alias
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAlias newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAlias newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAlias query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAlias whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAlias whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAlias whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAlias whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAlias whereUpdatedAt($value)
 */
	class CompanyAlias extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\CompanyAttendanceReport
 *
 * @property int $id
 * @property string $company_id
 * @property string $number
 * @property \Illuminate\Support\Carbon|null $date_from
 * @property \Illuminate\Support\Carbon|null $date_to
 * @property int $status
 * @property string|null $to_emails
 * @property string|null $cc_emails
 * @property int $with_attendance_times
 * @property int $with_logo
 * @property string|null $created_by_id
 * @property string|null $approved_by_id
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $approved_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Back\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\CompanyAttendanceReportsTrainee> $company_attendance_reports_trainee
 * @property-read int|null $company_attendance_reports_trainee_count
 * @property-read \App\Models\User|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\CompanyAttendanceReportsEmail> $emails
 * @property-read int|null $emails_count
 * @property-read mixed $approved_at_human
 * @property-read mixed $falls_under_ptc_net
 * @property-read mixed $period
 * @property-read mixed $updated_at_human
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Trainee> $trainees
 * @property-read int|null $trainees_count
 * @method static \Database\Factories\Back\CompanyAttendanceReportFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport whereApprovedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport whereCcEmails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport whereDateFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport whereDateTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport whereToEmails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport whereWithAttendanceTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport whereWithLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReport withoutTrashed()
 */
	class CompanyAttendanceReport extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\CompanyAttendanceReportsEmail
 *
 * @property int $id
 * @property int $company_attendance_report_id
 * @property string $email
 * @property string $type to, cc, bcc
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property string|null $clicked_confirmed_at
 * @property \Illuminate\Support\Carbon|null $opened_at
 * @property \Illuminate\Support\Carbon|null $failed_at
 * @property string|null $failed_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|void $delivered_at_timezone
 * @property-read string|void $failed_at_timezone
 * @property-read \App\Models\Back\CompanyAttendanceReport $report
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsEmail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsEmail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsEmail query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsEmail whereClickedConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsEmail whereCompanyAttendanceReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsEmail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsEmail whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsEmail whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsEmail whereFailedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsEmail whereFailedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsEmail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsEmail whereOpenedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsEmail whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsEmail whereUpdatedAt($value)
 */
	class CompanyAttendanceReportsEmail extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\CompanyAttendanceReportsTrainee
 *
 * @property int $id
 * @property int $company_attendance_report_id
 * @property string $trainee_id
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $status
 * @property string|null $comment
 * @property-read \App\Models\Back\CompanyAttendanceReport|null $company_attendance_reports
 * @property-read \App\Models\Back\CompanyAttendanceReport $report
 * @property-read \App\Models\Back\Trainee $trainee
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsTrainee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsTrainee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsTrainee query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsTrainee whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsTrainee whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsTrainee whereCompanyAttendanceReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsTrainee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsTrainee whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsTrainee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsTrainee whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsTrainee whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsTrainee whereTraineeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAttendanceReportsTrainee whereUpdatedAt($value)
 */
	class CompanyAttendanceReportsTrainee extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\CompanyContract
 *
 * @property string $id
 * @property string $team_id
 * @property string $company_id
 * @property string|null $reference_number
 * @property \Illuminate\Support\Carbon $contract_starts_at
 * @property \Illuminate\Support\Carbon|null $contract_ends_at
 * @property int|null $contract_period_in_months
 * @property bool $auto_renewal
 * @property int|null $trainees_count
 * @property int|null $trainee_salary
 * @property int|null $instructor_cost
 * @property int|null $company_reimbursement
 * @property string|null $notes
 * @property string|null $created_by_id
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Back\Company $company
 * @property-read mixed $contract_ends_at_timezone
 * @property-read mixed $contract_starts_at_timezone
 * @property-read mixed $has_attachments
 * @property-read string $resource_label
 * @property-read string $resource_type
 * @property-read string $show_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Instructor> $instructors
 * @property-read int|null $instructors_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Database\Factories\Back\CompanyContractFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract whereAutoRenewal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract whereCompanyReimbursement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract whereContractEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract whereContractPeriodInMonths($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract whereContractStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract whereInstructorCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract whereTraineeSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract whereTraineesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyContract whereUpdatedAt($value)
 */
	class CompanyContract extends \Eloquent implements \Spatie\MediaLibrary\HasMedia, \App\Models\SearchableLabels, \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\CompanyMail
 *
 * @property string $id
 * @property string $company_id
 * @property string $from
 * @property string|null $subject
 * @property string $sender
 * @property string $body_text
 * @property string $body_html
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Back\Company $company
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyMail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyMail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyMail query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyMail whereBodyHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyMail whereBodyText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyMail whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyMail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyMail whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyMail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyMail whereSender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyMail whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyMail whereUpdatedAt($value)
 */
	class CompanyMail extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable, \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\CompanyTraineeLinkAudit
 *
 * @property int $id
 * @property string $trainee_id
 * @property string $company_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Back\Company $company
 * @property-read \App\Models\Back\Trainee $trainee
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyTraineeLinkAudit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyTraineeLinkAudit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyTraineeLinkAudit query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyTraineeLinkAudit whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyTraineeLinkAudit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyTraineeLinkAudit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyTraineeLinkAudit whereTraineeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyTraineeLinkAudit whereUpdatedAt($value)
 */
	class CompanyTraineeLinkAudit extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\ComplaintsSettings
 *
 * @property int $id
 * @property string $team_id
 * @property int $enabled
 * @property array|null $emails
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ComplaintsSettings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComplaintsSettings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComplaintsSettings query()
 * @method static \Illuminate\Database\Eloquent\Builder|ComplaintsSettings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComplaintsSettings whereEmails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComplaintsSettings whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComplaintsSettings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComplaintsSettings whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComplaintsSettings whereUpdatedAt($value)
 */
	class ComplaintsSettings extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\Course
 *
 * @property string $id
 * @property string $team_id
 * @property string|null $instructor_id
 * @property string $name_ar
 * @property string $name_en
 * @property int|null $classroom_count
 * @property string|null $description
 * @property int $sharable
 * @property string|null $approval_code Approved by a regularity
 * @property int|null $days_duration
 * @property int|null $hours_duration
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\CourseBatch> $batches
 * @property-read int|null $batches_count
 * @property-read mixed $can_show_certificate
 * @property-read mixed $closest_course_batch
 * @property-read mixed $is_approved
 * @property-read mixed $is_pending_approval
 * @property-read mixed $my_attendance
 * @property-read string $resource_label
 * @property-read string $resource_type
 * @property-read string $show_url
 * @property-read mixed $training_package_url
 * @property-read \App\Models\Back\Instructor|null $instructor
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder|Course attending()
 * @method static \Database\Factories\Back\CourseFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Course query()
 * @method static \Illuminate\Database\Eloquent\Builder|Course responsibleToTeach()
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereApprovalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereClassroomCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereDaysDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereHoursDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereInstructorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereNameAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereSharable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Course withoutTrashed()
 */
	class Course extends \Eloquent implements \Spatie\MediaLibrary\HasMedia, \App\Models\SearchableLabels, \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\CourseBatch
 *
 * @property string $id
 * @property string $team_id
 * @property string|null $trainee_group_id
 * @property string|null $course_id
 * @property \Illuminate\Support\Carbon|null $starts_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property string $location_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Back\Course|null $course
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\CourseBatchSession> $course_batch_sessions
 * @property-read int|null $course_batch_sessions_count
 * @property-read mixed $ends_at_timezone
 * @property-read mixed $location_at_display
 * @property-read mixed $starts_at_timezone
 * @property-read \App\Models\Back\TraineeGroup|null $trainee_group
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Trainee> $trainees
 * @property-read int|null $trainees_count
 * @method static \Database\Factories\Back\CourseBatchFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatch query()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatch whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatch whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatch whereLocationAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatch whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatch whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatch whereTraineeGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatch whereUpdatedAt($value)
 */
	class CourseBatch extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\CourseBatchSession
 *
 * @property string $id
 * @property string $team_id
 * @property string $course_id
 * @property string $course_batch_id
 * @property \Illuminate\Support\Carbon|null $starts_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property string|null $zoom_link
 * @property string|null $zoom_meeting_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $start_url
 * @property string|null $join_url
 * @property int $sent_warnings_at
 * @property string|null $committed_attendances_at
 * @property \Illuminate\Support\Carbon|null $instructor_started_at
 * @property-read \App\Models\Back\AttendanceReport|null $attendance_report
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\AttendanceReportRecord> $attendance_snapshots
 * @property-read int|null $attendance_snapshots_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\CourseBatchSessionAttendance> $attendances
 * @property-read int|null $attendances_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Back\Course $course
 * @property-read \App\Models\Back\CourseBatch $course_batch
 * @property-read mixed $can_be_deleted
 * @property-read mixed $can_join
 * @property-read mixed $ends_at_timezone
 * @property-read mixed $instructor_started_at_timezone
 * @property-read mixed $starts_at_timezone
 * @property-read \App\Models\Back\Trainee $trainee
 * @property-read \App\Models\Back\TraineeGroup $trainee_group
 * @method static \Database\Factories\Back\CourseBatchSessionFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSession query()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSession whereCommittedAttendancesAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSession whereCourseBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSession whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSession whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSession whereInstructorStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSession whereJoinUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSession whereSentWarningsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSession whereStartUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSession whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSession whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSession whereZoomLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSession whereZoomMeetingId($value)
 */
	class CourseBatchSession extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\CourseBatchSessionAttendance
 *
 * @property string $id
 * @property string $course_batch_session_id
 * @property string $course_batch_id
 * @property string $course_id
 * @property string $team_id
 * @property string $trainee_id
 * @property string|null $trainee_user_id
 * @property string $session_starts_at
 * @property string $session_ends_at
 * @property \Illuminate\Support\Carbon|null $attended_at
 * @property int|null $attended
 * @property int $physical_attendance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $absence_reason
 * @property int|null $status
 * @property string|null $last_login_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Back\CourseBatchSession $course_batch_session
 * @property-read mixed $attendance_status
 * @property-read mixed $attended_at_timezone
 * @property-read \App\Models\Back\Trainee $trainee
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance query()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance whereAbsenceReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance whereAttended($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance whereAttendedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance whereCourseBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance whereCourseBatchSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance wherePhysicalAttendance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance whereSessionEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance whereSessionStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance whereTraineeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance whereTraineeUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseBatchSessionAttendance whereUpdatedAt($value)
 */
	class CourseBatchSessionAttendance extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\Entity
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Entity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Entity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Entity query()
 * @method static \Illuminate\Database\Eloquent\Builder|Entity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Entity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Entity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Entity whereUpdatedAt($value)
 */
	class Entity extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\ExportTraineesToExcelJobTracker
 *
 * @property string $id
 * @property int|null $trainee_status_id
 * @property string|null $queued_at
 * @property string|null $started_at
 * @property string|null $finished_at
 * @property string|null $failure_reason
 * @property string|null $user_id
 * @property string|null $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ExportTraineesToExcelJobTracker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExportTraineesToExcelJobTracker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExportTraineesToExcelJobTracker query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExportTraineesToExcelJobTracker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportTraineesToExcelJobTracker whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportTraineesToExcelJobTracker whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportTraineesToExcelJobTracker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportTraineesToExcelJobTracker whereQueuedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportTraineesToExcelJobTracker whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportTraineesToExcelJobTracker whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportTraineesToExcelJobTracker whereTraineeStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportTraineesToExcelJobTracker whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportTraineesToExcelJobTracker whereUserId($value)
 */
	class ExportTraineesToExcelJobTracker extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\FinancialAccount
 *
 * @property string $id
 * @property string $reference_number
 * @property string $team_id
 * @property int $created_by_system
 * @property string|null $created_by_id
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialAccount whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialAccount whereCreatedBySystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialAccount whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialAccount whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialAccount whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialAccount whereUpdatedAt($value)
 */
	class FinancialAccount extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\FinancialInvoice
 *
 * @property int $id
 * @property string $team_id
 * @property string $financial_account_id
 * @property string $reference_number
 * @property string|null $issued_at
 * @property int $created_by_system
 * @property string|null $created_by_id
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoice whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoice whereCreatedBySystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoice whereFinancialAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoice whereIssuedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoice whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoice whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoice whereUpdatedAt($value)
 */
	class FinancialInvoice extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\FinancialInvoiceLine
 *
 * @property int $id
 * @property string $team_id
 * @property string $financial_invoice_id
 * @property string $description
 * @property int $unit_price
 * @property int $qty
 * @property int $subtotal
 * @property int $tax
 * @property int $grand_total
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoiceLine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoiceLine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoiceLine query()
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoiceLine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoiceLine whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoiceLine whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoiceLine whereFinancialInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoiceLine whereGrandTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoiceLine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoiceLine whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoiceLine whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoiceLine whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoiceLine whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoiceLine whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialInvoiceLine whereUpdatedAt($value)
 */
	class FinancialInvoiceLine extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\FinancialSetting
 *
 * @property string $id
 * @property string $team_id
 * @property int $trainee_monthly_subscription
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialSetting whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialSetting whereTraineeMonthlySubscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinancialSetting whereUpdatedAt($value)
 */
	class FinancialSetting extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\GlobalMessages
 *
 * @property int $id
 * @property string|null $company_id
 * @property string $body
 * @property \Illuminate\Support\Carbon|null $starts_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property string $created_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Back\Company|null $company
 * @property-read string|void $expires_at_timezone
 * @property-read string|void $starts_at_timezone
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalMessages available()
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalMessages newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalMessages newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalMessages query()
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalMessages whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalMessages whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalMessages whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalMessages whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalMessages whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalMessages whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalMessages whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalMessages whereUpdatedAt($value)
 */
	class GlobalMessages extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\GosiData
 *
 * @method static \Illuminate\Database\Eloquent\Builder|GosiData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GosiData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GosiData query()
 */
	class GosiData extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\Instructor
 *
 * @property string $id
 * @property string $team_id
 * @property string|null $reference_number
 * @property string $name
 * @property string|null $identity_number
 * @property string $phone
 * @property string $email
 * @property string|null $twitter_link
 * @property string|null $city_id
 * @property string|null $user_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $provided_courses
 * @property string|null $approved_by_id
 * @property string|null $approved_at
 * @property int|null $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Back\Company $company
 * @property-read mixed $cv_full_copy_url
 * @property-read mixed $cv_summary_copy_url
 * @property-read mixed $is_approved
 * @property-read mixed $is_pending_approval
 * @property-read bool $is_pending_uploading_files
 * @property-read string $resource_label
 * @property-read string $resource_type
 * @property-read \route $show_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InboxMessage> $inbox_message_from
 * @property-read int|null $inbox_message_from_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InboxMessage> $inbox_message_to
 * @property-read int|null $inbox_message_to_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Trainee> $trainees
 * @property-read int|null $trainees_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Trainee> $trainees_contract
 * @property-read int|null $trainees_contract_count
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\Back\ZoomAccount|null $zoom_account
 * @method static \Database\Factories\Back\InstructorFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor query()
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor whereApprovedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor whereIdentityNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor whereProvidedCourses($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor whereTwitterLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Instructor withoutTrashed()
 */
	class Instructor extends \Eloquent implements \Spatie\MediaLibrary\HasMedia, \App\Models\SearchableLabels, \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\Invite
 *
 * @property string $id
 * @property string $team_id
 * @property string $role_id
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|Invite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invite query()
 * @method static \Illuminate\Database\Eloquent\Builder|Invite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invite whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invite whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invite wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invite whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invite whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invite whereUpdatedAt($value)
 */
	class Invite extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\Invoice
 *
 * @property string $id
 * @property string|null $trainee_id
 * @property string|null $company_id
 * @property int|null $entity_id
 * @property string|null $created_by_id
 * @property string $number
 * @property \Illuminate\Support\Carbon $from_date
 * @property \Illuminate\Support\Carbon $to_date
 * @property string $sub_total
 * @property string $tax
 * @property string $grand_total
 * @property int $status
 * @property string|null $rejection_reason_payment_receipt
 * @property int|null $payment_method
 * @property string|null $payment_reference_id
 * @property string|null $under_review_reason
 * @property string|null $payment_detail_brand
 * @property string|null $payment_detail_method
 * @property string|null $trainee_bank_payment_receipt_id
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $verified_by_id
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property \Illuminate\Support\Carbon|null $chased_at
 * @property string|null $chased_by_id
 * @property string|null $chased_note
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $objection_of_amount
 * @property string|null $edit_amount_reason
 * @property string|null $deleted_reason
 * @property int|null $center_id
 * @property string|null $pending_payroll
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $chased_by
 * @property-read \App\Models\Back\Company|null $company
 * @property-read \App\Models\User|null $created_by
 * @property-read mixed $can_upload_receipt
 * @property-read mixed $chase_boolean
 * @property-read string $chase_status
 * @property-read string $created_at_date
 * @property-read bool $is_paid
 * @property-read mixed $is_verified
 * @property-read mixed $month_of
 * @property-read mixed $noon_link
 * @property-read string $number_formatted
 * @property-read mixed $paid_at_time
 * @property-read mixed $payment_method_formatted
 * @property-read string $status_formatted
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\InvoiceItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Back\Trainee|null $trainee
 * @property-read \App\Models\TraineeBankPaymentReceipt|null $trainee_bank_payment_receipt
 * @property-read \App\Models\User|null $verified_by
 * @method static \Database\Factories\Back\InvoiceFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice isPaid(bool $is_paid)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice notPaid()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice paid()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereChasedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereChasedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereChasedNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereDeletedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereEditAmountReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereFromDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereGrandTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereObjectionOfAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice wherePaymentDetailBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice wherePaymentDetailMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice wherePaymentReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice wherePendingPayroll($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereRejectionReasonPaymentReceipt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereSubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereToDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereTraineeBankPaymentReceiptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereTraineeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereUnderReviewReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereVerifiedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice withoutTrashed()
 */
	class Invoice extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\InvoiceItem
 *
 * @property string $id
 * @property string $invoice_id
 * @property string $name_en
 * @property string $name_ar
 * @property int $quantity
 * @property float $sub_total
 * @property float $tax
 * @property float $grand_total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereGrandTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereNameAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereSubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereUpdatedAt($value)
 */
	class InvoiceItem extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\MaxNumber
 *
 * @property string $id
 * @property string $team_id
 * @property string $name
 * @property int $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MaxNumber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MaxNumber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MaxNumber query()
 * @method static \Illuminate\Database\Eloquent\Builder|MaxNumber whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaxNumber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaxNumber whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaxNumber whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaxNumber whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaxNumber whereValue($value)
 */
	class MaxNumber extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\MissedCourseNotice
 *
 * @property int $id
 * @property string $team_id
 * @property string $trainee_id
 * @property string $course_batch_session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MissedCourseNotice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MissedCourseNotice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MissedCourseNotice query()
 * @method static \Illuminate\Database\Eloquent\Builder|MissedCourseNotice whereCourseBatchSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MissedCourseNotice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MissedCourseNotice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MissedCourseNotice whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MissedCourseNotice whereTraineeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MissedCourseNotice whereUpdatedAt($value)
 */
	class MissedCourseNotice extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\RecruitmentCompany
 *
 * @property string $id
 * @property string $name
 * @property string $name_en
 * @property string $created_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Company> $companies
 * @property-read int|null $companies_count
 * @property-read \App\Models\User $createdBy
 * @method static \Illuminate\Database\Eloquent\Builder|RecruitmentCompany newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RecruitmentCompany newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RecruitmentCompany onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RecruitmentCompany query()
 * @method static \Illuminate\Database\Eloquent\Builder|RecruitmentCompany whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecruitmentCompany whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecruitmentCompany whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecruitmentCompany whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecruitmentCompany whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecruitmentCompany whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecruitmentCompany whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecruitmentCompany withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RecruitmentCompany withoutTrashed()
 */
	class RecruitmentCompany extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\Region
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Company> $companies
 * @property-read int|null $companies_count
 * @method static \Illuminate\Database\Eloquent\Builder|Region newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Region newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Region query()
 * @method static \Illuminate\Database\Eloquent\Builder|Region whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Region whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Region whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Region whereUpdatedAt($value)
 */
	class Region extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\RequiredTraineesFiles
 *
 * @property string $id
 * @property string $team_id
 * @property string $name_en
 * @property string $name_ar
 * @property int $required
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredTraineesFiles newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredTraineesFiles newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredTraineesFiles query()
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredTraineesFiles whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredTraineesFiles whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredTraineesFiles whereNameAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredTraineesFiles whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredTraineesFiles whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredTraineesFiles whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredTraineesFiles whereUpdatedAt($value)
 */
	class RequiredTraineesFiles extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\Resignation
 *
 * @property string $id
 * @property int|null $number
 * @property string $team_id
 * @property string $created_by_id
 * @property string $company_id
 * @property string $status
 * @property string $date
 * @property string $reason
 * @property string $emails_to
 * @property string|null $emails_cc
 * @property string|null $emails_bcc
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property string|null $received_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $sent_tries
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Back\Company $company
 * @property-read \App\Models\User $created_by
 * @property-read string|void $created_at_timezone
 * @property-read mixed $has_file
 * @property-read string|void $sent_at_timezone
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Trainee> $trainees
 * @property-read int|null $trainees_count
 * @method static \Illuminate\Database\Eloquent\Builder|Resignation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Resignation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Resignation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Resignation whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resignation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resignation whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resignation whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resignation whereEmailsBcc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resignation whereEmailsCc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resignation whereEmailsTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resignation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resignation whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resignation whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resignation whereReceivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resignation whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resignation whereSentTries($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resignation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resignation whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resignation whereUpdatedAt($value)
 */
	class Resignation extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable, \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\ResignationTrainee
 *
 * @property string $id
 * @property string $team_id
 * @property string $resignation_id
 * @property string $trainee_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|ResignationTrainee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ResignationTrainee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ResignationTrainee query()
 * @method static \Illuminate\Database\Eloquent\Builder|ResignationTrainee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResignationTrainee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResignationTrainee whereResignationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResignationTrainee whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResignationTrainee whereTraineeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResignationTrainee whereUpdatedAt($value)
 */
	class ResignationTrainee extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\SubscriptionPlan
 *
 * @property string $id
 * @property string $team_id
 * @property string $billing_interval
 * @property string $currency
 * @property int $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan whereBillingInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan whereUpdatedAt($value)
 */
	class SubscriptionPlan extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\SurveyLink
 *
 * @property int $id
 * @property string $team_id
 * @property string $type
 * @property string $url
 * @property string|null $created_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyLink query()
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyLink whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyLink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyLink whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyLink whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyLink whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyLink whereUrl($value)
 */
	class SurveyLink extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\Trainee
 *
 * @property string $id
 * @property string|null $zoho_contract_id
 * @property string $zoho_contract_status
 * @property string|null $zoho_sign_date
 * @property int $must_sign
 * @property int|null $trainee_agreement_id
 * @property string $team_id
 * @property string|null $user_id
 * @property string $name
 * @property string|null $email
 * @property string|null $identity_number
 * @property string|null $phone
 * @property string|null $phone_ownership_verified_at
 * @property int|null $phone_is_owned
 * @property string|null $phone_additional
 * @property string|null $national_address
 * @property string|null $birthday
 * @property string|null $educational_level_id
 * @property string|null $city_id
 * @property string|null $marital_status_id
 * @property int|null $children_count
 * @property string|null $company_id
 * @property int|null $entity_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $suspended_at
 * @property string|null $gosi_deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $instructor_id
 * @property string|null $trainee_group_id
 * @property int|null $status
 * @property string|null $approved_by_id
 * @property string|null $approved_at
 * @property string|null $deleted_remark
 * @property int $skip_uploading_id
 * @property \Illuminate\Support\Carbon|null $bill_from_date
 * @property \Illuminate\Support\Carbon|null $linked_date
 * @property float|null $override_training_costs
 * @property bool $ignore_attendance
 * @property bool $dont_edit_notice
 * @property string|null $suspended_by_id
 * @property string|null $deleted_by_id
 * @property string|null $posted_at
 * @property string|null $trainee_message
 * @property string|null $job_number
 * @property string|null $english_name
 * @property int $contract_signed_notification_sent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\AttendanceReportRecord> $absences_custom
 * @property-read int|null $absences_custom_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\AttendanceReportRecord> $attendanceReportRecords
 * @property-read int|null $attendance_report_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\CourseBatchSessionAttendance> $attendances
 * @property-read int|null $attendances_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\AttendanceReportRecord> $attendances_7to11
 * @property-read int|null $attendances_7to11_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Back\Company|null $company
 * @property-read \App\Models\User|null $deleted_by
 * @property-read \App\Models\EducationalLevel|null $educational_level
 * @property-read mixed $bank_account_copy_url
 * @property-read mixed $bill_from_date_formatted
 * @property-read mixed $clean_identity_number
 * @property-read mixed $clean_phone_additional
 * @property-read mixed $clean_phone
 * @property-read mixed $company_name
 * @property-read string $created_at_date
 * @property-read string|void $created_at_timezone
 * @property-read mixed $cv_url
 * @property-read string|void $deleted_at_timezone
 * @property-read mixed $has_outstanding_amount
 * @property-read mixed $identity_copy_url
 * @property-read mixed $is_approved
 * @property-read mixed $is_pending_approval
 * @property-read bool $is_pending_uploading_files
 * @property-read mixed $linked_date_formatted
 * @property-read mixed $name_selectable
 * @property-read mixed $national_address_copy_url
 * @property-read mixed $phone_ownership_status
 * @property-read mixed $qualification_copy_url
 * @property-read string $resource_label
 * @property-read string $resource_type
 * @property-read string $show_url
 * @property-read float $total_amount_owed
 * @property-read mixed $trainee_group_object
 * @property-read mixed $whatsapp_link
 * @property-read \App\Models\Back\Instructor|null $instructor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Invoice> $invoices
 * @property-read int|null $invoices_count
 * @property-read \App\Models\MaritalStatus|null $marital_status
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\ResignationTrainee> $resignations
 * @property-read int|null $resignations_count
 * @property-read \App\Models\Team $team
 * @property-read \App\Models\Back\TraineeAgreement|null $traineeAgreement
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\CertificatesImport> $trainee_certificate_imports
 * @property-read int|null $trainee_certificate_imports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\TraineeCertificate> $trainee_certificates
 * @property-read int|null $trainee_certificates_count
 * @property-read \App\Models\Back\TraineeGroup|null $trainee_group
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\AttendanceReportRecordWarning> $warnings
 * @property-read int|null $warnings_count
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee approved()
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee candidates()
 * @method static \Database\Factories\Back\TraineeFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee incomplete()
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee query()
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee responsibleToTeach()
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereApprovedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereBillFromDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereChildrenCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereContractSignedNotificationSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereDeletedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereDeletedRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereDontEditNotice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereEducationalLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereEnglishName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereGosiDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereIdentityNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereIgnoreAttendance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereInstructorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereJobNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereLinkedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereMaritalStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereMustSign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereNationalAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereOverrideTrainingCosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee wherePhoneAdditional($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee wherePhoneIsOwned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee wherePhoneOwnershipVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee wherePostedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereSkipUploadingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereSuspendedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereSuspendedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereTraineeAgreementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereTraineeGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereTraineeMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereZohoContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereZohoContractStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee whereZohoSignDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Trainee withoutTrashed()
 */
	class Trainee extends \Eloquent implements \Spatie\MediaLibrary\HasMedia, \App\Models\SearchableLabels, \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\TraineeAbsent
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeAbsent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeAbsent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeAbsent query()
 */
	class TraineeAbsent extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\TraineeAgreement
 *
 * @property int $id
 * @property string $trainee_id
 * @property string|null $rejected_at
 * @property string|null $accepted_at
 * @property string|null $otp_verified_at
 * @property string|null $otp
 * @property string|null $verified_mobile_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Back\Trainee $trainee
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeAgreement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeAgreement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeAgreement query()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeAgreement whereAcceptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeAgreement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeAgreement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeAgreement whereOtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeAgreement whereOtpVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeAgreement whereRejectedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeAgreement whereTraineeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeAgreement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeAgreement whereVerifiedMobileNumber($value)
 */
	class TraineeAgreement extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\TraineeBlockList
 *
 * @property string $id
 * @property string $team_id
 * @property string|null $trainee_id
 * @property string|null $name
 * @property string|null $identity_number
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $phone_additional
 * @property string $reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $english_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Back\Trainee|null $trainee
 * @method static \Database\Factories\Back\TraineeBlockListFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBlockList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBlockList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBlockList query()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBlockList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBlockList whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBlockList whereEnglishName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBlockList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBlockList whereIdentityNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBlockList whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBlockList wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBlockList wherePhoneAdditional($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBlockList whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBlockList whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBlockList whereTraineeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBlockList whereUpdatedAt($value)
 */
	class TraineeBlockList extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\TraineeCertificate
 *
 * @property int $id
 * @property string $trainee_id
 * @property string $course_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Back\Course $course
 * @property-read \App\Models\Back\Trainee $trainee
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCertificate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCertificate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCertificate query()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCertificate whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCertificate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCertificate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCertificate whereTraineeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCertificate whereUpdatedAt($value)
 */
	class TraineeCertificate extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\TraineeCompanyMovement
 *
 * @property int $id
 * @property string $company_id
 * @property string $trainee_id
 * @property string|null $trainee_name
 * @property string|null $trainee_identity_number
 * @property string|null $trainee_phone_number
 * @property \Illuminate\Support\Carbon|null $in_date
 * @property \Illuminate\Support\Carbon|null $out_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Back\Company $company
 * @property-read mixed $in_date_ksa
 * @property-read mixed $out_date_ksa
 * @property-read \App\Models\Back\Trainee $trainee
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCompanyMovement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCompanyMovement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCompanyMovement query()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCompanyMovement whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCompanyMovement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCompanyMovement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCompanyMovement whereInDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCompanyMovement whereOutDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCompanyMovement whereTraineeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCompanyMovement whereTraineeIdentityNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCompanyMovement whereTraineeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCompanyMovement whereTraineePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeCompanyMovement whereUpdatedAt($value)
 */
	class TraineeCompanyMovement extends \Eloquent {}
}

namespace App\Models\Back{
/**
 * This is a class stripped out of relationships for speed optimization.
 *
 * @property string $id
 * @property string|null $zoho_contract_id
 * @property string $zoho_contract_status
 * @property string|null $zoho_sign_date
 * @property int $must_sign
 * @property int|null $trainee_agreement_id
 * @property string $team_id
 * @property string|null $user_id
 * @property string $name
 * @property string|null $email
 * @property string|null $identity_number
 * @property string|null $phone
 * @property string|null $phone_ownership_verified_at
 * @property int|null $phone_is_owned
 * @property string|null $phone_additional
 * @property string|null $national_address
 * @property string|null $birthday
 * @property string|null $educational_level_id
 * @property string|null $city_id
 * @property string|null $marital_status_id
 * @property int|null $children_count
 * @property string|null $company_id
 * @property int|null $entity_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $suspended_at
 * @property string|null $gosi_deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $instructor_id
 * @property string|null $trainee_group_id
 * @property int|null $status
 * @property string|null $approved_by_id
 * @property string|null $approved_at
 * @property string|null $deleted_remark
 * @property int $skip_uploading_id
 * @property string|null $bill_from_date
 * @property string|null $linked_date
 * @property float|null $override_training_costs
 * @property int $ignore_attendance
 * @property int $dont_edit_notice
 * @property string|null $suspended_by_id
 * @property string|null $deleted_by_id
 * @property string|null $posted_at
 * @property string|null $trainee_message
 * @property string|null $job_number
 * @property string|null $english_name
 * @property int $contract_signed_notification_sent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Back\Company|null $company
 * @property-read mixed $trainee_group_object
 * @property-read \App\Models\Back\TraineeGroup|null $trainee_group
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract query()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereApprovedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereBillFromDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereChildrenCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereContractSignedNotificationSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereDeletedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereDeletedRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereDontEditNotice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereEducationalLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereEnglishName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereGosiDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereIdentityNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereIgnoreAttendance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereInstructorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereJobNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereLinkedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereMaritalStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereMustSign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereNationalAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereOverrideTrainingCosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract wherePhoneAdditional($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract wherePhoneIsOwned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract wherePhoneOwnershipVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract wherePostedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereSkipUploadingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereSuspendedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereSuspendedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereTraineeAgreementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereTraineeGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereTraineeMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereZohoContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereZohoContractStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract whereZohoSignDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeContract withoutTrashed()
 */
	class TraineeContract extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\TraineeGroup
 *
 * @property string $id
 * @property string $team_id
 * @property string|null $company_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read mixed $class_timings
 * @property-read mixed $name_selectable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Trainee> $trainees
 * @property-read int|null $trainees_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Trainee> $traineesWithTrashed
 * @property-read int|null $trainees_with_trashed_count
 * @method static \Database\Factories\Back\TraineeGroupFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeGroup whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeGroup whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeGroup whereUpdatedAt($value)
 */
	class TraineeGroup extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Back{
/**
 * App\Models\Back\ZoomAccount
 *
 * @property string $id
 * @property string $team_id
 * @property string $ZOOM_CLIENT_KEY
 * @property string $ZOOM_CLIENT_SECRET
 * @property string|null $instructor_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $account_id
 * @property string|null $client_id
 * @property string|null $client_secret
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Back\Instructor|null $instructor
 * @method static \Database\Factories\Back\ZoomAccountFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|ZoomAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ZoomAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ZoomAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|ZoomAccount whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ZoomAccount whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ZoomAccount whereClientSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ZoomAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ZoomAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ZoomAccount whereInstructorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ZoomAccount whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ZoomAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ZoomAccount whereZOOMCLIENTKEY($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ZoomAccount whereZOOMCLIENTSECRET($value)
 */
	class ZoomAccount extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * App\Models\City
 *
 * @property string $id
 * @property string $name
 * @property string|null $name_ar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City query()
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereNameAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereUpdatedAt($value)
 */
	class City extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CompanyAllowedUser
 *
 * @property int $id
 * @property string $company_id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAllowedUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAllowedUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAllowedUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAllowedUser whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAllowedUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAllowedUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAllowedUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyAllowedUser whereUserId($value)
 */
	class CompanyAllowedUser extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Complaint
 *
 * @property int $id
 * @property string $team_id
 * @property string $course_name
 * @property string $course_instructor
 * @property string $message
 * @property string $created_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Complaint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Complaint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Complaint query()
 * @method static \Illuminate\Database\Eloquent\Builder|Complaint whereCourseInstructor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Complaint whereCourseName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Complaint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Complaint whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Complaint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Complaint whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Complaint whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Complaint whereUpdatedAt($value)
 */
	class Complaint extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EducationalLevel
 *
 * @property string $id
 * @property int $order
 * @property string $name_en
 * @property string|null $name_ar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\EducationalLevelFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationalLevel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EducationalLevel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EducationalLevel query()
 * @method static \Illuminate\Database\Eloquent\Builder|EducationalLevel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationalLevel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationalLevel whereNameAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationalLevel whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationalLevel whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationalLevel whereUpdatedAt($value)
 */
	class EducationalLevel extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\GosiEmployeeData
 *
 * @property int $id
 * @property string $nin_or_iqama
 * @property array $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $reason_employment_office
 * @property int $reason_collection
 * @property int $reason_trainee_affairs
 * @property int $reason_sales
 * @property int $reason_other
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|GosiEmployeeData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GosiEmployeeData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GosiEmployeeData query()
 * @method static \Illuminate\Database\Eloquent\Builder|GosiEmployeeData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GosiEmployeeData whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GosiEmployeeData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GosiEmployeeData whereNinOrIqama($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GosiEmployeeData whereReasonCollection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GosiEmployeeData whereReasonEmploymentOffice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GosiEmployeeData whereReasonOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GosiEmployeeData whereReasonSales($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GosiEmployeeData whereReasonTraineeAffairs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GosiEmployeeData whereUpdatedAt($value)
 */
	class GosiEmployeeData extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * App\Models\InboxMessage
 *
 * @property string $id
 * @property string $team_id
 * @property string|null $from_id
 * @property string|null $to_id
 * @property string $body
 * @property string|null $read_at
 * @property int $is_system_message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $from
 * @property-read mixed $is_to_me
 * @property-read \App\Models\User|null $to
 * @method static \Database\Factories\InboxMessageFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|InboxMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InboxMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InboxMessage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InboxMessage owned()
 * @method static \Illuminate\Database\Eloquent\Builder|InboxMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|InboxMessage unread()
 * @method static \Illuminate\Database\Eloquent\Builder|InboxMessage whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboxMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboxMessage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboxMessage whereFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboxMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboxMessage whereIsSystemMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboxMessage whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboxMessage whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboxMessage whereToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboxMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboxMessage withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InboxMessage withoutTrashed()
 */
	class InboxMessage extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * App\Models\JobTracker
 *
 * @property string $id
 * @property string|null $reportable_type
 * @property string|null $reportable_id
 * @property array|null $metadata
 * @property string|null $queued_at
 * @property string|null $started_at
 * @property string|null $finished_at
 * @property string|null $failure_reason
 * @property string|null $user_id
 * @property string|null $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|JobTracker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JobTracker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JobTracker query()
 * @method static \Illuminate\Database\Eloquent\Builder|JobTracker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTracker whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTracker whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTracker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTracker whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTracker whereQueuedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTracker whereReportableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTracker whereReportableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTracker whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTracker whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTracker whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTracker whereUserId($value)
 */
	class JobTracker extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * App\Models\MaritalStatus
 *
 * @property string $id
 * @property int $order
 * @property string $name_en
 * @property string|null $name_ar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\MaritalStatusFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus whereNameAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus whereUpdatedAt($value)
 */
	class MaritalStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Media
 *
 * @property string $id
 * @property string $model_type
 * @property string $model_id
 * @property string|null $uuid
 * @property string $collection_name
 * @property string $name
 * @property string $file_name
 * @property string|null $mime_type
 * @property string $disk
 * @property string|null $conversions_disk
 * @property int $size
 * @property array $manipulations
 * @property array $custom_properties
 * @property array $responsive_images
 * @property int|null $order_column
 * @property string $team_id
 * @property string|null $user_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $created_at_timezone
 * @property-read mixed $download_url
 * @property-read string $extension
 * @property-read string $human_readable_size
 * @property-read string $type
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $model
 * @method static \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, static> all($columns = ['*'])
 * @method static \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, static> get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Media onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Media ordered()
 * @method static \Illuminate\Database\Eloquent\Builder|Media query()
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereCollectionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereConversionsDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereCustomProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereManipulations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereOrderColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereResponsiveImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Media withoutTrashed()
 */
	class Media extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Membership
 *
 * @property string $id
 * @property string $team_id
 * @property string $user_id
 * @property string|null $role
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Membership newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Membership newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Membership query()
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereUserId($value)
 */
	class Membership extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\NewEmail
 *
 * @property string $id
 * @property string|null $created_by_id
 * @property string $number
 * @property int $status
 * @property string $applicant
 * @property string $personal_email
 * @property string $phone
 * @property string $job_title
 * @property string $manager_name
 * @property string $manager_email
 * @property string $new_email
 * @property string|null $rejected_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $created_by
 * @property-read string $number_formatted
 * @property-read string $status_formatted
 * @method static \Illuminate\Database\Eloquent\Builder|NewEmail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewEmail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewEmail query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewEmail whereApplicant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewEmail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewEmail whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewEmail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewEmail whereJobTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewEmail whereManagerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewEmail whereManagerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewEmail whereNewEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewEmail whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewEmail wherePersonalEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewEmail wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewEmail whereRejectedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewEmail whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewEmail whereUpdatedAt($value)
 */
	class NewEmail extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Numbering
 *
 * @property string $id
 * @property string $team_id
 * @property string $name
 * @property int|null $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Numbering newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Numbering newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Numbering query()
 * @method static \Illuminate\Database\Eloquent\Builder|Numbering whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Numbering whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Numbering whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Numbering whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Numbering whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Numbering whereValue($value)
 */
	class Numbering extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Permission
 *
 * @property string $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $display_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereUpdatedAt($value)
 */
	class Permission extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\RequestCounter
 *
 * @property int $id
 * @property string $month
 * @property int $count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|RequestCounter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequestCounter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequestCounter query()
 * @method static \Illuminate\Database\Eloquent\Builder|RequestCounter whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestCounter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestCounter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestCounter whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestCounter whereUpdatedAt($value)
 */
	class RequestCounter extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * App\Models\Role
 *
 * @property string $id
 * @property string $name
 * @property string $guard_name
 * @property string $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $can_manage_users
 * @property-read mixed $display_name
 * @property-read mixed $order
 * @property-read mixed $role_description
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Team
 *
 * @property string $id
 * @property string $user_id
 * @property string $name
 * @property bool $personal_team
 * @property string|null $website_disabled_notice
 * @property int $website_disabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $online_payment
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TeamInvitation> $teamInvitations
 * @property-read int|null $team_invitations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereOnlinePayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team wherePersonalTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereWebsiteDisabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereWebsiteDisabledNotice($value)
 */
	class Team extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TeamInvitation
 *
 * @property int $id
 * @property string $team_id
 * @property string $email
 * @property string|null $role
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Team $team
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation query()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation whereUpdatedAt($value)
 */
	class TeamInvitation extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TraineeBankPaymentReceipt
 *
 * @property string $id
 * @property string $team_id
 * @property string $trainee_id
 * @property int $amount
 * @property string $sender_name
 * @property string $bank_from
 * @property string $bank_to
 * @property string $uploaded_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBankPaymentReceipt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBankPaymentReceipt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBankPaymentReceipt query()
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBankPaymentReceipt whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBankPaymentReceipt whereBankFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBankPaymentReceipt whereBankTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBankPaymentReceipt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBankPaymentReceipt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBankPaymentReceipt whereSenderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBankPaymentReceipt whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBankPaymentReceipt whereTraineeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBankPaymentReceipt whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TraineeBankPaymentReceipt whereUploadedById($value)
 */
	class TraineeBankPaymentReceipt extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable, \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property string $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $phone
 * @property string|null $remember_token
 * @property string|null $timezone
 * @property string|null $current_team_id
 * @property string|null $profile_photo_path
 * @property string $locale
 * @property string|null $completed_onboarding_at
 * @property int|null $entity_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property int $verify_phone_on_login
 * @property string|null $last_verified_phone_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Back\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Team|null $currentTeam
 * @property-read mixed $inbox_messages_count
 * @property-read mixed $last_login_at_timezone
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InboxMessage> $inbox_messages_for_me
 * @property-read int|null $inbox_messages_for_me_count
 * @property-read \App\Models\Back\Instructor|null $instructor
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $ownedTeams
 * @property-read int|null $owned_teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\Team|null $team
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $teams
 * @property-read int|null $teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\Back\Trainee|null $trainee
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User findByEmail($email)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCompletedOnboardingAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastVerifiedPhoneAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereVerifyPhoneOnLogin($value)
 */
	class User extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * App\Models\Verification
 *
 * @property int $id
 * @property string $user_id
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Verification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Verification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Verification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Verification whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verification whereUserId($value)
 */
	class Verification extends \Eloquent {}
}

