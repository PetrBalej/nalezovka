-- Enter an SQL query below, the aim of which is to get the centroid from the envelope of all records (to display the center of the map/region on the web) from the event table.
-- Gradually, you will "pack" (close in) the following functions in the order listed, starting with the coordinate column:
-- 1) enter the coordinates column into ST_AsText: ST_AsText(coordinates)
-- 2) enter the expression from 1) into GROUP_CONCAT(): GROUP_CONCAT(ST_AsText(coordinates))
-- 3) enter the expression from 2) into ST_GeomFromText(): ... follow the previous pattern
-- 4) enter the expression from 3) in ST_Envelope(): ...
-- 5) enter the expression from 4) into ST_Centroid(): ...
-- 6) enter the expression from 5) in ST_AsText(): ...
-- 7) use the expression from 6) in SELECT clause and set it alias: AS center
-- 8) complete the SQL query from 7) Add a table: FROM event
