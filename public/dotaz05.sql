-- The ST_X() and ST_Y() functions returns from the POINT type column (event.coordinates) the corresponding coordinates (Y - latitude; X - longitude)
-- Type below the ORDER BY part of an SQL query to sort the records by coordinate to get one _northernmost_ record
-- It is mandatory to specify the ASC or DESC sorting method.
-- Example: ORDER BY ST_Y(coordinates) DESC LIMIT 0,1
ORDER BY ST_Y(coordinates) DESC LIMIT 0,1