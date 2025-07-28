# UK Certificates Feature - Technical Specification

## Overview
The UK Certificates feature enables bulk processing and distribution of training certificates from UK institutes. The system processes ZIP files containing PDF certificates, matches trainees by identity numbers, and sends personalized emails with attached certificates.

## Architecture

### Core Components
- **Controller**: `UkCertificatesController` - Handles file upload, processing, and job dispatching
- **Models**: `UkCertificate`, `UkCertificateRow` - Database entities for tracking imports and individual certificates
- **Jobs**: `SendUkCertificateJob`, `SendIndividualUkCertificateJob` - Asynchronous email processing
- **Mail**: `UkCertificateMail` - Email template with PDF attachment support
- **Frontend**: Vue.js component with Inertia.js integration

### File Processing Flow
1. **Upload**: ZIP file uploaded to S3 (`uk-certificates/{job_id}/original.zip`)
2. **Extraction**: PDFs extracted from S3 ZIP, individual PDFs stored to S3
3. **Matching**: Identity numbers parsed from filenames, matched against `trainees.identity_number`
4. **Manual Linking**: Unmatched trainees can be manually linked via search interface
5. **Email Dispatch**: Individual jobs queued for each certificate email

## Database Schema

### uk_certificates Table
```sql
CREATE TABLE uk_certificates (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    course_id CHAR(36) NOT NULL,
    status VARCHAR(255) DEFAULT 'processing',
    total_files INT DEFAULT 0,
    matched_count INT DEFAULT 0,
    unmatched_count INT DEFAULT 0,
    sent_count INT DEFAULT 0,
    failed_count INT DEFAULT 0,
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);
```

### uk_certificate_rows Table
```sql
CREATE TABLE uk_certificate_rows (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    uk_certificate_id BIGINT NOT NULL,
    trainee_id CHAR(36) NULL,
    identity_number VARCHAR(255),
    trainee_name VARCHAR(255),
    filename VARCHAR(255),
    pdf_path VARCHAR(255) NULL,
    sent_at TIMESTAMP NULL,
    status VARCHAR(255) DEFAULT 'pending',
    error_message TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (uk_certificate_id) REFERENCES uk_certificates(id) ON DELETE CASCADE,
    FOREIGN KEY (trainee_id) REFERENCES trainees(id) ON DELETE SET NULL
);
```

## API Endpoints

### GET /back/uk-certificates
- **Purpose**: Display UK certificates interface
- **Response**: Paginated courses with instructor and creation date
- **Authentication**: Required

### POST /back/uk-certificates/upload-zip
- **Purpose**: Process uploaded ZIP file
- **Request**: Multipart form with `zip` file and `course_id`
- **Response**: JSON with matched/unmatched trainees
- **Processing**: 
  - Uploads ZIP to S3
  - Extracts PDFs, parses filenames (`{identity_number}_{name}.pdf`)
  - Matches identity numbers to trainees
  - Stores individual PDFs to S3
  - Creates database records

### POST /back/uk-certificates/finalize
- **Purpose**: Complete import and dispatch emails
- **Request**: `import_id` (required), `mappings` (optional)
- **Processing**: 
  - Processes manual trainee mappings if provided
  - Dispatches individual email jobs
  - Updates certificate status

## File Processing Logic

### Filename Parsing
- **Format**: `{identity_number}_{trainee_name}.pdf`
- **Validation**: 
  - Skips files starting with dot (hidden/system files)
  - Validates identity number and name are non-empty
  - Rejects names that are just dots or whitespace
- **Error Handling**: Invalid files saved as failed records

### S3 Storage Structure
```
uk-certificates/
├── {job_id}/
│   ├── original.zip          # Original uploaded ZIP
│   ├── {identity_number}_{name}.pdf
│   └── {identity_number}_{name}.pdf
```

### Cleanup
- **Model Events**: Automatic S3 cleanup when `UkCertificate` records are deleted
- **Scope**: Removes entire job directory and all associated PDFs

## Email System

### Job Architecture
1. **Main Job** (`SendUkCertificateJob`): Dispatches individual jobs
2. **Individual Job** (`SendIndividualUkCertificateJob`): Sends single email
3. **Benefits**: Scalable, retryable, independent failure handling

### Email Template
- **Template**: `emails.uk-certificate.blade.php`
- **Content**: Personalized greeting, certificate message, PDF attachment
- **Language**: Arabic with trainee name interpolation

### Email Processing
- **Synchronous**: Individual emails sent synchronously to avoid serialization issues
- **Attachments**: PDF content attached using `attachData()`
- **Error Handling**: Failed emails logged with error messages

## Frontend Interface

### Vue.js Component
- **Location**: `resources/js/Pages/Back/UkCertificates/Index.vue`
- **Features**:
  - Course selection with instructor and creation date
  - File upload with validation
  - Matched/unmatched trainee display
  - Manual trainee linking via search
  - Progress tracking and error handling

### Search Integration
- **Endpoint**: `/back/search` (existing trainee search)
- **Purpose**: Manual linking of unmatched trainees
- **UI**: Real-time search results with selection

## Status Tracking

### Certificate Statuses
- `processing`: Initial upload and processing
- `sending`: Emails being dispatched
- `sent`: All emails processed
- `failed`: Processing failed

### Row Statuses
- `pending`: Ready for email sending
- `sent`: Email successfully sent
- `failed`: Email failed with error message

## Security & Validation

### File Validation
- **Type**: ZIP files only
- **Content**: PDF files within ZIP
- **Size**: Limited by server configuration
- **Naming**: Strict format validation

### Data Validation
- **Identity Numbers**: Must exist in trainees table
- **Course Selection**: Must be valid course ID
- **Email Addresses**: Validated before sending

## Performance Considerations

### Scalability
- **Queue Processing**: Individual jobs for each email
- **S3 Storage**: Efficient file storage and retrieval
- **Database**: Proper indexing on foreign keys

### Error Handling
- **Graceful Degradation**: Failed emails don't affect others
- **Retry Logic**: Individual jobs can be retried
- **Logging**: Comprehensive error logging

## Deployment Notes

### AWS ECS Compatibility
- **S3-First Processing**: Files uploaded to S3 before processing
- **Container-Friendly**: No dependency on local file system
- **Scalable**: Multiple containers can process simultaneously

### Queue Configuration
- **Queue Name**: `default`
- **Workers**: Configure based on email volume
- **Retry Policy**: Individual job retries for failed emails

## Monitoring & Reporting

### Admin Notifications
- **Summary Email**: Sent to `shafiqalshaar@adv-line.com`
- **Content**: Success/failure counts, timing, course information
- **Trigger**: After all jobs are queued

### Database Queries
- **Status Tracking**: Real-time counts of matched/unmatched/sent/failed
- **Audit Trail**: Complete record of all processing steps
- **Cleanup**: Automatic S3 cleanup on record deletion

## Future Enhancements

### Potential Improvements
- **Batch Size Limits**: Configurable batch processing limits
- **Email Templates**: Multiple template options
- **Advanced Matching**: Fuzzy matching for similar names
- **Reporting Dashboard**: Real-time processing statistics
- **Webhook Integration**: External system notifications

### Technical Debt
- **Translation Management**: Centralized translation keys
- **Error Recovery**: Enhanced retry mechanisms
- **Performance Monitoring**: Queue and processing metrics
- **Documentation**: API documentation and usage guides 