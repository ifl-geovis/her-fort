@echo off
rem Der Pfad zum PHP Interpreter und zur JavaRE muss in der %PATH% Umgebungsvariable angegeben werden.
rem Zum Beispiel C:\xampp\php
rem Der Pfad zur Neo4j-Installation muss unter der %NEO4j% Umgebungsvariable gespeichert sein.

echo ===================================
echo === Fehlerueberpruefung.
echo === Neo4j muss laufen.
echo ===================================

php.exe -f test.php
