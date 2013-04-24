@echo off
rem Der Pfad zum PHP Interpreter und zur JavaRE muss in der %PATH% Umgebungsvariable angegeben werden.
rem Zum Beispiel C:\xampp\php
rem Der Pfad zur Neo4j-Installation muss unter der %NEO4j% Umgebungsvariable gespeichert sein.

rem %NEO4j%\bin\Neo4j.bat start

echo ===================================
echo === Wanderungsdatenbetrachtung.
echo === Lass uns schauen, wie sie aufgebaut sind...
echo ===================================

php.exe -d memory_limit=800M -f wanderungsdaten.php >spaltentest.txt

PAUSE
