# Google Drive API Setup Guide

## Overview
This guide explains how to set up Google Drive API integration for the UK Certificates feature to properly fetch all files from Google Drive folders.

## Problem
The previous HTML scraping approach could only read ~100 certificates from Google Drive folders, but the actual folders contain 530+ certificates. This is because Google Drive only shows a limited number of files in the initial HTML response.

## Solution
Use Google Drive API instead of HTML scraping to access all files in a folder.

## Setup Steps

### 1. Create Google Cloud Project
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Enable the Google Drive API for your project

### 2. Create Service Account
1. In Google Cloud Console, go to "IAM & Admin" > "Service Accounts"
2. Click "Create Service Account"
3. Give it a name (e.g., "ETraining UK Certificates")
4. Grant it the "Google Drive API" role
5. Create and download the JSON key file

### 3. Configure Environment Variables
Add these to your `.env` file:

```env
# Google Drive API Configuration
GOOGLE_DRIVE_PROJECT_ID=your-project-id
GOOGLE_DRIVE_PRIVATE_KEY_ID=your-private-key-id
GOOGLE_DRIVE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\nYour private key here\n-----END PRIVATE KEY-----\n"
GOOGLE_DRIVE_CLIENT_EMAIL=your-service-account@your-project.iam.gserviceaccount.com
GOOGLE_DRIVE_CLIENT_ID=your-client-id
GOOGLE_DRIVE_CLIENT_X509_CERT_URL=https://www.googleapis.com/robot/v1/metadata/x509/your-service-account%40your-project.iam.gserviceaccount.com
```

### 4. Alternative: Use Credentials File
Instead of environment variables, you can place the downloaded JSON key file at:
```
storage/app/google-drive-credentials.json
```

### 5. Share Google Drive Folder
1. Make sure the Google Drive folder containing certificates is shared with your service account email
2. The service account needs at least "Viewer" access to the folder

## Testing

### Test the Debug Command
```bash
php artisan debug:google-drive "https://drive.google.com/drive/folders/1AsebhRumoWX0KjgEnVBwMrcJfQKFJ_a2"
```

This should now return all 530+ certificates instead of just 100.

### Expected Output
```
🔍 Debugging Google Drive URL: https://drive.google.com/drive/folders/1AsebhRumoWX0KjgEnVBwMrcJfQKFJ_a2
Expected: Should find all 530+ certificates in the folder

📊 Results:
  Total files found: 530

📁 First 10 files extracted:
  1. 1003497573 Wajed AqAyl A Al-Omazi.pdf (1.8 MB)
  2. 1005151566 Ohud Mohammed Bin Saad Al-Shammari.pdf (1.9 MB)
  ... and 520 more files
```

## Production Deployment

### 1. Set Environment Variables
Make sure all Google Drive API environment variables are set in your production environment.

### 2. Verify Service Account Access
Ensure the service account has access to the Google Drive folders in production.

### 3. Monitor Logs
Check Laravel logs for any Google Drive API errors:
```bash
tail -f storage/logs/laravel.log | grep "Google Drive"
```

## Troubleshooting

### Common Issues

1. **"Could not extract folder ID from URL"**
   - Ensure the URL is a valid Google Drive folder URL
   - Check the URL format: `https://drive.google.com/drive/folders/FOLDER_ID`

2. **"Failed to initialize Google Client"**
   - Verify all environment variables are set correctly
   - Check if the credentials file exists and is readable
   - Ensure the service account JSON key is valid

3. **"Access denied" or "Permission denied"**
   - Verify the service account has access to the Google Drive folder
   - Check if the folder is shared with the service account email
   - Ensure the Google Drive API is enabled in your Google Cloud project

4. **"Quota exceeded"**
   - Google Drive API has quotas. Check your usage in Google Cloud Console
   - Consider implementing rate limiting if processing large numbers of files

### Debug Commands

```bash
# Test Google Drive API connection
php artisan debug:google-drive "YOUR_DRIVE_URL"

# Check environment variables
php artisan tinker
echo config('services.google.project_id');
```

## Security Notes

- Never commit the service account JSON key to version control
- Use environment variables in production
- The service account only has read access to Google Drive
- Consider implementing additional access controls if needed

## Performance

- The API approach fetches files in batches of 1000 (Google's limit)
- Large folders are processed efficiently with pagination
- Consider implementing caching for frequently accessed folder contents
