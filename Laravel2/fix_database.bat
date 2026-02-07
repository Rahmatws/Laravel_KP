@echo off
echo ========================================
echo  Fixing Laravel Database Schema
echo ========================================
echo.

cd /d "d:\laragon\www\Laravel2"

echo Running migration...
d:\laragon\bin\php\php-8.5.0-nts-Win32-vs17-x64\php.exe artisan migrate --force

echo.
echo Running seeder to reset admin onboarding...
d:\laragon\bin\php\php-8.5.0-nts-Win32-vs17-x64\php.exe artisan db:seed --class=ResetAdminOnboardingSeeder --force

echo.
echo ========================================
echo  Done! You can now:
echo  1. Logout from the application
echo  2. Login with rahmat@gmail.com
echo  3. You will be redirected to Import CSV SID
echo ========================================
echo.
pause
