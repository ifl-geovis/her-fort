@ECHO OFF
ECHO Konvertiert alle DBF Dateien in CSV Dateien.
ECHO Abbrechen mit "Strg"+"C".
ECHO ======================================================
ECHO JETZT FORTFAHREN?
ECHO ======================================================
PAUSE
ECHO Zum Testen werden nur *2000.dbf-Dateien konvertiert... 
ECHO ------------------------------------------------------
REM *2000.dbf austauschen durch *.dbf oder s*.dbf o.ä.
FOR /r %%h IN (*2000.dbf) DO dbf --trim b --noconv --csv %%h.csv %%h