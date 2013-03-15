@echo off
rem Der Pfad zum PHP Interpreter und zur JavaRE muss in der %PATH% Umgebungsvariable angegeben werden.
rem Zum Beispiel C:\xampp\php
rem Der Pfad zur Neo4j-Installation muss unter der %NEO4j% Umgebungsvariable gespeichert sein.

echo ===================================
echo === Datenimport.
echo === Neo4j darf nicht laufen.
echo ===================================
chcp 65001
java -Xmx1G -jar lib/batch-import-jar-with-dependencies.jar %NEO4j%/data/graph.db importdata/nodes.csv importdata/rels.csv
