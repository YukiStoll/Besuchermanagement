TASKKILL /F /FI "WINDOWTITLE eq Administrator:  Laravel Worker*"
TASKKILL /F /FI "WINDOWTITLE eq Administrator:  Laravel Worker*"
C:\xampp\php\php.exe artisan queue:retry all
start worker.cmd
exit